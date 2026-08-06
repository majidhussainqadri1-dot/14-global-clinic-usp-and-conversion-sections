<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'DAY_IN_SECONDS', 86400 );

function __( $text ) { return $text; }
function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) ); }
function wp_unslash( $value ) { return $value; }
function esc_url_raw( $value ) { return filter_var( $value, FILTER_SANITIZE_URL ); }
function home_url( $path = '/' ) { return 'https://sabrihomeopathy.com' . $path; }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function apply_filters( $tag, $value ) { return $value; }

require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-policy.php';

$failures = array();
function assert_true( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures[] = $message;
	}
}

$rules = GCU_Policy::business_rules();
assert_true( 0 === $rules['platform_commission_percent'], 'Platform commission must be exactly 0%.' );
assert_true( 'free' === $rules['approved_core_tier'], 'Approved core tier must be free.' );
assert_true( false === $rules['support_affects_visibility'], 'Support must not affect visibility.' );
assert_true( false === $rules['instant_doctor_approval'], 'Doctor approval must not be instant.' );
assert_true( false === $rules['cure_guarantee'], 'Cure guarantees must remain prohibited.' );

assert_true( GCU_Policy::transition_allowed( 'copy', 'draft', 'policy_review' ), 'Draft must move to policy review.' );
assert_true( ! GCU_Policy::transition_allowed( 'copy', 'draft', 'active' ), 'Draft must not jump directly to active.' );
assert_true( GCU_Policy::transition_allowed( 'placement', 'preview', 'active' ), 'Preview placement may activate.' );
assert_true( ! GCU_Policy::transition_allowed( 'experiment', 'proposed', 'running' ), 'Experiment may not skip approval.' );
assert_true( GCU_Policy::transition_allowed( 'experiment', 'analyzed', 'adopted' ), 'Analyzed experiment may be adopted.' );

assert_true( 'ur-PK' === GCU_Policy::sanitize_locale( 'ur_PK' ), 'Urdu locale normalization failed.' );
assert_true( 'en-US' === GCU_Policy::sanitize_locale( 'not a locale' ), 'Invalid locale must fall back safely.' );
assert_true( 'doctor' === GCU_Policy::sanitize_audience( 'doctor' ), 'Doctor audience must be accepted.' );
assert_true( 'all' === GCU_Policy::sanitize_audience( 'administrator' ), 'Unknown audience must fail to all.' );
assert_true( '' === GCU_Policy::same_origin_url( 'https://evil.example/path' ), 'Cross-origin URL must be rejected.' );
assert_true( 'https://sabrihomeopathy.com/path' === GCU_Policy::same_origin_url( 'https://sabrihomeopathy.com/path' ), 'Same-origin URL must be accepted.' );

$claims = GCU_Policy::canonical_claims();
foreach ( array( 'zero_platform_commission', 'free_approved_core', 'optional_support_no_ranking', 'verification_required', 'no_emergency_service', 'no_cure_guarantee' ) as $key ) {
	assert_true( isset( $claims[ $key ] ), 'Missing canonical claim: ' . $key );
}

$blocks = GCU_Policy::canonical_blocks();
assert_true( 'doctor_directory' === $blocks['patient_hero']['destination'], 'Patient CTA must target File 07.' );
assert_true( 'doctor_onboarding' === $blocks['doctor_hero']['destination'], 'Doctor CTA must target File 09.' );
assert_true( in_array( 'zero_platform_commission', $blocks['doctor_hero']['claim_keys'], true ), 'Doctor block must cite zero commission.' );

if ( $failures ) {
	fwrite( STDERR, "Policy tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Policy tests: PASS\n";
