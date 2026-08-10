<?php

defined( 'ABSPATH' ) || exit;

/**
 * File 14 Future Conversion & Trust Intelligence implementation.
 *
 * This layer owns only File-14 trust/copy/placement/handoff/measurement intelligence.
 * It never becomes doctor, clinic, appointment, payment, verification or shell source of truth.
 */
final class GCU_Future_Intelligence {
	const SCHEMA_VERSION = 1;
	const SCHEMA_OPTION = 'gcu_future_schema_version';
	const SAFE_MODE_OPTION = 'gcu_future_safe_mode';
	const LAST_ANOMALY_OPTION = 'gcu_future_last_anomaly';
	const LAST_PARITY_OPTION = 'gcu_future_last_parity';
	const REPORT_RETENTION_DAYS = 365;
	const RECORD_PAYLOAD_MAX = 20000;
	private static $booted = false;

	public static function bootstrap() {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;
		self::ensure_schema();
		self::schedule();
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
		add_filter( 'rest_request_before_callbacks', array( __CLASS__, 'workflow_preflight' ), 10, 3 );
		add_filter( 'gcu_public_route_html', array( __CLASS__, 'filter_public_route_html' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ), 30 );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 30 );
		add_action( 'admin_post_gcu_future_report', array( __CLASS__, 'submit_report' ) );
		add_action( 'admin_post_nopriv_gcu_future_report', array( __CLASS__, 'submit_report' ) );
		add_action( 'admin_post_gcu_future_resolve_report', array( __CLASS__, 'resolve_report' ) );
		add_action( 'gcu_future_daily_governance', array( __CLASS__, 'daily_governance' ) );
		add_action( 'gcu_future_hourly_intelligence', array( __CLASS__, 'hourly_intelligence' ) );
		add_action( 'gcu_lifecycle_cleanup', array( __CLASS__, 'cleanup' ) );
		add_action( 'BusinessPolicyChanged.v1', array( __CLASS__, 'business_policy_changed' ) );
	}

	public static function tables() {
		global $wpdb;
		return array(
			'records' => $wpdb->prefix . 'gcu_future_records',
			'reports' => $wpdb->prefix . 'gcu_future_reports',
		);
	}

	public static function ensure_schema() {
		if ( self::SCHEMA_VERSION === (int) get_option( self::SCHEMA_OPTION, 0 ) ) {
			$verified = self::verify_schema();
			if ( true === $verified ) {
				delete_option( self::SAFE_MODE_OPTION );
				return true;
			}
		}
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$c = $wpdb->get_charset_collate();
		$t = self::tables();
		$engine = "ENGINE=InnoDB $c";
		$sql = array();
		$sql[] = "CREATE TABLE {$t['records']} (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,record_type varchar(64) NOT NULL,record_key varchar(191) NOT NULL,locale varchar(32) NOT NULL DEFAULT 'en-US',region varchar(16) NOT NULL DEFAULT 'ZZ',status varchar(32) NOT NULL DEFAULT 'draft',is_public tinyint(1) NOT NULL DEFAULT 0,payload longtext NOT NULL,payload_hash char(64) NOT NULL,row_version bigint(20) unsigned NOT NULL DEFAULT 1,review_due_at datetime NULL,created_by bigint(20) unsigned NOT NULL DEFAULT 0,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),UNIQUE KEY record_identity (record_type,record_key,locale,region),KEY public_lookup (record_type,status,is_public,locale,region),KEY review_due (status,review_due_at)) $engine;";
		$sql[] = "CREATE TABLE {$t['reports']} (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,public_id char(36) NOT NULL,report_type varchar(64) NOT NULL,route_key varchar(64) NOT NULL,block_key varchar(191) NULL,locale varchar(32) NOT NULL DEFAULT 'en-US',reason_code varchar(64) NOT NULL,message text NULL,actor_hash char(64) NULL,status varchar(32) NOT NULL DEFAULT 'open',resolution text NULL,row_version bigint(20) unsigned NOT NULL DEFAULT 1,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),UNIQUE KEY public_id (public_id),KEY review_queue (status,created_at),KEY block_lookup (block_key,status)) $engine;";
		foreach ( $sql as $statement ) {
			dbDelta( $statement );
		}
		$verified = self::verify_schema();
		if ( is_wp_error( $verified ) ) {
			update_option( self::SAFE_MODE_OPTION, 1, false );
			GCU_Observability::log( 'error', 'future_schema_verification_failed', array( 'code' => $verified->get_error_code() ) );
			return $verified;
		}
		update_option( self::SCHEMA_OPTION, self::SCHEMA_VERSION, false );
		delete_option( self::SAFE_MODE_OPTION );
		self::seed_defaults();
		return true;
	}

	public static function verify_schema() {
		global $wpdb;
		$missing = array();
		$non_innodb = array();
		foreach ( self::tables() as $key => $table ) {
			$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
			if ( $table !== $found ) {
				$missing[] = $key;
				continue;
			}
			$status = $wpdb->get_row( $wpdb->prepare( 'SHOW TABLE STATUS LIKE %s', $wpdb->esc_like( $table ) ), ARRAY_A );
			$engine = is_array( $status ) && isset( $status['Engine'] ) ? strtolower( (string) $status['Engine'] ) : '';
			if ( 'innodb' !== $engine ) {
				$non_innodb[ $key ] = $engine ? $engine : 'unknown';
			}
		}
		return ( $missing || $non_innodb ) ? new WP_Error( 'gcu_future_schema_unverified', __( 'Future Conversion and Trust Intelligence storage is not safely verified.', 'global-clinic-usp-integration' ), array( 'missing' => $missing, 'non_innodb' => $non_innodb ) ) : true;
	}

	private static function seed_defaults() {
		$glossary = array( 'terms' => GCU_Future_Policy::terminology_lock(), 'source' => GCU_Future_Policy::PLAN_ID, 'reviewer' => 'Founder-approved plan', 'provenance' => 'approved amendment' );
		self::upsert_record( 'terminology_lock', 'protected_terms', 'en-US', 'ZZ', $glossary, 'active', false, 0, true );
		$change = array( 'title' => 'Future Conversion & Trust Intelligence v2.0', 'summary' => 'Twenty-four Founder-approved ethical conversion, trust, privacy, experiment and transparency enhancements were added to File 14.', 'effective_date' => '2026-08-10', 'material' => true );
		self::upsert_record( 'change_log', 'future_cti_v2_0', 'en-US', 'ZZ', $change, 'active', true, 0, true );
	}

	private static function schedule() {
		if ( ! wp_next_scheduled( 'gcu_future_daily_governance' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'gcu_future_daily_governance' );
		}
		if ( ! wp_next_scheduled( 'gcu_future_hourly_intelligence' ) ) {
			wp_schedule_event( time() + 10 * MINUTE_IN_SECONDS, 'hourly', 'gcu_future_hourly_intelligence' );
		}
	}

	public static function register_routes() {
		$ns = 'gcu/v1';
		register_rest_route( $ns, '/future/catalog', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( __CLASS__, 'rest_catalog' ), 'permission_callback' => '__return_true' ) );
		register_rest_route( $ns, '/future/handoff', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( __CLASS__, 'rest_handoff' ), 'permission_callback' => '__return_true' ) );
		register_rest_route( $ns, '/future/trust/(?P<claim_key>[a-z0-9_\-]+)', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( __CLASS__, 'rest_trust' ), 'permission_callback' => '__return_true' ) );
		register_rest_route( $ns, '/future/report', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( __CLASS__, 'rest_report' ), 'permission_callback' => '__return_true' ) );
		register_rest_route( $ns, '/future/change-log', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( __CLASS__, 'rest_change_log' ), 'permission_callback' => '__return_true' ) );
		register_rest_route( $ns, '/future/readiness', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( __CLASS__, 'rest_readiness' ), 'permission_callback' => '__return_true' ) );
		register_rest_route( $ns, '/future/preflight/copy', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( __CLASS__, 'rest_copy_preflight' ), 'permission_callback' => array( __CLASS__, 'can_manage_content' ) ) );
		register_rest_route( $ns, '/future/preflight/experiment', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( __CLASS__, 'rest_experiment_preflight' ), 'permission_callback' => array( __CLASS__, 'can_manage_experiments' ) ) );
		register_rest_route( $ns, '/future/quality', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( __CLASS__, 'rest_quality' ), 'permission_callback' => array( __CLASS__, 'can_view_analytics' ) ) );
		register_rest_route( $ns, '/future/friction', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( __CLASS__, 'rest_friction' ), 'permission_callback' => array( __CLASS__, 'can_view_analytics' ) ) );
		register_rest_route( $ns, '/future/scenarios', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( __CLASS__, 'rest_scenarios' ), 'permission_callback' => array( __CLASS__, 'can_system_check' ) ) );
		register_rest_route( $ns, '/future/faq-gaps', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( __CLASS__, 'rest_faq_gaps' ), 'permission_callback' => array( __CLASS__, 'can_manage_content' ) ) );
		register_rest_route( $ns, '/future/consistency', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( __CLASS__, 'rest_consistency' ), 'permission_callback' => array( __CLASS__, 'can_manage_content' ) ) );
		register_rest_route( $ns, '/future/ai-copy', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( __CLASS__, 'rest_ai_copy' ), 'permission_callback' => array( __CLASS__, 'can_manage_content' ) ) );
		register_rest_route( $ns, '/future/records', array( array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( __CLASS__, 'rest_records' ), 'permission_callback' => array( __CLASS__, 'can_manage_content' ) ), array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( __CLASS__, 'rest_record_write' ), 'permission_callback' => array( __CLASS__, 'can_manage_content' ) ) ) );
		register_rest_route( $ns, '/future/reports', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( __CLASS__, 'rest_reports' ), 'permission_callback' => array( __CLASS__, 'can_manage_content' ) ) );
		register_rest_route( $ns, '/future/claims/(?P<claim_key>[a-z0-9_\-]+)/revalidate', array( 'methods' => WP_REST_Server::EDITABLE, 'callback' => array( __CLASS__, 'rest_revalidate_claim' ), 'permission_callback' => array( __CLASS__, 'can_approve_claims' ) ) );
	}

	public static function rest_catalog() {
		return self::public_response( array( 'plan' => GCU_Future_Policy::PLAN_ID, 'features' => GCU_Future_Policy::feature_catalog(), 'version' => GCU_VERSION ) );
	}

	public static function rest_handoff( WP_REST_Request $request ) {
		$rate = GCU_Plugin::instance()->repository()->consume_rate_limit( 'future-handoff', 60 );
		if ( is_wp_error( $rate ) ) {
			return $rate;
		}
		$data = $request->get_json_params();
		$data = is_array( $data ) ? $data : array();
		$intent = sanitize_key( isset( $data['intent'] ) ? $data['intent'] : '' );
		$intents = GCU_Future_Policy::supported_intents();
		if ( ! isset( $intents[ $intent ] ) ) {
			return new WP_Error( 'gcu_future_invalid_intent', __( 'Choose one of the approved Global Clinic intents.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$context = GCU_Future_Policy::sanitize_handoff_context( $data );
		$handoff = self::handoff( $intent, $context );
		return self::no_store_response( $handoff, ! empty( $handoff['available'] ) ? 200 : 503 );
	}

	public static function rest_trust( WP_REST_Request $request ) {
		$key = sanitize_key( $request['claim_key'] );
		$claims = GCU_Plugin::instance()->repository()->public_claims( array( $key ) );
		if ( empty( $claims[ $key ] ) ) {
			return new WP_Error( 'gcu_future_claim_unavailable', __( 'This public trust claim is unavailable or requires review.', 'global-clinic-usp-integration' ), array( 'status' => 404 ) );
		}
		return self::public_response( array( 'claim' => $claims[ $key ], 'freshness' => self::claim_freshness( $claims[ $key ] ) ) );
	}

	public static function rest_report( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$data = is_array( $data ) ? $data : array();
		$result = self::create_report( $data );
		return is_wp_error( $result ) ? $result : self::no_store_response( $result, 201 );
	}

	public static function rest_change_log() {
		return self::public_response( array( 'items' => self::public_change_log( 20 ) ) );
	}

	public static function rest_readiness( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		return self::no_store_response( GCU_Future_Policy::doctor_readiness_check( is_array( $data ) ? $data : array() ) );
	}

	public static function rest_copy_preflight( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$data = is_array( $data ) ? $data : array();
		$previous = isset( $data['previous'] ) && is_array( $data['previous'] ) ? $data['previous'] : array();
		$current = isset( $data['current'] ) && is_array( $data['current'] ) ? $data['current'] : $data;
		$result = GCU_Future_Policy::copy_preflight( $current, $previous );
		$result['parity'] = self::parity_status();
		$result['safe'] = $result['safe'] && ! empty( $result['parity']['ok'] );
		return self::no_store_response( $result );
	}

	public static function rest_experiment_preflight( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$data = is_array( $data ) ? $data : array();
		$result = GCU_Future_Policy::experiment_preflight(
			isset( $data['variants'] ) && is_array( $data['variants'] ) ? $data['variants'] : array(),
			isset( $data['guardrails'] ) && is_array( $data['guardrails'] ) ? $data['guardrails'] : array(),
			isset( $data['sample_policy'] ) ? $data['sample_policy'] : '',
			isset( $data['privacy_policy'] ) ? $data['privacy_policy'] : ''
		);
		$result['parity'] = self::parity_status();
		$result['safe'] = $result['safe'] && ! empty( $result['parity']['ok'] );
		return self::no_store_response( $result );
	}

	public static function rest_quality() {
		return self::no_store_response( self::quality_score() );
	}

	public static function rest_friction( WP_REST_Request $request ) {
		$days = max( 1, min( 90, absint( $request->get_param( 'days' ) ? $request->get_param( 'days' ) : 30 ) ) );
		return self::no_store_response( self::friction_summary( $days ) );
	}

	public static function rest_scenarios() {
		return self::no_store_response( self::scenario_lab() );
	}

	public static function rest_faq_gaps() {
		return self::no_store_response( array( 'items' => self::faq_gap_candidates(), 'source' => 'approved aggregate question adapters only' ) );
	}

	public static function rest_consistency() {
		return self::no_store_response( self::consistency_graph() );
	}

	public static function rest_ai_copy( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$data = is_array( $data ) ? $data : array();
		return self::no_store_response( self::ai_copy_assist( $data ) );
	}

	public static function rest_records( WP_REST_Request $request ) {
		$type = sanitize_key( (string) $request->get_param( 'type' ) );
		return self::no_store_response( array( 'items' => self::records( $type, false, 100 ) ) );
	}

	public static function rest_record_write( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$data = is_array( $data ) ? $data : array();
		$type = sanitize_key( isset( $data['record_type'] ) ? $data['record_type'] : '' );
		$allowed = array( 'jurisdiction_copy', 'terminology_lock', 'change_log', 'faq_gap', 'ai_draft', 'scenario_note' );
		if ( ! in_array( $type, $allowed, true ) ) {
			return new WP_Error( 'gcu_future_record_type_forbidden', __( 'That Future Intelligence record type is outside File 14 scope.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$key = sanitize_key( isset( $data['record_key'] ) ? $data['record_key'] : '' );
		if ( ! $key ) {
			return new WP_Error( 'gcu_future_record_key_required', __( 'A stable record key is required.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$result = self::upsert_record(
			$type,
			$key,
			GCU_Policy::sanitize_locale( isset( $data['locale'] ) ? $data['locale'] : 'en-US' ),
			self::sanitize_region( isset( $data['region'] ) ? $data['region'] : 'ZZ' ),
			isset( $data['payload'] ) && is_array( $data['payload'] ) ? $data['payload'] : array(),
			isset( $data['status'] ) ? sanitize_key( $data['status'] ) : 'draft',
			! empty( $data['is_public'] ),
			isset( $data['expected_version'] ) ? absint( $data['expected_version'] ) : 0,
			false
		);
		return is_wp_error( $result ) ? $result : self::no_store_response( $result, 201 );
	}

	public static function rest_reports() {
		return self::no_store_response( array( 'items' => self::reports( 'open', 100 ) ) );
	}

	public static function rest_revalidate_claim( WP_REST_Request $request ) {
		$key = sanitize_key( $request['claim_key'] );
		$expected = absint( $request->get_param( 'expected_version' ) );
		$reason = sanitize_textarea_field( (string) $request->get_param( 'reason' ) );
		$result = self::revalidate_claim( $key, $expected, $reason );
		return is_wp_error( $result ) ? $result : self::no_store_response( $result );
	}

	public static function workflow_preflight( $response, $handler, WP_REST_Request $request ) {
		if ( null !== $response ) {
			return $response;
		}
		$route = $request->get_route();
		if ( ! preg_match( '#^/gcu/v1/workflow/(copy|experiment)/([a-f0-9\-]{36})$#', $route, $m ) ) {
			return $response;
		}
		$target = sanitize_key( (string) $request->get_param( 'target' ) );
		if ( 'copy' === $m[1] && in_array( $target, array( 'founder_approved', 'active' ), true ) ) {
			$row = GCU_Plugin::instance()->repository()->record_by_public_id( 'blocks', $m[2] );
			if ( ! $row ) {
				return $response;
			}
			$previous = self::previous_active_block( $row );
			$preflight = GCU_Future_Policy::copy_preflight( $row, $previous );
			$parity = self::parity_status();
			if ( ! $preflight['safe'] || empty( $parity['ok'] ) ) {
				return new WP_Error( 'gcu_future_copy_preflight_failed', __( 'Future Conversion and Trust governance blocked this copy until its meaning, dark-pattern and policy-parity risks are corrected.', 'global-clinic-usp-integration' ), array( 'status' => 409, 'preflight' => $preflight, 'parity' => $parity ) );
			}
		}
		if ( 'experiment' === $m[1] && in_array( $target, array( 'approved', 'running' ), true ) ) {
			$row = GCU_Plugin::instance()->repository()->record_by_public_id( 'experiments', $m[2] );
			if ( ! $row ) {
				return $response;
			}
			$preflight = GCU_Future_Policy::experiment_preflight(
				self::json_array( $row['variants'] ),
				self::json_array( $row['guardrails'] ),
				$row['sample_policy'],
				$row['privacy_policy']
			);
			if ( ! $preflight['safe'] || empty( self::parity_status()['ok'] ) ) {
				return new WP_Error( 'gcu_future_experiment_preflight_failed', __( 'The experiment failed the Future Intelligence safety preflight.', 'global-clinic-usp-integration' ), array( 'status' => 409, 'preflight' => $preflight ) );
			}
		}
		return $response;
	}

	private static function previous_active_block( array $row ) {
		global $wpdb;
		$t = GCU_Install::tables();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['blocks']} WHERE block_key=%s AND locale=%s AND status='active' AND id<>%d ORDER BY content_version DESC LIMIT 1", $row['block_key'], $row['locale'], (int) $row['id'] ), ARRAY_A );
	}

	public static function handoff( $intent, array $context ) {
		$intents = GCU_Future_Policy::supported_intents();
		if ( ! isset( $intents[ $intent ] ) ) {
			return array( 'available' => false, 'reason' => 'invalid_intent', 'alternatives' => array() );
		}
		$destination = $intents[ $intent ]['destination'];
		if ( 'how_it_works' === $destination ) {
			return array( 'available' => true, 'destination' => $destination, 'url' => home_url( '/clinic/how-it-works/' ), 'context' => $context, 'alternatives' => array() );
		}
		$health = GCU_Plugin::instance()->contracts()->destination( $destination );
		if ( ! empty( $health['available'] ) && ! empty( $health['url'] ) ) {
			$args = array_filter( array( 'country' => $context['country'], 'language' => $context['language'], 'mode' => $context['mode'] ) );
			$url = $args ? add_query_arg( $args, $health['url'] ) : $health['url'];
			$url = GCU_Hardening::strict_same_origin_url( $url );
			return array( 'available' => (bool) $url, 'destination' => $destination, 'url' => $url, 'context' => $context, 'alternatives' => array() );
		}
		return array( 'available' => false, 'destination' => $destination, 'reason' => isset( $health['reason'] ) ? $health['reason'] : 'unavailable', 'context' => $context, 'alternatives' => self::failover_matrix( $intent ) );
	}

	public static function failover_matrix( $intent ) {
		$alternatives = array();
		if ( 'patient' === $intent ) {
			$alternatives[] = array( 'label' => 'Understand the clinic journey', 'url' => home_url( '/clinic/how-it-works/' ), 'owner' => 'File 14 explanation' );
			$alternatives[] = array( 'label' => 'Return to Global Clinic', 'url' => home_url( '/global-clinic/' ), 'owner' => 'File 14' );
		} elseif ( 'doctor' === $intent ) {
			$alternatives[] = array( 'label' => 'Review onboarding requirements', 'url' => home_url( '/clinic/how-it-works/' ), 'owner' => 'File 14 explanation' );
			$alternatives[] = array( 'label' => 'Return to Global Clinic', 'url' => home_url( '/global-clinic/' ), 'owner' => 'File 14' );
		}
		return $alternatives;
	}

	public static function parity_status() {
		global $wpdb;
		$t = GCU_Install::tables();
		$canonical = GCU_Policy::canonical_claims();
		$required = array( 'zero_platform_commission', 'free_approved_core', 'optional_support_no_ranking' );
		$issues = array();
		$rules = GCU_Policy::business_rules();
		if ( 0 !== (int) $rules['platform_commission_percent'] ) {
			$issues[] = 'business_rule_commission_not_zero';
		}
		if ( 'free' !== $rules['approved_core_tier'] ) {
			$issues[] = 'business_rule_core_tier_not_free';
		}
		if ( ! empty( $rules['support_affects_visibility'] ) ) {
			$issues[] = 'business_rule_support_affects_visibility';
		}
		foreach ( $required as $key ) {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT claim_text,status,is_public,review_due_at,expires_at FROM {$t['claims']} WHERE claim_key=%s", $key ), ARRAY_A );
			if ( ! $row || 'active' !== $row['status'] || empty( $row['is_public'] ) ) {
				$issues[] = 'claim_unavailable:' . $key;
				continue;
			}
			if ( ! hash_equals( hash( 'sha256', (string) $canonical[ $key ]['text'] ), hash( 'sha256', (string) $row['claim_text'] ) ) ) {
				$issues[] = 'claim_text_drift:' . $key;
			}
			if ( ! self::claim_freshness( $row )['fresh'] ) {
				$issues[] = 'claim_stale:' . $key;
			}
		}
		$blocks = $wpdb->get_results( "SELECT title,body,cta_label FROM {$t['blocks']} WHERE status='active' LIMIT 500", ARRAY_A );
		foreach ( is_array( $blocks ) ? $blocks : array() as $block ) {
			$scan = GCU_Future_Policy::dark_pattern_scan( implode( ' ', array( $block['title'], wp_strip_all_tags( $block['body'] ), $block['cta_label'] ) ) );
			foreach ( $scan['flags'] as $flag ) {
				$issues[] = 'active_copy:' . $flag;
			}
		}
		$result = array( 'ok' => empty( $issues ), 'issues' => array_values( array_unique( $issues ) ), 'checked_at' => gmdate( 'c' ) );
		update_option( self::LAST_PARITY_OPTION, $result, false );
		return $result;
	}

	private static function claim_freshness( array $row ) {
		$now = time();
		$review = ! empty( $row['review_due_at'] ) ? strtotime( $row['review_due_at'] . ' UTC' ) : 0;
		$expires = ! empty( $row['expires_at'] ) ? strtotime( $row['expires_at'] . ' UTC' ) : 0;
		$fresh = ( ! $review || $review > $now ) && ( ! $expires || $expires > $now );
		return array( 'fresh' => $fresh, 'review_due_at' => isset( $row['review_due_at'] ) ? $row['review_due_at'] : null, 'expires_at' => isset( $row['expires_at'] ) ? $row['expires_at'] : null );
	}

	public static function claim_freshness_sentinel() {
		global $wpdb;
		$t = GCU_Install::tables();
		$rows = $wpdb->get_results( "SELECT * FROM {$t['claims']} WHERE status='active' AND ((review_due_at IS NOT NULL AND review_due_at<=UTC_TIMESTAMP()) OR (expires_at IS NOT NULL AND expires_at<=UTC_TIMESTAMP())) LIMIT 100", ARRAY_A );
		$count = 0;
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			$lock = GCU_Hardening::acquire_db_lock( 'future-claim:' . $row['claim_key'], 3 );
			if ( ! $lock ) {
				continue;
			}
			try {
				if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
					continue;
				}
				$history = array(
					'claim_key' => $row['claim_key'], 'row_version' => (int) $row['row_version'], 'status' => $row['status'],
					'claim_hash' => hash( 'sha256', wp_json_encode( $row ) ), 'reason' => 'freshness_sentinel', 'snapshot' => wp_json_encode( $row ),
					'actor_id' => 0, 'created_at' => current_time( 'mysql', true ),
				);
				$inserted = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$t['claim_history']} (claim_key,row_version,status,claim_hash,reason,snapshot,actor_id,created_at) VALUES (%s,%d,%s,%s,%s,%s,%d,%s)", $history['claim_key'], $history['row_version'], $history['status'], $history['claim_hash'], $history['reason'], $history['snapshot'], 0, $history['created_at'] ) );
				if ( false === $inserted ) {
					$wpdb->query( 'ROLLBACK' );
					continue;
				}
				$done = $wpdb->query( $wpdb->prepare( "UPDATE {$t['claims']} SET status='review_required',is_public=0,row_version=row_version+1,updated_at=%s WHERE id=%d AND row_version=%d AND status='active'", current_time( 'mysql', true ), (int) $row['id'], (int) $row['row_version'] ) );
				if ( 1 !== $done || false === $wpdb->query( 'COMMIT' ) ) {
					$wpdb->query( 'ROLLBACK' );
					continue;
				}
				$count++;
				GCU_Plugin::instance()->repository()->audit( 'claim_freshness_blocked', 'claim', $row['claim_key'], 'claim_governance', 'Review due or expiry reached', $row, array( 'status' => 'review_required', 'is_public' => 0 ) );
			} finally {
				GCU_Hardening::release_db_lock( $lock );
			}
		}
		return $count;
	}

	public static function revalidate_claim( $key, $expected, $reason ) {
		$reason = trim( sanitize_textarea_field( $reason ) );
		if ( strlen( $reason ) < 8 ) {
			return new WP_Error( 'gcu_future_revalidation_reason_required', __( 'A meaningful revalidation reason is required.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$canonical = GCU_Policy::canonical_claims();
		if ( ! isset( $canonical[ $key ] ) ) {
			return new WP_Error( 'gcu_future_unknown_claim', __( 'Only canonical File 14 claims can be revalidated here.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		global $wpdb;
		$t = GCU_Install::tables();
		$lock = GCU_Hardening::acquire_db_lock( 'future-claim:' . $key, 3 );
		if ( ! $lock ) {
			return new WP_Error( 'gcu_future_claim_lock_busy', __( 'Another claim update is running.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
		}
		try {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['claims']} WHERE claim_key=%s", $key ), ARRAY_A );
			if ( ! $row ) {
				return new WP_Error( 'gcu_future_claim_not_found', __( 'Claim not found.', 'global-clinic-usp-integration' ), array( 'status' => 404 ) );
			}
			if ( (int) $row['row_version'] !== (int) $expected ) {
				return new WP_Error( 'gcu_future_claim_version_conflict', __( 'The claim changed. Reload before revalidating.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
			}
			if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
				return new WP_Error( 'gcu_future_claim_transaction_failed', __( 'Claim transaction could not start.', 'global-clinic-usp-integration' ), array( 'status' => 500 ) );
			}
			$history = wp_json_encode( $row );
			$ins = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$t['claim_history']} (claim_key,row_version,status,claim_hash,reason,snapshot,actor_id,created_at) VALUES (%s,%d,%s,%s,%s,%s,%d,%s)", $key, (int) $row['row_version'], $row['status'], hash( 'sha256', $history ), $reason, $history, get_current_user_id(), current_time( 'mysql', true ) ) );
			if ( false === $ins ) {
				$wpdb->query( 'ROLLBACK' );
				return new WP_Error( 'gcu_future_claim_history_failed', __( 'Claim history could not be recorded.', 'global-clinic-usp-integration' ), array( 'status' => 500 ) );
			}
			$review_due = gmdate( 'Y-m-d H:i:s', time() + GCU_Policy::COPY_REVIEW_DAYS * DAY_IN_SECONDS );
			$done = $wpdb->query( $wpdb->prepare( "UPDATE {$t['claims']} SET claim_text=%s,basis=%s,owner_name=%s,status='active',is_public=%d,review_due_at=%s,expires_at=NULL,row_version=row_version+1,updated_at=%s WHERE id=%d AND row_version=%d", $canonical[ $key ]['text'], $canonical[ $key ]['basis'], $canonical[ $key ]['owner'], empty( $canonical[ $key ]['public'] ) ? 0 : 1, $review_due, current_time( 'mysql', true ), (int) $row['id'], (int) $expected ) );
			if ( 1 !== $done || false === $wpdb->query( 'COMMIT' ) ) {
				$wpdb->query( 'ROLLBACK' );
				return new WP_Error( 'gcu_future_claim_revalidation_failed', __( 'Claim revalidation could not be committed.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
			}
			GCU_Plugin::instance()->repository()->audit( 'claim_revalidated', 'claim', $key, 'claim_governance', $reason, $row, array( 'status' => 'active', 'review_due_at' => $review_due ) );
			return array( 'claim_key' => $key, 'status' => 'active', 'row_version' => (int) $expected + 1, 'review_due_at' => $review_due );
		} finally {
			GCU_Hardening::release_db_lock( $lock );
		}
	}

	public static function quality_score() {
		global $wpdb;
		$t = GCU_Install::tables();
		$rows = $wpdb->get_results( "SELECT funnel_stage,COUNT(*) total FROM {$t['events']} WHERE occurred_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 30 DAY) GROUP BY funnel_stage", ARRAY_A );
		$counts = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			$counts[ $row['funnel_stage'] ] = (int) $row['total'];
		}
		$selected = isset( $counts['cta_selected'] ) ? $counts['cta_selected'] : 0;
		$loaded = isset( $counts['destination_loaded'] ) ? $counts['destination_loaded'] : 0;
		$handoff = GCU_Future_Policy::cohort_allowed( $selected ) ? min( 100, round( 100 * $loaded / max( 1, $selected ), 1 ) ) : 0;
		$parity = self::parity_status();
		$stale = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t['claims']} WHERE status='review_required' OR (status='active' AND review_due_at IS NOT NULL AND review_due_at<=UTC_TIMESTAMP())" );
		$open_reports = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::tables()['reports'] . " WHERE status IN ('open','reviewing')" );
		$destinations = GCU_Plugin::instance()->contracts()->all_destination_health();
		$healthy = 0;
		foreach ( $destinations as $destination ) {
			$healthy += ! empty( $destination['available'] ) ? 1 : 0;
		}
		$destination_score = $destinations ? round( 100 * $healthy / count( $destinations ), 1 ) : 0;
		$performance = apply_filters( 'gcu_future_performance_score', null );
		$performance_verified = is_numeric( $performance );
		$metrics = array(
			'handoff_success' => $handoff,
			'accessibility' => (float) apply_filters( 'gcu_future_accessibility_score', 100 ),
			'claim_freshness' => $stale ? max( 0, 100 - 20 * $stale ) : 100,
			'privacy' => 100,
			'complaint_health' => max( 0, 100 - min( 100, $open_reports * 10 ) ),
			'destination_health' => $destination_score,
			'performance' => $performance_verified ? (float) $performance : 50,
		);
		return array( 'score' => GCU_Future_Policy::conversion_quality_score( $metrics ), 'provisional' => ! $performance_verified || ! GCU_Future_Policy::cohort_allowed( $selected ), 'metrics' => $metrics, 'sample_count' => $selected, 'small_cohort_suppressed' => ! GCU_Future_Policy::cohort_allowed( $selected ), 'parity' => $parity, 'performance_verified' => $performance_verified );
	}

	public static function friction_summary( $days = 30 ) {
		global $wpdb;
		$t = GCU_Install::tables();
		$days = max( 1, min( 90, (int) $days ) );
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT funnel_stage,COUNT(*) total FROM {$t['events']} WHERE occurred_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL %d DAY) GROUP BY funnel_stage", $days ), ARRAY_A );
		$total = 0;
		$stages = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			$total += (int) $row['total'];
			$stages[ $row['funnel_stage'] ] = (int) $row['total'];
		}
		if ( ! GCU_Future_Policy::cohort_allowed( $total ) ) {
			return array( 'suppressed' => true, 'threshold' => GCU_Future_Policy::MIN_COHORT, 'days' => $days, 'stages' => array(), 'dropoffs' => array() );
		}
		$order = array( 'impression', 'cta_selected', 'destination_loaded', 'application_started', 'booking_started' );
		$dropoffs = array();
		$previous = null;
		foreach ( $order as $stage ) {
			$count = isset( $stages[ $stage ] ) ? $stages[ $stage ] : 0;
			if ( null !== $previous && GCU_Future_Policy::cohort_allowed( $previous ) ) {
				$dropoffs[ $stage ] = round( max( 0, 100 * ( $previous - min( $previous, $count ) ) / max( 1, $previous ) ), 1 );
			}
			$previous = $count;
		}
		return array( 'suppressed' => false, 'threshold' => GCU_Future_Policy::MIN_COHORT, 'days' => $days, 'stages' => $stages, 'dropoffs' => $dropoffs );
	}

	public static function anomaly_detector() {
		global $wpdb;
		$t = GCU_Install::tables();
		$current = $wpdb->get_row( "SELECT SUM(funnel_stage='cta_selected') selected,SUM(funnel_stage='destination_loaded') loaded FROM {$t['events']} WHERE occurred_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 24 HOUR)", ARRAY_A );
		$baseline = $wpdb->get_row( "SELECT SUM(funnel_stage='cta_selected') selected,SUM(funnel_stage='destination_loaded') loaded FROM {$t['events']} WHERE occurred_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 24 HOUR) AND occurred_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 8 DAY)", ARRAY_A );
		$cs = isset( $current['selected'] ) ? (int) $current['selected'] : 0;
		$cl = isset( $current['loaded'] ) ? (int) $current['loaded'] : 0;
		$bs = isset( $baseline['selected'] ) ? (int) $baseline['selected'] : 0;
		$bl = isset( $baseline['loaded'] ) ? (int) $baseline['loaded'] : 0;
		if ( ! GCU_Future_Policy::cohort_allowed( $cs ) || ! GCU_Future_Policy::cohort_allowed( $bs ) ) {
			$result = array( 'status' => 'insufficient_sample', 'severity' => 'none', 'current_sample' => $cs, 'baseline_sample' => $bs, 'checked_at' => gmdate( 'c' ) );
			update_option( self::LAST_ANOMALY_OPTION, $result, false );
			return $result;
		}
		$current_rate = $cl / max( 1, $cs );
		$baseline_rate = $bl / max( 1, $bs );
		$relative_drop = $baseline_rate > 0 ? max( 0, ( $baseline_rate - $current_rate ) / $baseline_rate ) : 0;
		$severity = $relative_drop >= 0.6 ? 'high' : ( $relative_drop >= 0.4 ? 'medium' : 'none' );
		$result = array( 'status' => 'measured', 'severity' => $severity, 'current_rate' => round( $current_rate, 4 ), 'baseline_rate' => round( $baseline_rate, 4 ), 'relative_drop' => round( $relative_drop, 4 ), 'current_sample' => $cs, 'baseline_sample' => $bs, 'checked_at' => gmdate( 'c' ) );
		update_option( self::LAST_ANOMALY_OPTION, $result, false );
		return $result;
	}

	public static function early_stop_guard() {
		$parity = self::parity_status();
		$anomaly = get_option( self::LAST_ANOMALY_OPTION, array() );
		$destinations = GCU_Plugin::instance()->contracts()->all_destination_health();
		$destination_failure = false;
		foreach ( $destinations as $destination ) {
			if ( empty( $destination['available'] ) ) {
				$destination_failure = true;
				break;
			}
		}
		global $wpdb;
		$open_reports = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::tables()['reports'] . " WHERE status IN ('open','reviewing') AND created_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 7 DAY)" );
		$breach = empty( $parity['ok'] ) || $destination_failure || $open_reports >= 5 || ( isset( $anomaly['severity'] ) && 'high' === $anomaly['severity'] );
		if ( ! $breach ) {
			return 0;
		}
		$t = GCU_Install::tables();
		$rows = $wpdb->get_results( "SELECT * FROM {$t['experiments']} WHERE status='running' LIMIT 50", ARRAY_A );
		$count = 0;
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			$done = $wpdb->query( $wpdb->prepare( "UPDATE {$t['experiments']} SET status='stopped',row_version=row_version+1,updated_at=%s WHERE id=%d AND row_version=%d AND status='running'", current_time( 'mysql', true ), (int) $row['id'], (int) $row['row_version'] ) );
			if ( 1 === $done ) {
				$count++;
				GCU_Plugin::instance()->repository()->audit( 'experiment_early_stopped', 'experiment', $row['public_id'], 'experiment_safety', 'Automatic trust/safety guardrail breach', $row, array( 'status' => 'stopped' ) );
			}
		}
		return $count;
	}

	public static function faq_gap_candidates() {
		$signals = apply_filters( 'gcu_future_question_aggregates', array() );
		if ( ! is_array( $signals ) ) {
			return array();
		}
		global $wpdb;
		$t = GCU_Install::tables();
		$faq = $wpdb->get_col( "SELECT LOWER(title) FROM {$t['blocks']} WHERE block_type='faq' AND status='active'" );
		$out = array();
		foreach ( $signals as $signal ) {
			if ( ! is_array( $signal ) ) {
				continue;
			}
			$count = isset( $signal['count'] ) ? absint( $signal['count'] ) : 0;
			$question = trim( sanitize_text_field( isset( $signal['question'] ) ? $signal['question'] : '' ) );
			if ( ! $question || ! GCU_Future_Policy::cohort_allowed( $count ) ) {
				continue;
			}
			$norm = strtolower( $question );
			$covered = false;
			foreach ( is_array( $faq ) ? $faq : array() as $title ) {
				if ( self::text_similarity( $norm, $title ) >= 0.55 ) {
					$covered = true;
					break;
				}
			}
			if ( ! $covered ) {
				$out[] = array( 'question' => $question, 'count' => $count, 'status' => 'suggested_only', 'auto_publish' => false );
			}
		}
		usort( $out, static function ( $a, $b ) { return $b['count'] - $a['count']; } );
		return array_slice( $out, 0, 20 );
	}

	public static function consistency_graph() {
		global $wpdb;
		$t = GCU_Install::tables();
		$rows = $wpdb->get_results( "SELECT block_key,locale,title,body,cta_label,claim_keys FROM {$t['blocks']} WHERE status='active' ORDER BY block_key,locale", ARRAY_A );
		$nodes = array();
		$issues = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			$key = $row['block_key'];
			if ( ! isset( $nodes[ $key ] ) ) {
				$nodes[ $key ] = array();
			}
			$claims = self::json_array( $row['claim_keys'] );
			$nodes[ $key ][ $row['locale'] ] = array( 'claims' => $claims, 'hash' => hash( 'sha256', $row['title'] . '|' . wp_strip_all_tags( $row['body'] ) . '|' . $row['cta_label'] ) );
		}
		foreach ( $nodes as $key => $locales ) {
			foreach ( GCU_Policy::supported_locales() as $locale ) {
				if ( ! isset( $locales[ $locale ] ) ) {
					$issues[] = 'missing_locale:' . $key . ':' . $locale;
				}
			}
			$sets = array();
			foreach ( $locales as $locale => $data ) {
				$claims = $data['claims'];
				sort( $claims );
				$sets[ $locale ] = implode( ',', $claims );
			}
			if ( count( array_unique( $sets ) ) > 1 ) {
				$issues[] = 'claim_set_drift:' . $key;
			}
		}
		return array( 'ok' => empty( $issues ), 'issues' => $issues, 'nodes' => $nodes, 'terminology_lock' => GCU_Future_Policy::terminology_lock() );
	}

	public static function ai_copy_assist( array $data ) {
		$base = trim( wp_strip_all_tags( isset( $data['base_text'] ) ? $data['base_text'] : '' ) );
		$objective = sanitize_key( isset( $data['objective'] ) ? $data['objective'] : 'clarity' );
		if ( ! $base || strlen( $base ) > 5000 ) {
			return new WP_Error( 'gcu_future_ai_base_invalid', __( 'A bounded base text is required.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$all_claims = GCU_Plugin::instance()->repository()->public_claims( array_keys( GCU_Policy::canonical_claims() ) );
		$claim_texts = array();
		foreach ( $all_claims as $claim ) {
			$claim_texts[] = $claim['claim_text'];
		}
		$fallback = array();
		if ( 'concise' === $objective ) {
			$fallback[] = function_exists( 'mb_substr' ) ? mb_substr( $base, 0, 220 ) : substr( $base, 0, 220 );
		} elseif ( 'trust' === $objective ) {
			$fallback[] = $base . ' Review the linked trust details for the exact policy basis and limits.';
		} else {
			$fallback[] = $base;
		}
		$provider = apply_filters( 'gcu_future_ai_copy_provider', array(), array( 'base_text' => $base, 'objective' => $objective, 'approved_claims' => $all_claims, 'rules' => 'No new factual, medical, financial or verification claim. No auto-publish.' ) );
		$candidates = is_array( $provider ) && $provider ? $provider : $fallback;
		$safe = array();
		$rejected = array();
		foreach ( array_slice( $candidates, 0, 5 ) as $candidate ) {
			$text = trim( wp_strip_all_tags( is_array( $candidate ) && isset( $candidate['text'] ) ? $candidate['text'] : $candidate ) );
			$guard = GCU_Future_Policy::ai_copy_guard( $text, $claim_texts );
			if ( $guard['safe'] ) {
				$safe[] = array( 'text' => $text, 'guard' => $guard );
			} else {
				$rejected[] = array( 'text_hash' => hash( 'sha256', $text ), 'guard' => $guard );
			}
		}
		return array( 'candidates' => $safe, 'rejected' => $rejected, 'provider_used' => is_array( $provider ) && ! empty( $provider ), 'auto_publish' => false, 'approved_claim_count' => count( $all_claims ) );
	}

	public static function scenario_lab() {
		$dest = GCU_Plugin::instance()->contracts()->all_destination_health();
		return array(
			'scenarios' => array(
				array( 'key' => 'guest_en_ltr', 'locale' => 'en-US', 'direction' => 'ltr', 'audience' => 'guest' ),
				array( 'key' => 'patient_ur_rtl', 'locale' => 'ur-PK', 'direction' => 'rtl', 'audience' => 'patient' ),
				array( 'key' => 'doctor_ar_rtl', 'locale' => 'ar-SA', 'direction' => 'rtl', 'audience' => 'doctor' ),
				array( 'key' => 'measurement_denied', 'measurement' => false ),
				array( 'key' => 'low_bandwidth', 'save_data' => true ),
				array( 'key' => 'reduced_motion', 'reduced_motion' => true ),
				array( 'key' => 'mobile_320', 'viewport' => 320 ),
				array( 'key' => 'zoom_400', 'zoom_percent' => 400 ),
				array( 'key' => 'destination_degraded', 'destinations' => $dest ),
				array( 'key' => 'safe_mode', 'enabled' => (bool) get_option( 'gcu_enabled', 1 ) ),
			),
			'parity' => self::parity_status(),
			'consistency' => self::consistency_graph(),
			'future_schema' => self::verify_schema() === true ? 'verified' : 'unverified',
		);
	}

	public static function daily_governance() {
		self::claim_freshness_sentinel();
		self::parity_status();
		self::consistency_graph();
		foreach ( self::faq_gap_candidates() as $index => $candidate ) {
			self::upsert_record( 'faq_gap', 'candidate_' . substr( hash( 'sha256', $candidate['question'] ), 0, 16 ), 'en-US', 'ZZ', $candidate, 'suggested', false, 0, true );
			if ( $index >= 19 ) {
				break;
			}
		}
		self::cleanup();
	}

	public static function hourly_intelligence() {
		self::anomaly_detector();
		self::early_stop_guard();
	}

	public static function business_policy_changed() {
		self::parity_status();
		self::early_stop_guard();
	}

	public static function cleanup() {
		global $wpdb;
		$t = self::tables();
		$reports = $wpdb->query( $wpdb->prepare( "DELETE FROM {$t['reports']} WHERE status IN ('resolved','rejected') AND updated_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL %d DAY) LIMIT 200", self::REPORT_RETENTION_DAYS ) );
		$records = $wpdb->query( "DELETE FROM {$t['records']} WHERE status IN ('superseded','rejected') AND updated_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 730 DAY) LIMIT 200" );
		return array( 'reports' => $reports, 'records' => $records );
	}

	public static function create_report( array $data ) {
		$rate = GCU_Plugin::instance()->repository()->consume_rate_limit( 'future-report', 5 );
		if ( is_wp_error( $rate ) ) {
			return $rate;
		}
		$reasons = array( 'outdated', 'misleading', 'unclear', 'translation', 'broken_destination', 'faq_gap', 'other' );
		$reason = sanitize_key( isset( $data['reason_code'] ) ? $data['reason_code'] : '' );
		if ( ! in_array( $reason, $reasons, true ) ) {
			return new WP_Error( 'gcu_future_report_reason_invalid', __( 'Choose an approved report reason.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$message = trim( sanitize_textarea_field( isset( $data['message'] ) ? $data['message'] : '' ) );
		if ( function_exists( 'mb_substr' ) ) {
			$message = mb_substr( $message, 0, 500 );
		} else {
			$message = substr( $message, 0, 500 );
		}
		if ( self::report_contains_sensitive_data( $message ) ) {
			return new WP_Error( 'gcu_future_report_sensitive_data', __( 'Do not include personal, contact, identity or clinical details in a copy-quality report.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		$route = sanitize_key( isset( $data['route_key'] ) ? $data['route_key'] : 'global_clinic' );
		if ( ! in_array( $route, array( 'global_clinic', 'how_it_works' ), true ) ) {
			$route = 'global_clinic';
		}
		$block = sanitize_key( isset( $data['block_key'] ) ? $data['block_key'] : '' );
		$locale = GCU_Policy::sanitize_locale( isset( $data['locale'] ) ? $data['locale'] : 'en-US' );
		$id = isset( $data['report_id'] ) && wp_is_uuid( $data['report_id'] ) ? $data['report_id'] : wp_generate_uuid4();
		$actor = is_user_logged_in() ? 'u:' . get_current_user_id() : 'guest';
		$actor_hash = hash_hmac( 'sha256', $actor, wp_salt( 'auth' ) );
		global $wpdb;
		$t = self::tables();
		$now = current_time( 'mysql', true );
		$insert = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$t['reports']} (public_id,report_type,route_key,block_key,locale,reason_code,message,actor_hash,status,created_at,updated_at) VALUES (%s,'copy_quality',%s,%s,%s,%s,%s,%s,'open',%s,%s)", $id, $route, $block ? $block : null, $locale, $reason, $message, $actor_hash, $now, $now ) );
		if ( false === $insert ) {
			return new WP_Error( 'gcu_future_report_write_failed', __( 'The report could not be recorded safely.', 'global-clinic-usp-integration' ), array( 'status' => 500 ) );
		}
		if ( 1 === $insert ) {
			GCU_Plugin::instance()->repository()->audit( 'copy_quality_reported', 'future_report', $id, 'public_feedback', $reason, array(), array( 'route' => $route, 'block' => $block, 'locale' => $locale ) );
			GCU_Plugin::instance()->repository()->publish_event( 'ClinicUSPCopyQualityReported.v1', array( 'report_id' => $id, 'reason_code' => $reason, 'route_key' => $route, 'block_key' => $block ) );
		}
		return array( 'report_id' => $id, 'status' => 'open', 'deduplicated' => 0 === $insert );
	}

	private static function report_contains_sensitive_data( $message ) {
		return (bool) preg_match( '/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}|\+?\d[\d\s\-]{6,}\d|\b(?:CNIC|passport|diagnosis|prescription|patient id)\b/i', $message );
	}

	public static function reports( $status = 'open', $limit = 100 ) {
		global $wpdb;
		$t = self::tables();
		$limit = max( 1, min( 200, (int) $limit ) );
		$status = sanitize_key( $status );
		return $wpdb->get_results( $wpdb->prepare( "SELECT public_id,report_type,route_key,block_key,locale,reason_code,message,status,resolution,row_version,created_at,updated_at FROM {$t['reports']} WHERE status=%s ORDER BY created_at ASC LIMIT %d", $status, $limit ), ARRAY_A );
	}

	public static function resolve_report_record( $id, $expected, $status, $resolution ) {
		if ( ! in_array( $status, array( 'reviewing', 'resolved', 'rejected' ), true ) ) {
			return new WP_Error( 'gcu_future_report_status_invalid', __( 'Invalid report status.', 'global-clinic-usp-integration' ) );
		}
		$resolution = trim( sanitize_textarea_field( $resolution ) );
		if ( in_array( $status, array( 'resolved', 'rejected' ), true ) && strlen( $resolution ) < 8 ) {
			return new WP_Error( 'gcu_future_report_resolution_required', __( 'A meaningful resolution is required.', 'global-clinic-usp-integration' ) );
		}
		global $wpdb;
		$t = self::tables();
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['reports']} WHERE public_id=%s", $id ), ARRAY_A );
		if ( ! $row ) {
			return new WP_Error( 'gcu_future_report_not_found', __( 'Report not found.', 'global-clinic-usp-integration' ) );
		}
		if ( (int) $row['row_version'] !== (int) $expected ) {
			return new WP_Error( 'gcu_future_report_version_conflict', __( 'The report changed. Reload it.', 'global-clinic-usp-integration' ) );
		}
		$done = $wpdb->query( $wpdb->prepare( "UPDATE {$t['reports']} SET status=%s,resolution=%s,row_version=row_version+1,updated_at=%s WHERE id=%d AND row_version=%d", $status, $resolution, current_time( 'mysql', true ), (int) $row['id'], (int) $expected ) );
		if ( 1 !== $done ) {
			return new WP_Error( 'gcu_future_report_update_failed', __( 'The report could not be updated safely.', 'global-clinic-usp-integration' ) );
		}
		GCU_Plugin::instance()->repository()->audit( 'copy_quality_report_updated', 'future_report', $id, 'public_feedback_review', $resolution, $row, array( 'status' => $status ) );
		return array( 'report_id' => $id, 'status' => $status, 'row_version' => (int) $expected + 1 );
	}

	public static function upsert_record( $type, $key, $locale, $region, array $payload, $status = 'draft', $is_public = false, $expected = 0, $system = false ) {
		$ready = self::verify_schema();
		if ( is_wp_error( $ready ) ) {
			return $ready;
		}
		$type = sanitize_key( $type );
		$key = sanitize_key( $key );
		$locale = GCU_Policy::sanitize_locale( $locale );
		$region = self::sanitize_region( $region );
		$status = sanitize_key( $status );
		if ( ! in_array( $status, array( 'draft', 'suggested', 'review', 'active', 'superseded', 'rejected' ), true ) ) {
			$status = 'draft';
		}
		$payload = GCU_Hardening::sanitize_structured_value( $payload );
		$encoded = wp_json_encode( $payload );
		if ( false === $encoded || strlen( $encoded ) > self::RECORD_PAYLOAD_MAX ) {
			return new WP_Error( 'gcu_future_record_payload_invalid', __( 'Future Intelligence record payload is invalid or too large.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );
		}
		global $wpdb;
		$t = self::tables();
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t['records']} WHERE record_type=%s AND record_key=%s AND locale=%s AND region=%s", $type, $key, $locale, $region ), ARRAY_A );
		$now = current_time( 'mysql', true );
		$review_due = gmdate( 'Y-m-d H:i:s', time() + GCU_Policy::COPY_REVIEW_DAYS * DAY_IN_SECONDS );
		if ( $row ) {
			if ( ! $system && (int) $expected !== (int) $row['row_version'] ) {
				return new WP_Error( 'gcu_future_record_version_conflict', __( 'The Future Intelligence record changed. Reload it.', 'global-clinic-usp-integration' ), array( 'status' => 409, 'current_version' => (int) $row['row_version'] ) );
			}
			$done = $wpdb->query( $wpdb->prepare( "UPDATE {$t['records']} SET status=%s,is_public=%d,payload=%s,payload_hash=%s,row_version=row_version+1,review_due_at=%s,updated_at=%s WHERE id=%d AND row_version=%d", $status, $is_public ? 1 : 0, $encoded, hash( 'sha256', $encoded ), $review_due, $now, (int) $row['id'], (int) $row['row_version'] ) );
			if ( 1 !== $done ) {
				return new WP_Error( 'gcu_future_record_update_failed', __( 'The Future Intelligence record could not be updated.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
			}
			GCU_Plugin::instance()->repository()->audit( 'future_record_updated', 'future_record', $type . ':' . $key, 'future_intelligence_governance', '', $row, array( 'status' => $status, 'hash' => hash( 'sha256', $encoded ) ) );
			return array( 'record_type' => $type, 'record_key' => $key, 'locale' => $locale, 'region' => $region, 'status' => $status, 'row_version' => (int) $row['row_version'] + 1 );
		}
		$data = array( 'record_type' => $type, 'record_key' => $key, 'locale' => $locale, 'region' => $region, 'status' => $status, 'is_public' => $is_public ? 1 : 0, 'payload' => $encoded, 'payload_hash' => hash( 'sha256', $encoded ), 'review_due_at' => $review_due, 'created_by' => $system ? 0 : get_current_user_id(), 'created_at' => $now, 'updated_at' => $now );
		if ( false === $wpdb->insert( $t['records'], $data ) ) {
			return new WP_Error( 'gcu_future_record_insert_failed', __( 'The Future Intelligence record could not be created.', 'global-clinic-usp-integration' ), array( 'status' => 500 ) );
		}
		GCU_Plugin::instance()->repository()->audit( 'future_record_created', 'future_record', $type . ':' . $key, 'future_intelligence_governance', '', array(), array( 'status' => $status, 'hash' => hash( 'sha256', $encoded ) ) );
		return array( 'record_type' => $type, 'record_key' => $key, 'locale' => $locale, 'region' => $region, 'status' => $status, 'row_version' => 1 );
	}

	public static function records( $type = '', $public_only = false, $limit = 100 ) {
		global $wpdb;
		$t = self::tables();
		$limit = max( 1, min( 200, (int) $limit ) );
		$where = '1=1';
		$args = array();
		if ( $type ) {
			$where .= ' AND record_type=%s';
			$args[] = sanitize_key( $type );
		}
		if ( $public_only ) {
			$where .= " AND status='active' AND is_public=1 AND (review_due_at IS NULL OR review_due_at>UTC_TIMESTAMP())";
		}
		$sql = "SELECT record_type,record_key,locale,region,status,is_public,payload,row_version,review_due_at,updated_at FROM {$t['records']} WHERE $where ORDER BY updated_at DESC LIMIT $limit";
		$rows = $args ? $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A ) : $wpdb->get_results( $sql, ARRAY_A );
		foreach ( is_array( $rows ) ? $rows : array() as &$row ) {
			$row['payload'] = self::json_array( $row['payload'] );
		}
		unset( $row );
		return is_array( $rows ) ? $rows : array();
	}

	public static function public_change_log( $limit = 20 ) {
		$rows = self::records( 'change_log', true, $limit );
		$out = array();
		foreach ( $rows as $row ) {
			$out[] = array( 'key' => $row['record_key'], 'locale' => $row['locale'], 'payload' => $row['payload'], 'updated_at' => $row['updated_at'] );
		}
		return $out;
	}

	public static function jurisdiction_disclosure( $locale, $region ) {
		global $wpdb;
		$t = self::tables();
		$locale = GCU_Policy::sanitize_locale( $locale );
		$region = self::sanitize_region( $region );
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT payload FROM {$t['records']} WHERE record_type='jurisdiction_copy' AND record_key='global_clinic_disclosure' AND locale=%s AND region IN (%s,'ZZ') AND status='active' AND is_public=1 AND (review_due_at IS NULL OR review_due_at>UTC_TIMESTAMP()) ORDER BY (region=%s) DESC LIMIT 1", $locale, $region, $region ), ARRAY_A );
		return $row ? self::json_array( $row['payload'] ) : array();
	}

	public static function filter_public_route_html( $html, $route ) {
		if ( ! is_string( $html ) || ! in_array( $route, array( 'global_clinic', 'how_it_works' ), true ) || get_option( self::SAFE_MODE_OPTION, 0 ) ) {
			return $html;
		}
		$locale = GCU_Plugin::instance()->frontend()->current_locale();
		$future = self::render_future_sections( $route, $locale );
		$pos = strrpos( $html, '</div>' );
		if ( false === $pos ) {
			return $html . $future;
		}
		return substr( $html, 0, $pos ) . $future . substr( $html, $pos );
	}

	private static function render_future_sections( $route, $locale ) {
		$region = isset( $_GET['country'] ) ? self::sanitize_region( wp_unslash( $_GET['country'] ) ) : 'ZZ';
		$context = GCU_Future_Policy::sanitize_handoff_context( array(
			'country' => 'ZZ' !== $region ? $region : '',
			'language' => isset( $_GET['language'] ) ? wp_unslash( $_GET['language'] ) : '',
			'mode' => isset( $_GET['mode'] ) ? wp_unslash( $_GET['mode'] ) : '',
		) );
		$html = '<section class="gcu-future" aria-labelledby="gcu-future-intent-title"><h2 id="gcu-future-intent-title">Choose your next step</h2><p>Choose explicitly. File 14 does not infer hidden health, identity or behavioral intent.</p><div class="gcu-future__intent-grid">';
		foreach ( GCU_Future_Policy::supported_intents() as $intent => $definition ) {
			$handoff = self::handoff( $intent, $context );
			if ( ! empty( $handoff['available'] ) && ! empty( $handoff['url'] ) ) {
				$html .= '<a class="gcu-future__intent" href="' . esc_url( $handoff['url'] ) . '" data-gcu-future-intent="' . esc_attr( $intent ) . '"><strong>' . esc_html( $definition['label'] ) . '</strong><span>' . esc_html( self::intent_description( $intent ) ) . '</span></a>';
			} else {
				$html .= '<div class="gcu-future__intent gcu-future__intent--degraded"><strong>' . esc_html( $definition['label'] ) . '</strong><span>Canonical destination is temporarily unavailable.</span>';
				foreach ( isset( $handoff['alternatives'] ) ? $handoff['alternatives'] : array() as $alternative ) {
					$html .= '<a href="' . esc_url( $alternative['url'] ) . '">' . esc_html( $alternative['label'] ) . '</a>';
				}
				$html .= '</div>';
			}
		}
		$html .= '</div></section>';
		$disclosure = self::jurisdiction_disclosure( $locale, $region );
		if ( ! empty( $disclosure['body'] ) ) {
			$html .= '<aside class="gcu-future__disclosure" role="note"><strong>Regional information</strong><p>' . esc_html( $disclosure['body'] ) . '</p></aside>';
		}
		$html .= self::render_trust_evidence( $locale );
		$html .= self::render_patient_guide();
		$html .= self::render_doctor_readiness();
		$html .= self::render_change_log();
		$html .= self::render_report_form( $route, $locale );
		return $html;
	}

	private static function intent_description( $intent ) {
		if ( 'patient' === $intent ) {
			return 'Search the canonical doctor directory and continue to the owner for availability or booking.';
		}
		if ( 'doctor' === $intent ) {
			return 'Review the requirements and continue to the File 09 onboarding owner.';
		}
		return 'Read the transparent patient and doctor journeys before taking an action.';
	}

	private static function render_trust_evidence( $locale ) {
		$claims = GCU_Plugin::instance()->repository()->public_claims( array_keys( GCU_Policy::canonical_claims() ) );
		if ( ! $claims ) {
			return '<section class="gcu-future gcu-future--warning"><h2>Trust evidence</h2><p>Trust claims are temporarily unavailable because current evidence requires review.</p></section>';
		}
		$html = '<section class="gcu-future" aria-labelledby="gcu-future-trust-title"><h2 id="gcu-future-trust-title">Trust evidence</h2><p>Open a claim to see its basis, owner and review date.</p>';
		foreach ( $claims as $claim ) {
			$html .= '<details class="gcu-future__evidence"><summary>' . esc_html( GCU_Policy::localized_claim_text( $claim['claim_key'], $locale, $claim['claim_text'] ) ) . '</summary><dl><dt>Basis</dt><dd>' . esc_html( $claim['basis'] ) . '</dd><dt>Owner</dt><dd>' . esc_html( $claim['owner_name'] ) . '</dd><dt>Effective</dt><dd>' . esc_html( $claim['effective_at'] ) . '</dd><dt>Review due</dt><dd>' . esc_html( $claim['review_due_at'] ? $claim['review_due_at'] : 'not set' ) . '</dd></dl></details>';
		}
		return $html . '</section>';
	}

	private static function render_patient_guide() {
		$html = '<section class="gcu-future" aria-labelledby="gcu-future-patient-guide"><h2 id="gcu-future-patient-guide">Choose a doctor safely</h2><ol>';
		foreach ( GCU_Future_Policy::patient_guide() as $step ) {
			$html .= '<li>' . esc_html( $step ) . '</li>';
		}
		return $html . '</ol></section>';
	}

	private static function render_doctor_readiness() {
		$items = array(
			'identity_ready' => 'My account identity information is ready.',
			'professional_evidence_ready' => 'My professional evidence is ready for File 09 review.',
			'profile_ready' => 'My public professional profile information is ready.',
			'clinic_information_ready' => 'My clinic information is ready.',
			'languages_ready' => 'I have listed the languages in which I can consult.',
			'consultation_modes_ready' => 'I have defined online/in-person consultation modes.',
			'privacy_ready' => 'I understand the privacy and public/private information boundaries.',
			'rules_accepted' => 'I am ready to accept the platform rules and verification process.',
		);
		$html = '<section class="gcu-future" aria-labelledby="gcu-future-readiness-title"><h2 id="gcu-future-readiness-title">Global Clinic readiness self-check</h2><p>This is non-binding. Only File 09 / File 00 can determine verification or activation.</p><form class="gcu-future-readiness" data-gcu-readiness-form>';
		foreach ( $items as $key => $label ) {
			$html .= '<label><input type="checkbox" name="' . esc_attr( $key ) . '" value="1"> <span>' . esc_html( $label ) . '</span></label>';
		}
		$html .= '<button type="button" class="gcu-button" data-gcu-readiness-calculate>Check readiness</button><p class="gcu-future-readiness__result" role="status" aria-live="polite">Complete the checklist to estimate preparation only.</p></form></section>';
		return $html;
	}

	private static function render_change_log() {
		$items = self::public_change_log( 5 );
		if ( ! $items ) {
			return '';
		}
		$html = '<section class="gcu-future" aria-labelledby="gcu-future-change-title"><h2 id="gcu-future-change-title">Clinic trust and policy change log</h2><ul>';
		foreach ( $items as $item ) {
			$title = isset( $item['payload']['title'] ) ? $item['payload']['title'] : $item['key'];
			$summary = isset( $item['payload']['summary'] ) ? $item['payload']['summary'] : '';
			$date = isset( $item['payload']['effective_date'] ) ? $item['payload']['effective_date'] : $item['updated_at'];
			$html .= '<li><strong>' . esc_html( $title ) . '</strong> <time>' . esc_html( $date ) . '</time><p>' . esc_html( $summary ) . '</p></li>';
		}
		return $html . '</ul></section>';
	}

	private static function render_report_form( $route, $locale ) {
		$action = esc_url( admin_url( 'admin-post.php' ) );
		$nonce = wp_nonce_field( 'gcu_future_report', '_wpnonce', true, false );
		return '<section class="gcu-future" aria-labelledby="gcu-future-report-title"><h2 id="gcu-future-report-title">Report unclear, outdated or misleading clinic information</h2><p>Do not include personal, contact, identity or clinical details.</p><form method="post" action="' . $action . '" class="gcu-future-report"><input type="hidden" name="action" value="gcu_future_report"><input type="hidden" name="route_key" value="' . esc_attr( $route ) . '"><input type="hidden" name="locale" value="' . esc_attr( $locale ) . '">' . $nonce . '<label>Reason <select name="reason_code" required><option value="outdated">Outdated</option><option value="misleading">Misleading</option><option value="unclear">Unclear</option><option value="translation">Translation</option><option value="broken_destination">Broken destination</option><option value="faq_gap">Missing FAQ</option><option value="other">Other</option></select></label><label>Short explanation <textarea name="message" maxlength="500" rows="3"></textarea></label><button class="gcu-button" type="submit">Send report</button></form></section>';
	}

	public static function enqueue_assets() {
		$route = sanitize_key( (string) get_query_var( 'gcu_route' ) );
		if ( ! in_array( $route, array( 'global_clinic', 'how_it_works' ), true ) ) {
			return;
		}
		wp_enqueue_style( 'gcu-future-intelligence', GCU_URL . 'assets/css/gcu-future-intelligence.css', array( 'gcu-public' ), GCU_VERSION );
		if ( ! GCU_Privacy::low_bandwidth_requested() ) {
			wp_enqueue_script( 'gcu-future-intelligence', GCU_URL . 'assets/js/gcu-future-intelligence.js', array(), GCU_VERSION, true );
		}
	}

	public static function submit_report() {
		check_admin_referer( 'gcu_future_report' );
		$data = array(
			'reason_code' => isset( $_POST['reason_code'] ) ? wp_unslash( $_POST['reason_code'] ) : '',
			'message' => isset( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : '',
			'route_key' => isset( $_POST['route_key'] ) ? wp_unslash( $_POST['route_key'] ) : 'global_clinic',
			'locale' => isset( $_POST['locale'] ) ? wp_unslash( $_POST['locale'] ) : 'en-US',
		);
		$result = self::create_report( $data );
		$referer = wp_get_referer();
		$referer = $referer ? GCU_Hardening::strict_same_origin_url( $referer ) : home_url( '/global-clinic/' );
		if ( ! $referer ) {
			$referer = home_url( '/global-clinic/' );
		}
		$url = add_query_arg( 'gcu_reported', is_wp_error( $result ) ? '0' : '1', $referer );
		wp_safe_redirect( $url );
		exit;
	}

	public static function resolve_report() {
		check_admin_referer( 'gcu_future_resolve_report' );
		if ( ! self::can_manage_content() ) {
			wp_die( esc_html__( 'You are not authorized to review File 14 reports.', 'global-clinic-usp-integration' ) );
		}
		$id = isset( $_POST['report_id'] ) ? sanitize_text_field( wp_unslash( $_POST['report_id'] ) ) : '';
		$expected = isset( $_POST['expected_version'] ) ? absint( $_POST['expected_version'] ) : 0;
		$status = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';
		$resolution = isset( $_POST['resolution'] ) ? sanitize_textarea_field( wp_unslash( $_POST['resolution'] ) ) : '';
		self::resolve_report_record( $id, $expected, $status, $resolution );
		wp_safe_redirect( admin_url( 'options-general.php?page=global-clinic-usp-future' ) );
		exit;
	}

	public static function admin_menu() {
		add_options_page( __( 'Global Clinic Future Intelligence', 'global-clinic-usp-integration' ), __( 'Clinic Future Intelligence', 'global-clinic-usp-integration' ), GCU_Capabilities::SYSTEM_CHECK, 'global-clinic-usp-future', array( __CLASS__, 'admin_page' ) );
	}

	public static function admin_page() {
		if ( ! self::can_system_check() ) {
			wp_die( esc_html__( 'You are not authorized to view this page.', 'global-clinic-usp-integration' ) );
		}
		$catalog = GCU_Future_Policy::feature_catalog();
		$quality = self::quality_score();
		$parity = self::parity_status();
		$reports = self::reports( 'open', 50 );
		$consistency = self::consistency_graph();
		?><div class="wrap"><h1><?php esc_html_e( 'File 14 — Future Conversion & Trust Intelligence', 'global-clinic-usp-integration' ); ?></h1><p><strong><?php echo esc_html( GCU_Future_Policy::PLAN_ID ); ?></strong> · Software <?php echo esc_html( GCU_VERSION ); ?> · Future schema <?php echo esc_html( self::SCHEMA_VERSION ); ?></p><p><?php esc_html_e( 'This layer governs ethical conversion, trust evidence, copy safety, handoff quality and privacy-safe intelligence. It does not own doctor, clinic, appointment, payment, verification or shell truth.', 'global-clinic-usp-integration' ); ?></p><h2>Current guardrails</h2><table class="widefat striped"><tbody><tr><th>Future schema</th><td><?php echo esc_html( true === self::verify_schema() ? 'verified' : 'unverified' ); ?></td></tr><tr><th>Policy parity</th><td><?php echo esc_html( $parity['ok'] ? 'pass' : 'blocked: ' . implode( ', ', $parity['issues'] ) ); ?></td></tr><tr><th>Conversion quality</th><td><?php echo esc_html( $quality['score'] . ( $quality['provisional'] ? ' (provisional)' : '' ) ); ?></td></tr><tr><th>Message consistency</th><td><?php echo esc_html( $consistency['ok'] ? 'pass' : 'review required' ); ?></td></tr><tr><th>Open copy reports</th><td><?php echo esc_html( count( $reports ) ); ?></td></tr></tbody></table><h2>Founder-approved 24 enhancements</h2><table class="widefat striped"><thead><tr><th>ID</th><th>Feature</th><th>Priority</th><th>Surface</th><th>Boundary</th></tr></thead><tbody><?php foreach ( $catalog as $id => $feature ) : ?><tr><td><code><?php echo esc_html( $id ); ?></code></td><td><?php echo esc_html( $feature['title'] ); ?></td><td><?php echo esc_html( $feature['priority'] ); ?></td><td><?php echo esc_html( $feature['surface'] ); ?></td><td><?php echo esc_html( $feature['boundary'] ); ?></td></tr><?php endforeach; ?></tbody></table><h2>Open misleading-copy reports</h2><?php if ( ! $reports ) : ?><p>None.</p><?php else : ?><table class="widefat striped"><thead><tr><th>Reason</th><th>Route / block</th><th>Message</th><th>Created</th><th>Resolution</th></tr></thead><tbody><?php foreach ( $reports as $report ) : ?><tr><td><?php echo esc_html( $report['reason_code'] ); ?></td><td><?php echo esc_html( $report['route_key'] . ( $report['block_key'] ? ' / ' . $report['block_key'] : '' ) ); ?></td><td><?php echo esc_html( $report['message'] ); ?></td><td><?php echo esc_html( $report['created_at'] ); ?></td><td><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="gcu_future_resolve_report"><input type="hidden" name="report_id" value="<?php echo esc_attr( $report['public_id'] ); ?>"><input type="hidden" name="expected_version" value="<?php echo esc_attr( $report['row_version'] ); ?>"><?php wp_nonce_field( 'gcu_future_resolve_report' ); ?><select name="status"><option value="reviewing">Reviewing</option><option value="resolved">Resolved</option><option value="rejected">Rejected</option></select><input type="text" name="resolution" maxlength="500" placeholder="Resolution / correction reference"><button class="button">Update</button></form></td></tr><?php endforeach; ?></tbody></table><?php endif; ?><h2>Governed API</h2><p><code>/wp-json/gcu/v1/future/*</code> — catalog, handoff, trust, reports, quality, friction, scenarios, preflight, consistency, AI-safe draft assistance and governed records.</p></div><?php
	}

	public static function can_manage_content() {
		return GCU_Capabilities::can( GCU_Capabilities::MANAGE_CONTENT, null, 'future_intelligence_content' );
	}
	public static function can_manage_experiments() {
		return GCU_Capabilities::can( GCU_Capabilities::MANAGE_EXPERIMENTS, null, 'future_intelligence_experiments' );
	}
	public static function can_view_analytics() {
		return GCU_Capabilities::can( GCU_Capabilities::VIEW_ANALYTICS, null, 'future_intelligence_analytics' );
	}
	public static function can_system_check() {
		return GCU_Capabilities::can( GCU_Capabilities::SYSTEM_CHECK, null, 'future_intelligence_system' );
	}
	public static function can_approve_claims() {
		return GCU_Capabilities::can( GCU_Capabilities::APPROVE_CLAIMS, null, 'future_intelligence_claims' );
	}

	private static function sanitize_region( $region ) {
		$region = strtoupper( sanitize_text_field( (string) $region ) );
		return preg_match( '/^[A-Z]{2}$/', $region ) ? $region : 'ZZ';
	}

	private static function json_array( $value ) {
		if ( is_array( $value ) ) {
			return $value;
		}
		$decoded = json_decode( (string) $value, true );
		return is_array( $decoded ) ? $decoded : array();
	}

	private static function text_similarity( $a, $b ) {
		$a = array_values( array_unique( preg_split( '/[^a-z0-9]+/i', strtolower( (string) $a ), -1, PREG_SPLIT_NO_EMPTY ) ) );
		$b = array_values( array_unique( preg_split( '/[^a-z0-9]+/i', strtolower( (string) $b ), -1, PREG_SPLIT_NO_EMPTY ) ) );
		if ( ! $a || ! $b ) {
			return 0.0;
		}
		$intersection = count( array_intersect( $a, $b ) );
		$union = count( array_unique( array_merge( $a, $b ) ) );
		return $union ? $intersection / $union : 0.0;
	}

	private static function public_response( array $data, $status = 200 ) {
		$response = new WP_REST_Response( $data, $status );
		$response->header( 'Cache-Control', 'public, max-age=60, stale-while-revalidate=60' );
		$response->header( 'Vary', 'Accept-Language' );
		return $response;
	}

	private static function no_store_response( $data, $status = 200 ) {
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$response = $data instanceof WP_REST_Response ? $data : new WP_REST_Response( $data, $status );
		$response->header( 'Cache-Control', 'no-store, private' );
		$response->header( 'Pragma', 'no-cache' );
		return $response;
	}
}
