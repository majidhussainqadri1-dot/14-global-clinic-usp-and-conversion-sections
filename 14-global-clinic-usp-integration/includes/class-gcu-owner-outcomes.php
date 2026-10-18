<?php
defined( 'ABSPATH' ) || exit;

/**
 * F14-FR-011 / FUT-11..14: owner-origin, acquisition-consented, non-identifying
 * outcome counters. No public REST/browser event can invoke this producer path.
 *
 * These are native post-commit observations, not appointment success, doctor
 * verification or cross-person journey tracking. Deployed owner parity and
 * staging evidence remain independent release gates.
 */
final class GCU_Owner_Outcomes {
	const CONTRACT = 'gcu.owner-outcome.v1';

	public static function bootstrap() {
		// File07 emits this only while rendering its own public directory.
		add_action( 'ddd_after_directory', array( __CLASS__, 'directory_served' ), 20, 0 );
		// File09 emits this only after the application submission commits.
		add_action( 'gdo_application_submitted', array( __CLASS__, 'application_submitted' ), 20, 2 );
		// File08 v1 owner endpoint emits this only after a committed booking request.
		add_action( 'wca_appointment_requested_committed_v1', array( __CLASS__, 'booking_started' ), 20, 1 );
	}

	public static function directory_served() {
		if ( ! class_exists( 'DDD_Cross_File_Contracts' ) ) { return false; }
		// A successful server render is not proof the visitor's browser loaded.
		return self::record( 'file07', 'destination_served', wp_generate_uuid4() );
	}

	public static function application_submitted( $application_id, $unused_user_id = 0 ) {
		// R57 / File09 native contract: never turn a malformed, padded or
		// overflowing owner identifier into a different integer event identity.
		// File09's canonical post-commit hook supplies the actual database id.
		if ( ! class_exists( 'GDO_Application' )
			|| ! GCU_Future_Policy::valid_aggregate_count( $application_id )
			|| (int) $application_id <= 0 ) { return false; }
		// The owner fired its post-commit hook. Never read its private evidence.
		return self::record( 'file09', 'application_started', (string) $application_id );
	}

	public static function booking_started( $public_ref ) {
		if ( ! class_exists( 'WCA_Service' ) || ! is_string( $public_ref ) || ! wp_is_uuid( $public_ref ) ) { return false; }
		return self::record( 'file08', 'booking_started', strtolower( $public_ref ) );
	}

	public static function consented() {
		// Optional analytics consent is distinct from medical/booking consent.
		return GCU_Privacy::measurement_allowed()
			&& ! GCU_Privacy::low_bandwidth_requested();
	}

	public static function reportable( $event_type, $stage ) {
		return ( 'File07.DirectoryServed.v1' === $event_type && 'destination_served' === $stage )
			|| ( 'File08.BookingStarted.v1' === $event_type && 'booking_started' === $stage )
			|| ( 'File09.ApplicationStarted.v1' === $event_type && 'application_started' === $stage );
	}

	private static function record( $owner, $stage, $owner_reference ) {
		$types = array(
			'file07' => array( 'destination_served', 'File07.DirectoryServed.v1', 'doctor_directory' ),
			'file08' => array( 'booking_started', 'File08.BookingStarted.v1', 'clinic' ),
			'file09' => array( 'application_started', 'File09.ApplicationStarted.v1', 'doctor_onboarding' ),
		);
		if ( ! isset( $types[ $owner ] ) || $stage !== $types[ $owner ][0]
			|| ! is_string( $owner_reference ) || '' === $owner_reference || strlen( $owner_reference ) > 80
			|| ! self::consented() ) { return false; }
		$ready = GCU_Install::ready_for_mutation();
		if ( is_wp_error( $ready ) ) { return false; }
		// A pseudonymous subject allows the native WP privacy eraser/exporter
		// to honor withdrawal; an unerasable owner-reference digest is not
		// anonymous merely because the raw ID was not stored.
		$subject = GCU_Plugin::instance()->privacy()->event_subject_hash();
		if ( ! is_string( $subject ) || ! preg_match( '/^[a-f0-9]{64}$/', $subject ) ) { return false; }
		$rate = GCU_Plugin::instance()->repository()->consume_rate_limit( 'owner-' . $owner, GCU_Policy::EVENT_RATE_LIMIT );
		if ( is_wp_error( $rate ) ) { return false; }
		// This pseudonymous one-way identifier does not store the doctor,
		// appointment, patient, application or browser subject in File14.
		$h = hash_hmac( 'sha256', self::CONTRACT . '|' . $owner . '|' . $stage . '|' . $owner_reference, wp_salt( 'auth' ) );
		$uuid = substr( $h, 0, 8 ) . '-' . substr( $h, 8, 4 ) . '-4' . substr( $h, 13, 3 )
			. '-a' . substr( $h, 17, 3 ) . '-' . substr( $h, 20, 12 );
		global $wpdb;
		$tables = GCU_Install::tables();
		$now = current_time( 'mysql', true );
		$kind = $types[ $owner ];
		$inserted = $wpdb->query( $wpdb->prepare(
			"INSERT IGNORE INTO {$tables['events']} (event_id,event_type,event_version,funnel_stage,destination_key,subject_hash,source_value,medium_value,campaign_value,ref_value,consent_state,occurred_at,created_at)
			 VALUES (%s,%s,1,%s,%s,%s,NULL,NULL,NULL,NULL,'granted',%s,%s)",
			$uuid, $kind[1], $stage, $kind[2], $subject, $now, $now
		) );
		if ( 1 === $inserted ) { return true; }
		if ( 0 !== $inserted ) { return false; }
		$existing = $wpdb->get_row( $wpdb->prepare(
			"SELECT event_type,event_version,funnel_stage,destination_key,consent_state,subject_hash FROM {$tables['events']} WHERE event_id=%s",
			$uuid
		), ARRAY_A );
		// R57: an INSERT IGNORE duplicate is only idempotent with complete,
		// strictly typed owner evidence. Do not coerce malformed schema/version
		// values such as '1junk', '01' or true into event_version=1.
		$duplicate = is_array( $existing )
			&& isset( $existing['event_type'], $existing['event_version'], $existing['funnel_stage'],
				$existing['destination_key'], $existing['consent_state'], $existing['subject_hash'] )
			&& is_string( $existing['event_type'] ) && is_string( $existing['funnel_stage'] )
			&& is_string( $existing['destination_key'] ) && is_string( $existing['consent_state'] )
			&& is_string( $existing['subject_hash'] )
			&& in_array( $existing['event_version'], array( 1, '1' ), true )
			&& self::reportable( $existing['event_type'], $existing['funnel_stage'] )
			&& $kind[1] === $existing['event_type']
			&& $kind[2] === $existing['destination_key'] && 'granted' === $existing['consent_state']
			&& hash_equals( $subject, $existing['subject_hash'] );
		if ( ! $duplicate ) { GCU_Observability::log( 'warning', 'owner_outcome_identity_conflict', array( 'owner' => $owner ) ); }
		return $duplicate;
	}

	/**
	 * F14-FUT-14. Compare independent, native, consented OWNER EVENT VOLUMES,
	 * not click-to-booking rates. Refuse small samples and incomplete windows.
	 * Observation is not evidence of causation or representative traffic.
	 */
	public static function observed_anomaly() {
		global $wpdb;
		$tables = GCU_Install::tables();
		$rows = $wpdb->get_results(
			"SELECT event_type,funnel_stage,
			SUM(CASE WHEN occurred_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 24 HOUR) THEN 1 ELSE 0 END) AS current_count,
			SUM(CASE WHEN occurred_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 24 HOUR) THEN 1 ELSE 0 END) AS previous_seven_days
			FROM {$tables['events']}
			WHERE occurred_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 8 DAY)
			AND consent_state='granted' AND subject_hash IS NOT NULL AND event_version=1
			AND event_type IN ('File07.DirectoryServed.v1','File08.BookingStarted.v1','File09.ApplicationStarted.v1')
			GROUP BY event_type,funnel_stage",
			ARRAY_A
		);
		if ( ! is_array( $rows ) ) {
			// Missing SQL evidence is not a quiet or low-volume owner period.
			// Never expose owner/person IDs or diagnostic SQL to analytics.
			GCU_Observability::log( 'error', 'owner_anomaly_volume_read_unverified', array() );
			return array(
				'status' => 'owner_event_volume_read_unverified',
				'severity' => 'unverified', 'suppressed' => true, 'signals' => array(),
				'owner_correlated_funnel' => false, 'booking_success_rate' => null,
				'inference_limit' => 'owner_event_sql_evidence_unavailable',
				'checked_at' => gmdate( 'c' ),
			);
		}
		$signals = array();
		$highest = 'none';
		$minimum = GCU_Future_Policy::MIN_COHORT;
		foreach ( $rows as $row ) {
			if ( ! is_array( $row )
				|| ! isset( $row['event_type'], $row['funnel_stage'], $row['current_count'], $row['previous_seven_days'] )
				|| ! is_string( $row['event_type'] ) || '' === trim( $row['event_type'] )
				|| ! is_string( $row['funnel_stage'] ) || '' === trim( $row['funnel_stage'] )
				|| ! GCU_Future_Policy::valid_aggregate_count( $row['current_count'] )
				|| ! GCU_Future_Policy::valid_aggregate_count( $row['previous_seven_days'] ) ) {
				GCU_Observability::log( 'error', 'owner_anomaly_volume_row_unverified', array() );
				return array(
					'status' => 'owner_event_volume_read_unverified', 'severity' => 'unverified',
					'suppressed' => true, 'signals' => array(),
					'owner_correlated_funnel' => false, 'booking_success_rate' => null,
					'inference_limit' => 'owner_event_sql_evidence_unavailable',
					'checked_at' => gmdate( 'c' ),
				);
			}
			if ( ! self::reportable( $row['event_type'], $row['funnel_stage'] ) ) { continue; }
			$current = (int) $row['current_count'];
			$previous = (int) $row['previous_seven_days'];
			// 10 current and 70 historic events are required before disclosing
			// even a stage-level percentage; never expose a small cohort count.
			if ( $current < $minimum || $previous < 7 * $minimum ) { continue; }
			$historic_daily_average = $previous / 7;
			$drop = max( 0.0, ( $historic_daily_average - $current ) / $historic_daily_average );
			$severity = $drop >= 0.60 ? 'high' : ( $drop >= 0.40 ? 'medium' : 'none' );
			if ( 'high' === $severity || ( 'medium' === $severity && 'none' === $highest ) ) { $highest = $severity; }
			$signals[] = array(
				'event_type' => $row['event_type'], 'stage' => $row['funnel_stage'],
				'relative_volume_drop' => round( $drop, 4 ), 'severity' => $severity,
				'sample_at_least' => $minimum,
			);
		}
		return array(
			'status' => $signals ? 'consented_owner_volume_observed' : 'insufficient_owner_event_evidence',
			'severity' => $highest, 'suppressed' => ! $signals, 'signals' => $signals,
			'owner_correlated_funnel' => false, 'booking_success_rate' => null,
			'inference_limit' => 'opt_in_native_event_volume_not_population_conversion_or_causation',
			'checked_at' => gmdate( 'c' ),
		);
	}

	/** Aggregates are separate from browser CTA metrics; never imply success rates. */
	public static function owner_stage_summary( $days = 30 ) {
		$auth = GCU_Capabilities::require_capability( GCU_Capabilities::VIEW_ANALYTICS, null, 'future_intelligence_analytics' );
		if ( is_wp_error( $auth ) ) { return $auth; }
		global $wpdb;
		$t = GCU_Install::tables();
		$days = max( 1, min( 90, absint( $days ) ) );
		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT event_type,funnel_stage,COUNT(*) AS total FROM {$t['events']}
			 WHERE occurred_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL %d DAY)
			 AND consent_state='granted' AND subject_hash IS NOT NULL AND event_version=1
			 AND event_type IN ('File07.DirectoryServed.v1','File08.BookingStarted.v1','File09.ApplicationStarted.v1')
			 GROUP BY event_type,funnel_stage", $days
		), ARRAY_A );
		if ( ! is_array( $rows ) ) {
			GCU_Observability::log( 'error', 'owner_stage_volume_read_unverified', array() );
			return new WP_Error( 'gcu_owner_stage_read_unverified', __( 'Owner stage analytics could not be verified.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
		}
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || ! isset( $row['event_type'], $row['funnel_stage'], $row['total'] )
				|| ! is_string( $row['event_type'] ) || '' === trim( $row['event_type'] )
				|| ! is_string( $row['funnel_stage'] ) || '' === trim( $row['funnel_stage'] )
				|| ! GCU_Future_Policy::valid_aggregate_count( $row['total'] ) ) {
				GCU_Observability::log( 'error', 'owner_stage_volume_row_unverified', array() );
				return new WP_Error( 'gcu_owner_stage_row_unverified', __( 'Owner stage analytics evidence was incomplete.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
			}
		}
		$valid = array_values( array_filter( $rows, static function ( $row ) {
			return self::reportable( $row['event_type'], $row['funnel_stage'] );
		} ) );
		$total = array_sum( array_map( static function ( $row ) { return (int) $row['total']; }, $valid ) );
		$items = array();
		$restricted = $total < GCU_Future_Policy::MIN_COHORT;
		if ( ! $restricted ) {
			foreach ( $valid as $row ) {
				$n = (int) $row['total'];
				// A null count alongside an owner event/stage name still exposes
				// the presence of a rare applicant or appointment subgroup.
				// Suppress the entire subgroup, including its category label.
				if ( $n < GCU_Future_Policy::MIN_COHORT ) {
					$restricted = true;
					continue;
				}
				$items[] = array( 'stage' => $row['funnel_stage'], 'owner_contract' => $row['event_type'],
					'count' => $n, 'suppressed' => false );
			}
		}
		return array( 'items' => $items, 'suppressed' => $restricted,
			'minimum_cohort' => 10, 'linked_journey' => false,
			'success_rate' => null, 'live_accepted' => false, 'contract' => self::CONTRACT );
	}
}
