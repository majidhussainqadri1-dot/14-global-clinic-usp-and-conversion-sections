<?php

define( 'ABSPATH', __DIR__ . '/' );

function wp_strip_all_tags( $value ) { return strip_tags( (string) $value ); }
function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function absint( $value ) { return abs( (int) $value ); }

require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-review80-hardening.php';

$failures = array();
function assert_review80( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures[] = $message;
	}
}

$valid = array(
	'claim_integrity' => true,
	'privacy' => true,
	'accessibility' => true,
	'error_rate' => 0,
	'complaints' => array( 'max_rate' => 0.02 ),
);
assert_review80( true === GCU_Review80_Hardening::guardrails_valid( $valid ), 'Complete meaningful guardrails must pass.' );
$missing = $valid;
unset( $missing['privacy'] );
assert_review80( false === GCU_Review80_Hardening::guardrails_valid( $missing ), 'Missing privacy guardrail must fail.' );
$disabled = $valid;
$disabled['accessibility'] = false;
assert_review80( false === GCU_Review80_Hardening::guardrails_valid( $disabled ), 'Explicitly disabled mandatory guardrail must fail.' );

$ur = GCU_Review80_Hardening::multilingual_dark_pattern_scan( 'صرف آج درخواست دیں، شفا کی ضمانت حاصل کریں۔' );
assert_review80( false === $ur['safe'] && in_array( 'fake_scarcity_ur', $ur['flags'], true ) && in_array( 'guarantee_ur', $ur['flags'], true ), 'Urdu scarcity/guarantee copy must be blocked.' );
$ar = GCU_Review80_Hardening::multilingual_dark_pattern_scan( 'الفرصة الأخيرة، شفاء مضمون.' );
assert_review80( false === $ar['safe'] && in_array( 'fake_scarcity_ar', $ar['flags'], true ) && in_array( 'guarantee_ar', $ar['flags'], true ), 'Arabic scarcity/guarantee copy must be blocked.' );
$commission = GCU_Review80_Hardening::multilingual_dark_pattern_scan( 'کلینک پر 10 فیصد کمیشن ہوگا۔' );
assert_review80( false === $commission['safe'] && in_array( 'positive_commission_ur', $commission['flags'], true ), 'Positive Urdu commission drift must be blocked.' );
$safe = GCU_Review80_Hardening::multilingual_dark_pattern_scan( 'پلیٹ فارم کمیشن صفر فیصد ہے اور رضاکارانہ تعاون سے درجہ بندی نہیں خریدی جاسکتی۔' );
assert_review80( true === $safe['safe'], 'Truthful Urdu no-advantage copy must pass the narrow multilingual guard.' );

assert_review80( true === GCU_Review80_Hardening::question_contains_sensitive_data( 'My email is person@example.com; why is this FAQ unclear?' ), 'Email-bearing aggregate question must be rejected.' );
assert_review80( true === GCU_Review80_Hardening::question_contains_sensitive_data( 'Patient ID 12345 appears on this page.' ), 'Patient-record marker must be rejected.' );
assert_review80( false === GCU_Review80_Hardening::question_contains_sensitive_data( 'How does doctor verification work?' ), 'Ordinary aggregate FAQ question should pass.' );

$friction = GCU_Review80_Hardening::sanitize_friction_payload( array(
	'suppressed' => false,
	'stages' => array( 'impression' => 30, 'cta_selected' => 12, 'destination_loaded' => 4, 'application_started' => 2 ),
	'dropoffs' => array( 'cta_selected' => 60.0, 'destination_loaded' => 66.7, 'application_started' => 50.0 ),
) );
assert_review80( null === $friction['stages']['destination_loaded'], 'Sub-threshold stage count must be suppressed.' );
assert_review80( null === $friction['stages']['application_started'], 'Every sub-threshold stage count must be suppressed.' );
assert_review80( ! isset( $friction['dropoffs']['destination_loaded'] ) && ! isset( $friction['dropoffs']['application_started'] ), 'Drop-offs based on small stages must be suppressed.' );
assert_review80( in_array( 'destination_loaded', $friction['suppressed_stages'], true ), 'Suppressed stage list must be explicit.' );

$scenario = GCU_Review80_Hardening::normalize_scenario_payload(
	array( 'scenarios' => array( array( 'key' => 'safe_mode', 'enabled' => true ), array( 'key' => 'mobile_320', 'viewport' => 320 ) ) ),
	false,
	true
);
assert_review80( false === $scenario['scenarios'][0]['enabled'], 'Scenario safe_mode must reflect the Future CTI safe-mode option, not module enabled state.' );
assert_review80( true === $scenario['scenarios'][0]['module_enabled'], 'Scenario must expose module-enabled state separately.' );

if ( $failures ) {
	fwrite( STDERR, "Review80 hardening tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Review80 hardening tests: PASS\n";
