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
		if ( ! class_exists( 'GDO_Application' ) || ! is_int( $application_id ) && ! ctype_digit( (string) $application_id ) ) { return false; }
		if ( (int) $application_id <= 0 ) { return false; }
		// The owner fired its post-commit hook. Never read its private evidence.
		return self::record( 'file09', 'application_started', (string) (int) $application_id );
	}

	public static function booking_started( $public_ref ) {
		if ( ! class_exists( 'WCA_Service' ) || ! is_string( $public_ref ) || ! wp_is_uuid( $public_ref ) ) { return false; }
		return self::record( 'file08', 'booking_started', strtolower( $public_ref ) );
	}

	public static function consented() {
		// Optional analytics consent is distinct from medical/booking consent.
		return GCU_Policy::analytics_consent()
			&& ! GCU_Privacy::global_privacy_control_requested()
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
			 VALUES (%s,%s,1,%s,%s,NULL,NULL,NULL,NULL,NULL,'granted',%s,%s)",
			$uuid, $kind[1], $stage, $kind[2], $now, $now
		) );
		if ( 1 === $inserted ) { return true; }
		if ( 0 !== $inserted ) { return false; }
		$existing = $wpdb->get_row( $wpdb->prepare(
			"SELECT event_type,event_version,funnel_stage,destination_key,consent_state,subject_hash FROM {$tables['events']} WHERE event_id=%s",
			$uuid
		), ARRAY_A );
		$duplicate = is_array( $existing ) && self::reportable( (string) $existing['event_type'], (string) $existing['funnel_stage'] )
			&& $kind[1] === $existing['event_type'] && 1 === (int) $existing['event_version']
			&& $kind[2] === $existing['destination_key'] && 'granted' === $existing['consent_state']
			&& empty( $existing['subject_hash'] );
		if ( ! $duplicate ) { GCU_Observability::log( 'warning', 'owner_outcome_identity_conflict', array( 'owner' => $owner ) ); }
		return $duplicate;
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
			 AND event_type IN ('File07.DirectoryServed.v1','File08.BookingStarted.v1','File09.ApplicationStarted.v1')
			 GROUP BY event_type,funnel_stage", $days
		), ARRAY_A );
		$valid = array_values( array_filter( is_array( $rows ) ? $rows : array(), static function ( $row ) {
			return self::reportable( $row['event_type'], $row['funnel_stage'] );
		} ) );
		$total = array_sum( array_map( static function ( $row ) { return (int) $row['total']; }, $valid ) );
		$items = array();
		if ( $total >= 10 ) {
			foreach ( $valid as $row ) {
				$n = (int) $row['total'];
				$items[] = array( 'stage' => $row['funnel_stage'], 'owner_contract' => $row['event_type'],
					'count' => $n >= 10 ? $n : null, 'suppressed' => $n < 10 );
			}
		}
		return array( 'items' => $items, 'suppressed' => $total < 10,
			'minimum_cohort' => 10, 'linked_journey' => false,
			'success_rate' => null, 'live_accepted' => false, 'contract' => self::CONTRACT );
	}
}
