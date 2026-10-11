<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'DAY_IN_SECONDS', 86400 );

function __( $text ) { return $text; }
function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) ); }
function wp_strip_all_tags( $value ) { return strip_tags( (string) $value ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ); }
function wp_json_encode( $value ) { return json_encode( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); }
function absint( $value ) { return abs( (int) $value ); }

require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-policy.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-i18n.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php';

$failures = array();
function assert_future( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures[] = $message;
	}
}

$catalog = GCU_Future_Policy::feature_catalog();
assert_future( 24 === count( $catalog ), 'Exactly 24 Founder-approved Future Intelligence features are required.' );
for ( $i = 1; $i <= 24; $i++ ) {
	$id = 'F14-FUT-' . str_pad( (string) $i, 2, '0', STR_PAD_LEFT );
	assert_future( isset( $catalog[ $id ] ) && ! empty( $catalog[ $id ]['approved'] ), 'Missing approved feature ' . $id );
}
assert_future( 'P0' === $catalog['F14-FUT-01']['priority'], 'Ethical Intent Router must remain P0.' );
assert_future( 'P1' === $catalog['F14-FUT-24']['priority'], 'Doctor Readiness Self-Check must remain P1.' );

$context = GCU_Future_Policy::sanitize_handoff_context( array( 'country' => 'pk', 'language' => 'ur', 'mode' => 'online' ) );
assert_future( 'PK' === $context['country'] && 'ur' === $context['language'] && 'online' === $context['mode'], 'Safe handoff context normalization failed.' );
$bad_context = GCU_Future_Policy::sanitize_handoff_context( array( 'country' => 'Pakistan', 'language' => 'xx', 'mode' => 'secret' ) );
assert_future( '' === $bad_context['country'] && '' === $bad_context['language'] && '' === $bad_context['mode'], 'Unsafe handoff context must fail closed.' );

$clear = GCU_Future_Policy::dark_pattern_scan( 'Review the verified profile and continue to the canonical owner when ready.' );
assert_future( true === $clear['safe'], 'Ordinary truthful copy should pass dark-pattern checks.' );
$truthful_no_advantage = GCU_Future_Policy::dark_pattern_scan( 'Voluntary support is optional and does not purchase ranking, visibility, verification or basic service.' );
assert_future( true === $truthful_no_advantage['safe'], 'Truthful no-advantage disclosure must not be misclassified as paid visibility.' );
$scarcity = GCU_Future_Policy::dark_pattern_scan( 'Last chance! Only today. Hurry and act now.' );
assert_future( false === $scarcity['safe'] && in_array( 'fake_scarcity', $scarcity['flags'], true ), 'Fake scarcity must be blocked.' );
$guarantee = GCU_Future_Policy::dark_pattern_scan( 'Get a guaranteed cure and guaranteed income.' );
assert_future( false === $guarantee['safe'] && in_array( 'guaranteed_result', $guarantee['flags'], true ), 'Guaranteed cure/income language must be blocked.' );
$paid = GCU_Future_Policy::dark_pattern_scan( 'Donate now to improve ranking and visibility.' );
assert_future( false === $paid['safe'] && in_array( 'paid_visibility', $paid['flags'], true ), 'Paid visibility language must be blocked.' );

$semantic = GCU_Future_Policy::semantic_risk_scan( 'The platform charges 0% commission.', 'The platform provides clinic tools.' );
assert_future( false === $semantic['safe'] && in_array( 'protected_meaning_changed:commission', $semantic['flags'], true ), 'Protected commission meaning drift must be detected.' );

$copy = GCU_Future_Policy::copy_preflight(
	array( 'title' => 'Find a Global Doctor', 'body' => 'Review the public profile without a cure guarantee.', 'cta_label' => 'Continue' ),
	array( 'title' => 'Find a Global Doctor', 'body' => 'Review the public profile without a cure guarantee.', 'cta_label' => 'Continue' )
);
assert_future( true === $copy['safe'], 'Unchanged truthful copy should pass preflight.' );

$guards = array( 'claim_integrity' => true, 'privacy' => true, 'accessibility' => true, 'error_rate' => true, 'complaints' => true );
$experiment = GCU_Future_Policy::experiment_preflight( array( 'A' => array( 'text' => 'Find a doctor' ), 'B' => array( 'text' => 'Review doctors' ) ), $guards, 'random eligible adults; no sensitive profiling', 'consented aggregate measurement' );
assert_future( true === $experiment['safe'], 'Complete ethical experiment preflight should pass.' );
$missing_guard = GCU_Future_Policy::experiment_preflight( array( 'A', 'B' ), array( 'privacy' => true ), 'random', 'aggregate' );
assert_future( false === $missing_guard['safe'], 'Missing mandatory experiment guardrails must fail.' );
$sensitive = GCU_Future_Policy::experiment_preflight( array( 'A', 'B' ), $guards, 'health profiling', 'aggregate' );
assert_future( false === $sensitive['safe'], 'Sensitive health profiling must be blocked.' );

assert_future( false === GCU_Future_Policy::cohort_allowed( 9 ), 'Cohorts below 10 must be suppressed.' );
assert_future( true === GCU_Future_Policy::cohort_allowed( 10 ), 'Cohorts at the approved minimum may be reported.' );

// R6: never publish a composite score based on an unconfirmed owner arrival,
// fabricated perfect accessibility, missing performance or a tiny cohort.
$no_owner = GCU_Future_Policy::quality_evidence_status( 25, 0, 95, 90 );
assert_future( false === $no_owner['complete'] && in_array( 'owner_handoff_confirmation_unavailable', $no_owner['missing'], true ), 'CTA clicks alone do not prove owner-side handoff.' );
$no_accessibility = GCU_Future_Policy::quality_evidence_status( 25, 12, null, 90 );
assert_future( false === $no_accessibility['complete'] && in_array( 'accessibility_measurement_unavailable', $no_accessibility['missing'], true ), 'Missing measured accessibility must never score as 100.' );
$no_performance = GCU_Future_Policy::quality_evidence_status( 25, 12, 90, null );
assert_future( false === $no_performance['complete'] && in_array( 'performance_measurement_unavailable', $no_performance['missing'], true ), 'Missing performance evidence blocks the composite score.' );
$small_quality_cohort = GCU_Future_Policy::quality_evidence_status( 9, 3, 90, 90 );
assert_future( false === $small_quality_cohort['complete'] && in_array( 'insufficient_cta_sample', $small_quality_cohort['missing'], true ), 'A small cohort cannot produce a quality score.' );
$unattested_quality = GCU_Future_Policy::quality_evidence_status( 25, 12, 93, 91 );
assert_future( false === $unattested_quality['complete'] && in_array( 'owner_handoff_confirmation_unavailable', $unattested_quality['missing'], true ), 'A forged browser destination_loaded count is never owner attestation.' );
assert_future( false === GCU_Future_Policy::owner_confirmation_contract_ready(), 'No versioned owner handoff attestation is currently registered.' );
$rest_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-rest.php' );
assert_future( false !== strpos( $rest_source, 'gcu_owner_stage_attestation_required' ), 'The public event endpoint must reject owner-only completion stages.' );
$verified_quality = GCU_Future_Policy::quality_evidence_status( 25, 12, 93, 91, true );
assert_future( true === $verified_quality['complete'] && empty( $verified_quality['missing'] ), 'Measured owner handoff, accessibility and performance permit scoring.' );
$future_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
assert_future( false !== strpos( $future_source, "'dropoff_status' => 'owner_correlated_transition_evidence_unavailable'" ), 'Unrelated doctor/patient paths must never be reported as one sequential dropoff funnel.' );
assert_future( false === strpos( $future_source, "array( 'impression', 'cta_selected', 'destination_loaded', 'application_started', 'booking_started' )" ), 'Obsolete linear patient/doctor stage chain must be absent.' );


$score = GCU_Future_Policy::conversion_quality_score( array( 'handoff_success' => 100, 'accessibility' => 100, 'claim_freshness' => 100, 'privacy' => 100, 'complaint_health' => 100, 'destination_health' => 100, 'performance' => 100 ) );
assert_future( 100.0 === $score, 'Perfect conversion quality inputs must score 100.' );
$low_score = GCU_Future_Policy::conversion_quality_score( array( 'handoff_success' => -5, 'privacy' => 150 ) );
assert_future( $low_score >= 0 && $low_score <= 100, 'Conversion quality score must be bounded.' );


$r7_grounded = GCU_Future_Policy::approved_vocabulary_guard(
    'Global Clinic doctor support',
    'Global Clinic doctor',
    array( 'Voluntary support is optional.' )
);
assert_future( true === $r7_grounded['safe'], 'R7 source-grounded AI wording may be suggested as a draft.' );
$r7_hallucination = GCU_Future_Policy::approved_vocabulary_guard(
    'Global Clinic has 200000 doctors',
    'Global Clinic has doctors',
    array( 'Doctor verification requires review.' )
);
assert_future( false === $r7_hallucination['safe'] && $r7_hallucination['novel_term_count'] >= 1, 'R7 AI provider cannot invent unsupported doctor totals.' );
$r7_ur = GCU_Future_Policy::approved_vocabulary_guard( 'عالمی کلینک', 'عالمی کلینک', array() );
assert_future( true === $r7_ur['safe'], 'R7 Unicode-aware provider vocabulary must preserve Urdu terms.' );

$r7_change = array(
    'title' => 'Approved update', 'summary' => 'Transparent change',
    'effective_date' => '2026-10-10',
    'source' => GCU_Future_Policy::PLAN_ID,
    'reviewer' => 'Approved reviewer', 'provenance' => 'Governed review'
);
assert_future( true === GCU_Future_Policy::validate_public_record_payload( 'change_log', $r7_change )['safe'], 'R7 dated, sourced and reviewed change log is eligible for publication checks.' );
$r7_missing = GCU_Future_Policy::validate_public_record_payload( 'change_log', array( 'title' => 'Unsupported' ) );
assert_future( false === $r7_missing['safe'] && in_array( 'source', $r7_missing['missing'], true ), 'R7 source-free public change logs must be rejected.' );
$r7_bad_date = $r7_change;
$r7_bad_date['effective_date'] = '2026-02-30';
assert_future( false === GCU_Future_Policy::validate_public_record_payload( 'change_log', $r7_bad_date )['safe'], 'R7 invalid effective dates must not publish.' );
$r7_bad_region = GCU_Future_Policy::validate_public_record_payload( 'jurisdiction_copy', array( 'body' => 'Available' ) );
assert_future( false === $r7_bad_region['safe'], 'R7 regional copy requires approved source, reviewer and date.' );
$r7_lock = GCU_Future_Policy::validate_public_record_payload( 'terminology_lock', array(
    'terms' => GCU_Future_Policy::terminology_lock(), 'source' => GCU_Future_Policy::PLAN_ID,
    'reviewer' => 'Founder-approved plan', 'provenance' => 'Approved amendment'
) );
assert_future( true === $r7_lock['safe'], 'R7 complete three-language protected terminology lock passes the evidence shape gate.' );
$r7_bad_lock = GCU_Future_Policy::validate_public_record_payload( 'terminology_lock', array(
    'terms' => array( 'appointment' => array( 'en-US' => 'Appointment' ) ),
    'source' => 'Plan', 'reviewer' => 'Reviewer', 'provenance' => 'Reviewed'
) );
assert_future( false === $r7_bad_lock['safe'], 'R7 partial protected language coverage must not publish.' );
$r7_future_code = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
assert_future( false !== strpos( $r7_future_code, "'privacy_verified' => is_numeric" ) && false === strpos( $r7_future_code, "'privacy' => 100" ), 'R7 privacy effectiveness must not be set to a fabricated constant 100.' );
assert_future( false !== strpos( $r7_future_code, 'gcu_future_record_evidence_required' ), 'R7 active/public Future governance records require server-side content-provenance gates.' );
assert_future( false !== strpos( $r7_future_code, 'approved_vocabulary_guard( $text, $base, $claim_texts )' ), 'R7 AI provider output is grounded before draft suggestions are returned.' );


// R8: the File 14 acquisition owner cannot certify File 07/08/09 outcomes.
assert_future( true === GCU_Future_Policy::acquisition_stage_reportable( 'impression' ), 'R8: impression belongs to File 14.' );
assert_future( true === GCU_Future_Policy::acquisition_stage_reportable( 'cta_selected' ), 'R8: CTA choice belongs to File 14.' );
foreach ( array( 'destination_loaded', 'application_started', 'booking_started', 'invented_stage' ) as $stage ) {
    assert_future( false === GCU_Future_Policy::acquisition_stage_reportable( $stage ), 'R8: owner/invalid stage must not be an acquisition event: ' . $stage );
}
$r8_repo_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-repository.php' );
assert_future( false !== strpos( $r8_repo_source, 'if(!GCU_Future_Policy::acquisition_stage_reportable($stage))' ), 'R8: repository-level write rejects owner-forged stage independently of REST.' );
assert_future( false !== strpos( $r8_repo_source, 'array_filter(is_array($rows)?$rows:array()' ), 'R8: legacy owner-only rows must be excluded from acquisition analytics.' );
$r8_future_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
assert_future( false !== strpos( $r8_future_source, "owner_stage_evidence_status' => 'not_attested_excluded" ), 'R8: friction report must disclose excluded historical owner events.' );
assert_future( false !== strpos( $r8_future_source, 'GCU_Future_Policy::acquisition_stage_reportable( isset( $row' ), 'R8: friction totals must reject legacy owner-only rows.' );
assert_future( false !== strpos( $r8_future_source, '$loaded = 0;' ), 'R8: unsigned legacy destination_loaded records cannot create fake handoff success.' );

// R9: Future REST readers must fail closed before touching unverified schema
// even when wp_options incorrectly claims a completed migration.
$r9_future_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
assert_future( false !== strpos( $r9_future_source, 'function rest_storage_ready()' ), 'R9 read barrier helper must exist.' );
assert_future( false !== strpos( $r9_future_source, '$ready = self::runtime_ready();' ) && false !== strpos( $r9_future_source, '$ready = self::verify_schema();' ), 'R9 read barrier must confirm both runtime state and physical table/column verification.' );
assert_future( false !== strpos( $r9_future_source, 'gcu_future_rest_storage_unready' ) && false !== strpos( $r9_future_source, "'status' => 503" ), 'R9 unsafe schema must return sanitized 503 rather than SQL/table details.' );
foreach ( array( 'rest_change_log', 'rest_copy_preflight', 'rest_experiment_preflight', 'rest_quality', 'rest_friction', 'rest_scenarios', 'rest_faq_gaps', 'rest_consistency', 'rest_ai_copy', 'rest_records', 'rest_reports' ) as $handler ) {
    $r9_handler_at = strpos( $r9_future_source, 'function ' . $handler . '(' );
    assert_future( false !== $r9_handler_at && false !== strpos( substr( $r9_future_source, $r9_handler_at, 240 ), 'self::rest_storage_ready()' ), 'R9 storage gate missing on ' . $handler );
}
assert_future( false !== strpos( $r9_future_source, "consume_rate_limit( 'future-ai-copy', 12 )" ), 'R9 provider-backed AI copy endpoint requires a bounded per-actor rate limit.' );
assert_future( false !== strpos( $r9_future_source, 'if ( is_wp_error( self::rest_storage_ready() ) ) { return $html; }' ), 'R9 degraded future schema must not break canonical public landing HTML.' );

// R10: only exact Future chrome labels are translated; no canonical base or
// founder-approved dynamic claim text may undergo blanket string replacement.
$r10_markup = '<section><h2>Choose your next step</h2><p>Choose your next step yesterday</p><p data-label="Choose your next step">Trust evidence for doctors</p><form data-gcu-readiness-form></form></section>';
$r10_translated = GCU_Future_I18n::localize_fragment( $r10_markup, 'ur-PK' );
assert_future( false !== strpos( $r10_translated, '<h2>اپنا اگلا مرحلہ منتخب کریں</h2>' ), 'R10 known standalone Future heading translates into Urdu.' );
assert_future( false !== strpos( $r10_translated, 'Choose your next step yesterday' ), 'R10 partial words inside source-derived paragraphs cannot be rewritten.' );
assert_future( false !== strpos( $r10_translated, 'data-label="Choose your next step"' ), 'R10 approved markup attributes are not translated.' );
assert_future( false !== strpos( $r10_translated, 'Trust evidence for doctors' ), 'R10 text with an unmatched meaning does not receive substring translations.' );
assert_future( false !== strpos( $r10_translated, 'data-gcu-readiness-prefix=' ), 'R10 progressive readiness gets localized live-status labels only inside its own fragment.' );
$r10_arabic = GCU_Future_I18n::localize_fragment( '<h2>Choose your next step</h2>', 'ar-SA' );
assert_future( false !== strpos( $r10_arabic, '<h2>اختر خطوتك التالية</h2>' ), 'R10 Arabic Future heading translation is preserved.' );
$r10_future_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
$r10_i18n_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-i18n.php' );
assert_future( false !== strpos( $r10_future_source, '<!--gcu-future-start-->' ) && false !== strpos( $r10_future_source, '<!--gcu-future-end-->' ), 'R10 only generated Future fragment may enter localization.' );
assert_future( false === strpos( $r10_i18n_source, 'strtr( $html, $replacements )' ), 'R10 no global whole-page string substitutions.' );
assert_future( false !== strpos( $r10_future_source, 'data-gcu-readiness-calculate hidden' ) && false !== strpos( $r10_future_source, 'data-gcu-readiness-fallback' ), 'R10 no-script readers see an honest static checklist, not an inert button.' );
$r10_js_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/assets/js/gcu-future-intelligence.js' );
$r10_css_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/assets/css/gcu-future-intelligence.css' );
assert_future( false !== strpos( $r10_js_source, 'button.hidden=false' ) && false !== strpos( $r10_js_source, 'fallback.hidden=true' ), 'R10 button is exposed only after working event-listener initialization.' );
assert_future( false !== strpos( $r10_css_source, '[hidden] { display: none !important; }' ), 'R10 hidden controls cannot be overridden by authored CTA display CSS.' );


// R11: report UUID replay is idempotent only when original normalized fields,
// including actor provenance, agree; a reviewed report does not revert to open.
$r11_report = array(
    'report_type' => 'copy_quality', 'route_key' => 'global_clinic',
    'block_key' => null, 'locale' => 'en-US', 'reason_code' => 'misleading',
    'message' => 'The page copy needs clarification.', 'actor_hash' => null,
    'status' => 'resolved',
);
$r11_same = $r11_report;
unset( $r11_same['status'] );
assert_future( GCU_Future_Intelligence::report_replay_matches( $r11_report, $r11_same ), 'R11 identical normalized report retry must remain idempotent after moderation.' );
$r11_changed = $r11_same;
$r11_changed['message'] = 'A different report was submitted.';
assert_future( ! GCU_Future_Intelligence::report_replay_matches( $r11_report, $r11_changed ), 'R11 reused UUID with different report copy must not return success.' );
$r11_changed = $r11_same;
$r11_changed['actor_hash'] = 'different-actor';
assert_future( ! GCU_Future_Intelligence::report_replay_matches( $r11_report, $r11_changed ), 'R11 reused UUID must not cross actor-provenance boundary.' );
$r11_changed = $r11_same;
$r11_changed['route_key'] = 'how_it_works';
assert_future( ! GCU_Future_Intelligence::report_replay_matches( $r11_report, $r11_changed ), 'R11 reused UUID must not cross canonical route identity.' );
unset( $r11_same['reason_code'] );
assert_future( ! GCU_Future_Intelligence::report_replay_matches( $r11_report, $r11_same ), 'R11 missing retry identity field must fail closed.' );
$r11_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
assert_future( false !== strpos( $r11_source, 'gcu_future_report_identity_conflict' ), 'R11 duplicate mismatch must return a controlled conflict, not a false successful report.' );
assert_future( false !== strpos( $r11_source, "'status'=>is_array(\$existing_report)?sanitize_key(\$existing_report['status']):'open'" ), 'R11 true duplicate must return currently governed report state.' );
assert_future( false !== strpos( $r11_source, 'function seed_record_if_absent' ) && false !== strpos( $r11_source, 'if ( null !== $existing ) { return true; }' ), 'R11 migration must not overwrite Founder-approved existing governed records.' );
assert_future( false !== strpos( $r11_source, 'future_seed_verification_failed' ) && false !== strpos( $r11_source, '$seeded = self::seed_defaults();' ), 'R11 migration must not mark schema ready after a failed safe seed.' );

$terms = GCU_Future_Policy::terminology_lock();
assert_future( isset( $terms['verified_doctor']['en-US'], $terms['verified_doctor']['ur-PK'], $terms['verified_doctor']['ar-SA'] ), 'Terminology lock must cover English, Urdu and Arabic.' );
$ur = GCU_Future_I18n::strings( 'ur-PK' );
$ar = GCU_Future_I18n::strings( 'ar-SA' );
foreach ( array( 'Choose your next step', 'Trust evidence', 'Choose a doctor safely', 'Global Clinic readiness self-check', 'Send report', 'Preparation estimate:' ) as $key ) {
	assert_future( isset( $ur[ $key ] ) && '' !== $ur[ $key ], 'Missing Urdu Future UI key: ' . $key );
	assert_future( isset( $ar[ $key ] ) && '' !== $ar[ $key ], 'Missing Arabic Future UI key: ' . $key );
}

$ready = GCU_Future_Policy::doctor_readiness_check( array_fill_keys( array( 'identity_ready','professional_evidence_ready','profile_ready','clinic_information_ready','languages_ready','consultation_modes_ready','privacy_ready','rules_accepted' ), true ) );
assert_future( 100 === $ready['score'] && false === $ready['binding'] && 'File 09 / File 00' === $ready['verification_owner'], 'Readiness self-check must remain non-binding and owner-safe.' );

$ai_safe = GCU_Future_Policy::ai_copy_guard( 'Review a verified doctor profile.', array( 'Doctor access is activated after verification review.' ) );
assert_future( true === $ai_safe['safe'], 'AI guard should permit protected concepts supported by approved claims.' );
$ai_bad = GCU_Future_Policy::ai_copy_guard( 'Guaranteed cure and guaranteed income today.', array( 'Verification is not a cure guarantee.' ) );
assert_future( false === $ai_bad['safe'], 'AI guard must reject dark-pattern or guaranteed outcome copy.' );

if ( $failures ) {
	fwrite( STDERR, "Future Intelligence tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Future Intelligence tests: PASS\n";
