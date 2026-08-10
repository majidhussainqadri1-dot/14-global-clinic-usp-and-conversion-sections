<?php

defined( 'ABSPATH' ) || exit;

/**
 * Pure policy helpers for the Founder-approved File 14 Future Conversion & Trust Intelligence amendment.
 * No companion module data is owned or mutated here.
 */
final class GCU_Future_Policy {
	const PLAN_ID = 'SSH-F14-FUTURE-CTI-2026-v2.0';
	const MIN_COHORT = 10;

	public static function feature_catalog() {
		return array(
			'F14-FUT-01' => self::feature( 'Ethical Intent Router', 'P0', 'public', 'Explicit user intent only; no hidden profiling.' ),
			'F14-FUT-02' => self::feature( 'Smart Destination Handoff', 'P0', 'public', 'Allowlisted context only; canonical destination owner remains Files 07/08/09.' ),
			'F14-FUT-03' => self::feature( 'Trust Evidence Drawer', 'P0', 'public', 'Public current claims only; evidence metadata is read-only.' ),
			'F14-FUT-04' => self::feature( 'Claim Freshness Sentinel', 'P0', 'system', 'Stale public claims fail closed and require review.' ),
			'F14-FUT-05' => self::feature( 'Zero-Commission Parity Sentinel', 'P0', 'system', '0% commission, one free tier and optional support must remain in parity.' ),
			'F14-FUT-06' => self::feature( 'Jurisdiction-Aware Truthful Copy Engine', 'P0', 'public', 'Only Founder-approved regional disclosure variants; no eligibility decision.' ),
			'F14-FUT-07' => self::feature( 'Semantic Copy-Diff and Meaning Risk Detector', 'P0', 'governance', 'Protected meanings cannot silently drift.' ),
			'F14-FUT-08' => self::feature( 'Dark-Pattern Linter', 'P0', 'governance', 'No fake scarcity, coercive consent, hidden fees or guarantee language.' ),
			'F14-FUT-09' => self::feature( 'Destination Failover Matrix', 'P0', 'public', 'Dead CTAs are replaced by truthful owner-safe alternatives.' ),
			'F14-FUT-10' => self::feature( 'Scenario Preview Laboratory', 'P0', 'admin', 'Preview role, locale, dependency and accessibility states without changing owner truth.' ),
			'F14-FUT-11' => self::feature( 'Conversion Quality Score', 'P0', 'analytics', 'Quality, trust and successful handoff outrank raw clicks.' ),
			'F14-FUT-12' => self::feature( 'Misleading-Copy Report and Correction Loop', 'P0', 'public', 'Structured report, review and auditable resolution.' ),
			'F14-FUT-13' => self::feature( 'Privacy-Safe Friction Analytics', 'P1', 'analytics', 'Aggregate drop-off only; no health or identity profiling.' ),
			'F14-FUT-14' => self::feature( 'Conversion Anomaly Detector', 'P1', 'system', 'Detect material funnel and destination anomalies with minimum sample gates.' ),
			'F14-FUT-15' => self::feature( 'Small-Cohort Privacy Guard', 'P1', 'analytics', 'Suppress cohorts below the approved minimum.' ),
			'F14-FUT-16' => self::feature( 'Experiment Safety Preflight Simulator', 'P0', 'governance', 'Experiments must pass claim, privacy, accessibility and dark-pattern guardrails.' ),
			'F14-FUT-17' => self::feature( 'Experiment Early-Stop and Rollback Guard', 'P1', 'system', 'Running experiments stop on governed safety or trust breaches.' ),
			'F14-FUT-18' => self::feature( 'FAQ Gap Intelligence', 'P1', 'governance', 'Only aggregate approved question signals may suggest FAQ drafts.' ),
			'F14-FUT-19' => self::feature( 'Message Consistency Graph', 'P1', 'governance', 'Protected policy meanings are compared across blocks, locales and placements.' ),
			'F14-FUT-20' => self::feature( 'Translation Provenance and Terminology Lock', 'P1', 'governance', 'Protected terminology is versioned across en-US, ur-PK and ar-SA.' ),
			'F14-FUT-21' => self::feature( 'AI Ethical Copy Assistant', 'P1', 'governance', 'Draft assistance is bounded by approved claims; never auto-publishes or invents facts.' ),
			'F14-FUT-22' => self::feature( 'Public Clinic Trust and Change Log', 'P1', 'public', 'Material policy/copy governance changes are transparently published.' ),
			'F14-FUT-23' => self::feature( 'Patient Choose Safely Decision Guide', 'P1', 'public', 'Educational decision support only; no diagnosis or doctor ranking.' ),
			'F14-FUT-24' => self::feature( 'Doctor Global Clinic Readiness Self-Check', 'P1', 'public', 'Non-binding readiness checklist; File 09 remains verification owner.' ),
		);
	}

	private static function feature( $title, $priority, $surface, $boundary ) {
		return array( 'title' => $title, 'priority' => $priority, 'surface' => $surface, 'boundary' => $boundary, 'approved' => true );
	}

	public static function supported_intents() {
		return array(
			'patient' => array( 'destination' => 'doctor_directory', 'label' => 'Find a Global Doctor' ),
			'doctor'  => array( 'destination' => 'doctor_onboarding', 'label' => 'Start Your Global Clinic' ),
			'learn'   => array( 'destination' => 'how_it_works', 'label' => 'Understand How It Works' ),
		);
	}

	public static function sanitize_handoff_context( array $input ) {
		$country = isset( $input['country'] ) ? strtoupper( sanitize_text_field( (string) $input['country'] ) ) : '';
		if ( ! preg_match( '/^[A-Z]{2}$/', $country ) ) {
			$country = '';
		}
		$language = isset( $input['language'] ) ? strtolower( sanitize_text_field( (string) $input['language'] ) ) : '';
		if ( ! in_array( $language, array( 'en', 'ur', 'ar' ), true ) ) {
			$language = '';
		}
		$mode = isset( $input['mode'] ) ? sanitize_key( (string) $input['mode'] ) : '';
		if ( ! in_array( $mode, array( 'online', 'in_person', 'hybrid' ), true ) ) {
			$mode = '';
		}
		return array( 'country' => $country, 'language' => $language, 'mode' => $mode );
	}

	public static function protected_concepts() {
		return array(
			'commission'   => array( '0% commission', 'zero commission', 'commission', 'عمول', 'کمیشن' ),
			'free_tier'    => array( 'free tier', 'free', 'مفت', 'مجاني' ),
			'support'      => array( 'voluntary support', 'donation', 'رضاکارانہ', 'عطیہ', 'تبرع', 'الدعم التطوعي' ),
			'verification' => array( 'verified', 'verification', 'approval', 'تصدیق', 'منظوری', 'موث', 'موافقة' ),
			'emergency'    => array( 'emergency', 'urgent', 'ہنگامی', 'طوارئ' ),
			'outcome'      => array( 'cure', 'guarantee', 'income', 'result', 'شفا', 'ضمانت', 'آمدن', 'شفاء', 'ضمان', 'دخل' ),
			'payment'      => array( 'fee', 'payment', 'فیس', 'ادائیگی', 'رسوم', 'دفع' ),
		);
	}

	public static function semantic_risk_scan( $before, $after ) {
		$before = self::normalize_text( $before );
		$after  = self::normalize_text( $after );
		$flags  = array();
		foreach ( self::protected_concepts() as $concept => $terms ) {
			$old = self::contains_any( $before, $terms );
			$new = self::contains_any( $after, $terms );
			if ( $old !== $new ) {
				$flags[] = 'protected_meaning_changed:' . $concept;
			}
		}
		foreach ( self::prohibited_patterns() as $key => $pattern ) {
			if ( preg_match( $pattern, $after ) ) {
				$flags[] = 'prohibited_meaning:' . $key;
			}
		}
		return array( 'safe' => empty( $flags ), 'risk' => empty( $flags ) ? 'low' : 'high', 'flags' => array_values( array_unique( $flags ) ) );
	}

	public static function dark_pattern_scan( $text ) {
		$text  = self::normalize_text( $text );
		$flags = array();
		$patterns = array(
			'fake_scarcity'     => '/\b(last chance|only today|limited slots?|hurry|act now|ending soon)\b/i',
			'guaranteed_result' => '/\b(guaranteed cure|guaranteed income|100% guaranteed|certain cure|instant approval)\b/i',
			'paid_visibility'   => '/\b(?:pay|donat(?:e|ion)|support)\b(?![^.!?]{0,60}\b(?:does not|do not|cannot|never|not)\b)[^.!?]{0,60}\b(?:rank|ranking|visibility|verification|priority)\b/i',
			'hidden_fee'        => '/\b(no fees?|free)\b.{0,40}\b(hidden|later|extra)\s+(fee|charge)\b/i',
			'coercive_consent'  => '/\b(accept all|required consent|must consent|continue only if you agree to tracking)\b/i',
			'shame_copy'        => '/\b(don.?t miss out|serious doctors always|smart patients always|you will regret)\b/i',
		);
		foreach ( $patterns as $key => $pattern ) {
			if ( preg_match( $pattern, $text ) ) {
				$flags[] = $key;
			}
		}
		return array( 'safe' => empty( $flags ), 'flags' => $flags );
	}

	public static function copy_preflight( array $record, array $previous = array() ) {
		$current = implode( ' ', array_filter( array(
			isset( $record['title'] ) ? $record['title'] : '',
			isset( $record['body'] ) ? wp_strip_all_tags( $record['body'] ) : '',
			isset( $record['cta_label'] ) ? $record['cta_label'] : '',
		) ) );
		$prior = implode( ' ', array_filter( array(
			isset( $previous['title'] ) ? $previous['title'] : '',
			isset( $previous['body'] ) ? wp_strip_all_tags( $previous['body'] ) : '',
			isset( $previous['cta_label'] ) ? $previous['cta_label'] : '',
		) ) );
		$dark = self::dark_pattern_scan( $current );
		$semantic = $prior ? self::semantic_risk_scan( $prior, $current ) : array( 'safe' => true, 'risk' => 'low', 'flags' => array() );
		$flags = array_merge( $dark['flags'], $semantic['flags'] );
		return array( 'safe' => empty( $flags ), 'flags' => array_values( array_unique( $flags ) ), 'dark_pattern' => $dark, 'semantic' => $semantic );
	}

	public static function experiment_preflight( array $variants, array $guardrails, $sample_policy, $privacy_policy ) {
		$flags = array();
		if ( count( $variants ) < 2 ) {
			$flags[] = 'at_least_two_variants_required';
		}
		foreach ( $variants as $index => $variant ) {
			$text = is_array( $variant ) ? wp_json_encode( $variant ) : (string) $variant;
			$scan = self::dark_pattern_scan( $text );
			foreach ( $scan['flags'] as $flag ) {
				$flags[] = 'variant_' . absint( $index ) . ':' . $flag;
			}
		}
		$required = array( 'claim_integrity', 'privacy', 'accessibility', 'error_rate', 'complaints' );
		foreach ( $required as $key ) {
			if ( ! array_key_exists( $key, $guardrails ) ) {
				$flags[] = 'missing_guardrail:' . $key;
			}
		}
		$sensitive = strtolower( (string) $sample_policy . ' ' . (string) $privacy_policy );
		foreach ( array( 'minor profiling', 'health profiling', 'patient profiling', 'diagnosis targeting', 'identity evidence targeting' ) as $forbidden ) {
			if ( false !== strpos( $sensitive, $forbidden ) ) {
				$flags[] = 'sensitive_sampling:' . sanitize_key( $forbidden );
			}
		}
		return array( 'safe' => empty( $flags ), 'flags' => array_values( array_unique( $flags ) ) );
	}

	public static function conversion_quality_score( array $metrics ) {
		$defaults = array( 'handoff_success' => 0, 'accessibility' => 100, 'claim_freshness' => 100, 'privacy' => 100, 'complaint_health' => 100, 'destination_health' => 100, 'performance' => 100 );
		$m = array_merge( $defaults, $metrics );
		$weights = array( 'handoff_success' => 25, 'accessibility' => 15, 'claim_freshness' => 15, 'privacy' => 15, 'complaint_health' => 10, 'destination_health' => 10, 'performance' => 10 );
		$score = 0.0;
		foreach ( $weights as $key => $weight ) {
			$value = max( 0.0, min( 100.0, (float) $m[ $key ] ) );
			$score += $value * ( $weight / 100 );
		}
		return round( $score, 1 );
	}

	public static function cohort_allowed( $count, $threshold = self::MIN_COHORT ) {
		return (int) $count >= max( self::MIN_COHORT, (int) $threshold );
	}

	public static function terminology_lock() {
		return array(
			'verified_doctor' => array( 'en-US' => 'Verified Doctor', 'ur-PK' => 'تصدیق شدہ ڈاکٹر', 'ar-SA' => 'طبيب موثَّق' ),
			'global_clinic' => array( 'en-US' => 'Global Clinic', 'ur-PK' => 'عالمی کلینک', 'ar-SA' => 'العيادة العالمية' ),
			'zero_commission' => array( 'en-US' => '0% platform commission', 'ur-PK' => 'پلیٹ فارم کمیشن صفر فیصد', 'ar-SA' => 'عمولة المنصة 0%' ),
			'voluntary_support' => array( 'en-US' => 'Voluntary support', 'ur-PK' => 'رضاکارانہ تعاون', 'ar-SA' => 'الدعم التطوعي' ),
			'emergency_service' => array( 'en-US' => 'Emergency service', 'ur-PK' => 'ہنگامی خدمت', 'ar-SA' => 'خدمة طوارئ' ),
			'appointment' => array( 'en-US' => 'Appointment', 'ur-PK' => 'ملاقات / وقت', 'ar-SA' => 'موعد' ),
		);
	}

	public static function patient_guide() {
		return array(
			'Check the doctor verification state and the source profile.',
			'Review language, consultation mode, professional scope and any displayed fee before continuing.',
			'Use only the canonical clinic or appointment owner for availability and booking.',
			'Remember that verification is not a cure or outcome guarantee.',
			'For urgent or life-threatening symptoms, seek immediate local emergency care.',
		);
	}

	public static function doctor_readiness_check( array $answers ) {
		$items = array( 'identity_ready', 'professional_evidence_ready', 'profile_ready', 'clinic_information_ready', 'languages_ready', 'consultation_modes_ready', 'privacy_ready', 'rules_accepted' );
		$complete = 0;
		$missing = array();
		foreach ( $items as $item ) {
			if ( ! empty( $answers[ $item ] ) ) {
				$complete++;
			} else {
				$missing[] = $item;
			}
		}
		return array( 'score' => (int) round( 100 * $complete / count( $items ) ), 'missing' => $missing, 'binding' => false, 'verification_owner' => 'File 09 / File 00' );
	}

	public static function ai_copy_guard( $draft, array $approved_claim_texts ) {
		$draft = trim( wp_strip_all_tags( (string) $draft ) );
		$dark = self::dark_pattern_scan( $draft );
		$unsupported = array();
		foreach ( self::protected_concepts() as $concept => $terms ) {
			if ( self::contains_any( self::normalize_text( $draft ), $terms ) ) {
				$found = false;
				foreach ( $approved_claim_texts as $claim ) {
					if ( self::contains_any( self::normalize_text( $claim ), $terms ) ) {
						$found = true;
						break;
					}
				}
				if ( ! $found ) {
					$unsupported[] = $concept;
				}
			}
		}
		return array( 'safe' => $dark['safe'] && empty( $unsupported ), 'dark_pattern_flags' => $dark['flags'], 'unsupported_protected_concepts' => $unsupported );
	}

	private static function prohibited_patterns() {
		return array(
			'guaranteed_cure' => '/\b(guaranteed|certain)\s+(cure|recovery|result)\b/i',
			'guaranteed_income' => '/\b(guaranteed|certain)\s+(income|earning|revenue)\b/i',
			'instant_verification' => '/\b(instant|automatic)\s+(verification|approval)\b/i',
			'paid_ranking' => '/\b(?:pay|donat(?:e|ion)|support)\b(?![^.!?]{0,60}\b(?:does not|do not|cannot|never|not)\b)[^.!?]{0,60}\b(?:rank|ranking|visibility|verification|priority)\b/i',
			'positive_commission' => '/\b([1-9][0-9]*(?:\.[0-9]+)?)\s*%\s*(?:platform\s+)?commission\b/i',
		);
	}

	private static function normalize_text( $text ) {
		$text = wp_strip_all_tags( (string) $text );
		$text = function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
		return preg_replace( '/\s+/u', ' ', trim( $text ) );
	}

	private static function contains_any( $text, array $terms ) {
		foreach ( $terms as $term ) {
			$needle = self::normalize_text( $term );
			if ( '' !== $needle && false !== strpos( $text, $needle ) ) {
				return true;
			}
		}
		return false;
	}
}
