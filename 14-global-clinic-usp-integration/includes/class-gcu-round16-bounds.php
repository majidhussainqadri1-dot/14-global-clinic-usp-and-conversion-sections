<?php

defined( 'ABSPATH' ) || exit;

/**
 * Eleventh fresh cycle hardening retained from Round 16 and extended in Round 20.
 * Keeps diagnostic scans bounded and report workflows idempotent/fail-closed.
 */
final class GCU_Round16_Bounds {
	const MAX_QUESTION_SIGNALS = 500;
	const MAX_FAQ_TITLES = 500;
	const MAX_CONSISTENCY_ROWS = 1000;

	public static function bootstrap() {
		add_filter( 'gcu_future_question_aggregates', array( __CLASS__, 'bound_question_signals' ), PHP_INT_MAX );
		add_filter( 'rest_request_before_callbacks', array( __CLASS__, 'safe_future_rest_paths' ), 6, 3 );
		add_filter( 'rest_request_before_callbacks', array( __CLASS__, 'guard_consistency_rest' ), 7, 3 );
		add_action( 'gcu_future_daily_governance', array( __CLASS__, 'guard_daily_consistency' ), -100 );
		add_action( 'current_screen', array( __CLASS__, 'guard_future_admin_screen' ), 1 );

		// Replace the native Future public renderer with a DB-preflighted wrapper that also
		// gives the HTML report form a stable UUID for duplicate-submit protection.
		remove_filter( 'gcu_public_route_html', array( 'GCU_Future_Intelligence', 'filter_public_route_html' ), 10 );
		add_filter( 'gcu_public_route_html', array( __CLASS__, 'safe_future_html' ), 10, 2 );

		// Replace report mutations after Future Intelligence registered its handlers.
		remove_action( 'admin_post_gcu_future_report', array( 'GCU_Future_Intelligence', 'submit_report' ) );
		remove_action( 'admin_post_nopriv_gcu_future_report', array( 'GCU_Future_Intelligence', 'submit_report' ) );
		remove_action( 'admin_post_gcu_future_resolve_report', array( 'GCU_Future_Intelligence', 'resolve_report' ) );
		add_action( 'admin_post_gcu_future_report', array( __CLASS__, 'submit_report' ) );
		add_action( 'admin_post_nopriv_gcu_future_report', array( __CLASS__, 'submit_report' ) );
		add_action( 'admin_post_gcu_future_resolve_report', array( __CLASS__, 'resolve_report' ) );
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
		global $wpdb;
		$tables = GCU_Future_Intelligence::tables();
		$wpdb->last_error = '';
		$wpdb->get_var( "SELECT public_id FROM {$tables['reports']} ORDER BY id ASC LIMIT 1" );
		if ( '' !== (string) $wpdb->last_error ) {
			GCU_Observability::log( 'error', 'future_admin_reports_preflight_failed', array() );
			wp_die( esc_html__( 'Future reports could not be read safely. Retry after database health is restored.', 'global-clinic-usp-integration' ) );
		}
	}

	public static function safe_future_html( $html, $route ) {
		global $wpdb;
		$tables = GCU_Future_Intelligence::tables();
		$wpdb->last_error = '';
		$wpdb->get_var( "SELECT id FROM {$tables['records']} ORDER BY id ASC LIMIT 1" );
		if ( '' !== (string) $wpdb->last_error ) {
			GCU_Observability::log( 'error', 'future_public_records_preflight_failed', array( 'route' => sanitize_key( (string) $route ) ) );
			return $html;
		}
		$out = GCU_Future_Intelligence::filter_public_route_html( $html, $route );
		if ( ! is_string( $out ) ) {
			return $out;
		}
		$marker = '<input type="hidden" name="action" value="gcu_future_report">';
		if ( false !== strpos( $out, $marker ) && false === strpos( $out, 'name="report_id"' ) ) {
			$out = str_replace( $marker, $marker . '<input type="hidden" name="report_id" value="' . esc_attr( wp_generate_uuid4() ) . '">', $out );
		}
		return $out;
	}

	public static function safe_future_rest_paths( $response, $handler, WP_REST_Request $request ) {
		if ( null !== $response ) {
			return $response;
		}
		$route = $request->get_route();
		if ( '/gcu/v1/future/report' === $route && WP_REST_Server::CREATABLE === $request->get_method() ) {
			$data = $request->get_json_params();
			$data = is_array( $data ) ? $data : array();
			if ( ! empty( $data['report_id'] ) ) {
				$guard = self::report_identity_guard( $data );
				if ( is_wp_error( $guard ) ) {
					return $guard;
				}
			}
			return $response;
		}
		if ( ! in_array( $route, array( '/gcu/v1/future/reports', '/gcu/v1/future/records' ), true ) ) {
			return $response;
		}
		if ( ! GCU_Future_Intelligence::can_manage_content() ) {
			return new WP_Error( 'gcu_future_read_forbidden', __( 'You are not authorized to read Future Intelligence records.', 'global-clinic-usp-integration' ), array( 'status' => 403 ) );
		}
		$result = '/gcu/v1/future/reports' === $route ? self::safe_reports_page( $request ) : self::safe_records_page( $request );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		$rest = new WP_REST_Response( $result, 200 );
		$rest->header( 'Cache-Control', 'no-store, private' );
		$rest->header( 'Pragma', 'no-cache' );
		return $rest;
	}

	public static function submit_report() {
		check_admin_referer( 'gcu_future_report' );
		$data = array(
			'report_id' => isset( $_POST['report_id'] ) ? sanitize_text_field( wp_unslash( $_POST['report_id'] ) ) : '',
			'reason_code' => isset( $_POST['reason_code'] ) ? wp_unslash( $_POST['reason_code'] ) : '',
			'message' => isset( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : '',
			'route_key' => isset( $_POST['route_key'] ) ? wp_unslash( $_POST['route_key'] ) : 'global_clinic',
			'locale' => isset( $_POST['locale'] ) ? wp_unslash( $_POST['locale'] ) : 'en-US',
		);
		$result = wp_is_uuid( $data['report_id'] ) ? self::report_identity_guard( $data ) : new WP_Error( 'gcu_future_report_id_required', __( 'A stable report identifier is required. Reload the page and retry.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		if ( ! is_wp_error( $result ) ) {
			$result = GCU_Future_Intelligence::create_report( $data );
		}
		$referer = wp_get_referer();
		$referer = $referer ? GCU_Hardening::strict_same_origin_url( $referer ) : home_url( '/global-clinic/' );
		if ( ! $referer ) {
			$referer = home_url( '/global-clinic/' );
		}
		wp_safe_redirect( add_query_arg( 'gcu_reported', is_wp_error( $result ) ? '0' : '1', $referer ) );
		exit;
	}

	public static function resolve_report() {
		check_admin_referer( 'gcu_future_resolve_report' );
		if ( ! GCU_Future_Intelligence::can_manage_content() ) {
			wp_die( esc_html__( 'You are not authorized to review File 14 reports.', 'global-clinic-usp-integration' ) );
		}
		$id = isset( $_POST['report_id'] ) ? sanitize_text_field( wp_unslash( $_POST['report_id'] ) ) : '';
		$expected = isset( $_POST['expected_version'] ) ? absint( $_POST['expected_version'] ) : 0;
		$status = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';
		$resolution = isset( $_POST['resolution'] ) ? sanitize_textarea_field( wp_unslash( $_POST['resolution'] ) ) : '';
		$result = self::resolve_report_record_safe( $id, $expected, $status, $resolution );
		$state = is_wp_error( $result ) ? 'failed' : 'updated';
		wp_safe_redirect( add_query_arg( 'gcu_report_update', $state, admin_url( 'options-general.php?page=global-clinic-usp-future' ) ) );
		exit;
	}

	public static function reports_for_admin() {
		global $wpdb;
		$tables = GCU_Future_Intelligence::tables();
		$wpdb->last_error = '';
		$reports = $wpdb->get_results( $wpdb->prepare( "SELECT public_id,report_type,route_key,block_key,locale,reason_code,message,status,resolution,row_version,created_at,updated_at FROM {$tables['reports']} WHERE status=%s ORDER BY created_at ASC LIMIT %d", 'open', 50 ), ARRAY_A );
		if ( '' !== (string) $wpdb->last_error || ! is_array( $reports ) ) {
			wp_die( esc_html__( 'Future reports could not be read safely.', 'global-clinic-usp-integration' ) );
		}
		return $reports;
	}

	public static function resolve_report_action( $id, $expected, $status, $resolution ) {
		$result = self::resolve_report_record_safe( $id, $expected, $status, $resolution );
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}
		return $result;
	}

	private static function report_identity_guard( array $data ) {
		$id = isset( $data['report_id'] ) ? sanitize_text_field( (string) $data['report_id'] ) : '';
		if ( ! wp_is_uuid( $id ) ) {
			return new WP_Error( 'gcu_future_report_id_invalid', __( 'A valid report identifier is required.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$reason = sanitize_key( isset( $data['reason_code'] ) ? $data['reason_code'] : '' );
		$message = trim( sanitize_textarea_field( isset( $data['message'] ) ? $data['message'] : '' ) );
		$message = function_exists( 'mb_substr' ) ? mb_substr( $message, 0, 500 ) : substr( $message, 0, 500 );
		$route = sanitize_key( isset( $data['route_key'] ) ? $data['route_key'] : 'global_clinic' );
		if ( ! in_array( $route, array( 'global_clinic', 'how_it_works' ), true ) ) {
			$route = 'global_clinic';
		}
		$block = sanitize_key( isset( $data['block_key'] ) ? $data['block_key'] : '' );
		$locale = GCU_Policy::sanitize_locale( isset( $data['locale'] ) ? $data['locale'] : 'en-US' );
		$actor_hash = is_user_logged_in() ? GCU_Integrity::future_actor_hash( get_current_user_id() ) : null;
		global $wpdb;
		$tables = GCU_Future_Intelligence::tables();
		$wpdb->last_error = '';
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT report_type,route_key,block_key,locale,reason_code,message,actor_hash FROM {$tables['reports']} WHERE public_id=%s LIMIT 1", $id ), ARRAY_A );
		if ( '' !== (string) $wpdb->last_error ) {
			return new WP_Error( 'gcu_future_report_identity_query_failed', __( 'The report identity could not be verified safely.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
		}
		if ( ! $row ) {
			return true;
		}
		$expected = array( 'report_type' => 'copy_quality', 'route_key' => $route, 'block_key' => $block, 'locale' => $locale, 'reason_code' => $reason, 'message' => $message, 'actor_hash' => null === $actor_hash ? '' : $actor_hash );
		foreach ( $expected as $key => $value ) {
			$stored = isset( $row[ $key ] ) && null !== $row[ $key ] ? (string) $row[ $key ] : '';
			if ( ! hash_equals( $stored, (string) $value ) ) {
				return new WP_Error( 'gcu_future_report_identity_conflict', __( 'This report identifier is already bound to different report data.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
			}
		}
		return true;
	}

	private static function safe_reports_page( WP_REST_Request $request ) {
		$status = sanitize_key( (string) $request->get_param( 'status' ) );
		if ( ! in_array( $status, array( 'open', 'reviewing', 'resolved', 'rejected' ), true ) ) {
			$status = 'open';
		}
		$limit = max( 1, min( 100, absint( $request->get_param( 'limit' ) ? $request->get_param( 'limit' ) : 50 ) ) );
		$cursor = absint( $request->get_param( 'cursor' ) );
		global $wpdb;
		$tables = GCU_Future_Intelligence::tables();
		$wpdb->last_error = '';
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT id,public_id,report_type,route_key,block_key,locale,reason_code,message,status,resolution,row_version,created_at,updated_at FROM {$tables['reports']} WHERE status=%s AND id>%d ORDER BY id ASC LIMIT %d", $status, $cursor, $limit + 1 ), ARRAY_A );
		if ( '' !== (string) $wpdb->last_error || ! is_array( $rows ) ) {
			return new WP_Error( 'gcu_future_reports_page_query_failed', __( 'Reports could not be read safely.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
		}
		$more = count( $rows ) > $limit;
		if ( $more ) {
			$rows = array_slice( $rows, 0, $limit );
		}
		$next = $more && $rows ? (int) $rows[ count( $rows ) - 1 ]['id'] : null;
		foreach ( $rows as &$row ) {
			unset( $row['id'] );
		}
		unset( $row );
		return array( 'items' => $rows, 'next_cursor' => $next, 'limit' => $limit );
	}

	private static function safe_records_page( WP_REST_Request $request ) {
		$type = sanitize_key( (string) $request->get_param( 'type' ) );
		$limit = max( 1, min( 100, absint( $request->get_param( 'limit' ) ? $request->get_param( 'limit' ) : 50 ) ) );
		$cursor = absint( $request->get_param( 'cursor' ) );
		global $wpdb;
		$tables = GCU_Future_Intelligence::tables();
		$where = 'id>%d';
		$args = array( $cursor );
		if ( $type ) {
			$where .= ' AND record_type=%s';
			$args[] = $type;
		}
		$args[] = $limit + 1;
		$sql = "SELECT id,record_type,record_key,locale,region,status,is_public,payload,row_version,review_due_at,updated_at FROM {$tables['records']} WHERE {$where} ORDER BY id ASC LIMIT %d";
		$wpdb->last_error = '';
		$rows = $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A );
		if ( '' !== (string) $wpdb->last_error || ! is_array( $rows ) ) {
			return new WP_Error( 'gcu_future_records_page_query_failed', __( 'Future records could not be read safely.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
		}
		$more = count( $rows ) > $limit;
		if ( $more ) {
			$rows = array_slice( $rows, 0, $limit );
		}
		$next = $more && $rows ? (int) $rows[ count( $rows ) - 1 ]['id'] : null;
		foreach ( $rows as &$row ) {
			unset( $row['id'] );
			$decoded = json_decode( (string) $row['payload'], true );
			$row['payload'] = is_array( $decoded ) ? $decoded : array();
		}
		unset( $row );
		return array( 'items' => $rows, 'next_cursor' => $next, 'limit' => $limit );
	}

	private static function resolve_report_record_safe( $id, $expected, $status, $resolution ) {
		$ready = GCU_Future_Intelligence::runtime_ready();
		if ( is_wp_error( $ready ) ) {
			return $ready;
		}
		if ( ! wp_is_uuid( $id ) ) {
			return new WP_Error( 'gcu_future_report_id_invalid', __( 'A valid report identifier is required.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		if ( ! in_array( $status, array( 'reviewing', 'resolved', 'rejected' ), true ) ) {
			return new WP_Error( 'gcu_future_report_status_invalid', __( 'Invalid report status.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$resolution = trim( sanitize_textarea_field( $resolution ) );
		if ( in_array( $status, array( 'resolved', 'rejected' ), true ) && strlen( $resolution ) < 8 ) {
			return new WP_Error( 'gcu_future_report_resolution_required', __( 'A meaningful resolution is required.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$lock = GCU_Hardening::acquire_db_lock( 'future-report:' . $id, 3 );
		if ( ! $lock ) {
			return new WP_Error( 'gcu_future_report_lock_busy', __( 'Another report update is in progress.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
		}
		try {
			global $wpdb;
			$tables = GCU_Future_Intelligence::tables();
			$wpdb->last_error = '';
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tables['reports']} WHERE public_id=%s", $id ), ARRAY_A );
			if ( '' !== (string) $wpdb->last_error ) {
				return new WP_Error( 'gcu_future_report_read_failed', __( 'The report could not be read safely.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
			}
			if ( ! $row ) {
				return new WP_Error( 'gcu_future_report_not_found', __( 'Report not found.', 'global-clinic-usp-integration' ), array( 'status' => 404 ) );
			}
			if ( (int) $row['row_version'] !== (int) $expected ) {
				return new WP_Error( 'gcu_future_report_version_conflict', __( 'The report changed. Reload it.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
			}
			$repo = GCU_Plugin::instance()->repository();
			if ( ! $repo->begin_owned_transaction() ) {
				return new WP_Error( 'gcu_future_report_transaction_failed', __( 'The report transaction could not start.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
			}
			$wpdb->last_error = '';
			$done = $wpdb->query( $wpdb->prepare( "UPDATE {$tables['reports']} SET status=%s,resolution=%s,row_version=row_version+1,updated_at=%s WHERE id=%d AND row_version=%d", $status, $resolution, current_time( 'mysql', true ), (int) $row['id'], (int) $expected ) );
			if ( '' !== (string) $wpdb->last_error || 1 !== $done || false === $repo->audit( 'copy_quality_report_updated', 'future_report', $id, 'public_feedback_review', $resolution, $row, array( 'status' => $status ) ) ) {
				$repo->rollback_owned_transaction();
				return new WP_Error( 'gcu_future_report_update_failed', __( 'The report could not be updated with its mandatory audit record.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
			}
			if ( ! $repo->commit_owned_transaction() ) {
				$repo->rollback_owned_transaction();
				return new WP_Error( 'gcu_future_report_commit_failed', __( 'The report update could not be committed safely.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
			}
			return array( 'report_id' => $id, 'status' => $status, 'row_version' => (int) $expected + 1 );
		} finally {
			GCU_Hardening::release_db_lock( $lock );
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
