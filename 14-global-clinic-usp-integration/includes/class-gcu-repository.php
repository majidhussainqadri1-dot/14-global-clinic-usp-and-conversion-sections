<?php

defined( 'ABSPATH' ) || exit;

/**
 * Canonical command/query boundary for File 14-owned records.
 */
final class GCU_Repository {
	public function active_blocks( $slot = '', $audience = 'all', $locale = 'en-US' ) {
		global $wpdb;
		$tables   = GCU_Install::tables();
		$slot     = sanitize_key( $slot );
		$audience = GCU_Policy::sanitize_audience( $audience );
		$locale   = GCU_Policy::sanitize_locale( $locale );
		$where    = "b.status = 'active' AND p.status = 'active' AND b.locale = %s";
		$args     = array( $locale );
		if ( $slot ) {
			$where .= ' AND p.slot_key = %s';
			$args[] = $slot;
		}
		if ( 'all' !== $audience ) {
			$where .= " AND (p.audience = %s OR p.audience = 'all')";
			$args[] = $audience;
		}
		$sql = "SELECT b.public_id, b.block_key, b.audience, b.block_type, b.slot_key, b.locale, b.title, b.body, b.cta_label, b.cta_destination, b.claim_keys, b.content_version, p.route_key, p.priority
			FROM {$tables['blocks']} b
			INNER JOIN {$tables['placements']} p ON p.block_key = b.block_key
			WHERE $where
			AND (p.starts_at IS NULL OR p.starts_at <= UTC_TIMESTAMP())
			AND (p.ends_at IS NULL OR p.ends_at > UTC_TIMESTAMP())
			ORDER BY p.priority ASC, b.id ASC
			LIMIT 100";
		$rows = $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		foreach ( $rows as &$row ) {
			$row['claim_keys'] = json_decode( (string) $row['claim_keys'], true );
			if ( ! is_array( $row['claim_keys'] ) ) {
				$row['claim_keys'] = array();
			}
		}
		return $rows;
	}

	public function public_claims( array $keys ) {
		global $wpdb;
		$keys = array_values( array_unique( array_filter( array_map( 'sanitize_key', $keys ) ) ) );
		if ( empty( $keys ) ) {
			return array();
		}
		$tables = GCU_Install::tables();
		$marks  = implode( ',', array_fill( 0, count( $keys ), '%s' ) );
		$sql    = "SELECT claim_key, claim_text, basis, owner_name, effective_at, review_due_at, expires_at
			FROM {$tables['claims']}
			WHERE claim_key IN ($marks) AND status = 'active' AND is_public = 1
			AND (expires_at IS NULL OR expires_at > UTC_TIMESTAMP())
			ORDER BY claim_key ASC";
		$rows = $wpdb->get_results( $wpdb->prepare( $sql, $keys ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$out  = array();
		foreach ( $rows as $row ) {
			$out[ $row['claim_key'] ] = $row;
		}
		return $out;
	}

	public function create_content_draft( array $data, $actor_id ) {
		$auth = GCU_Capabilities::require_capability( GCU_Capabilities::MANAGE_CONTENT, null, 'create_content_draft' );
		if ( is_wp_error( $auth ) ) {
			return $auth;
		}
		global $wpdb;
		$tables = GCU_Install::tables();
		$key    = sanitize_key( isset( $data['block_key'] ) ? $data['block_key'] : '' );
		if ( ! $key ) {
			return new WP_Error( 'gcu_invalid_block_key', __( 'A valid block key is required.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$locale = GCU_Policy::sanitize_locale( isset( $data['locale'] ) ? $data['locale'] : 'en-US' );
		$latest = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(content_version) FROM {$tables['blocks']} WHERE block_key = %s AND locale = %s", $key, $locale ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$now    = current_time( 'mysql', true );
		$record = array(
			'public_id'       => wp_generate_uuid4(),
			'block_key'       => $key,
			'audience'        => GCU_Policy::sanitize_audience( isset( $data['audience'] ) ? $data['audience'] : 'all' ),
			'block_type'      => sanitize_key( isset( $data['block_type'] ) ? $data['block_type'] : 'content' ),
			'slot_key'        => sanitize_key( isset( $data['slot_key'] ) ? $data['slot_key'] : 'global_clinic_primary' ),
			'locale'          => $locale,
			'title'           => sanitize_text_field( isset( $data['title'] ) ? $data['title'] : '' ),
			'body'            => wp_kses_post( isset( $data['body'] ) ? $data['body'] : '' ),
			'cta_label'       => sanitize_text_field( isset( $data['cta_label'] ) ? $data['cta_label'] : '' ),
			'cta_destination' => sanitize_key( isset( $data['cta_destination'] ) ? $data['cta_destination'] : '' ),
			'claim_keys'      => wp_json_encode( array_values( array_unique( array_filter( array_map( 'sanitize_key', isset( $data['claim_keys'] ) && is_array( $data['claim_keys'] ) ? $data['claim_keys'] : array() ) ) ) ) ),
			'status'          => 'draft',
			'content_version' => $latest + 1,
			'created_by'      => absint( $actor_id ),
			'created_at'      => $now,
			'updated_at'      => $now,
		);
		if ( '' === $record['title'] || '' === trim( wp_strip_all_tags( $record['body'] ) ) ) {
			return new WP_Error( 'gcu_incomplete_content', __( 'Title and body are required.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$ok = $wpdb->insert( $tables['blocks'], $record );
		if ( false === $ok ) {
			return new WP_Error( 'gcu_write_failed', __( 'The content version could not be created.', 'global-clinic-usp-integration' ), array( 'status' => 500 ) );
		}
		$this->audit( 'content_created', 'content_block', $record['public_id'], 'content_governance', '', array(), $record );
		return $this->content_by_public_id( $record['public_id'] );
	}

	public function transition( $machine, $public_id, $expected_version, $target, $reason = '' ) {
		$machine = sanitize_key( $machine );
		$target  = sanitize_key( $target );
		$map = array(
			'copy'       => array( 'table' => 'blocks', 'cap' => GCU_Capabilities::MANAGE_CONTENT, 'status' => 'status' ),
			'placement'  => array( 'table' => 'placements', 'cap' => GCU_Capabilities::MANAGE_PLACEMENTS, 'status' => 'status' ),
			'experiment' => array( 'table' => 'experiments', 'cap' => GCU_Capabilities::MANAGE_EXPERIMENTS, 'status' => 'status' ),
		);
		if ( ! isset( $map[ $machine ] ) ) {
			return new WP_Error( 'gcu_invalid_machine', __( 'Unknown workflow type.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$auth = GCU_Capabilities::require_capability( $map[ $machine ]['cap'], $public_id, 'transition_' . $machine );
		if ( is_wp_error( $auth ) ) {
			return $auth;
		}
		if ( 'copy' === $machine && in_array( $target, array( 'founder_approved', 'active', 'withdrawn' ), true ) ) {
			$approval = GCU_Capabilities::require_capability( GCU_Capabilities::APPROVE_CLAIMS, $public_id, 'approve_copy' );
			if ( is_wp_error( $approval ) ) {
				return $approval;
			}
		}
		if ( 'experiment' === $machine && in_array( $target, array( 'approved', 'adopted' ), true ) ) {
			$approval = GCU_Capabilities::require_capability( GCU_Capabilities::APPROVE_CLAIMS, $public_id, 'founder_approve_experiment' );
			if ( is_wp_error( $approval ) ) {
				return $approval;
			}
		}
		global $wpdb;
		$tables = GCU_Install::tables();
		$table  = $tables[ $map[ $machine ]['table'] ];
		$row    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE public_id = %s", sanitize_text_field( $public_id ) ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( ! $row ) {
			return new WP_Error( 'gcu_not_found', __( 'The requested record was not found.', 'global-clinic-usp-integration' ), array( 'status' => 404 ) );
		}
		if ( (int) $row['row_version'] !== (int) $expected_version ) {
			return new WP_Error( 'gcu_version_conflict', __( 'The record changed. Reload it before retrying.', 'global-clinic-usp-integration' ), array( 'status' => 409, 'current_version' => (int) $row['row_version'] ) );
		}
		if ( 'experiment' === $machine && 'running' === $target ) {
			if ( empty( $row['hypothesis'] ) || empty( $row['variants'] ) || empty( $row['guardrails'] ) || empty( $row['success_metric'] ) || empty( $row['sample_policy'] ) || empty( $row['privacy_policy'] ) || empty( $row['ends_at'] ) ) {
				return new WP_Error( 'gcu_experiment_incomplete', __( 'The approved experiment is missing required governance fields.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
			}
			if ( strtotime( $row['ends_at'] . ' UTC' ) <= time() ) {
				return new WP_Error( 'gcu_experiment_window_invalid', __( 'The experiment end date must be in the future.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
			}
		}
		if ( ! GCU_Policy::transition_allowed( $machine, $row['status'], $target ) ) {
			return new WP_Error( 'gcu_invalid_transition', __( 'This state transition is not allowed.', 'global-clinic-usp-integration' ), array( 'status' => 409, 'from' => $row['status'], 'to' => $target ) );
		}
		$update = array(
			'status'      => $target,
			'row_version' => (int) $row['row_version'] + 1,
			'updated_at'  => current_time( 'mysql', true ),
		);
		if ( 'copy' === $machine && 'founder_approved' === $target ) {
			$update['approved_by'] = get_current_user_id();
			$update['approved_at'] = current_time( 'mysql', true );
		}
		if ( 'experiment' === $machine && 'approved' === $target ) {
			$update['approved_by'] = get_current_user_id();
		}
		$formats = array_fill( 0, count( $update ), '%s' );
		$updated = $wpdb->update( $table, $update, array( 'id' => (int) $row['id'], 'row_version' => (int) $expected_version ), $formats, array( '%d', '%d' ) );
		if ( 1 !== $updated ) {
			return new WP_Error( 'gcu_concurrent_update', __( 'A concurrent update prevented this transition.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
		}
		$this->audit( 'state_transition', $machine, $public_id, 'workflow_governance', $reason, $row, array_merge( $row, $update ) );
		if ( 'copy' === $machine && in_array( $target, array( 'active', 'withdrawn', 'superseded' ), true ) ) {
			wp_cache_delete( 'gcu_public_blocks', 'gcu' );
			$this->publish_event( 'ClinicUSPContentPublished.v1', array( 'public_id' => $public_id, 'status' => $target ) );
		}
		return array( 'public_id' => $public_id, 'status' => $target, 'row_version' => $update['row_version'] );
	}


	public function create_placement( array $data ) {
		$auth = GCU_Capabilities::require_capability( GCU_Capabilities::MANAGE_PLACEMENTS, null, 'create_placement' );
		if ( is_wp_error( $auth ) ) {
			return $auth;
		}
		$key = sanitize_key( isset( $data['placement_key'] ) ? $data['placement_key'] : '' );
		$block_key = sanitize_key( isset( $data['block_key'] ) ? $data['block_key'] : '' );
		$route_key = sanitize_key( isset( $data['route_key'] ) ? $data['route_key'] : 'global_clinic' );
		$slot_key = sanitize_key( isset( $data['slot_key'] ) ? $data['slot_key'] : '' );
		$allowed_slots = array( 'global_clinic_primary', 'global_clinic_trust', 'global_clinic_steps', 'global_clinic_faq' );
		if ( ! $key || ! $block_key || 'global_clinic' !== $route_key || ! in_array( $slot_key, $allowed_slots, true ) ) {
			return new WP_Error( 'gcu_invalid_placement', __( 'The placement contract is incomplete or outside File 14 ownership.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		global $wpdb;
		$tables = GCU_Install::tables();
		$now = current_time( 'mysql', true );
		$record = array(
			'public_id' => wp_generate_uuid4(), 'placement_key' => $key, 'block_key' => $block_key,
			'route_key' => $route_key, 'slot_key' => $slot_key,
			'audience' => GCU_Policy::sanitize_audience( isset( $data['audience'] ) ? $data['audience'] : 'all' ),
			'priority' => max( 1, min( 1000, absint( isset( $data['priority'] ) ? $data['priority'] : 100 ) ) ),
			'status' => 'planned', 'starts_at' => null, 'ends_at' => null,
			'created_by' => get_current_user_id(), 'created_at' => $now, 'updated_at' => $now,
		);
		$ok = $wpdb->insert( $tables['placements'], $record );
		if ( false === $ok ) {
			return new WP_Error( 'gcu_placement_write_failed', __( 'The placement could not be created or conflicts with an existing key.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
		}
		$this->audit( 'placement_created', 'placement', $record['public_id'], 'placement_governance', '', array(), $record );
		return $this->record_by_public_id( 'placements', $record['public_id'] );
	}

	public function create_experiment( array $data ) {
		$auth = GCU_Capabilities::require_capability( GCU_Capabilities::MANAGE_EXPERIMENTS, null, 'create_experiment' );
		if ( is_wp_error( $auth ) ) {
			return $auth;
		}
		$key = sanitize_key( isset( $data['experiment_key'] ) ? $data['experiment_key'] : '' );
		$variants = isset( $data['variants'] ) && is_array( $data['variants'] ) ? array_values( array_filter( array_map( 'sanitize_text_field', $data['variants'] ) ) ) : array();
		$guardrails = isset( $data['guardrails'] ) && is_array( $data['guardrails'] ) ? array_values( array_filter( array_map( 'sanitize_text_field', $data['guardrails'] ) ) ) : array();
		$hypothesis = sanitize_textarea_field( isset( $data['hypothesis'] ) ? $data['hypothesis'] : '' );
		$success_metric = sanitize_text_field( isset( $data['success_metric'] ) ? $data['success_metric'] : '' );
		$sample_policy = sanitize_textarea_field( isset( $data['sample_policy'] ) ? $data['sample_policy'] : '' );
		$privacy_policy = sanitize_textarea_field( isset( $data['privacy_policy'] ) ? $data['privacy_policy'] : '' );
		$ends_at = isset( $data['ends_at'] ) ? sanitize_text_field( $data['ends_at'] ) : '';
		if ( ! $key || ! $hypothesis || count( $variants ) < 2 || ! $guardrails || ! $success_metric || ! $sample_policy || ! $privacy_policy || ! $ends_at || false === strtotime( $ends_at . ' UTC' ) ) {
			return new WP_Error( 'gcu_invalid_experiment', __( 'Hypothesis, at least two variants, guardrails, success metric, sample/privacy policy and a valid end date are required.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		global $wpdb;
		$tables = GCU_Install::tables();
		$now = current_time( 'mysql', true );
		$record = array(
			'public_id' => wp_generate_uuid4(), 'experiment_key' => $key, 'hypothesis' => $hypothesis,
			'variants' => wp_json_encode( $variants ), 'audience' => GCU_Policy::sanitize_audience( isset( $data['audience'] ) ? $data['audience'] : 'all' ),
			'guardrails' => wp_json_encode( $guardrails ), 'success_metric' => $success_metric,
			'sample_policy' => $sample_policy, 'privacy_policy' => $privacy_policy, 'status' => 'proposed',
			'starts_at' => null, 'ends_at' => gmdate( 'Y-m-d H:i:s', strtotime( $ends_at . ' UTC' ) ),
			'created_by' => get_current_user_id(), 'created_at' => $now, 'updated_at' => $now,
		);
		$ok = $wpdb->insert( $tables['experiments'], $record );
		if ( false === $ok ) {
			return new WP_Error( 'gcu_experiment_write_failed', __( 'The experiment could not be created or conflicts with an existing key.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
		}
		$this->audit( 'experiment_proposed', 'experiment', $record['public_id'], 'experiment_governance', '', array(), $record );
		return $this->record_by_public_id( 'experiments', $record['public_id'] );
	}

	public function withdraw_claim( $claim_key, $expected_version, $reason ) {
		$auth = GCU_Capabilities::require_capability( GCU_Capabilities::APPROVE_CLAIMS, $claim_key, 'withdraw_claim' );
		if ( is_wp_error( $auth ) ) {
			return $auth;
		}
		global $wpdb;
		$tables = GCU_Install::tables();
		$key = sanitize_key( $claim_key );
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tables['claims']} WHERE claim_key = %s", $key ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( ! $row ) {
			return new WP_Error( 'gcu_claim_not_found', __( 'The claim was not found.', 'global-clinic-usp-integration' ), array( 'status' => 404 ) );
		}
		if ( (int) $row['row_version'] !== (int) $expected_version ) {
			return new WP_Error( 'gcu_version_conflict', __( 'The claim changed. Reload it before retrying.', 'global-clinic-usp-integration' ), array( 'status' => 409, 'current_version' => (int) $row['row_version'] ) );
		}
		$update = array( 'status' => 'withdrawn', 'is_public' => 0, 'row_version' => (int) $row['row_version'] + 1, 'updated_at' => current_time( 'mysql', true ) );
		$done = $wpdb->update( $tables['claims'], $update, array( 'id' => (int) $row['id'], 'row_version' => (int) $expected_version ), array( '%s', '%d', '%d', '%s' ), array( '%d', '%d' ) );
		if ( 1 !== $done ) {
			return new WP_Error( 'gcu_concurrent_update', __( 'A concurrent update prevented claim withdrawal.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
		}
		wp_cache_delete( 'gcu_public_blocks', 'gcu' );
		$this->audit( 'claim_withdrawn', 'claim', $key, 'claim_governance', $reason, $row, array_merge( $row, $update ) );
		$this->publish_event( 'ClinicUSPClaimWithdrawn.v1', array( 'claim_key' => $key, 'reason_code' => sanitize_key( $reason ) ) );
		return array( 'claim_key' => $key, 'status' => 'withdrawn', 'row_version' => $update['row_version'] );
	}

	public function record_by_public_id( $table_key, $public_id ) {
		global $wpdb;
		$tables = GCU_Install::tables();
		if ( ! isset( $tables[ $table_key ] ) || ! in_array( $table_key, array( 'blocks', 'placements', 'experiments' ), true ) ) {
			return null;
		}
		$table = $tables[ $table_key ];
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE public_id = %s", sanitize_text_field( $public_id ) ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	public function record_event( array $data ) {
		if ( ! GCU_Policy::analytics_consent() ) {
			return new WP_Error( 'gcu_consent_required', __( 'Measurement consent is required.', 'global-clinic-usp-integration' ), array( 'status' => 403 ) );
		}
		$stage = sanitize_key( isset( $data['stage'] ) ? $data['stage'] : '' );
		$allowed = array( 'impression', 'cta_selected', 'destination_loaded', 'application_started', 'booking_started' );
		if ( ! in_array( $stage, $allowed, true ) ) {
			return new WP_Error( 'gcu_invalid_stage', __( 'Unknown funnel stage.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		global $wpdb;
		$tables   = GCU_Install::tables();
		$campaign = GCU_Policy::sanitize_campaign( isset( $data['campaign'] ) && is_array( $data['campaign'] ) ? $data['campaign'] : array() );
		$event_id = isset( $data['event_id'] ) && wp_is_uuid( $data['event_id'] ) ? $data['event_id'] : wp_generate_uuid4();
		$inserted = $wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO {$tables['events']} (event_id,event_type,event_version,funnel_stage,destination_key,source_value,medium_value,campaign_value,ref_value,consent_state,occurred_at,created_at) VALUES (%s,%s,1,%s,%s,%s,%s,%s,%s,'granted',%s,%s)",
				$event_id,
				'ClinicUSPFunnelEvent',
				$stage,
				sanitize_key( isset( $data['destination'] ) ? $data['destination'] : '' ),
				$campaign['source'],
				$campaign['medium'],
				$campaign['campaign'],
				$campaign['ref'],
				current_time( 'mysql', true ),
				current_time( 'mysql', true )
			)
		); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( false === $inserted ) {
			return new WP_Error( 'gcu_event_write_failed', __( 'The event could not be recorded.', 'global-clinic-usp-integration' ), array( 'status' => 500 ) );
		}
		if ( 'cta_selected' === $stage ) {
			$this->publish_event( 'ClinicUSPCTASelected.v1', array( 'event_id' => $event_id, 'destination' => sanitize_key( isset( $data['destination'] ) ? $data['destination'] : '' ) ) );
		}
		return array( 'event_id' => $event_id, 'deduplicated' => 0 === $inserted );
	}

	public function funnel_summary( $days = 30 ) {
		$auth = GCU_Capabilities::require_capability( GCU_Capabilities::VIEW_ANALYTICS, null, 'view_funnel_summary' );
		if ( is_wp_error( $auth ) ) {
			return $auth;
		}
		global $wpdb;
		$days   = max( 1, min( 365, absint( $days ) ) );
		$tables = GCU_Install::tables();
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT funnel_stage, COUNT(*) AS total FROM {$tables['events']} WHERE occurred_at >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL %d DAY) GROUP BY funnel_stage ORDER BY funnel_stage",
				$days
			),
			ARRAY_A
		); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$total = array_sum( array_map( static function ( $row ) { return (int) $row['total']; }, $rows ) );
		if ( $total < 10 ) {
			return array( 'suppressed' => true, 'threshold' => 10, 'days' => $days, 'stages' => array() );
		}
		return array( 'suppressed' => false, 'threshold' => 10, 'days' => $days, 'stages' => $rows );
	}

	public function content_by_public_id( $public_id ) {
		global $wpdb;
		$tables = GCU_Install::tables();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tables['blocks']} WHERE public_id = %s", sanitize_text_field( $public_id ) ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	public function audit( $action, $object_type, $object_id, $purpose, $reason, array $before, array $after ) {
		global $wpdb;
		$tables = GCU_Install::tables();
		$trace  = GCU_Policy::trace_id();
		$wpdb->insert(
			$tables['audit'],
			array(
				'trace_id'    => $trace,
				'actor_id'    => get_current_user_id(),
				'action_name' => sanitize_key( $action ),
				'object_type' => sanitize_key( $object_type ),
				'object_id'   => sanitize_text_field( $object_id ),
				'purpose'     => sanitize_key( $purpose ),
				'reason'      => sanitize_textarea_field( $reason ),
				'before_hash' => empty( $before ) ? null : hash( 'sha256', wp_json_encode( $before ) ),
				'after_hash'  => empty( $after ) ? null : hash( 'sha256', wp_json_encode( $after ) ),
				'created_at'  => current_time( 'mysql', true ),
			),
			array( '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
		return $trace;
	}

	public function publish_event( $event_name, array $payload ) {
		global $wpdb;
		$tables = GCU_Install::tables();
		$event_id = wp_generate_uuid4();
		$envelope = array(
			'event_id' => $event_id, 'event_name' => sanitize_text_field( $event_name ), 'event_version' => 1,
			'occurred_at' => gmdate( 'c' ), 'producer' => 'File 14', 'payload' => $payload,
		);
		$inserted = $wpdb->insert(
			$tables['outbox'],
			array( 'event_id'=>$event_id, 'event_name'=>$envelope['event_name'], 'event_version'=>1, 'payload'=>wp_json_encode( $envelope ), 'status'=>'pending', 'attempts'=>0, 'next_attempt_at'=>current_time( 'mysql', true ), 'created_at'=>current_time( 'mysql', true ) ),
			array( '%s','%s','%d','%s','%s','%d','%s','%s' )
		);
		if ( false === $inserted ) {
			GCU_Observability::log( 'error', 'outbox_write_failed', array( 'event_name' => $event_name ) );
			return false;
		}
		$this->dispatch_outbox( $event_id );
		return $event_id;
	}

	public function dispatch_outbox( $event_id = '', $limit = 20 ) {
		global $wpdb;
		$tables = GCU_Install::tables();
		$where = "status IN ('pending','retry') AND (next_attempt_at IS NULL OR next_attempt_at <= UTC_TIMESTAMP())";
		$args = array();
		if ( $event_id ) {
			$where .= ' AND event_id = %s';
			$args[] = sanitize_text_field( $event_id );
		}
		$limit = max( 1, min( 100, absint( $limit ) ) );
		$sql = "SELECT * FROM {$tables['outbox']} WHERE $where ORDER BY id ASC LIMIT $limit";
		$rows = $args ? $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A ) : $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		foreach ( $rows as $row ) {
			$envelope = json_decode( (string) $row['payload'], true );
			if ( ! is_array( $envelope ) ) {
				$wpdb->update( $tables['outbox'], array( 'status'=>'dead', 'last_error_code'=>'invalid_payload', 'attempts'=>(int)$row['attempts']+1 ), array( 'id'=>(int)$row['id'] ) );
				continue;
			}
			try {
				do_action( 'gcu_platform_event_v1', $envelope );
				do_action( 'sabri_platform_event', $row['event_name'], $envelope );
				$wpdb->update( $tables['outbox'], array( 'status'=>'dispatched', 'attempts'=>(int)$row['attempts']+1, 'dispatched_at'=>current_time( 'mysql', true ), 'last_error_code'=>null ), array( 'id'=>(int)$row['id'] ) );
			} catch ( Throwable $error ) {
				$attempts = (int) $row['attempts'] + 1;
				$status = $attempts >= 5 ? 'dead' : 'retry';
				$delay = min( 3600, 60 * ( 2 ** min( 5, $attempts ) ) );
				$wpdb->update( $tables['outbox'], array( 'status'=>$status, 'attempts'=>$attempts, 'next_attempt_at'=>gmdate( 'Y-m-d H:i:s', time()+$delay ), 'last_error_code'=>'consumer_failure' ), array( 'id'=>(int)$row['id'] ) );
				GCU_Observability::log( 'warning', 'outbox_dispatch_failed', array( 'event_name'=>$row['event_name'], 'attempts'=>$attempts ) );
			}
		}
		return count( $rows );
	}

	public function accept_inbound_event( $event_name, array $payload ) {
		global $wpdb;
		$tables = GCU_Install::tables();
		$event_id = isset( $payload['event_id'] ) && wp_is_uuid( $payload['event_id'] ) ? $payload['event_id'] : '';
		if ( ! $event_id ) {
			return false;
		}
		$inserted = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$tables['inbox']} (event_id,event_name,payload_hash,processed_at) VALUES (%s,%s,%s,%s)", $event_id, sanitize_text_field( $event_name ), hash( 'sha256', wp_json_encode( $payload ) ), current_time( 'mysql', true ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return 1 === $inserted;
	}
}

