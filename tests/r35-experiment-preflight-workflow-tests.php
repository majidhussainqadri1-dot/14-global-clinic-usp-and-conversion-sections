<?php
/**
 * File14 R35: forty DIFFERENT workflow approval/activation scenarios.
 * Real GCU_Repository::validate_transition_target and real FUT-16 preflight.
 * A WordPress-free policy harness, NOT production WordPress E2E.
 */
define( 'ABSPATH', __DIR__ . '/' );
function __( $s, $domain = '' ) { return $s; }
function sanitize_key( $s ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $s ) ); }
function absint( $x ) { return abs( (int) $x ); }
function wp_json_encode( $x ) { return json_encode( $x ); }
function wp_strip_all_tags( $s ) { return strip_tags( (string) $s ); }
function get_option( $k, $fallback = false ) { global $r35_paused; return 'gcu_experiment_serving_paused' === $k ? $r35_paused : $fallback; }
function is_wp_error( $x ) { return $x instanceof WP_Error; }
class WP_Error {
    private $code;
    public function __construct( $code, $message = '', $data = array() ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
class GCU_Install {
    public static function tables() { return array( 'blocks' => 'wp_gcu_blocks', 'experiments' => 'wp_gcu_experiments' ); }
}
class GCU_Future_Intelligence {
    public static $parity = true;
    public static function parity_status() { return array( 'ok' => self::$parity ); }
}
class GCU_Experiment_Baseline {
    public static function ready( $row ) { return true; }
}
class GCU_Policy {}
class GCU_Capabilities {}
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-repository.php';

if ( ! defined( 'DAY_IN_SECONDS' ) ) { define( 'DAY_IN_SECONDS', 86400 ); }
$guards = array( 'claim_integrity' => true, 'privacy' => true, 'accessibility' => true, 'error_rate' => 0, 'complaints' => 0 );
$valid = array(
    'variants' => json_encode( array( 'Review doctors', 'Read clinic information' ) ),
    'guardrails' => json_encode( $guards ),
    'sample_policy' => 'consented adult aggregate sample',
    'privacy_policy' => 'opt-in aggregate only; never patient level',
    'status' => 'proposed', 'ends_at' => gmdate( 'Y-m-d H:i:s', time() + 2 * DAY_IN_SECONDS ),
    'row_version' => 1
);
$cases = array();
// Eight accepted scenarios establish that protective gating does not block
// genuine founder-approved neutral experiments or zero-error ceilings.
for ( $i = 0; $i < 8; $i++ ) {
    $x = $valid;
    $x['variants'] = json_encode( array( 'Neutral information ' . $i, 'Transparent information ' . $i ) );
    if ( $i % 2 ) { $x['guardrails'] = json_encode( array_merge( $guards, array( 'complaints' => $i ) ) ); }
    $cases[] = array( "neutral accepted $i", $x, $i % 2 ? 'running' : 'approved', true, true, false );
}
// Eight independent forbidden/disabled mandatory guardrail shapes.
$disabled = array(
    array( 'privacy', false ), array( 'privacy', 0 ),
    array( 'privacy', '0' ), array( 'privacy', 'off' ),
    array( 'claim_integrity', 0 ), array( 'claim_integrity', false ),
    array( 'accessibility', 0 ), array( 'accessibility', '' )
);
foreach ( $disabled as $i => $pair ) {
    $x = $valid; $g = $guards; $g[ $pair[0] ] = $pair[1];
    $x['guardrails'] = json_encode( $g );
    $cases[] = array( "disabled $i", $x, $i % 2 ? 'running' : 'approved', false, true, false );
}
// Missing checks may not pass on either lifecycle edge.
$keys = array_keys( $guards );
for ( $i = 0; $i < 8; $i++ ) {
    $x = $valid; $g = $guards; unset( $g[ $keys[ $i % count( $keys ) ] ] );
    $x['guardrails'] = json_encode( $g );
    $cases[] = array( "missing safeguard $i", $x, $i % 2 ? 'running' : 'approved', false, true, false );
}
// No dark patterns or health/identity profiling on approved/running variants.
$unsafe = array(
    array( 'variants', json_encode( array( 'Last chance', 'Neutral' ) ) ),
    array( 'variants', json_encode( array( 'Guaranteed cure', 'Neutral' ) ) ),
    array( 'variants', json_encode( array( 'Accept all tracking', 'Neutral' ) ) ),
    array( 'variants', json_encode( array( 'Donate for priority ranking', 'Neutral' ) ) ),
    array( 'sample_policy', 'health profiling' ),
    array( 'sample_policy', 'minor profiling' ),
    array( 'privacy_policy', 'patient profiling' ),
    array( 'privacy_policy', 'identity evidence targeting' )
);
foreach ( $unsafe as $i => $edit ) {
    $x = $valid; $x[ $edit[0] ] = $edit[1];
    $cases[] = array( "prohibited pattern $i", $x, $i % 2 ? 'running' : 'approved', false, true, false );
}
// Remaining eight verify runtime parity, pause, date and malformed payloads.
for ( $i = 0; $i < 8; $i++ ) {
    $x = $valid; $parity = true; $pause = false; $expect = false;
    switch ( $i ) {
        case 0: $parity = false; break;
        case 1: $pause = true; break;
        case 2: $x['ends_at'] = gmdate( 'Y-m-d H:i:s', time() - 60 ); break;
        case 3: $x['ends_at'] = gmdate( 'Y-m-d H:i:s', time() + 110 * DAY_IN_SECONDS ); break;
        case 4: $x['variants'] = json_encode( array( 'Single' ) ); break;
        case 5: $x['variants'] = '{malformed'; break;
        case 6: $x['guardrails'] = '{malformed'; break;
        case 7: $x['sample_policy'] = 'diagnosis targeting'; break;
    }
    $cases[] = array( "runtime/date malformed $i", $x, 'running', $expect, $parity, $pause );
}
if ( 40 !== count( $cases ) ) { fwrite( STDERR, 'Not forty cases' ); exit( 2 ); }
$method = new ReflectionMethod( 'GCU_Repository', 'validate_transition_target' );
$method->setAccessible( true );
$repo = new GCU_Repository();
$errors = array();
foreach ( $cases as $i => $case ) {
    $r35_paused = $case[5]; GCU_Future_Intelligence::$parity = $case[4];
    $outcome = $method->invoke( $repo, 'experiment', $case[1], $case[2] );
    $safe = ! is_wp_error( $outcome );
    if ( $safe !== $case[3] ) { $errors[] = 'Case ' . ( $i + 1 ) . ': ' . $case[0] . ' returned ' . ( is_wp_error($outcome) ? $outcome->get_error_code() : 'allowed' ); }
    echo sprintf( 'R35 Workflow preflight %02d/40: %s — %s' . PHP_EOL, $i + 1, $safe === $case[3] ? 'PASS' : 'FAIL', $case[0] );
}
if ( $errors ) { fwrite( STDERR, implode( PHP_EOL, $errors ) . PHP_EOL ); exit( 1 ); }
echo "R35: all forty independent workflow approval/activation cases passed (8 allowed; 32 forbidden). Source-mode, no WP staging acceptance." . PHP_EOL;
