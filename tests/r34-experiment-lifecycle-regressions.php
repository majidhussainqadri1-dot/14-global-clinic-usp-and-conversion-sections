<?php
/** R34 source-behavior harness: 40 independent constructed WordPress-free
 * experiment lifecycle scenarios. NO WordPress DB/browser/live parity claim.
 */
define( 'ABSPATH', __DIR__ . '/' );
define( 'ARRAY_A', 'ARRAY_A' );
function __( $s, $domain = '' ) { return $s; }
function sanitize_key( $s ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $s ) ); }
function current_time( $which, $gmt = false ) { return '2026-10-13 15:59:00'; }
function get_option( $key, $default = false ) { global $r34_opts; return array_key_exists( $key, $r34_opts ) ? $r34_opts[ $key ] : $default; }
function update_option( $key, $value, $autoload = false ) { global $r34_opts; $r34_opts[ $key ] = $value; return true; }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
class WP_Error {
    private $code;
    public function __construct( $code, $message = '', $metadata = array() ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
class R34DB {
    public $expired = 0;
    public $fail_check = false;
    public $include_running = true;
    public $rows = array();
    public $read_sql = array();
    public function get_var( $sql ) {
        $this->read_sql[] = $sql;
        if ( false !== strpos( $sql, 'gcu_future_reports' ) ) { return 0; }
        if ( false !== strpos( $sql, 'COUNT(*)' ) && false !== strpos( $sql, 'gcu_experiments' ) ) {
            return $this->fail_check ? false : $this->expired;
        }
        return 0;
    }
    public function get_results( $sql, $mode = ARRAY_A ) {
        $this->read_sql[] = $sql;
        if ( ! $this->include_running ) { return array(); }
        if ( false !== strpos( $sql, ' AND (ends_at IS NULL OR ends_at<=UTC_TIMESTAMP())' ) && 0 === $this->expired ) { return array(); }
        return $this->rows;
    }
    public function prepare( $sql ) { return $sql; }
    public function query( $sql ) { return 1; }
}
class GCU_Install {
    public static function tables() { return array( 'experiments'=>'wp_gcu_experiments', 'placements'=>'wp_gcu_placements' ); }
}
class GCU_Future_Intelligence {
    const LAST_ANOMALY_OPTION = 'gcu_test_anomaly';
    public static $policy_ok = true;
    public static function parity_status() { return array( 'ok' => self::$policy_ok ); }
    public static function tables() { return array( 'reports' => 'wp_gcu_future_reports' ); }
}
class R34Repository {
    public $audits = array();
    public function begin_owned_transaction() { return true; }
    public function rollback_owned_transaction() { return true; }
    public function commit_owned_transaction() { return true; }
    public function audit( $name, $type, $ref, $purpose, $reason, $before, $after ) {
        $this->audits[] = compact( 'name','reason','after' );
        return true;
    }
}
class GCU_Plugin {
    private static $instance;
    public $repo;
    public $contract;
    public function __construct() { $this->repo = new R34Repository(); $this->contract = new R34Contracts(); }
    public static function instance() { if ( ! self::$instance ) { self::$instance = new self(); } return self::$instance; }
    public function repository() { return $this->repo; }
    public function contracts() { return $this->contract; }
}
class R34Contracts {
    public function all_destination_health() { return array( array( 'available' => true ) ); }
}
class GCU_Hardening {
    public static $locks = array();
    public static function acquire_db_lock( $name, $seconds = 0 ) { self::$locks[] = $name; return 'safe-lock'; }
    public static function release_db_lock( $name ) {}
}
class GCU_Observability {
    public static $errors = array();
    public static function log( $level, $name, $payload ) { self::$errors[] = $name; }
}
class GCU_Experiment_Baseline {
    public static $restored = true;
    public static function ready( $row ) { return true; }
    public static function reconcile_after_stop( $row ) {
        return array( 'restored'=>self::$restored, 'reason'=>self::$restored ? 'approved_baseline_unchanged' : 'placement_drift_requires_owner_reconciliation', 'manual_review'=>true );
    }
}
// R52: the production experiment guard now delegates strict COUNT validation
// to the real canonical Future policy. Load its actual method, not a mock.
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-fifth-review-hardening.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-repository.php';

$wpdb = new R34DB();
$r34_opts = array();
$failures = array();
$stopped = 0;
$skipped = 0;
$errors = 0;
for ( $pass = 1; $pass <= 40; $pass++ ) {
    // Ten variants each: expired/stop; active/not due; unavailable DB clock
    // decision/fail-closed; and real policy breach/stop regardless of expiry.
    $mode = ( $pass - 1 ) % 4;
    $wpdb->read_sql = array();
    $wpdb->rows = array( array(
        'id' => $pass, 'public_id' => sprintf( '%08d-1111-4111-8111-123456789abc', $pass ),
        'status' => 'running', 'row_version' => $pass, 'ends_at' => '2026-10-13 15:30:00'
    ) );
    $wpdb->expired = 0 === $mode ? $pass : 0;
    $wpdb->fail_check = 2 === $mode;
    $wpdb->include_running = true;
    $r34_opts = array( GCU_Future_Intelligence::LAST_ANOMALY_OPTION => array( 'severity' => 'none' ) );
    GCU_Future_Intelligence::$policy_ok = 3 !== $mode;
    GCU_Experiment_Baseline::$restored = 3 !== $mode;
    $before = count( GCU_Plugin::instance()->repository()->audits );
    $result = GCU_Fifth_Review_Hardening::transactional_early_stop_guard();
    $stops_now = count( GCU_Plugin::instance()->repository()->audits ) - $before;
    $expected = 0 === $mode || 3 === $mode;
    $ok = 2 === $mode ? is_wp_error( $result ) && 'gcu_experiment_expiry_check_failed' === $result->get_error_code() && 0 === $stops_now
        : ( $expected ? 1 === $result && 1 === $stops_now : 0 === $result && 0 === $stops_now );
    if ( $expected && ( ! isset( $wpdb->read_sql[1] ) || ( 0 === $mode && false === strpos( implode( ' ', $wpdb->read_sql ), 'ends_at<=UTC_TIMESTAMP()' ) ) ) ) { $ok = false; }
    if ( 3 === $mode && empty( $r34_opts['gcu_experiment_serving_paused'] ) ) { $ok = false; }
    if ( $expected && 'workflow:experiment:' . $wpdb->rows[0]['public_id'] !== end( GCU_Hardening::$locks ) ) { $ok = false; }
    if ( ! $ok ) { $failures[] = "Review/pass $pass mode $mode failed (outcome or atomic reason/lock)"; }
    if ( $expected ) { $stopped++; } elseif ( 2 === $mode ) { $errors++; } else { $skipped++; }
    echo sprintf( "R34 lifecycle pass %02d: %s — %s\n", $pass, $ok ? 'PASS' : 'FAIL', array( 'expired stop', 'healthy active', 'unknown clock/DB safe error', 'policy breach with drift pause' )[ $mode ] );
}
// Native experiment transitions reject a global pause before any otherwise
// valid copy-only experiment is allowed to bypass the placement guard.
$r34_opts['gcu_experiment_serving_paused'] = 1;
$reflection = new ReflectionMethod( 'GCU_Repository', 'validate_transition_target' );
$reflection->setAccessible( true );
$repo = new GCU_Repository();
$blocked = $reflection->invoke( $repo, 'experiment',
    array( 'variants' => json_encode( array( 'safe copy A', 'safe copy B' ) ), 'status' => 'approved', 'ends_at'=>'2026-10-14 10:00:00' ), 'running' );
if ( ! is_wp_error( $blocked ) || 'gcu_experiment_globally_paused' !== $blocked->get_error_code() ) {
    $failures[] = 'Copy-only experiment was allowed to evade global restoration pause.';
}
if ( $failures ) { fwrite( STDERR, implode( "\n", $failures ) . "\n" ); exit( 1 ); }
echo "R34 forty lifecycle scenario passes: PASS ($stopped audited stops; $skipped healthy continuations; $errors fail-closed errors). Simulation only; 40 full WordPress E2E runs NOT claimed.\n";
