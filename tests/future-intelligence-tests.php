<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'DAY_IN_SECONDS', 86400 );

function __( $text ) { return $text; }
function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) ); }
function wp_strip_all_tags( $value ) { return strip_tags( (string) $value ); }
function wp_json_encode( $value ) { return json_encode( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); }
function absint( $value ) { return abs( (int) $value ); }

require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-policy.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-i18n.php';

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

$score = GCU_Future_Policy::conversion_quality_score( array( 'handoff_success' => 100, 'accessibility' => 100, 'claim_freshness' => 100, 'privacy' => 100, 'complaint_health' => 100, 'destination_health' => 100, 'performance' => 100 ) );
assert_future( 100.0 === $score, 'Perfect conversion quality inputs must score 100.' );
$low_score = GCU_Future_Policy::conversion_quality_score( array( 'handoff_success' => -5, 'privacy' => 150 ) );
assert_future( $low_score >= 0 && $low_score <= 100, 'Conversion quality score must be bounded.' );

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
