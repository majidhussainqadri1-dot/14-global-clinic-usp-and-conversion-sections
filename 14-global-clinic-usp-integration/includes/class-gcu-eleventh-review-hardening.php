<?php

defined( 'ABSPATH' ) || exit;

/**
 * Eleventh-cycle postcondition guards.
 *
 * These guards do not create a second schema/migration owner. They verify and,
 * where safe and deterministic, repair File 14's own installation receipts
 * after the canonical GCU_Install / GCU_Future_Intelligence routines run.
 */
final class GCU_Eleventh_Review_Hardening {
	const FUTURE_DAILY = 'gcu_future_daily_governance';
	const FUTURE_HOURLY = 'gcu_future_hourly_intelligence';

	public static function bootstrap() {
		add_action( 'init', array( __CLASS__, 'runtime_postconditions' ), 1 );
	}

	public static function activation_verify() {
		$result = self::verify_all( true );
		if ( is_wp_error( $result ) ) {
			update_option( 'gcu_enabled', 0, false );
			update_option( GCU_Future_Intelligence::SAFE_MODE_OPTION, 1, false );
			update_option( GCU_Install::UPGRADE_ERROR, array( 'code' => sanitize_key( $result->get_error_code() ), 'occurred_at' => time(), 'version' => GCU_VERSION, 'schema' => GCU_SCHEMA_VERSION ), false );
			wp_die( esc_html( $result->get_error_message() ) );
		}
	}

	public static function runtime_postconditions() {
		if ( ! get_option( 'gcu_enabled', 1 ) ) { return; }
		if ( GCU_VERSION !== (string) get_option( GCU_Install::VERSION_OPTION, '' ) || GCU_SCHEMA_VERSION !== (int) get_option( GCU_Install::SCHEMA_OPTION, 0 ) ) { return; }
		$result = self::verify_all( false );
		if ( is_wp_error( $result ) ) {
			update_option( GCU_Future_Intelligence::SAFE_MODE_OPTION, 1, false );
			GCU_Observability::log( 'error', 'eleventh_postcondition_failed', array( 'code' => $result->get_error_code() ) );
		}
	}

	public static function verify_all( $repair = false ) {
		$snapshot = self::verify_snapshot_integrity();
		if ( is_wp_error( $snapshot ) ) { return $snapshot; }
		$migration = self::verify_migration_receipt( $repair );
		if ( is_wp_error( $migration ) ) { return $migration; }
		$future = self::verify_future_defaults( $repair );
		if ( is_wp_error( $future ) ) { return $future; }
		$cron = self::verify_future_cron( $repair );
		if ( is_wp_error( $cron ) ) { return $cron; }
		return true;
	}

	private static function verify_snapshot_integrity() {
		$snapshot = get_option( GCU_Install::SNAPSHOT_OPTION, array() );
		if ( ! is_array( $snapshot ) || empty( $snapshot['snapshot_hash'] ) ) {
			return new WP_Error( 'gcu_snapshot_receipt_missing', __( 'The File 14 rollback snapshot receipt is missing.', 'global-clinic-usp-integration' ) );
		}
		$stored_hash = (string) $snapshot['snapshot_hash'];
		unset( $snapshot['snapshot_hash'] );
		$encoded = wp_json_encode( $snapshot );
		if ( false === $encoded || ! preg_match( '/^[a-f0-9]{64}$/', $stored_hash ) || ! hash_equals( $stored_hash, hash( 'sha256', $encoded ) ) ) {
			return new WP_Error( 'gcu_snapshot_receipt_corrupt', __( 'The persisted File 14 rollback snapshot failed full-payload integrity verification.', 'global-clinic-usp-integration' ) );
		}
		return true;
	}

	private static function verify_migration_receipt( $repair ) {
		if ( ! get_option( 'gcu_legacy_migrated', 0 ) ) {
			return new WP_Error( 'gcu_migration_receipt_missing', __( 'File 14 migration completion is not durably recorded.', 'global-clinic-usp-integration' ) );
		}
		$receipt = get_option( GCU_Install::MIGRATION_LOG, null );
		$valid = is_array( $receipt ) && array_key_exists( 'detected', $receipt ) && isset( $receipt['legacy_snapshot'], $receipt['action'], $receipt['migrated_at'] );
		if ( ! $valid && $repair ) {
			$legacy = array(
				'sgc_version' => get_option( 'sgc_version', null ),
				'sgc_schema_version' => get_option( 'sgc_schema_version', null ),
				'sgc_placements' => get_option( 'sgc_placements', null ),
				'sgc_page_map' => get_option( 'sgc_page_map', null ),
			);
			$detected = array_filter( $legacy, static function( $value ) { return null !== $value; } );
			$receipt = array(
				'detected' => ! empty( $detected ),
				'legacy_snapshot' => $detected,
				'action' => 'read-only inventory; no legacy page, option or companion record deleted or overwritten',
				'migrated_at' => time(),
			);
			update_option( GCU_Install::MIGRATION_LOG, $receipt, false );
			$stored = get_option( GCU_Install::MIGRATION_LOG, null );
			$valid = is_array( $stored ) && hash_equals( hash( 'sha256', wp_json_encode( $receipt ) ), hash( 'sha256', wp_json_encode( $stored ) ) );
		}
		return $valid ? true : new WP_Error( 'gcu_migration_evidence_unverified', __( 'File 14 migration evidence could not be persisted and verified safely.', 'global-clinic-usp-integration' ) );
	}

	private static function verify_future_defaults( $repair ) {
		$schema = GCU_Future_Intelligence::verify_schema();
		if ( is_wp_error( $schema ) ) { return $schema; }
		global $wpdb;
		$tables = GCU_Future_Intelligence::tables();
		$required = array(
			array( 'terminology_lock', 'protected_terms', false, array( 'terms' => GCU_Future_Policy::terminology_lock(), 'source' => GCU_Future_Policy::PLAN_ID, 'reviewer' => 'Founder-approved plan', 'provenance' => 'approved amendment' ) ),
			array( 'change_log', 'future_cti_v2_0', true, array( 'title' => 'Future Conversion & Trust Intelligence v2.0', 'summary' => 'Twenty-four Founder-approved ethical conversion, trust, privacy, experiment and transparency enhancements were added to File 14.', 'effective_date' => '2026-08-10', 'material' => true ) ),
		);
		foreach ( $required as $item ) {
			$wpdb->last_error = '';
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tables['records']} WHERE record_type=%s AND record_key=%s AND locale='en-US' AND region='ZZ' LIMIT 1", $item[0], $item[1] ) );
			if ( '' !== (string) $wpdb->last_error ) {
				return new WP_Error( 'gcu_future_default_probe_failed', __( 'Future governance defaults could not be verified safely.', 'global-clinic-usp-integration' ) );
			}
			if ( ! $exists && $repair ) {
				$created = GCU_Future_Intelligence::upsert_record( $item[0], $item[1], 'en-US', 'ZZ', $item[3], 'active', $item[2], 0, true );
				if ( is_wp_error( $created ) ) { return $created; }
				$wpdb->last_error = '';
				$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tables['records']} WHERE record_type=%s AND record_key=%s AND locale='en-US' AND region='ZZ' LIMIT 1", $item[0], $item[1] ) );
				if ( '' !== (string) $wpdb->last_error ) { return new WP_Error( 'gcu_future_default_readback_failed', __( 'A repaired Future governance default could not be read back safely.', 'global-clinic-usp-integration' ) ); }
			}
			if ( ! $exists ) {
				return new WP_Error( 'gcu_future_default_missing', __( 'A required Future governance default is missing.', 'global-clinic-usp-integration' ) );
			}
		}
		return true;
	}

	private static function verify_future_cron( $repair ) {
		$jobs = array(
			array( self::FUTURE_DAILY, time() + HOUR_IN_SECONDS, 'daily' ),
			array( self::FUTURE_HOURLY, time() + 10 * MINUTE_IN_SECONDS, 'hourly' ),
		);
		foreach ( $jobs as $job ) {
			if ( ! wp_next_scheduled( $job[0] ) && $repair ) {
				$result = wp_schedule_event( $job[1], $job[2], $job[0], array(), true );
				if ( is_wp_error( $result ) || false === $result ) {
					$code = is_wp_error( $result ) ? $result->get_error_code() : 'schedule_failed';
					GCU_Observability::log( 'error', 'future_cron_schedule_failed', array( 'hook' => $job[0], 'code' => $code ) );
					return new WP_Error( 'gcu_future_cron_schedule_failed', __( 'File 14 could not schedule a required Future governance job.', 'global-clinic-usp-integration' ), array( 'hook' => $job[0], 'code' => $code ) );
				}
			}
			if ( ! wp_next_scheduled( $job[0] ) ) {
				return new WP_Error( 'gcu_future_cron_missing', __( 'A required Future governance job is not scheduled.', 'global-clinic-usp-integration' ), array( 'hook' => $job[0] ) );
			}
		}
		return true;
	}
}
