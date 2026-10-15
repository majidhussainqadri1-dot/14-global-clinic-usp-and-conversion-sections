<?php

defined( 'ABSPATH' ) || exit;

/**
 * Fifth independent 80-pass review hardening for File 14.
 *
 * This layer is deliberately narrow: it strengthens fail-closed runtime schema
 * truth, Founder approval for public Future governance, conversion-event replay
 * identity, AI-copy privacy/multilingual safety, and audited experiment early-stop
 * behavior without taking canonical ownership from Files 00/07/08/09/20/24/25.
 */
final class GCU_Fifth_Review_Hardening {
	private static $schema_gate = null;
	private static $schema_gate_running = false;

	public static function bootstrap() {
		add_filter( 'pre_option_gcu_enabled', array( __CLASS__, 'runtime_schema_gate' ), 1, 3 );
		add_filter( 'rest_request_before_callbacks', array( __CLASS__, 'guard_rest_request' ), 1, 3 );
		add_filter( 'rest_post_dispatch', array( __CLASS__, 'harden_rest_response' ), 1, 3 );

		// Replace the non-transactional Future early-stop worker with the reviewed
		// transaction + audit implementation below. Anomaly measurement remains native.
		remove_action( 'gcu_future_hourly_intelligence', array( 'GCU_Future_Intelligence', 'hourly_intelligence' ) );
		add_action( 'gcu_future_hourly_intelligence', array( __CLASS__, 'hourly_intelligence' ) );
	}

	/**
	 * Make runtime readiness depend on actual table/engine/column verification,
	 * not merely version options. The result is cached only for this PHP request.
	 */
	public static function runtime_schema_gate( $pre_option, $option = 'gcu_enabled', $default_value = false ) {
		unset( $option, $default_value );
		if ( false !== $pre_option ) {
			return $pre_option;
		}
		if ( true === self::$schema_gate_running ) {
			return $pre_option;
		}
		if ( null !== self::$schema_gate ) {
			return self::$schema_gate ? $pre_option : 0;
		}
		if ( ! class_exists( 'GCU_Install' ) ) {
			return $pre_option;
		}

		self::$schema_gate_running = true;
		$base = GCU_Install::verify_schema();
		$future = true;
		if ( class_exists( 'GCU_Future_Intelligence' ) ) {
			$future = GCU_Future_Intelligence::verify_schema();
		}
		self::$schema_gate_running = false;
		self::$schema_gate = ! is_wp_error( $base ) && ! is_wp_error( $future );

		return self::$schema_gate ? $pre_option : 0;
	}

	public static function guard_rest_request( $response, $handler, WP_REST_Request $request ) {
		unset( $handler );
		if ( null !== $response ) {
			return $response;
		}

		$route = $request->get_route();

		// Founder-level approval is mandatory before a Future governance record can
		// become public or active. Content-management permission alone is insufficient.
		if ( '/gcu/v1/future/records' === $route && self::is_mutating_request( $request ) ) {
			$data = $request->get_json_params();
			$data = is_array( $data ) ? $data : array();
			$types = GCU_Future_Policy::validate_future_record_input_types( $data );
			if ( ! $types['safe'] ) {
				return new WP_Error( 'gcu_fifth_governance_input_type_invalid', __( 'Governance fields have invalid types.', 'global-clinic-usp-integration' ), array( 'status' => 400, 'fields' => $types['invalid_fields'] ) );
			}
			$status = sanitize_key( isset( $data['status'] ) ? $data['status'] : 'draft' );
			$is_public = isset( $data['is_public'] ) && in_array( $data['is_public'], array( true, 1, '1' ), true );
			if ( 'active' === $status || $is_public ) {
				$approved = GCU_Capabilities::require_capability(
					GCU_Capabilities::APPROVE_CLAIMS,
					isset( $data['record_key'] ) ? sanitize_key( $data['record_key'] ) : null,
					'future_public_governance'
				);
				if ( is_wp_error( $approved ) ) {
					return $approved;
				}
			}
		}

		// Future AI copy is a copy-governance helper, never a path for sending
		// patient/contact/identity/clinical detail to a provider adapter.
		if ( '/gcu/v1/future/ai-copy' === $route && self::is_mutating_request( $request ) ) {
			$data = $request->get_json_params();
			$data = is_array( $data ) ? $data : array();
			if ( isset( $data['base_text'] ) && ! is_string( $data['base_text'] ) ) {
				return new WP_Error( 'gcu_fifth_ai_input_type_invalid', __( 'AI copy text must be a plain string.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
			}
			$base = isset( $data['base_text'] ) ? $data['base_text'] : '';
			if ( self::contains_sensitive_copy_data( $base ) ) {
				return new WP_Error(
					'gcu_fifth_ai_sensitive_input_blocked',
					__( 'AI copy assistance cannot receive personal, contact, identity or clinical details.', 'global-clinic-usp-integration' ),
					array( 'status' => 400 )
				);
			}
		}

		// A reused conversion event UUID is idempotent only when the event identity
		// is the same. Conflicting stage/destination/subject/campaign reuse is rejected.
		if ( '/gcu/v1/events' === $route && self::is_mutating_request( $request ) ) {
			$conflict = self::conversion_event_identity_conflict( $request );
			if ( is_wp_error( $conflict ) ) {
				return $conflict;
			}
		}

		return $response;
	}

	public static function harden_rest_response( $response, $server, WP_REST_Request $request ) {
		unset( $server );
		if ( '/gcu/v1/future/ai-copy' !== $request->get_route() || ! ( $response instanceof WP_REST_Response ) ) {
			return $response;
		}
		$data = $response->get_data();
		if ( ! is_array( $data ) || empty( $data['candidates'] ) || ! is_array( $data['candidates'] ) ) {
			return $response;
		}

		$safe = array();
		$rejected = isset( $data['rejected'] ) && is_array( $data['rejected'] ) ? $data['rejected'] : array();
		foreach ( array_slice( $data['candidates'], 0, 5 ) as $candidate ) {
			if ( ! is_array( $candidate ) || ! isset( $candidate['text'] ) || ! is_string( $candidate['text'] ) ) {
				$rejected[] = array( 'text_hash' => null, 'guard' => array( 'safe' => false, 'flags' => array( 'candidate_text_invalid' ) ) );
				continue;
			}
			$text = $candidate['text'];
			$scan = GCU_Review80_Hardening::multilingual_dark_pattern_scan( $text );
			if ( ! empty( $scan['safe'] ) ) {
				$safe[] = $candidate;
				continue;
			}
			$rejected[] = array(
				'text_hash' => hash( 'sha256', $text ),
				'guard' => array( 'safe' => false, 'multilingual_flags' => isset( $scan['flags'] ) ? $scan['flags'] : array() ),
			);
		}
		$data['candidates'] = $safe;
		$data['rejected'] = $rejected;
		$data['multilingual_guard_applied'] = true;
		$response->set_data( $data );
		return $response;
	}

	private static function conversion_event_identity_conflict( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$data = is_array( $data ) ? $data : array();
		$event_id = isset( $data['event_id'] ) && is_string( $data['event_id'] ) ? sanitize_text_field( $data['event_id'] ) : '';
		if ( '' === $event_id || ! wp_is_uuid( $event_id ) ) {
			return true;
		}

		global $wpdb;
		$tables = GCU_Install::tables();
		$existing = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT funnel_stage,destination_key,subject_hash,source_value,medium_value,campaign_value,ref_value FROM {$tables['events']} WHERE event_id=%s",
				$event_id
			),
			ARRAY_A
		);
		if ( ! is_array( $existing ) ) {
			return true;
		}

		$stage = isset( $data['stage'] ) && is_string( $data['stage'] ) ? sanitize_key( $data['stage'] ) : '';
		$destination = isset( $data['destination'] ) && is_string( $data['destination'] ) ? sanitize_key( $data['destination'] ) : '';
		$campaign = GCU_Plugin::instance()->privacy()->current_campaign();
		$campaign = is_array( $campaign ) ? GCU_Policy::sanitize_campaign( $campaign ) : array( 'source' => '', 'medium' => '', 'campaign' => '', 'ref' => '' );
		$subject = GCU_Plugin::instance()->privacy()->event_subject_hash();

		$same = hash_equals( (string) $existing['funnel_stage'], $stage )
			&& hash_equals( (string) $existing['destination_key'], $destination )
			&& hash_equals( (string) $existing['subject_hash'], (string) $subject )
			&& hash_equals( (string) $existing['source_value'], (string) $campaign['source'] )
			&& hash_equals( (string) $existing['medium_value'], (string) $campaign['medium'] )
			&& hash_equals( (string) $existing['campaign_value'], (string) $campaign['campaign'] )
			&& hash_equals( (string) $existing['ref_value'], (string) $campaign['ref'] );

		if ( $same ) {
			return true;
		}
		if ( class_exists( 'GCU_Observability' ) ) {
			GCU_Observability::log( 'warning', 'conversion_event_identity_conflict', array( 'event_id' => $event_id ) );
		}
		return new WP_Error(
			'gcu_conversion_event_identity_conflict',
			__( 'This event identifier was already used for a different conversion event.', 'global-clinic-usp-integration' ),
			array( 'status' => 409 )
		);
	}

	public static function hourly_intelligence() {
		$ready = GCU_Future_Intelligence::runtime_ready();
		if ( is_wp_error( $ready ) ) {
			return $ready;
		}
		GCU_Future_Intelligence::anomaly_detector();
		return self::transactional_early_stop_guard();
	}

	/**
	 * Stop running experiments only when the state update and its mandatory audit
	 * record can commit together. An audit failure therefore cannot leave an
	 * unaudited stopped experiment behind.
	 */
	public static function transactional_early_stop_guard() {
		$parity = GCU_Future_Intelligence::parity_status();
		$anomaly = get_option( GCU_Future_Intelligence::LAST_ANOMALY_OPTION, array() );
		$destinations = GCU_Plugin::instance()->contracts()->all_destination_health();
		$destination_failure = false;
		foreach ( $destinations as $destination ) {
			if ( empty( $destination['available'] ) ) {
				$destination_failure = true;
				break;
			}
		}

		global $wpdb;
		$future_tables = GCU_Future_Intelligence::tables();
		$open_reports_result = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$future_tables['reports']} WHERE status IN ('open','reviewing') AND created_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 7 DAY)"
		);
		if ( false === $open_reports_result || null === $open_reports_result || ! is_numeric( $open_reports_result ) ) {
			GCU_Observability::log( 'error', 'experiment_complaint_count_unverified', array() );
			return new WP_Error( 'gcu_experiment_complaint_count_failed', __( 'Experiment complaint safety could not be verified.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
		}
		$open_reports = (int) $open_reports_result;
		$breach = empty( $parity['ok'] )
			|| $destination_failure
			|| $open_reports >= 5
			|| ( isset( $anomaly['severity'] ) && 'high' === $anomaly['severity'] );
		$tables = GCU_Install::tables();
		// F14-FUT-17: the approved experiment window is authoritative, even
		// when every trust/destination/complaint signal remains healthy. Expiry
		// must trigger an audited stop rather than silently extending exposure.
		$expired_where = "(ends_at IS NULL OR ends_at<=UTC_TIMESTAMP())";
		$expired_count = null;
		if ( ! $breach ) {
			$expired_count = $wpdb->get_var(
				"SELECT COUNT(*) FROM {$tables['experiments']} WHERE status='running' AND {$expired_where}"
			);
			if ( null === $expired_count || false === $expired_count || ! is_numeric( $expired_count ) || (float) $expired_count < 0 ) {
				GCU_Observability::log( 'error', 'experiment_expiry_check_unverified', array() );
				return new WP_Error( 'gcu_experiment_expiry_check_failed', __( 'Experiment window safety could not be verified.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
			}
			if ( 0 === (int) $expired_count ) { return 0; }
		}
		$expiration_filter = $breach ? '' : " AND {$expired_where}";
		// Read ONE extra row beyond the bounded 50-stop transaction workload.
		// Without this sentinel, a trust breach with 51+ running experiments
		// returns "50 stopped" even though the safety sweep is incomplete.
		$rows = $wpdb->get_results(
			"SELECT * FROM {$tables['experiments']} WHERE status='running'{$expiration_filter} LIMIT 51", ARRAY_A
		);
		if ( ! is_array( $rows ) ) {
			GCU_Observability::log( 'error', 'experiment_running_list_unverified', array() );
			return new WP_Error( 'gcu_experiment_running_list_failed', __( 'Running experiment safety could not be verified.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
		}
		if ( ! $breach && (int) $expired_count > 0 && 0 === count( $rows ) ) {
			// A positive DB count followed by an empty result is conflicting
			// evidence (or a concurrent change), never proof of a clean stop.
			GCU_Observability::log( 'error', 'experiment_expiry_list_mismatch', array() );
			return new WP_Error( 'gcu_experiment_expiry_list_mismatch', __( 'Experiment expiry count and stop list disagreed; verification is required.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
		}
		$backlog = count( $rows ) > 50;
		if ( $backlog ) { $rows = array_slice( $rows, 0, 50 ); }
		$count = 0;
		$incomplete = 0;
		foreach ( $rows as $row ) {
			$lock = GCU_Hardening::acquire_db_lock( 'workflow:experiment:' . $row['public_id'], 3 );
			if ( ! $lock ) {
				$incomplete++;
				continue;
			}
			try {
				if ( ! GCU_Plugin::instance()->repository()->begin_owned_transaction() ) {
					$incomplete++;
					continue;
				}
				$updated = $wpdb->query(
					$wpdb->prepare(
						"UPDATE {$tables['experiments']} SET status='stopped',row_version=row_version+1,updated_at=%s WHERE id=%d AND row_version=%d AND status='running'",
						current_time( 'mysql', true ),
						(int) $row['id'],
						(int) $row['row_version']
					)
				);
				if ( 1 !== $updated ) {
					$incomplete++;
					GCU_Plugin::instance()->repository()->rollback_owned_transaction();
					continue;
				}
				$baseline_result = GCU_Experiment_Baseline::reconcile_after_stop( $row );
				$audit = GCU_Plugin::instance()->repository()->audit(
					'experiment_early_stopped',
					'experiment',
					$row['public_id'],
					'experiment_safety',
					$breach ? 'Automatic trust/safety guardrail breach' : 'Approved experiment end time reached',
					$row,
					array( 'status' => 'stopped', 'row_version' => (int) $row['row_version'] + 1, 'baseline_reconciliation' => $baseline_result )
				);
				if ( false === $audit ) {
					$incomplete++;
					GCU_Plugin::instance()->repository()->rollback_owned_transaction();
					continue;
				}
				if ( ! GCU_Plugin::instance()->repository()->commit_owned_transaction() ) {
					$incomplete++;
					GCU_Plugin::instance()->repository()->rollback_owned_transaction();
					continue;
				}
				if ( empty( $baseline_result['restored'] ) ) {
					// The experiment is stopped; unknown concurrent editorial state is
					// never overwritten. Operator review and a safe serving pause are
					// explicitly recorded instead of a false "restored" assertion.
					update_option( 'gcu_experiment_restore_manual_review', array(
						'experiment_hash' => hash( 'sha256', (string) $row['public_id'] ),
						'reason' => isset( $baseline_result['reason'] ) ? sanitize_key( $baseline_result['reason'] ) : 'unverified',
						'created_at' => current_time( 'mysql', true ),
					), false );
					update_option( 'gcu_experiment_serving_paused', 1, false );
				}
				$count++;
			} finally {
				GCU_Hardening::release_db_lock( $lock );
			}
		}
		if ( $incomplete > 0 || $backlog ) {
			// The 51st row proves at least one experiment was unprocessed.
			// Report a bounded signal without any experiment or subject IDs.
			GCU_Observability::log( 'error', 'experiment_stop_batch_incomplete', array(
				'failed_count' => $incomplete,
				'stopped_count' => $count,
				'backlog_detected' => $backlog,
			) );
			return new WP_Error( 'gcu_experiment_stop_batch_incomplete', __( 'The running experiment stop batch was incomplete; operator review or another bounded stop sweep is required.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
		}
		return $count;
	}

	private static function contains_sensitive_copy_data( $text ) {
		$text = (string) $text;
		if ( class_exists( 'GCU_Review80_Hardening' ) && GCU_Review80_Hardening::question_contains_sensitive_data( $text ) ) {
			return true;
		}
		return (bool) preg_match(
			'/\b(?:patient|diagnosis|prescription|medical\s*record|case\s*(?:no|number)|CNIC|NICOP|passport)\b|(?:مریض|تشخیص|نسخہ|میڈیکل\s*ریکارڈ|شناختی\s*کارڈ|پاسپورٹ)|(?:مريض|تشخيص|وصفة|سجل\s*طبي|هوية|جواز\s*السفر)/iu',
			$text
		);
	}

	private static function is_mutating_request( WP_REST_Request $request ) {
		return in_array( strtoupper( $request->get_method() ), array( 'POST', 'PUT', 'PATCH', 'DELETE' ), true );
	}
}
