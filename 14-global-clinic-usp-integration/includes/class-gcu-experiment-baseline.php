<?php
defined( 'ABSPATH' ) || exit;

/**
 * F14-FUT-17: immutable owner-approved placement baseline and early-stop
 * reconciliation, stored in File14's existing private governed record table.
 *
 * This implementation cannot attribute an arbitrary concurrent placement edit
 * to an experiment. It therefore NEVER overwrites drifted rows: it stops
 * experimental serving and calls for manual reconciliation instead.
 */
final class GCU_Experiment_Baseline {
	const RECORD_TYPE = 'experiment_baseline';
	const CONTRACT = 'gcu.experiment-baseline.v1';

	public static function placement_keys( $variants ) {
		if ( ! is_array( $variants ) || count( $variants ) < 2 ) { return null; }
		$keys = array();
		foreach ( $variants as $variant ) {
			if ( ! is_array( $variant ) ) {
				if ( is_string( $variant ) && ! GCU_Future_Policy::experiment_requires_baseline_restore( array( $variant ) ) ) { continue; }
				return null;
			}
			if ( ! GCU_Future_Policy::experiment_requires_baseline_restore( array( $variant ) ) ) { continue; }
			if ( ! isset( $variant['placement_key'] ) || ! is_string( $variant['placement_key'] ) ) { return null; }
			$key = sanitize_key( $variant['placement_key'] );
			if ( ! $key || $key !== $variant['placement_key'] || strlen( $key ) > 191 ) { return null; }
			$keys[ $key ] = true;
		}
		return array_keys( $keys );
	}

	private static function identity( $row ) {
		return isset( $row['public_id'] ) && is_string( $row['public_id'] ) && wp_is_uuid( $row['public_id'] )
			? strtolower( $row['public_id'] ) : '';
	}

	private static function record_key( $row ) {
		return 'exp_' . str_replace( '-', '', self::identity( $row ) );
	}

	private static function read_placement( $key, $locking = false ) {
		global $wpdb;
		$t = GCU_Install::tables();
		$sql = $wpdb->prepare(
			"SELECT id,public_id,placement_key,block_key,route_key,slot_key,audience,priority,status,starts_at,ends_at,row_version
			 FROM {$t['placements']} WHERE placement_key=%s" . ( $locking ? ' FOR UPDATE' : '' ), $key
		);
		return $wpdb->get_row( $sql, ARRAY_A );
	}

	private static function shape( $row ) {
		// R60: an incomplete SELECT result is not a durable owner-approved
		// baseline. Nullable schedule fields must still be *present*.
		$fields = array( 'public_id', 'placement_key', 'block_key', 'route_key', 'slot_key', 'audience', 'priority', 'status', 'starts_at', 'ends_at', 'row_version' );
		if ( ! is_array( $row ) ) { return null; }
		foreach ( $fields as $field ) {
			if ( ! array_key_exists( $field, $row ) ) { return null; }
		}
		if ( ! is_string( $row['public_id'] ) || ! wp_is_uuid( $row['public_id'] )
			|| ! is_string( $row['placement_key'] ) || '' === $row['placement_key']
			|| sanitize_key( $row['placement_key'] ) !== $row['placement_key']
			|| ! GCU_Future_Policy::valid_aggregate_count( $row['row_version'] )
			|| (int) $row['row_version'] < 1 ) { return null; }
		// R61 / File20: SQL NOT NULL text columns must have genuine,
		// nonempty source strings. A hash-consistent corrupt snapshot is not
		// proof of a real owner placement, even when its keys are present.
		foreach ( array( 'block_key', 'route_key', 'slot_key', 'audience', 'status' ) as $field ) {
			if ( ! is_string( $row[ $field ] ) || '' === trim( $row[ $field ] ) ) { return null; }
		}
		foreach ( array( 'starts_at', 'ends_at' ) as $field ) {
			if ( null !== $row[ $field ] && ( ! is_string( $row[ $field ] ) || '' === trim( $row[ $field ] ) ) ) { return null; }
		}
		// File14 schema: priority is a *signed 32-bit SQL INT*. Unlike an
		// aggregate count, negative values are valid, but decimal coercion is not.
		$priority = $row['priority'];
		if ( ! is_int( $priority ) && ( ! is_string( $priority )
			|| ! preg_match( '/^(0|-?[1-9][0-9]*)$/D', $priority ) ) ) { return null; }
		if ( false === filter_var( $priority, FILTER_VALIDATE_INT, array(
			'options' => array( 'min_range' => -2147483648, 'max_range' => 2147483647 ),
		) ) ) { return null; }
		return array_intersect_key( $row, array_fill_keys( $fields, true ) );
	}

	public static function capture_approved( $experiment, $approver ) {
		// Called inside the File14 owner workflow transaction at Founder approval.
		$ref = self::identity( $experiment );
		// R59 / Founder audit: preserve *raw* approver and row-version truth.
		// absint() or (int) would turn a negative, fractional, forged or
		// overflowing identity into an apparently approved new baseline.
		if ( ! $ref || ! GCU_Future_Policy::valid_aggregate_count( $approver )
			|| (int) $approver < 1 ) {
			return new WP_Error( 'gcu_baseline_approval_missing', 'A real approving actor and experiment are required.' );
		}
		if ( ! is_array( $experiment ) || ! isset( $experiment['row_version'], $experiment['variants'] )
			|| ! GCU_Future_Policy::valid_aggregate_count( $experiment['row_version'] )
			|| (int) $experiment['row_version'] >= PHP_INT_MAX
			|| ! is_string( $experiment['variants'] ) ) {
			return new WP_Error( 'gcu_baseline_version_unverified', 'The approved experiment version and variants must be verified.' );
		}
		$approver = (int) $approver;
		$variants = json_decode( $experiment['variants'], true );
		$keys = self::placement_keys( $variants );
		if ( null === $keys ) { return new WP_Error( 'gcu_baseline_mapping_invalid', 'Placement-mutating variants require explicit canonical placement keys.' ); }
		if ( count( $keys ) > 20 ) { return new WP_Error( 'gcu_baseline_scope_exceeded', 'A baseline may cover at most twenty owner placements.' ); }
		$placements = array();
		foreach ( $keys as $key ) {
			$p = self::read_placement( $key, true );
			if ( ! is_array( $p ) ) { return new WP_Error( 'gcu_baseline_placement_missing', 'Every linked placement must already exist.' ); }
			$shape = self::shape( $p );
			if ( null === $shape || $shape['placement_key'] !== $key ) {
				return new WP_Error( 'gcu_baseline_placement_unverified', 'An approved placement requires complete canonical owner evidence.' );
			}
			$placements[ $key ] = $shape;
		}
		ksort( $placements );
		$data = array(
			'contract' => self::CONTRACT, 'experiment' => $ref,
			'approval_version' => (int) $experiment['row_version'] + 1,
			'approved_by' => $approver, 'approved_at' => current_time( 'mysql', true ),
			'placements' => $placements,
			'requires_manual_review_after_stop' => true,
		);
		$json = wp_json_encode( $data );
		if ( ! is_string( $json ) || strlen( $json ) > 65535 ) { return new WP_Error( 'gcu_baseline_encoding_failed', 'The approved baseline cannot be encoded safely.' ); }
		global $wpdb;
		$t = GCU_Future_Intelligence::tables();
		$now = current_time( 'mysql', true );
		$hash = hash( 'sha256', $json );
		$ok = $wpdb->insert( $t['records'], array(
			'record_type' => self::RECORD_TYPE, 'record_key' => self::record_key( $experiment ),
			'locale' => 'en-US', 'region' => 'ZZ', 'status' => 'active', 'is_public' => 0,
			'payload' => $json, 'payload_hash' => $hash, 'row_version' => 1,
			'review_due_at' => null, 'created_by' => $approver,
			'created_at' => $now, 'updated_at' => $now,
		) );
		return false === $ok ? new WP_Error( 'gcu_baseline_store_failed', 'A versioned experiment baseline already exists or could not be committed.' ) : true;
	}

	private static function snapshot( $experiment ) {
		if ( ! self::identity( $experiment ) ) { return null; }
		global $wpdb;
		$t = GCU_Future_Intelligence::tables();
		$row = $wpdb->get_row( $wpdb->prepare(
			"SELECT payload,payload_hash,created_by FROM {$t['records']}
			 WHERE record_type=%s AND record_key=%s AND locale='en-US' AND region='ZZ' AND status='active' AND is_public=0 LIMIT 1",
			self::RECORD_TYPE, self::record_key( $experiment )
		), ARRAY_A );
		if ( ! is_array( $row ) || ! isset( $row['payload'], $row['payload_hash'], $row['created_by'] )
			|| ! is_string( $row['payload'] ) || ! is_string( $row['payload_hash'] )
			|| ! GCU_Future_Policy::valid_aggregate_count( $row['created_by'] )
			|| (int) $row['created_by'] < 1
			|| ! hash_equals( $row['payload_hash'], hash( 'sha256', $row['payload'] ) ) ) { return null; }
		$s = json_decode( $row['payload'], true );
		if ( ! is_array( $s ) || ( $s['contract'] ?? '' ) !== self::CONTRACT
			|| ( $s['experiment'] ?? '' ) !== self::identity( $experiment )
			|| ! isset( $s['placements'] ) || ! is_array( $s['placements'] )
			|| ! isset( $s['approved_by'], $s['approval_version'] )
			|| ! GCU_Future_Policy::valid_aggregate_count( $s['approved_by'] )
			|| ! GCU_Future_Policy::valid_aggregate_count( $s['approval_version'] )
			|| (int) $s['approved_by'] < 1
			|| (int) $row['created_by'] !== (int) $s['approved_by'] ) { return null; }
		// A matching hash proves only bytes, not a complete placement contract.
		foreach ( $s['placements'] as $key => $saved ) {
			if ( ! is_string( $key ) || ! is_array( $saved )
				|| self::shape( $saved ) !== $saved
				|| $saved['placement_key'] !== $key ) { return null; }
		}
		return $s;
	}

	public static function ready( $experiment ) {
		$s = self::snapshot( $experiment );
		// R58 / FUT16: a cast must never convert corrupt approval metadata
		// into matching experiment identity. Use original canonical DB values.
		if ( ! $s || ! isset( $experiment['row_version'], $experiment['approved_by'], $experiment['variants'] )
			|| ! is_string( $experiment['variants'] )
			|| ! GCU_Future_Policy::valid_aggregate_count( $experiment['row_version'] )
			|| ! GCU_Future_Policy::valid_aggregate_count( $experiment['approved_by'] )
			|| (int) $experiment['approved_by'] < 1
			|| (int) $experiment['row_version'] !== (int) $s['approval_version']
			|| (int) $experiment['approved_by'] !== (int) $s['approved_by'] ) { return false; }
		$keys = self::placement_keys( json_decode( $experiment['variants'], true ) );
		if ( null === $keys || count( $keys ) !== count( $s['placements'] ) ) { return false; }
		foreach ( $keys as $key ) {
			if ( ! isset( $s['placements'][ $key ] ) ) { return false; }
			$live = self::read_placement( $key );
			if ( ! is_array( $live ) || self::shape( $live ) !== $s['placements'][ $key ] ) { return false; }
		}
		return true;
	}

	/** Use inside the same transaction as the terminal experiment status/audit. */
	public static function reconcile_after_stop( $experiment ) {
		$s = self::snapshot( $experiment );
		if ( ! $s ) { return array( 'restored' => false, 'reason' => 'approved_snapshot_missing', 'manual_review' => true ); }
		// R56 / FUT17: integrity of snapshot bytes is not sufficient.
		// Before claiming even a *no-op*, confirm the same Founder approval,
		// original placement mapping and permitted approved->running version
		// progression. Fail closed on stale or rewritten experiment metadata.
		$approval_version = isset( $s['approval_version'] ) ? $s['approval_version'] : null;
		$current_version = isset( $experiment['row_version'] ) ? $experiment['row_version'] : null;
		$current_approver = isset( $experiment['approved_by'] ) ? $experiment['approved_by'] : null;
		$approved_by = isset( $s['approved_by'] ) ? $s['approved_by'] : null;
		$status = isset( $experiment['status'] ) ? $experiment['status'] : '';
		$expected_version = 'running' === $status ? 1 : ( 'approved' === $status ? 0 : null );
		$keys = isset( $experiment['variants'] ) && is_string( $experiment['variants'] )
			? self::placement_keys( json_decode( $experiment['variants'], true ) ) : null;
		if ( null === $expected_version || ! GCU_Future_Policy::valid_aggregate_count( $approval_version )
			|| ! GCU_Future_Policy::valid_aggregate_count( $current_version )
			|| ! GCU_Future_Policy::valid_aggregate_count( $approved_by )
			|| ! GCU_Future_Policy::valid_aggregate_count( $current_approver )
			|| (int) $approved_by < 1 || (int) $current_approver !== (int) $approved_by
			|| (int) $current_version !== (int) $approval_version + $expected_version
			|| null === $keys || count( $keys ) !== count( $s['placements'] ) ) {
			return array( 'restored' => false, 'reason' => 'approved_experiment_baseline_identity_unverified', 'manual_review' => true );
		}
		foreach ( $keys as $key ) {
			if ( ! isset( $s['placements'][ $key ] ) ) {
				return array( 'restored' => false, 'reason' => 'approved_experiment_baseline_identity_unverified', 'manual_review' => true );
			}
		}
		foreach ( $s['placements'] as $key => $before ) {
			$now = self::read_placement( $key, true );
			if ( ! is_array( $now ) || self::shape( $now ) !== $before ) {
				// Never clobber a possibly unrelated change with an invented
				// experiment-owned CAS version. Containment is mandatory.
				return array( 'restored' => false, 'reason' => 'placement_drift_requires_owner_reconciliation', 'manual_review' => true );
			}
		}
		// No placement mutation was served. The approved baseline remains
		// intact: this is a VERIFIED no-op, not a pretend SQL rollback.
		return array( 'restored' => true, 'reason' => 'approved_baseline_unchanged', 'manual_review' => true );
	}
}
