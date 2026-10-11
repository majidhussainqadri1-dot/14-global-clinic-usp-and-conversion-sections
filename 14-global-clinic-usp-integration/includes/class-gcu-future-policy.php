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

	/** Explicit journeys are selected by the visitor; nested JSON cannot imply intent. */
	public static function sanitize_explicit_intent( $value ) {
		if ( ! is_string( $value ) ) {
			return '';
		}
		$intent = sanitize_key( $value );
		return array_key_exists( $intent, self::supported_intents() ) ? $intent : '';
	}

	/** Typed REST governance input prevents PHP array-to-string coercion and
	 * rejects silently substituted empty evidence or false public flags. */
	public static function validate_future_record_input_types( array $input ) {
		$invalid = array();
		foreach ( array( 'record_type', 'record_key', 'locale', 'region', 'status' ) as $field ) {
			if ( array_key_exists( $field, $input ) && ! is_string( $input[ $field ] ) ) {
				$invalid[] = $field;
			}
		}
		if ( array_key_exists( 'payload', $input ) && ! is_array( $input['payload'] ) ) {
			$invalid[] = 'payload';
		}
		if ( array_key_exists( 'expected_version', $input ) ) {
			$v = $input['expected_version'];
			if ( ! ( ( is_int( $v ) && $v >= 0 ) || ( is_string( $v ) && ctype_digit( $v ) ) ) ) {
				$invalid[] = 'expected_version';
			}
		}
		if ( array_key_exists( 'is_public', $input ) && ! in_array( $input['is_public'], array( true, false, 0, 1, '0', '1' ), true ) ) {
			$invalid[] = 'is_public';
		}
		return array( 'safe' => empty( $invalid ), 'invalid_fields' => $invalid );
	}

	/** Claim revalidation changes public trust state. Never coerce nested inputs. */
	public static function validate_revalidation_input_types( $claim_key, $reason, $version ) {
		return is_string( $claim_key ) && is_string( $reason )
			&& ( ( is_int( $version ) && $version > 0 ) || ( is_string( $version ) && ctype_digit( $version ) && (int) $version > 0 ) );
	}

	/**
	 * Query parameters can be replaced by JSON/form values in WP_REST_Request.
	 * Never stringify arrays or promote negative pagination/window values.
	 */
	public static function bounded_query_integer( $value, $default, $maximum, $allow_zero = false ) {
		if ( null === $value ) { return $default; }
		if ( is_int( $value ) ) {
			if ( $value < ( $allow_zero ? 0 : 1 ) ) { return null; }
			return min( $maximum, $value );
		}
		if ( ! is_string( $value ) || '' === $value || strlen( $value ) > 9 || ! ctype_digit( $value ) ) {
			return null;
		}
		$parsed = (int) $value;
		if ( $parsed < ( $allow_zero ? 0 : 1 ) ) { return null; }
		return min( $maximum, $parsed );
	}

	/** A requested public trust claim must be an exact canonical-shaped key. */
	public static function safe_public_claim_key( $value ) {
		return is_string( $value ) && preg_match( '/^[a-z0-9_-]{1,100}$/D', $value ) ? $value : '';
	}

	/**
	 * Read-only editor pagination cannot misinterpret a malformed filter as the
	 * default list or silently convert a negative cursor into a positive one.
	 */
	public static function validate_future_read_query( array $params, $kind ) {
		$invalid = array();
		$type = '';
		$status = 'open';
		if ( 'records' === $kind ) {
			if ( isset( $params['type'] ) ) {
				if ( ! is_string( $params['type'] ) || ( '' !== $params['type'] && ! preg_match( '/^[a-z0-9_-]{1,80}$/D', $params['type'] ) ) ) {
					$invalid[] = 'type';
				} else {
					$type = $params['type'];
				}
			}
		} elseif ( 'reports' === $kind ) {
			if ( isset( $params['status'] ) ) {
				if ( ! is_string( $params['status'] ) || ! in_array( $params['status'], array( 'open', 'reviewing', 'resolved', 'rejected' ), true ) ) {
					$invalid[] = 'status';
				} else {
					$status = $params['status'];
				}
			}
		} else {
			$invalid[] = 'kind';
		}
		$limit = self::bounded_query_integer( isset( $params['limit'] ) ? $params['limit'] : null, 50, 100 );
		$cursor = self::bounded_query_integer( isset( $params['cursor'] ) ? $params['cursor'] : null, 0, 2147483647, true );
		if ( null === $limit ) { $invalid[] = 'limit'; }
		if ( null === $cursor ) { $invalid[] = 'cursor'; }
		return array( 'safe' => empty( $invalid ), 'invalid_fields' => $invalid, 'type' => $type, 'status' => $status, 'limit' => $limit, 'cursor' => $cursor );
	}

	public static function sanitize_handoff_context( array $input ) {
		$country = isset( $input['country'] ) && is_string( $input['country'] ) ? strtoupper( sanitize_text_field( $input['country'] ) ) : '';
		if ( ! preg_match( '/^[A-Z]{2}$/', $country ) ) {
			$country = '';
		}
		$language = isset( $input['language'] ) && is_string( $input['language'] ) ? strtolower( sanitize_text_field( $input['language'] ) ) : '';
		if ( ! in_array( $language, array( 'en', 'ur', 'ar' ), true ) ) {
			$language = '';
		}
		$mode = isset( $input['mode'] ) && is_string( $input['mode'] ) ? sanitize_key( $input['mode'] ) : '';
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

	/**
	 * Inspect only textual copy. User-provided nested JSON must never be cast
	 * into a plausible Founder-approved title, body or CTA.
	 */
	public static function copy_preflight( array $record, array $previous = array() ) {
		$flags = array();
		$parts = array( 'current' => array(), 'previous' => array() );
		foreach ( array( 'current' => $record, 'previous' => $previous ) as $phase => $copy ) {
			foreach ( array( 'title', 'body', 'cta_label' ) as $field ) {
				$value = isset( $copy[ $field ] ) ? $copy[ $field ] : '';
				if ( ! is_string( $value ) ) {
					$flags[] = 'invalid_copy_field_type:' . $phase . ':' . $field;
					continue;
				}
				$parts[ $phase ][] = 'body' === $field ? wp_strip_all_tags( $value ) : $value;
			}
		}
		$current = implode( ' ', array_filter( $parts['current'] ) );
		$prior = implode( ' ', array_filter( $parts['previous'] ) );
		$dark = self::dark_pattern_scan( $current );
		$semantic = $prior ? self::semantic_risk_scan( $prior, $current ) : array( 'safe' => true, 'risk' => 'low', 'flags' => array() );
		$flags = array_merge( $flags, $dark['flags'], $semantic['flags'] );
		return array( 'safe' => empty( $flags ), 'flags' => array_values( array_unique( $flags ) ), 'dark_pattern' => $dark, 'semantic' => $semantic );
	}

	public static function experiment_preflight( array $variants, array $guardrails, $sample_policy, $privacy_policy ) {
		$flags = array();
		if ( count( $variants ) < 2 ) {
			$flags[] = 'at_least_two_variants_required';
		}
		foreach ( $variants as $index => $variant ) {
			if ( ! is_array( $variant ) && ! is_string( $variant ) ) {
				$flags[] = 'invalid_variant_type';
				continue;
			}
			$text = is_array( $variant ) ? wp_json_encode( $variant ) : $variant;
			if ( ! is_string( $text ) ) {
				$flags[] = 'invalid_variant_encoding';
				continue;
			}
			$scan = self::dark_pattern_scan( $text );
			foreach ( $scan['flags'] as $flag ) {
				$flags[] = 'variant_' . absint( $index ) . ':' . $flag;
			}
		}
		$required = array( 'claim_integrity', 'privacy', 'accessibility', 'error_rate', 'complaints' );
		foreach ( $required as $key ) {
			if ( ! array_key_exists( $key, $guardrails ) ) {
				$flags[] = 'missing_guardrail:' . $key;
				continue;
			}
			// Presence alone cannot prove a safety control is enabled. False,
			// blank, non-scalar or explicitly disabled settings fail closed.
			$value = $guardrails[ $key ];
			$valid = true === $value
				|| ( is_numeric( $value ) && ! is_bool( $value ) && (float) $value >= 0 && is_finite( (float) $value ) )
				|| ( is_string( $value ) && '' !== trim( $value ) && ! in_array( strtolower( trim( $value ) ), array( 'false', 'off', 'no', 'none', 'disabled', 'null' ), true ) );
			if ( ! $valid ) {
				$flags[] = 'invalid_guardrail:' . $key;
			}
		}
		if ( ! is_string( $sample_policy ) || ! is_string( $privacy_policy ) ) {
			$flags[] = 'invalid_sampling_policy_type';
		}
		$sensitive = strtolower( ( is_string( $sample_policy ) ? $sample_policy : '' ) . ' ' . ( is_string( $privacy_policy ) ? $privacy_policy : '' ) );
		foreach ( array( 'minor profiling', 'health profiling', 'patient profiling', 'diagnosis targeting', 'identity evidence targeting' ) as $forbidden ) {
			if ( false !== strpos( $sensitive, $forbidden ) ) {
				$flags[] = 'sensitive_sampling:' . sanitize_key( $forbidden );
			}
		}
		return array( 'safe' => empty( $flags ), 'flags' => array_values( array_unique( $flags ) ) );
	}

	/**
	 * An aggregate score is publishable only when its essential components have
	 * actually been observed. A click is not proof of an owner-side arrival.
	 * Unknown accessibility/performance evidence must never default to 100%.
	 */
	/**
	 * There is currently no approved, versioned owner-origin acknowledgement
	 * contract registered between File14 and Files07/08/09. Do not infer one
	 * from a browser-supplied stage or a raw aggregate.
	 */
	/**
	 * File 14 can record only its own acquisition interactions. Destination
	 * arrival, doctor application and booking are canonical companion facts:
	 * they require a separate, versioned owner-native acknowledgement contract.
	 * This predicate also excludes untrusted historical events from analytics.
	 */
	public static function acquisition_stage_reportable( $stage ) {
		return in_array( sanitize_key( (string) $stage ), array( 'impression', 'cta_selected' ), true );
	}

	public static function owner_confirmation_contract_ready() {
		return false;
	}

	/** Only actual 0..100 measured proportions are evidence; never use
	 * booleans, arrays, nonfinite or out-of-range values as verified signals. */
	public static function valid_quality_percentage( $value ) {
		if ( is_bool( $value ) || ! is_numeric( $value ) ) { return false; }
		$number = (float) $value;
		return is_finite( $number ) && $number >= 0.0 && $number <= 100.0;
	}

	/** Accepted question volume is an actual aggregate, never abs(-n). */
	public static function valid_faq_aggregate_signal( $signal ) {
		if ( ! is_array( $signal ) || ! isset( $signal['question'], $signal['count'] ) || ! is_string( $signal['question'] ) ) {
			return false;
		}
		$count = $signal['count'];
		$nonnegative = ( is_int( $count ) && $count >= 0 )
			|| ( is_string( $count ) && ctype_digit( $count ) && strlen( $count ) <= 12 );
		return $nonnegative && '' !== trim( $signal['question'] ) && self::cohort_allowed( (int) $count );
	}

	/** No cast from nested JSON to an AI editor instruction or to safe copy. */
	public static function valid_ai_copy_inputs( array $input ) {
		return isset( $input['base_text'] ) && is_string( $input['base_text'] )
			&& ( ! array_key_exists( 'objective', $input ) ||
				( is_string( $input['objective'] ) && in_array( sanitize_key( $input['objective'] ), array( 'clarity', 'concise', 'trust' ), true ) ) );
	}

	public static function normalize_ai_candidate_text( $candidate ) {
		if ( is_array( $candidate ) ) {
			if ( ! isset( $candidate['text'] ) || ! is_string( $candidate['text'] ) ) { return null; }
			$candidate = $candidate['text'];
		}
		if ( ! is_string( $candidate ) ) { return null; }
		return trim( wp_strip_all_tags( $candidate ) );
	}

	public static function quality_evidence_status( $selected, $owner_loaded, $accessibility, $performance, $owner_attested = false ) {
		$missing = array();
		if ( ! self::cohort_allowed( $selected ) ) {
			$missing[] = 'insufficient_cta_sample';
		}
		if ( (int) $owner_loaded < 1 || true !== $owner_attested ) {
			$missing[] = 'owner_handoff_confirmation_unavailable';
		}
		// Scores outside the defined 0–100 measurement scale are not evidence.
		if ( ! self::valid_quality_percentage( $accessibility ) ) {
			$missing[] = 'accessibility_measurement_unavailable';
		}
		if ( ! self::valid_quality_percentage( $performance ) ) {
			$missing[] = 'performance_measurement_unavailable';
		}
		return array( 'complete' => empty( $missing ), 'missing' => $missing );
	}

	public static function conversion_quality_score( array $metrics ) {
		$defaults = array( 'handoff_success' => 0, 'accessibility' => 0, 'claim_freshness' => 0, 'privacy' => 0, 'complaint_health' => 0, 'destination_health' => 0, 'performance' => 0 );
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
			// Only checked boolean/checkbox values indicate readiness. The JSON
			// strings "false", "no", arbitrary text or arrays must not inflate it.
			$value = isset( $answers[ $item ] ) ? $answers[ $item ] : null;
			if ( true === $value || 1 === $value || '1' === $value ) {
				$complete++;
			} else {
				$missing[] = $item;
			}
		}
		return array( 'score' => (int) round( 100 * $complete / count( $items ) ), 'missing' => $missing, 'binding' => false, 'verification_owner' => 'File 09 / File 00' );
	}

	/**
	 * Future governance records may be marked active/public only with verifiable
	 * content shape and provenance. This does not grant publication permission:
	 * Founder approval and state/ownership checks remain separately mandatory.
	 */
	public static function validate_public_record_payload( $type, array $payload ) {
		$required = array(
			'jurisdiction_copy' => array( 'body', 'source', 'reviewer', 'provenance', 'effective_date' ),
			'terminology_lock' => array( 'source', 'reviewer', 'provenance' ),
			'change_log' => array( 'title', 'summary', 'effective_date', 'source', 'reviewer', 'provenance' ),
		);
		$type = sanitize_key( $type );
		if ( ! isset( $required[ $type ] ) ) {
			return array( 'safe' => true, 'missing' => array() );
		}
		$missing = array();
		foreach ( $required[ $type ] as $key ) {
			if ( ! isset( $payload[ $key ] ) || ! is_string( $payload[ $key ] ) || '' === trim( $payload[ $key ] ) ) {
				$missing[] = $key;
			}
		}
		if ( in_array( $type, array( 'jurisdiction_copy', 'change_log' ), true ) && isset( $payload['effective_date'] ) ) {
			$date = (string) $payload['effective_date'];
			if ( ! preg_match( '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/D', $date, $part )
				|| ! checkdate( (int) $part[2], (int) $part[3], (int) $part[1] ) ) {
				$missing[] = 'valid_effective_date';
			}
		}
		if ( 'terminology_lock' === $type ) {
			if ( empty( $payload['terms'] ) || ! is_array( $payload['terms'] ) ) {
				$missing[] = 'terms';
			} else {
				// The approved protected concept set is a minimum. A partial payload
				// cannot silently erase verified doctor, zero commission, emergency,
				// voluntary support or other canonical multilingual terminology.
				foreach ( array_keys( self::terminology_lock() ) as $protected_term ) {
					if ( ! array_key_exists( $protected_term, $payload['terms'] ) ) {
						$missing[] = 'protected_term_' . $protected_term;
					}
				}
				foreach ( $payload['terms'] as $term => $locales ) {
					if ( ! is_array( $locales ) ) {
						$missing[] = 'term_locales';
						break;
					}
					foreach ( array( 'en-US', 'ur-PK', 'ar-SA' ) as $locale ) {
						if ( empty( $locales[ $locale ] ) || ! is_string( $locales[ $locale ] ) ) {
							$missing[] = 'term_locale_' . sanitize_key( $locale );
						}
					}
				}
			}
		}
		return array( 'safe' => empty( $missing ), 'missing' => array_values( array_unique( $missing ) ) );
	}

	/**
	 * Conservative source-grounding of provider-created AI copy drafts:
	 * new words/numbers outside the editor's input and current approved claims
	 * are refused. Human semantic review is still mandatory for every draft.
	 */
	public static function approved_vocabulary_guard( $candidate, $base, array $approved_claim_texts ) {
		$source = self::normalize_text( (string) $base . ' ' . implode( ' ', array_map( 'strval', $approved_claim_texts ) ) );
		$draft = self::normalize_text( $candidate );
		$allowed = preg_split( '/[^\\p{L}\\p{N}%]+/u', $source, -1, PREG_SPLIT_NO_EMPTY );
		$words = preg_split( '/[^\\p{L}\\p{N}%]+/u', $draft, -1, PREG_SPLIT_NO_EMPTY );
		if ( ! is_array( $allowed ) || ! is_array( $words ) || empty( $words ) ) {
			return array( 'safe' => false, 'novel_term_count' => 0 );
		}
		$lookup = array_fill_keys( $allowed, true );
		$unapproved = 0;
		foreach ( array_unique( $words ) as $word ) {
			if ( ! isset( $lookup[ $word ] ) ) {
				$unapproved++;
			}
		}
		return array( 'safe' => 0 === $unapproved, 'novel_term_count' => $unapproved );
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
