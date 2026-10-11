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

// R19: Legacy funnel and administrative mutation inputs must never be absint-coerced.
assert_future( 7 === GCU_Future_Policy::strict_mutation_version( 7 ), 'Positive integer row versions remain valid.' );
assert_future( 7 === GCU_Future_Policy::strict_mutation_version( '7' ), 'Positive decimal string row versions remain valid.' );
foreach ( array( -7, '-7', 0, '0', 1.2, false, true, array( 7 ), (object) array( 'version' => 7 ), '999999999999999999999' ) as $invalid_version ) {
	assert_future( null === GCU_Future_Policy::strict_mutation_version( $invalid_version ), 'Malformed optimistic-concurrency version must be rejected.' );
}
assert_future( 2147483647 === GCU_Future_Policy::strict_mutation_version( '2147483647' ), 'Maximum supported row version must be valid.' );
assert_future( null === GCU_Future_Policy::strict_mutation_version( '2147483648' ), 'Overflow row version must be rejected.' );
assert_future( 'running' === GCU_Future_Policy::safe_workflow_target( 'running' ), 'Canonical workflow status must remain valid.' );
foreach ( array( 'RUNNING', 'running now', '  active', '', array( 'running' ), true, 1 ) as $invalid_target ) {
	assert_future( '' === GCU_Future_Policy::safe_workflow_target( $invalid_target ), 'Malformed target cannot become a valid workflow state.' );
}
assert_future( 30 === GCU_Future_Policy::bounded_query_integer( null, 30, 90 ), 'Unspecified funnel window defaults to 30 days.' );
assert_future( 90 === GCU_Future_Policy::bounded_query_integer( '999', 30, 90 ), 'Valid long windows are safely capped to 90 days.' );
foreach ( array( -30, '-30', 0, array( 'days' => 30 ), false, '1.5', 'abc' ) as $invalid_days ) {
	assert_future( null === GCU_Future_Policy::bounded_query_integer( $invalid_days, 30, 90 ), 'Malformed funnel window must not be converted to a positive period.' );
}

// R20 typed inputs: valid public queries and adversarial nested/malformed payloads.
assert_future( true === GCU_Future_Policy::public_blocks_query_is_typed( null, null, null ), 'Default public blocks filters valid.' );
assert_future( true === GCU_Future_Policy::public_blocks_query_is_typed( 'global_clinic_primary', 'doctor', 'ur-PK' ), 'Typed public block filters valid.' );
foreach ( array( array( array('x'), null, null ), array( 'bad-slot', null, null ), array( null, array('patient'), null ), array( null, 'patient', (object) array('locale'=>'en-US') ), array( null, null, 'ur-PK?x=1' ) ) as $bad_filters ) {
    assert_future( false === GCU_Future_Policy::public_blocks_query_is_typed( $bad_filters[0], $bad_filters[1], $bad_filters[2] ), 'Invalid public block input must fail closed.' );
}
assert_future( true === GCU_Future_Policy::acquisition_event_input_is_typed( array( 'stage'=>'cta_selected', 'destination'=>'doctor_directory', 'event_id'=>'123e4567-e89b-42d3-a456-426614174000' ) ), 'Valid acquisition event allowed.' );
foreach ( array( array('stage'=>array('impression')), array('stage'=>'cta_selected','destination'=>array('clinic')), array('stage'=>'impression','event_id'=>array('id')), array('stage'=>'impression','event_id'=>'bogus'), array('stage'=>'CTA_SELECTED') ) as $bad_event ) {
    assert_future( false === GCU_Future_Policy::acquisition_event_input_is_typed( $bad_event ), 'Nested/malformed acquisition events denied.' );
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
assert_future( false !== strpos( $r7_future_code, "'privacy_verified' => GCU_Future_Policy::valid_quality_percentage" ) && false === strpos( $r7_future_code, "'privacy' => 100" ), 'R7 privacy effectiveness must not be set to a fabricated constant 100.' );
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


// R12 — public self-check readiness must not accept truthy strings/objects as
// checked input; it is non-binding and never a File09/File00 verification.
$r12_answers = array(
    'identity_ready' => 'false', 'professional_evidence_ready' => 'no',
    'profile_ready' => array( 'checked' ), 'clinic_information_ready' => 'yes',
    'languages_ready' => false, 'consultation_modes_ready' => '0',
    'privacy_ready' => 0, 'rules_accepted' => '',
);
$r12_false = GCU_Future_Policy::doctor_readiness_check( $r12_answers );
assert_future( 0 === $r12_false['score'] && 8 === count( $r12_false['missing'] ) && false === $r12_false['binding'], 'R12: false/no/array strings must not count as a completed doctor readiness item.' );
$r12_answers['identity_ready'] = true;
$r12_answers['professional_evidence_ready'] = 1;
$r12_answers['profile_ready'] = '1';
$r12_checked = GCU_Future_Policy::doctor_readiness_check( $r12_answers );
assert_future( 38 === $r12_checked['score'] && 5 === count( $r12_checked['missing'] ) && 'File 09 / File 00' === $r12_checked['verification_owner'], 'R12: explicit boolean/integer/checkbox-string checked values are counted without claiming approval.' );
$r12_terms = GCU_Future_Policy::terminology_lock();
$r12_complete = array( 'terms' => $r12_terms, 'source' => 'Approved File14 plan', 'reviewer' => 'Founder', 'provenance' => 'Approved review' );
assert_future( true === GCU_Future_Policy::validate_public_record_payload( 'terminology_lock', $r12_complete )['safe'], 'R12: complete protected terminology passes.' );
$r12_partial = $r12_complete;
unset( $r12_partial['terms']['zero_commission'] );
$r12_check = GCU_Future_Policy::validate_public_record_payload( 'terminology_lock', $r12_partial );
assert_future( false === $r12_check['safe'] && in_array( 'protected_term_zero_commission', $r12_check['missing'], true ), 'R12: a three-language but incomplete terminology lock cannot be active or public.' );
$r12_partial = $r12_complete;
unset( $r12_partial['terms']['verified_doctor'] );
assert_future( false === GCU_Future_Policy::validate_public_record_payload( 'terminology_lock', $r12_partial )['safe'], 'R12: verified doctor terminology must not be deleted from the approved concept set.' );
$r12_partial = $r12_complete;
$r12_partial['terms']['global_clinic']['ur-PK'] = '';
assert_future( false === GCU_Future_Policy::validate_public_record_payload( 'terminology_lock', $r12_partial )['safe'], 'R12: an empty Urdu protected concept cannot pass review.' );


// R13: Public handoff and URL country/language/mode fields must reject
// non-scalar JSON/GET values without PHP warnings or accidental routing.
$r13_bad_context = GCU_Future_Policy::sanitize_handoff_context( array(
    'country' => array( 'PK' ), 'language' => array( 'ur' ),
    'mode' => array( 'online' ),
) );
assert_future( array( 'country' => '', 'language' => '', 'mode' => '' ) === $r13_bad_context, 'R13 array-valued public handoff context must safely fail closed.' );
$r13_bad_context = GCU_Future_Policy::sanitize_handoff_context( array(
    'country' => new stdClass(), 'language' => true, 'mode' => 7,
) );
assert_future( array( 'country' => '', 'language' => '', 'mode' => '' ) === $r13_bad_context, 'R13 object/bool/numeric context cannot be interpreted as a canonical destination filter.' );
$r13_good_context = GCU_Future_Policy::sanitize_handoff_context( array(
    'country' => 'PK', 'language' => 'ur', 'mode' => 'online',
) );
assert_future( 'PK' === $r13_good_context['country'] && 'ur' === $r13_good_context['language'] && 'online' === $r13_good_context['mode'], 'R13 valid canonical handoff remains available.' );

// R13: malformed region-specific legacy disclosures must not mask separately
// approved generic text; malformed generic disclosures must never publish.
if ( ! defined( 'ARRAY_A' ) ) { define( 'ARRAY_A', 'ARRAY_A' ); }
class R13_Jurisdiction_Fixture {
    public $prefix = 'wp_';
    public $rows = array();
    public $query = '';
    public function prepare( $query ) { $this->query = $query; return $query; }
    public function get_results( $query, $output = null ) { return $this->rows; }
}
$r13_prior_wpdb = isset( $GLOBALS['wpdb'] ) ? $GLOBALS['wpdb'] : null;
$GLOBALS['wpdb'] = new R13_Jurisdiction_Fixture();
$r13_generic = array(
    'body' => 'Review canonical doctor information and local professional rules.',
    'source' => 'Approved regional policy', 'reviewer' => 'Founder',
    'provenance' => 'Approved public disclosure', 'effective_date' => '2026-10-11',
);
$GLOBALS['wpdb']->rows = array(
    array( 'region' => 'PK', 'payload' => '{"body":"Unverified regional legacy text"}' ),
    array( 'region' => 'ZZ', 'payload' => json_encode( $r13_generic ) ),
);
$r13_fallback = GCU_Future_Intelligence::jurisdiction_disclosure( 'en-US', 'PK' );
assert_future( $r13_generic === $r13_fallback, 'R13 invalid regional record cannot conceal a validated general disclosure.' );
assert_future( false !== strpos( $GLOBALS['wpdb']->query, 'LIMIT 2' ), 'R13 candidate order must inspect both region-specific and approved general records.' );
$GLOBALS['wpdb']->rows = array(
    array( 'region' => 'PK', 'payload' => json_encode( $r13_generic ) ),
    array( 'region' => 'ZZ', 'payload' => '{"body":"Unsigned generic text"}' ),
);
assert_future( $r13_generic === GCU_Future_Intelligence::jurisdiction_disclosure( 'en-US', 'PK' ), 'R13 valid approved regional text retains precedence.' );
$GLOBALS['wpdb']->rows = array(
    array( 'region' => 'PK', 'payload' => '{"body":"Unverified region"}' ),
    array( 'region' => 'ZZ', 'payload' => '{"body":"Unverified generic"}' ),
);
assert_future( array() === GCU_Future_Intelligence::jurisdiction_disclosure( 'en-US', 'PK' ), 'R13 no unverified regional or generic disclosure may publish.' );
assert_future( array() === GCU_Future_Intelligence::jurisdiction_disclosure( 'en-US', array( 'PK' ) ), 'R13 non-scalar country in disclosure does not gain privileged regional scope.' );
$GLOBALS['wpdb'] = $r13_prior_wpdb;


// R14 / FUT-01: nested JSON values cannot infer a patient/doctor/learn intent.
foreach ( array( array('doctor'), (object) array('intent'=>'patient'), true, 1, null, 'unknown' ) as $bad_intent ) {
    assert_future( '' === GCU_Future_Policy::sanitize_explicit_intent( $bad_intent ), 'R14: non-scalar or unknown journey must be rejected.' );
}
foreach ( array( 'patient', 'doctor', 'learn' ) as $allowed_intent ) {
    assert_future( $allowed_intent === GCU_Future_Policy::sanitize_explicit_intent( $allowed_intent ), 'R14: approved explicit user journey remains available.' );
}
$r14_rest_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
assert_future( false !== strpos( $r14_rest_source, "GCU_Future_Policy::sanitize_explicit_intent( isset( \$data['intent'] )" ), 'R14: REST handoff must call tested typed, approved explicit-intent guard.' );

// R14 / FUT-22: invalid newest historical public governance rows cannot crowd
// an older, separately reviewed and properly sourced change out of a small log.
if ( ! defined( 'ARRAY_A' ) ) { define( 'ARRAY_A', 'ARRAY_A' ); }
class R14_Change_Log_Fixture {
    public $prefix = 'wp_';
    public $rows = array();
    public $pages = 0;
    public function prepare( $sql, $arguments ) { return $sql; }
    public function get_results( $sql, $mode = null ) {
        $this->pages++;
        if ( ! preg_match( '/LIMIT ([0-9]+) OFFSET ([0-9]+)/', $sql, $m ) ) { return array(); }
        return array_slice( $this->rows, (int) $m[2], (int) $m[1] );
    }
}
$r14_previous_wpdb = isset( $GLOBALS['wpdb'] ) ? $GLOBALS['wpdb'] : null;
$r14_db = new R14_Change_Log_Fixture();
$r14_valid_payload = array(
    'title' => 'Founder-reviewed transparency change',
    'summary' => 'Policy wording was reviewed and documented.',
    'source' => 'Approved F14 amendment',
    'reviewer' => 'Founder',
    'provenance' => 'Governance review',
    'effective_date' => '2026-10-11',
);
for ( $r14_i = 0; $r14_i < 100; $r14_i++ ) {
    $r14_db->rows[] = array(
        'record_key' => 'legacy_incomplete_' . $r14_i, 'locale' => 'en-US',
        'updated_at' => '2026-10-11 07:00:00',
        'payload' => json_encode( array( 'title' => 'No evidence supplied', 'summary' => 'Historical invalid row' ) ),
    );
}
$r14_db->rows[] = array(
    'record_key' => 'governed_change', 'locale' => 'en-US',
    'updated_at' => '2026-10-10 06:00:00', 'payload' => json_encode( $r14_valid_payload ),
);
$GLOBALS['wpdb'] = $r14_db;
$r14_decoded = GCU_Future_Intelligence::records( 'change_log', true, 1, 100 );
assert_future( 1 === count( $r14_decoded ) && is_array( $r14_decoded[0]['payload'] ) && 'Founder-reviewed transparency change' === $r14_decoded[0]['payload']['title'], 'R14 regression-discovered: records() must decode JSON payload into returned array, not a temporary foreach copy.' );
$r14_db->pages = 0;
$r14_visible = GCU_Future_Intelligence::public_change_log( 1 );
assert_future( 1 === count( $r14_visible ) && 'governed_change' === $r14_visible[0]['key'], 'R14: approved older governance entry must not be hidden by 100 newer malformed legacy rows.' );
assert_future( 2 === $r14_db->pages, 'R14: bounded stable pagination must search beyond first invalid public batch.' );
$r14_db->rows = array_slice( $r14_db->rows, 0, 100 );
$r14_db->pages = 0;
assert_future( array() === GCU_Future_Intelligence::public_change_log( 1 ), 'R14: invalid change records must never be published even without valid fallback.' );
assert_future( 2 === $r14_db->pages, 'R14: search terminates upon an empty next page.' );
$GLOBALS['wpdb'] = $r14_previous_wpdb;


// R15 — Future REST governance must never coerce JSON arrays/objects into
// controlled record types, keys, locale, version, publication or claim reasons.
$r15_valid_record = array(
    'record_type' => 'change_log', 'record_key' => 'founder_change',
    'locale' => 'en-US', 'region' => 'ZZ', 'status' => 'draft',
    'payload' => array( 'title' => 'A reviewed policy note' ),
    'expected_version' => '2', 'is_public' => false,
);
$r15_guard = GCU_Future_Policy::validate_future_record_input_types( $r15_valid_record );
assert_future( true === $r15_guard['safe'], 'R15 ordinary approved governance write fields remain accepted.' );
foreach ( array( 'record_type', 'record_key', 'locale', 'region', 'status', 'expected_version', 'is_public', 'payload' ) as $field ) {
    $r15_bad = $r15_valid_record;
    $r15_bad[ $field ] = 'payload' === $field ? (object) array( 'nested' => true ) : array( 'nested' );
    $r15_result = GCU_Future_Policy::validate_future_record_input_types( $r15_bad );
    assert_future( false === $r15_result['safe'] && in_array( $field, $r15_result['invalid_fields'], true ), 'R15 nested governed field must fail closed: ' . $field );
}
$r15_bad = $r15_valid_record;
$r15_bad['expected_version'] = -3;
assert_future( false === GCU_Future_Policy::validate_future_record_input_types( $r15_bad )['safe'], 'R15 negative row version must be rejected, not passed through absint.' );
$r15_bad = $r15_valid_record;
$r15_bad['is_public'] = 'yes';
assert_future( false === GCU_Future_Policy::validate_future_record_input_types( $r15_bad )['safe'], 'R15 arbitrary nonempty text cannot mark record publicly visible.' );
assert_future( GCU_Future_Policy::validate_revalidation_input_types( 'zero_commission', 'Review authoritative claim evidence.', '2' ), 'R15 valid claim revalidation remains allowed.' );
assert_future( ! GCU_Future_Policy::validate_revalidation_input_types( array( 'zero_commission' ), 'Review reason', 1 ), 'R15 arrays cannot identify an approved claim.' );
assert_future( ! GCU_Future_Policy::validate_revalidation_input_types( 'zero_commission', array( 'review reason' ), 1 ), 'R15 nested reason cannot enter audited claim revalidation.' );
assert_future( ! GCU_Future_Policy::validate_revalidation_input_types( 'zero_commission', 'Review reason', -2 ), 'R15 invalid negative row version cannot be coerced to a positive expected version.' );
assert_future( ! GCU_Future_Policy::validate_revalidation_input_types( 'zero_commission', 'Review reason', array( 2 ) ), 'R15 nested row version must fail closed.' );
$r15_future_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
assert_future( false !== strpos( $r15_future_source, 'gcu_future_record_input_type_invalid' ), 'R15 REST governed record writer must return explicit bad-input response.' );
assert_future( false !== strpos( $r15_future_source, 'gcu_future_revalidation_input_type_invalid' ), 'R15 REST claim revalidation must reject nested types before side effects.' );

// F14-FUT-16/F14-FUT-17 experiment safety must reject unstructured
// sampling/privacy policy rather than converting a JSON array into "Array".
$r15_guardrails = array( 'claim_integrity' => true, 'privacy' => true, 'accessibility' => true, 'error_rate' => true, 'complaints' => true );
$r15_invalid_experiment = GCU_Future_Policy::experiment_preflight( array( 'Neutral CTA', 'Approved title' ), $r15_guardrails, array( 'health profiling' ), array( 'minor profiling' ) );
assert_future( false === $r15_invalid_experiment['safe'] && in_array( 'invalid_sampling_policy_type', $r15_invalid_experiment['flags'], true ), 'R15 nested sample/privacy policies may never pass ethical experiment preflight.' );
$r15_invalid_variant = GCU_Future_Policy::experiment_preflight( array( (object) array( 'title' => 'Unsafe' ), 'Safe title' ), $r15_guardrails, 'consented aggregate', 'no profiling' );
assert_future( false === $r15_invalid_variant['safe'] && in_array( 'invalid_variant_type', $r15_invalid_variant['flags'], true ), 'R15 unsupported experiment variant object must be rejected, not cast into warnings.' );
$r15_good_experiment = GCU_Future_Policy::experiment_preflight( array( 'Neutral CTA', 'Approved title' ), $r15_guardrails, 'consented aggregate', 'no profiling' );
assert_future( true === $r15_good_experiment['safe'], 'R15 normal approved text experiments remain eligible for preflight only (not activation).' );


// R16-D01 (FUT16 / approved-copy safety): no nested JSON field may be
// silently coerced into ethical title, CTA, body or prior reviewed copy.
$r16_good_copy = array( 'title' => 'Review doctors', 'body' => 'Read public profiles carefully.', 'cta_label' => 'Continue' );
assert_future( GCU_Future_Policy::copy_preflight( $r16_good_copy, $r16_good_copy )['safe'], 'R16 unchanged textual source-backed copy remains eligible for review.' );
foreach ( array( 'title', 'body', 'cta_label' ) as $field ) {
    $r16_bad_copy = $r16_good_copy;
    $r16_bad_copy[ $field ] = array( 'nested' => 'deceptive copy' );
    $r16_check = GCU_Future_Policy::copy_preflight( $r16_bad_copy );
    assert_future( ! $r16_check['safe'] && in_array( 'invalid_copy_field_type:current:' . $field, $r16_check['flags'], true ), 'R16 current malformed copy field must fail before sanitization: ' . $field );
    $r16_old = $r16_good_copy;
    $r16_old[ $field ] = (object) array( 'nested' => 'prior copy' );
    $r16_check = GCU_Future_Policy::copy_preflight( $r16_good_copy, $r16_old );
    assert_future( ! $r16_check['safe'] && in_array( 'invalid_copy_field_type:previous:' . $field, $r16_check['flags'], true ), 'R16 prior nested copy cannot bypass meaning-change review: ' . $field );
}

// R16-D02 (FUT16): guardrail presence is not evidence of an active control.
$r16_guards = array( 'claim_integrity' => true, 'privacy' => true, 'accessibility' => true, 'error_rate' => 0, 'complaints' => 'review queue monitored' );
assert_future( GCU_Future_Policy::experiment_preflight( array( 'A', 'B' ), $r16_guards, 'aggregate adult cohort', 'consented aggregate' )['safe'], 'R16 enabled controls and a zero-error numeric threshold remain valid.' );
foreach ( array( false, null, '', 'false', 'disabled', array( 'ignored' ), (object) array( 'enabled' => true ) ) as $bad_guard ) {
    $r16_invalid = $r16_guards;
    $r16_invalid['privacy'] = $bad_guard;
    $r16_check = GCU_Future_Policy::experiment_preflight( array( 'A', 'B' ), $r16_invalid, 'aggregate adult cohort', 'consented aggregate' );
    assert_future( ! $r16_check['safe'] && in_array( 'invalid_guardrail:privacy', $r16_check['flags'], true ), 'R16 missing/disabled privacy safeguards cannot be treated as approved.');
}

// R16-D03 (FUT11): percentage inputs must be evidence on the 0–100 scale.
$r16_valid_measurement = GCU_Future_Policy::quality_evidence_status( 30, 4, 0, 100, true );
assert_future( $r16_valid_measurement['complete'], 'R16 valid measured boundary values remain eligible if genuine owner evidence is attested.' );
$r16_low_access = GCU_Future_Policy::quality_evidence_status( 30, 4, -2, 95, true );
assert_future( ! $r16_low_access['complete'] && in_array( 'accessibility_measurement_unavailable', $r16_low_access['missing'], true ), 'R16 negative accessibility is not valid scoring evidence.' );
$r16_high_perf = GCU_Future_Policy::quality_evidence_status( 30, 4, 95, 120, true );
assert_future( ! $r16_high_perf['complete'] && in_array( 'performance_measurement_unavailable', $r16_high_perf['missing'], true ), 'R16 performance above 100 is not valid measured evidence.' );
$r16_unknown_perf = GCU_Future_Policy::quality_evidence_status( 30, 4, 93, array(100), true );
assert_future( ! $r16_unknown_perf['complete'], 'R16 structured data cannot become performance evidence.' );


// R17 / FUT-21: an editor's base/objective or provider candidate must be real
// text, not coerced PHP "Array" or arbitrary object-generated claims.
assert_future( GCU_Future_Policy::valid_ai_copy_inputs( array( 'base_text' => 'Read our actual policy.', 'objective' => 'clarity' ) ), 'R17 valid reviewed AI editor inputs remain accepted.' );
assert_future( ! GCU_Future_Policy::valid_ai_copy_inputs( array( 'base_text' => array( 'invented claim' ), 'objective' => 'trust' ) ), 'R17 nested base_text cannot become a provider drafting instruction.' );
assert_future( ! GCU_Future_Policy::valid_ai_copy_inputs( array( 'base_text' => 'Safe source text', 'objective' => array( 'trust' ) ) ), 'R17 nested objective cannot become approved editing intent.' );
assert_future( ! GCU_Future_Policy::valid_ai_copy_inputs( array( 'base_text' => 'Safe source text', 'objective' => 'automatic_publish' ) ), 'R17 unapproved AI objective cannot bypass constrained drafting mode.' );
assert_future( 'Verified profile only.' === GCU_Future_Policy::normalize_ai_candidate_text( array( 'text' => '<b>Verified profile only.</b>' ) ), 'R17 typed provider suggestions retain safe plain text.' );
assert_future( null === GCU_Future_Policy::normalize_ai_candidate_text( array( 'text' => array( 'unverified' ) ) ), 'R17 nested provider candidate cannot become a textual medical or financial claim.' );
assert_future( null === GCU_Future_Policy::normalize_ai_candidate_text( (object) array( 'text' => 'unsupported' ) ), 'R17 object provider suggestions rejected.' );

// R17 / FUT-11: percentages are measured only in [0..100]; a stray
// plugin/filter cannot report negative/overflow privacy as verified.
foreach ( array( 0, 100, 95.5, '75' ) as $metric ) {
    assert_future( GCU_Future_Policy::valid_quality_percentage( $metric ), 'R17 ordinary bounded percentage remains valid.' );
}
foreach ( array( -1, 101, '1e309', false, true, array( 95 ), 'n/a', null ) as $metric ) {
    assert_future( ! GCU_Future_Policy::valid_quality_percentage( $metric ), 'R17 nonfinite, invalid, out-of-range or nested quality metric must not prove evidence.' );
}
assert_future( ! GCU_Future_Policy::quality_evidence_status( 30, 4, 101, 95, true )['complete'], 'R17 range validation applies to accessibility quality completeness.' );
$r17_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
assert_future( false !== strpos( $r17_source, 'valid_quality_percentage( $privacy_effectiveness )' ) && false !== strpos( $r17_source, "'privacy_verified' => GCU_Future_Policy::valid_quality_percentage" ), 'R17 public quality/verified privacy status must use strict measured scale.' );

// R17 / FUT-18 and Small-Cohort Privacy Guard: no absolute-value promotion of
// negative counts; question must be text, actual positive aggregate >= floor.
assert_future( GCU_Future_Policy::valid_faq_aggregate_signal( array( 'question' => 'How do I choose a qualified doctor?', 'count' => 25 ) ), 'R17 genuine sufficient approved aggregate remains eligible.' );
assert_future( ! GCU_Future_Policy::valid_faq_aggregate_signal( array( 'question' => 'Sensitive question', 'count' => -25 ) ), 'R17 negative cohort count cannot turn into 25 through absint().' );
assert_future( ! GCU_Future_Policy::valid_faq_aggregate_signal( array( 'question' => array( 'patient text' ), 'count' => 25 ) ), 'R17 nested or private question cannot be coerced into aggregate FAQ suggestion.' );
assert_future( ! GCU_Future_Policy::valid_faq_aggregate_signal( array( 'question' => 'A rare question', 'count' => 2 ) ), 'R17 low-frequency question must remain privacy-suppressed.' );
assert_future( ! GCU_Future_Policy::valid_faq_aggregate_signal( array( 'question' => 'Unsupported floating sample', 'count' => 25.7 ) ), 'R17 fabricated/fractional question count rejected.' );
assert_future( false === strpos( $r17_source, 'absint( $signal[' . "'count'" . '] )' ), 'R17 source no longer transforms negative question frequency to a positive public cohort.' );
assert_future( false !== strpos( $r17_source, 'gcu_future_ai_input_type_invalid' ), 'R17 REST AI draft handler returns controlled bad-input error.' );


// R18 / FUT03: even a public REST request may inject arrays in preference
// to route path parameters. Claim lookup accepts only shaped scalar claim IDs.
assert_future( 'zero_commission' === GCU_Future_Policy::safe_public_claim_key( 'zero_commission' ), 'R18 genuine public trust lookup retains canonical key.' );
foreach ( array( array( 'zero_commission' ), (object) array( 'claim_key' => 'zero_commission' ), -5, true, '', 'CLAIM', 'zero commission' ) as $wrong_claim ) {
    assert_future( '' === GCU_Future_Policy::safe_public_claim_key( $wrong_claim ), 'R18 malformed public claim key must fail closed.' );
}

// R18 / FUT22: editor record/report list params are exact typed identifiers,
// never cast nested JSON or negative pagination to valid positive cursors.
$r18_default_records = GCU_Future_Policy::validate_future_read_query( array(), 'records' );
assert_future( $r18_default_records['safe'] && 50 === $r18_default_records['limit'] && 0 === $r18_default_records['cursor'] && '' === $r18_default_records['type'], 'R18 records query still defaults to first page.' );
$r18_valid_reports = GCU_Future_Policy::validate_future_read_query( array( 'status'=>'reviewing', 'limit'=>'25', 'cursor'=>'9' ), 'reports' );
assert_future( $r18_valid_reports['safe'] && 25 === $r18_valid_reports['limit'] && 9 === $r18_valid_reports['cursor'] && 'reviewing' === $r18_valid_reports['status'], 'R18 approved versioned complaint list remains accessible.' );
foreach ( array( 'limit', 'cursor' ) as $field ) {
    foreach ( array( -5, array( 5 ), (object) array( 'x' => 5 ), '1.5' ) as $bad_value ) {
        $r18_bad = GCU_Future_Policy::validate_future_read_query( array( $field => $bad_value ), 'records' );
        assert_future( ! $r18_bad['safe'] && in_array( $field, $r18_bad['invalid_fields'], true ), 'R18 malformed pagination cannot silently become a different cursor/page.' );
    }
}
$r18_bad = GCU_Future_Policy::validate_future_read_query( array( 'type' => array( 'ai_draft' ) ), 'records' );
assert_future( ! $r18_bad['safe'] && in_array( 'type', $r18_bad['invalid_fields'], true ), 'R18 nested record type must not leak an unfiltered listing.' );
$r18_bad = GCU_Future_Policy::validate_future_read_query( array( 'status' => 'unknown' ), 'reports' );
assert_future( ! $r18_bad['safe'] && in_array( 'status', $r18_bad['invalid_fields'], true ), 'R18 unsupported report state cannot silently list open complaints.' );
$r18_bad = GCU_Future_Policy::validate_future_read_query( array( 'status' => array( 'open' ) ), 'reports' );
assert_future( ! $r18_bad['safe'] && in_array( 'status', $r18_bad['invalid_fields'], true ), 'R18 nested report status is invalid.' );
assert_future( 100 === GCU_Future_Policy::bounded_query_integer( '180', 50, 100 ), 'R18 positive large limit remains bounded to safe max.' );

// R18 / FUT13: privacy analytics reporting days must be a positive scalar,
// not abs(-n) or accidentally substituted nested JSON.
assert_future( 30 === GCU_Future_Policy::bounded_query_integer( null, 30, 90 ), 'R18 friction reports retain documented default range.' );
assert_future( 90 === GCU_Future_Policy::bounded_query_integer( '250', 30, 90 ), 'R18 long but valid window is bounded safely.' );
foreach ( array( -30, ' -30', '-30', 0, array( 30 ), true, '1e2', '' ) as $invalid_days ) {
    assert_future( null === GCU_Future_Policy::bounded_query_integer( $invalid_days, 30, 90 ), 'R18 malformed/negative days cannot become a real aggregation period.' );
}
$r18_source = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
foreach ( array( 'gcu_future_claim_query_invalid', 'gcu_future_records_query_invalid', 'gcu_future_reports_query_invalid', 'gcu_future_friction_window_invalid' ) as $error_code ) {
    assert_future( false !== strpos( $r18_source, $error_code ), 'R18 REST endpoint must enforce typed query before owner read: ' . $error_code );
}

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
