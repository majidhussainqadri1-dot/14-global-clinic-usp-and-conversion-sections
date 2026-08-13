<?php

defined( 'ABSPATH' ) || exit;

/** Eleventh fresh cycle Round 16: hard bounds for diagnostic/governance scans. */
final class GCU_Round16_Bounds {
	const MAX_QUESTION_SIGNALS = 500;
	const MAX_FAQ_TITLES = 500;
	const MAX_CONSISTENCY_ROWS = 1000;

	public static function bootstrap() {
		add_filter( 'gcu_future_question_aggregates', array( __CLASS__, 'bound_question_signals' ), PHP_INT_MAX );
		add_filter( 'rest_request_before_callbacks', array( __CLASS__, 'guard_consistency_rest' ), 7, 3 );
		add_action( 'gcu_future_daily_governance', array( __CLASS__, 'guard_daily_consistency' ), -100 );
		add_action( 'current_screen', array( __CLASS__, 'guard_future_admin_screen' ), 1 );
	}

	public static function bound_question_signals( $signals ) {
		if ( ! is_array( $signals ) ) {
			return null;
		}
		$faq_count = self::active_block_count( 'faq' );
		if ( count( $signals ) > self::MAX_QUESTION_SIGNALS || is_wp_error( $faq_count ) || $faq_count > self::MAX_FAQ_TITLES ) {
			GCU_Observability::log( 'warning', 'future_faq_gap_scan_suppressed', array( 'signals' => count( $signals ), 'faq_count' => is_wp_error( $faq_count ) ? null : $faq_count, 'signal_ceiling' => self::MAX_QUESTION_SIGNALS, 'faq_ceiling' => self::MAX_FAQ_TITLES ) );
			return null;
		}
		return $signals;
	}

	public static function guard_consistency_rest( $response, $handler, WP_REST_Request $request ) {
		if ( null !== $response || ! in_array( $request->get_route(), array( '/gcu/v1/future/consistency', '/gcu/v1/future/scenarios' ), true ) ) {
			return $response;
		}
		$count = self::active_block_count();
		if ( is_wp_error( $count ) ) {
			return $count;
		}
		if ( $count > self::MAX_CONSISTENCY_ROWS ) {
			return new WP_Error( 'gcu_future_consistency_scan_ceiling', __( 'Message consistency requires operator review because the active-content scan ceiling was exceeded.', 'global-clinic-usp-integration' ), array( 'status' => 503, 'ceiling' => self::MAX_CONSISTENCY_ROWS ) );
		}
		return $response;
	}

	public static function guard_daily_consistency() {
		$count = self::active_block_count();
		if ( is_wp_error( $count ) || $count > self::MAX_CONSISTENCY_ROWS ) {
			remove_action( 'gcu_future_daily_governance', array( 'GCU_Future_Intelligence', 'daily_governance' ) );
			GCU_Observability::log( 'error', 'future_consistency_scan_suppressed', array( 'count' => is_wp_error( $count ) ? null : $count, 'ceiling' => self::MAX_CONSISTENCY_ROWS ) );
		}
	}

	public static function guard_future_admin_screen( $screen ) {
		if ( ! is_object( $screen ) || empty( $screen->id ) || 'settings_page_global-clinic-usp-future' !== $screen->id ) {
			return;
		}
		$count = self::active_block_count();
		if ( is_wp_error( $count ) || $count > self::MAX_CONSISTENCY_ROWS ) {
			wp_die( esc_html__( 'Future Intelligence diagnostics are paused until the active-content scan size is reviewed by an operator.', 'global-clinic-usp-integration' ) );
		}
	}

	private static function active_block_count( $type = '' ) {
		global $wpdb;
		$tables = GCU_Install::tables();
		$sql = "SELECT COUNT(*) FROM {$tables['blocks']} WHERE status='active'";
		if ( 'faq' === $type ) {
			$sql .= " AND block_type='faq'";
		}
		$wpdb->last_error = '';
		$count = $wpdb->get_var( $sql );
		if ( '' !== (string) $wpdb->last_error || null === $count ) {
			return new WP_Error( 'gcu_future_governance_count_failed', __( 'Future governance scan size could not be verified safely.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
		}
		return (int) $count;
	}
}
