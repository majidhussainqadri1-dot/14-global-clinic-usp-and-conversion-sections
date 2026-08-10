<?php

defined( 'ABSPATH' ) || exit;

/**
 * Corrective hardening found during the 2026-08-10 eighty-pass File 14 review.
 *
 * This additive layer closes fail-open gaps around claim freshness, experiment
 * preflight, multilingual copy safety and privacy-minimized intelligence without
 * taking canonical ownership from Files 00/07/08/09/20/24/25.
 */
final class GCU_Review80_Hardening {
	const MIN_COHORT = 10;

	private static $public_guard_blocked = false;
	private static $public_guard_html = '';

	public static function bootstrap() {
		add_filter( 'rest_request_before_callbacks', array( __CLASS__, 'guard_rest_request' ), 5, 3 );
		add_filter( 'rest_post_dispatch', array( __CLASS__, 'harden_rest_response' ), 20, 3 );
		add_filter( 'gcu_future_question_aggregates', array( __CLASS__, 'filter_question_aggregates' ), 5 );
		add_filter( 'gcu_public_route_html', array( __CLASS__, 'enforce_public_claim_parity' ), 5, 2 );
		add_filter( 'gcu_public_route_html', array( __CLASS__, 'finalize_public_guard' ), 99, 2 );
	}

	/** Require every mandatory experiment guardrail to carry a meaningful value. */
	public static function guardrails_valid( array $guardrails ) {
		$required = array( 'claim_integrity', 'privacy', 'accessibility', 'error_rate', 'complaints' );
		foreach ( $required as $key ) {
			if ( ! array_key_exists( $key, $guardrails ) ) {
				return false;
			}
			$value = $guardrails[ $key ];
			if ( false === $value || null === $value || '' === $value || ( is_array( $value ) && empty( $value ) ) ) {
				return false;
			}
		}
		return true;
	}

	/** Narrow multilingual negative-pattern checks for the three approved File 14 locales. */
	public static function multilingual_dark_pattern_scan( $text ) {
		$text = wp_strip_all_tags( (string) $text );
		$flags = array();
		$patterns = array(
			'fake_scarcity_ur' => '/(?:صرف\s*آج|آخری\s*موقع|فوراً\s*(?:کریں|درخواست)|جلدی\s*کریں)/u',
			'guarantee_ur' => '/(?:شفا\s*کی\s*ضمانت|یقینی\s*شفا|آمدن\s*کی\s*ضمانت|فوری\s*منظوری)/u',
			'positive_commission_ur' => '/(?:[1-9][0-9]*\s*(?:%|فیصد)).{0,30}(?:کمیشن|عمول)/u',
			'fake_scarcity_ar' => '/(?:اليوم\s*فقط|الفرصة\s*الأخيرة|سارع\s*الآن|لفترة\s*محدودة)/u',
			'guarantee_ar' => '/(?:ضمان\s*الشفاء|شفاء\s*مضمون|دخل\s*مضمون|موافقة\s*فورية)/u',
			'positive_commission_ar' => '/(?:[1-9][0-9]*\s*%).{0,30}(?:عمولة)/u',
		);
		foreach ( $patterns as $key => $pattern ) {
			if ( preg_match( $pattern, $text ) ) {
				$flags[] = $key;
			}
		}
		if ( preg_match( '/(?:عطیہ|تعاون|ادائیگی)[^۔.!?]{0,60}(?:درجہ\s*بندی|نمائش|تصدیق|ترجیح)/u', $text ) && ! preg_match( '/(?:عطیہ|تعاون|ادائیگی)[^۔.!?]{0,60}(?:نہیں|نہ)[^۔.!?]{0,30}(?:درجہ\s*بندی|نمائش|تصدیق|ترجیح)|(?:عطیہ|تعاون|ادائیگی)[^۔.!?]{0,60}(?:درجہ\s*بندی|نمائش|تصدیق|ترجیح)[^۔.!?]{0,30}(?:نہیں|نہ)/u', $text ) ) {
			$flags[] = 'paid_visibility_ur';
		}
		if ( preg_match( '/(?:تبرع|دعم|دفع)[^.!?؟]{0,60}(?:ترتيب|ظهور|تحقق|أولوية)/u', $text ) && ! preg_match( '/(?:تبرع|دعم|دفع)[^.!?؟]{0,60}(?:لا|ليس|لن)[^.!?؟]{0,30}(?:ترتيب|ظهور|تحقق|أولوية)|(?:تبرع|دعم|دفع)[^.!?؟]{0,60}(?:ترتيب|ظهور|تحقق|أولوية)[^.!?؟]{0,30}(?:لا|ليس|لن)/u', $text ) ) {
			$flags[] = 'paid_visibility_ar';
		}
		return array( 'safe' => empty( $flags ), 'flags' => array_values( array_unique( $flags ) ) );
	}

	/** Reject aggregates containing direct personal/contact/identity or explicit patient-record markers. */
	public static function question_contains_sensitive_data( $question ) {
		$question = (string) $question;
		return (bool) preg_match( '/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}|\+?\d[\d\s\-]{6,}\d|\b(?:CNIC|NICOP|passport|patient\s*id|medical\s*record|prescription\s*(?:no|number)|case\s*(?:no|number))\b|(?:شناختی\s*کارڈ|پاسپورٹ|مریض|تشخیص|نسخہ|میڈیکل\s*ریکارڈ|فون|موبائل|ای\s*میل)|(?:هوية|جواز\s*السفر|مريض|تشخيص|وصفة|سجل\s*طبي|هاتف|جوال|بريد\s*إلكتروني)/iu', $question );
	}

	public static function filter_question_aggregates( $signals ) {
		if ( ! is_array( $signals ) ) {
			return array();
		}
		$out = array();
		foreach ( array_slice( $signals, 0, 200 ) as $signal ) {
			if ( ! is_array( $signal ) ) {
				continue;
			}
			$question = isset( $signal['question'] ) ? sanitize_text_field( $signal['question'] ) : '';
			$count = isset( $signal['count'] ) ? absint( $signal['count'] ) : 0;
			if ( '' === $question || $count < self::MIN_COHORT || self::question_contains_sensitive_data( $question ) ) {
				continue;
			}
			$signal['question'] = GCU_Hardening::bounded_text( $question, 300 );
			$signal['count'] = $count;
			$out[] = $signal;
		}
		return $out;
	}

	/** Suppress every individual funnel stage whose cohort is below the privacy threshold. */
	public static function sanitize_friction_payload( array $data ) {
		if ( ! empty( $data['suppressed'] ) || empty( $data['stages'] ) || ! is_array( $data['stages'] ) ) {
			return $data;
		}
		$order = array( 'impression', 'cta_selected', 'destination_loaded', 'application_started', 'booking_started' );
		$suppressed = array();
		$original = $data['stages'];
		foreach ( $data['stages'] as $stage => $count ) {
			if ( (int) $count < self::MIN_COHORT ) {
				$data['stages'][ $stage ] = null;
				$suppressed[] = $stage;
			}
		}
		if ( isset( $data['dropoffs'] ) && is_array( $data['dropoffs'] ) ) {
			$previous = null;
			foreach ( $order as $stage ) {
				$current = isset( $original[ $stage ] ) ? (int) $original[ $stage ] : 0;
				if ( null !== $previous && ( $previous < self::MIN_COHORT || $current < self::MIN_COHORT ) ) {
					unset( $data['dropoffs'][ $stage ] );
				}
				$previous = $current;
			}
		}
		$data['suppressed_stages'] = array_values( array_unique( $suppressed ) );
		$data['threshold'] = self::MIN_COHORT;
		return $data;
	}

	public static function normalize_scenario_payload( array $data, $future_safe_mode, $module_enabled ) {
		if ( empty( $data['scenarios'] ) || ! is_array( $data['scenarios'] ) ) {
			return $data;
		}
		foreach ( $data['scenarios'] as &$scenario ) {
			if ( is_array( $scenario ) && isset( $scenario['key'] ) && 'safe_mode' === $scenario['key'] ) {
				$scenario['enabled'] = (bool) $future_safe_mode;
				$scenario['module_enabled'] = (bool) $module_enabled;
			}
		}
		unset( $scenario );
		return $data;
	}

	private static function request_experiment_payload( WP_REST_Request $request ) {
		$route = $request->get_route();
		if ( '/gcu/v1/future/preflight/experiment' === $route || '/gcu/v1/experiments' === $route ) {
			$data = $request->get_json_params();
			return is_array( $data ) ? $data : array();
		}
		if ( preg_match( '#^/gcu/v1/workflow/experiment/([a-f0-9\-]{36})$#', $route, $match ) ) {
			$target = sanitize_key( (string) $request->get_param( 'target' ) );
			if ( ! in_array( $target, array( 'approved', 'running' ), true ) ) {
				return array();
			}
			$row = GCU_Plugin::instance()->repository()->record_by_public_id( 'experiments', $match[1] );
			if ( ! is_array( $row ) ) {
				return array();
			}
			$row['variants'] = self::json_array( isset( $row['variants'] ) ? $row['variants'] : array() );
			$row['guardrails'] = self::json_array( isset( $row['guardrails'] ) ? $row['guardrails'] : array() );
			return $row;
		}
		return array();
	}

	private static function guard_experiment_payload( array $data ) {
		if ( ! $data ) {
			return true;
		}
		$guardrails = isset( $data['guardrails'] ) && is_array( $data['guardrails'] ) ? $data['guardrails'] : array();
		if ( ! self::guardrails_valid( $guardrails ) ) {
			return new WP_Error( 'gcu_review80_experiment_guardrails_incomplete', __( 'Every mandatory experiment safety guardrail must be present and enabled before approval or execution.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
		}
		$variants = isset( $data['variants'] ) && is_array( $data['variants'] ) ? $data['variants'] : array();
		foreach ( array_slice( $variants, 0, 20 ) as $variant ) {
			$text = is_array( $variant ) ? wp_json_encode( $variant ) : (string) $variant;
			$scan = self::multilingual_dark_pattern_scan( $text );
			if ( ! $scan['safe'] ) {
				return new WP_Error( 'gcu_review80_multilingual_experiment_copy_blocked', __( 'An experiment variant contains prohibited multilingual dark-pattern or guarantee language.', 'global-clinic-usp-integration' ), array( 'status' => 409, 'flags' => $scan['flags'] ) );
			}
		}
		return true;
	}

	private static function guard_copy_workflow( WP_REST_Request $request ) {
		$route = $request->get_route();
		if ( ! preg_match( '#^/gcu/v1/workflow/copy/([a-f0-9\-]{36})$#', $route, $match ) ) {
			return true;
		}
		$target = sanitize_key( (string) $request->get_param( 'target' ) );
		if ( ! in_array( $target, array( 'founder_approved', 'active' ), true ) ) {
			return true;
		}
		$row = GCU_Plugin::instance()->repository()->record_by_public_id( 'blocks', $match[1] );
		if ( ! is_array( $row ) ) {
			return true;
		}
		$text = implode( ' ', array( isset( $row['title'] ) ? $row['title'] : '', isset( $row['body'] ) ? wp_strip_all_tags( $row['body'] ) : '', isset( $row['cta_label'] ) ? $row['cta_label'] : '' ) );
		$scan = self::multilingual_dark_pattern_scan( $text );
		return $scan['safe'] ? true : new WP_Error( 'gcu_review80_multilingual_copy_blocked', __( 'The copy contains prohibited multilingual dark-pattern, guarantee, paid-visibility or positive-commission language.', 'global-clinic-usp-integration' ), array( 'status' => 409, 'flags' => $scan['flags'] ) );
	}

	public static function guard_rest_request( $response, $handler, WP_REST_Request $request ) {
		if ( null !== $response ) {
			return $response;
		}
		$route = $request->get_route();
		if ( 0 === strpos( $route, '/gcu/v1/' ) && '/gcu/v1/health' !== $route ) {
			$base_ready = GCU_Install::ready_for_runtime();
			if ( is_wp_error( $base_ready ) ) { return $base_ready; }
		}
		if ( 0 === strpos( $route, '/gcu/v1/future/' ) ) {
			$future_ready = GCU_Future_Intelligence::runtime_ready();
			if ( is_wp_error( $future_ready ) ) {
				return $future_ready;
			}
		}
		if ( '/gcu/v1/blocks' === $route || 0 === strpos( $route, '/gcu/v1/future/trust/' ) ) {
			GCU_Future_Intelligence::claim_freshness_sentinel();
		}
		if ( '/gcu/v1/blocks' === $route ) {
			$parity = GCU_Future_Intelligence::parity_status();
			if ( empty( $parity['ok'] ) ) {
				return new WP_Error( 'gcu_review80_public_copy_guarded', __( 'File 14 public conversion copy is temporarily withheld until its trust and policy evidence is current.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
			}
		}
		$experiment = self::guard_experiment_payload( self::request_experiment_payload( $request ) );
		if ( is_wp_error( $experiment ) ) {
			return $experiment;
		}
		$copy = self::guard_copy_workflow( $request );
		return is_wp_error( $copy ) ? $copy : $response;
	}

	public static function enforce_public_claim_parity( $html, $route ) {
		if ( ! in_array( $route, array( 'global_clinic', 'how_it_works' ), true ) ) {
			return $html;
		}
		GCU_Future_Intelligence::claim_freshness_sentinel();
		$parity = GCU_Future_Intelligence::parity_status();
		if ( ! empty( $parity['ok'] ) ) {
			return $html;
		}
		self::$public_guard_blocked = true;
		self::$public_guard_html = '<div class="gcu-page" data-gcu-review80-safe-state="true"><section class="gcu-state gcu-state--error" role="status" aria-live="polite"><h1>' . esc_html__( 'Global Clinic information is under trust review', 'global-clinic-usp-integration' ) . '</h1><p>' . esc_html__( 'Public conversion claims are temporarily withheld until current evidence and policy parity are verified. Canonical clinical, doctor and appointment owners remain unchanged.', 'global-clinic-usp-integration' ) . '</p></section></div>';
		return self::$public_guard_html;
	}

	public static function finalize_public_guard( $html, $route ) {
		if ( self::$public_guard_blocked && in_array( $route, array( 'global_clinic', 'how_it_works' ), true ) ) {
			return self::$public_guard_html;
		}
		return $html;
	}

	public static function harden_rest_response( $response, WP_REST_Server $server, WP_REST_Request $request ) {
		if ( ! $response instanceof WP_REST_Response ) {
			return $response;
		}
		$route = $request->get_route();
		if ( 0 !== strpos( $route, '/gcu/v1/' ) ) {
			return $response;
		}
		$data = $response->get_data();
		if ( ! is_array( $data ) ) {
			return $response;
		}
		if ( '/gcu/v1/future/friction' === $route ) {
			$response->set_data( self::sanitize_friction_payload( $data ) );
		}
		if ( '/gcu/v1/future/scenarios' === $route ) {
			$response->set_data( self::normalize_scenario_payload( $data, (bool) get_option( GCU_Future_Intelligence::SAFE_MODE_OPTION, 0 ), (bool) get_option( 'gcu_enabled', 1 ) ) );
		}
		if ( '/gcu/v1/future/quality' === $route ) {
			if ( ! empty( $data['small_cohort_suppressed'] ) ) {
				$data['sample_count'] = null;
			}
			$unverified = array();
			if ( ! has_filter( 'gcu_future_accessibility_score' ) ) {
				$unverified[] = 'accessibility';
			}
			if ( empty( $data['performance_verified'] ) ) {
				$unverified[] = 'performance';
			}
			$unverified[] = 'privacy_effectiveness';
			$data['unverified_metrics'] = array_values( array_unique( $unverified ) );
			$data['provisional'] = ! empty( $data['provisional'] ) || ! empty( $unverified );
			$response->set_data( $data );
		}
		$headers = $response->get_headers();
		if ( empty( $headers['Cache-Control'] ) ) {
			$response->header( 'Cache-Control', 'no-store, private' );
		}
		return $response;
	}

	private static function json_array( $value ) {
		if ( is_array( $value ) ) {
			return $value;
		}
		$decoded = json_decode( (string) $value, true );
		return is_array( $decoded ) ? $decoded : array();
	}
}
