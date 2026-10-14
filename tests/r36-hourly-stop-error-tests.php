<?php
/**
 * R36 forty runtime-simulation cases for required experiment stop failures.
 * Uses File14's REAL GCU_Future_Intelligence::hourly_intelligence() method.
 * Not actual WP cron / DB / Staging integration.
 */
define( 'ABSPATH', __DIR__ . '/' );
function __( $s, $domain = '' ) { return $s; }
function sanitize_key( $s ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $s ) ); }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
class WP_Error {
    private $code;
    public function __construct( $code, $message = '', $data = array() ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
class GCU_Install {
    public static function ready_for_runtime() { return true; }
}
class GCU_Future_Policy {
    public static function owner_confirmation_contract_ready() { return false; }
}
class GCU_Fifth_Review_Hardening {
    public static $next;
    public static function transactional_early_stop_guard() { return self::$next; }
}
class GCU_Observability {
    public static $logs = array();
    public static function log( $level, $event, $properties ) {
        self::$logs[] = array( 'level' => $level, 'event' => $event, 'properties' => $properties );
    }
}
function get_option( $key, $default = false ) {
    return 'gcu_future_schema_version' === $key ? 1 : $default;
}
function update_option( $key, $value, $autoload = false ) { return true; }
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php';
$failures = array();
for ( $i = 1; $i <= 40; $i++ ) {
    GCU_Observability::$logs = array();
    $error_mode = $i % 2 === 0;
    GCU_Fifth_Review_Hardening::$next = $error_mode
        ? new WP_Error( 'gcu_r36_safety_failure_' . $i )
        : $i;
    $observed = GCU_Future_Intelligence::hourly_intelligence();
    $logs = GCU_Observability::$logs;
    $pass = $error_mode
        ? is_wp_error( $observed )
            && $observed->get_error_code() === 'gcu_r36_safety_failure_' . $i
            && count( $logs ) === 1
            && $logs[0]['event'] === 'experiment_stop_guard_failed'
            && $logs[0]['properties']['origin'] === 'hourly_intelligence'
            && $logs[0]['properties']['code'] === 'gcu_r36_safety_failure_' . $i
        : $observed === $i && count( $logs ) === 0;
    if ( ! $pass ) { $failures[] = 'hourly stop case ' . $i . ' lost result or warning'; }
    echo sprintf( 'R36 stop guard %02d/40: %s — %s' . PHP_EOL, $i, $pass ? 'PASS' : 'FAIL', $error_mode ? 'error propagated + alert logged' : 'safe result forwarded' );
}
$src = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
if ( false === strpos( $src, "return self::checked_experiment_stop( 'business_policy_changed' );" ) ||
    false === strpos( $src, "'gcu_experiment_stop_guard_missing'" ) ) {
    $failures[] = 'Scheduled policy change must forward errors and missing stop-guard cannot pretend success.';
}
if ( $failures ) { fwrite( STDERR, implode( PHP_EOL, $failures ) . PHP_EOL ); exit( 1 ); }
echo "R36 stop guard: forty behavioral simulations PASS (20 errors propagated and audited; 20 successful results); no live claim." . PHP_EOL;
