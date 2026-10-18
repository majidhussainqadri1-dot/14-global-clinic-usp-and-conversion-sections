<?php
/**
 * Behavioral proof for File08/File14 owner-outcome boundary:
 * real GCU_Hardening::is_sensitive_path + real GCU_Privacy::measurement_allowed.
 * Isolated PHP only; does not assert production deployments or stored events.
 */
define( 'ABSPATH', __DIR__ . '/' );
function wp_unslash( $x ) { return $x; }
function wp_parse_url( $x, $part = -1 ) { return parse_url( $x, $part ); }
function trailingslashit( $x ) { return rtrim( $x, '/' ) . '/'; }
class GCU_Policy {
    public static function analytics_consent() { return true; }
}
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-hardening.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-privacy.php';

function gcu_check_route( $name, $path, $rest_query, $sensitive ) {
    $_SERVER['REQUEST_URI'] = $path;
    unset( $_GET['rest_route'], $_SERVER['HTTP_SEC_GPC'], $_SERVER['HTTP_SAVE_DATA'] );
    if ( null !== $rest_query ) { $_GET['rest_route'] = $rest_query; }
    $actual = GCU_Hardening::is_sensitive_path();
    if ( $actual !== $sensitive ) {
        fwrite( STDERR, "File14 REST sensitivity regression: {$name}; expected " . (int) $sensitive . ', got ' . (int) $actual . PHP_EOL );
        exit( 1 );
    }
    // Explicit acquisition consent must never override a sensitive route.
    $measured = GCU_Privacy::measurement_allowed();
    if ( $measured === $sensitive ) {
        fwrite( STDERR, "File14 owner-event consent boundary regression: {$name}" . PHP_EOL );
        exit( 1 );
    }
}
gcu_check_route( 'public clinic remains optional-analytics eligible', '/global-clinic/', null, false );
gcu_check_route( 'existing page booking guard', '/appointments/book/doctor/', null, true );
gcu_check_route( 'canonical File08 REST booking POST', '/wp-json/wca/v1/appointments', null, true );
gcu_check_route( 'nested/index.php File08 REST path', '/index.php/wp-json/wca/v1/appointments', null, true );
gcu_check_route( 'File09 applicant REST path', '/wp-json/gdo/v1/applications', null, true );
gcu_check_route( 'clinical REST path', '/wp-json/cf01/v1/encounters', null, true );
gcu_check_route( 'support-case REST path', '/wp-json/cf02/v1/cases', null, true );
gcu_check_route( 'nonpretty REST booking', '/?rest_route=%2Fwca%2Fv1%2Fappointments', '/wca/v1/appointments', true );
gcu_check_route( 'nonpretty onboarding', '/index.php?rest_route=/gdo/v1/applications', '/gdo/v1/applications', true );
gcu_check_route( 'encoded nonpretty route', '/?rest_route=%2Fwca%2Fv1%2Fappointments', '%2Fwca%2Fv1%2Fappointments', true );
gcu_check_route( 'malformed array route fails closed', '/?rest_route[]=wca', array( 'wca' ), true );
gcu_check_route( 'unrelated WP REST path not mislabeled', '/wp-json/wp/v2/posts', null, false );
gcu_check_route( 'unrelated File14 public REST path not mislabeled', '/wp-json/gcu/v1/public', null, false );
$_SERVER['REQUEST_URI'] = '/global-clinic/';
unset( $_GET['rest_route'] );
$_SERVER['HTTP_SEC_GPC'] = '1';
if ( GCU_Privacy::measurement_allowed() ) {
    fwrite( STDERR, "File14 GPC must deny optional measurement even on public route\n" );
    exit( 1 );
}
echo "File14/File08 REST sensitivity and optional measurement: PASS 14/14 (isolated PHP)\n";
