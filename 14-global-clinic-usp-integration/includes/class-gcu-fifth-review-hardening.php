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
			$status = sanitize_key( isset( $data['status'] ) ? $data['status'] : 'draft' );
			$is_public = ! empty( $data['is_public'] );
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
			$base = isset( $data['base_text'] ) ? (string) $data['base_text'] : '';
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
			$text = is_array( $candidate ) && isset( $candidate['text'] ) ? (string) $candidate['text'] : '';
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
		$event_id = isset( $data['event_id'] ) ? sanitize_text_field( (string) $data['event_id'] ) : '';
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

		$stage = sanitize_key( isset( $data['stage'] ) ? $data['stage'] : '' );
		$destination = sanitize_key( isset( $data['destination'] ) ? $data['destination'] : '' );
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
		$open_reports = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$future_tables['reports']} WHERE status IN ('open','reviewing') AND created_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 7 DAY)"
		);
		$breach = empty( $parity['ok'] )
			|| $destination_failure
			|| $open_reports >= 5
			|| ( isset( $anomaly['severity'] ) && 'high' === $anomaly['severity'] );
		if ( ! $breach ) {
			return 0;
		}

		$tables = GCU_Install::tables();
		$rows = $wpdb->get_results( "SELECT * FROM {$tables['experiments']} WHERE status='running' LIMIT 50", ARRAY_A );
		$count = 0;
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			$lock = GCU_Hardening::acquire_db_lock( 'experiment-early-stop:' . $row['public_id'], 3 );
			if ( ! $lock ) {
				continue;
			}
			try {
				if ( ! GCU_Plugin::instance()->repository()->begin_owned_transaction() ) {
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
					GCU_Plugin::instance()->repository()->rollback_owned_transaction();
					continue;
				}
				$audit = GCU_Plugin::instance()->repository()->audit(
					'experiment_early_stopped',
					'experiment',
					$row['public_id'],
					'experiment_safety',
					'Automatic trust/safety guardrail breach',
					$row,
					array( 'status' => 'stopped', 'row_version' => (int) $row['row_version'] + 1 )
				);
				if ( false === $audit ) {
					GCU_Plugin::instance()->repository()->rollback_owned_transaction();
					continue;
				}
				if ( ! GCU_Plugin::instance()->repository()->commit_owned_transaction() ) {
					GCU_Plugin::instance()->repository()->rollback_owned_transaction();
					continue;
				}
				$count++;
			} finally {
				GCU_Hardening::release_db_lock( $lock );
			}
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
