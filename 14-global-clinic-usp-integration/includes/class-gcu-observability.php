<?php

defined( 'ABSPATH' ) || exit;

final class GCU_Observability {
	public function hooks() {
		add_action( 'gcu_daily_governance_check', array( $this, 'daily_governance_check' ) );
		add_action( 'gcu_process_outbox', array( $this, 'process_outbox' ) );
	}

	public function health_report() {
		global $wpdb;
		$tables = GCU_Install::tables();
		$missing = array();
		foreach ( $tables as $name => $table ) {
			$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
			if ( $table !== $found ) {
				$missing[] = $name;
			}
		}
		$stale_claims = 0;
		if ( empty( $missing ) ) {
			$stale_claims = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tables['claims']} WHERE status = 'active' AND review_due_at IS NOT NULL AND review_due_at <= UTC_TIMESTAMP()" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
		$destinations = GCU_Plugin::instance()->contracts()->all_destination_health();
		$queue = array( 'pending' => 0, 'retry' => 0, 'dead' => 0 );
		if ( empty( $missing ) ) {
			$rows = $wpdb->get_results( "SELECT status, COUNT(*) AS total FROM {$tables['outbox']} WHERE status IN ('pending','retry','dead') GROUP BY status", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			foreach ( $rows as $row ) {
				if ( isset( $queue[ $row['status'] ] ) ) {
					$queue[ $row['status'] ] = (int) $row['total'];
				}
			}
		}
		$localization_missing = GCU_I18n::missing_keys();
		return array(
			'version'               => GCU_VERSION,
			'plan_version'          => GCU_PLAN_VERSION,
			'central_plan_baseline' => GCU_CENTRAL_PLAN_BASELINE,
			'brand_primary'         => GCU_BRAND_PRIMARY,
			'schema_version'        => (int) get_option( GCU_Install::SCHEMA_OPTION, 0 ),
			'enabled'               => (bool) get_option( 'gcu_enabled', 1 ),
			'missing_tables'        => $missing,
			'stale_claims'          => $stale_claims,
			'destinations'          => $destinations,
			'event_queue'           => $queue,
			'localization_complete' => empty( $localization_missing ),
			'localization_missing'  => $localization_missing,
			'legacy_migration'      => get_option( GCU_Install::MIGRATION_LOG, array() ),
			'policy_revalidation'   => get_option( 'gcu_policy_revalidation_required', array() ),
			'generated_at'          => gmdate( 'c' ),
		);
	}

	public function process_outbox() {
		return GCU_Plugin::instance()->repository()->dispatch_outbox( '', 50 );
	}

	public function daily_governance_check() {
		$report = $this->health_report();
		update_option( 'gcu_last_health_report', $report, false );
		if ( ! empty( $report['missing_tables'] ) || $report['stale_claims'] > 0 || $report['event_queue']['dead'] > 0 || ! $report['localization_complete'] ) {
			do_action( 'gcu_operational_alert_v1', array( 'severity' => 'warning', 'report' => $report, 'owner' => 'File 14 release operator' ) );
		}
	}

	public static function log( $level, $code, array $context = array() ) {
		$record = array(
			'level'       => sanitize_key( $level ),
			'code'        => sanitize_key( $code ),
			'trace_id'    => isset( $context['trace_id'] ) ? sanitize_text_field( $context['trace_id'] ) : GCU_Policy::trace_id(),
			'context'     => self::redact( $context ),
			'occurred_at' => gmdate( 'c' ),
		);
		do_action( 'gcu_structured_log_v1', $record );
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( wp_json_encode( array( 'gcu' => $record ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
		return $record['trace_id'];
	}

	private static function redact( array $context ) {
		$forbidden = array( 'email', 'phone', 'health', 'identity', 'evidence', 'message', 'token', 'nonce', 'secret', 'cookie', 'ip' );
		foreach ( $context as $key => $value ) {
			foreach ( $forbidden as $needle ) {
				if ( false !== strpos( strtolower( (string) $key ), $needle ) ) {
					$context[ $key ] = '[redacted]';
					continue 2;
				}
			}
		}
		return $context;
	}
}
