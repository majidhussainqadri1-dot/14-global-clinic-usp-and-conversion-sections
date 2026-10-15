<?php
/**
 * R38: forty additional direct-method cases for FUT-17 batch overflow and count/list mismatch.
 * Calls real GCU_Fifth_Review_Hardening::transactional_early_stop_guard().
 * These are 40 isolated PHP/SQL mocks, NOT real WordPress staging acceptance.
 */
define( 'ABSPATH', __DIR__ . '/' );
define( 'ARRAY_A', 'ARRAY_A' );
function __( $s, $domain = '' ) { return $s; }
function sanitize_key( $s ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $s ) ); }
function current_time( $format, $gmt = false ) { return '2026-10-14 16:30:00'; }
function get_option( $key, $default = false ) { return $default; }
function update_option( $key, $value, $autoload = false ) { return true; }
function is_wp_error( $x ) { return $x instanceof WP_Error; }
class WP_Error {
    private $code;
    public function __construct( $code, $msg = '', $data = array() ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
class R37DB {
    public $mode = 0;
    public $audit_failure = true;
    public $reads = array();
    public function get_var( $sql ) {
        $this->reads[] = $sql;
        if ( false !== strpos( $sql, 'gcu_future_reports' ) ) {
            return 0 === $this->mode ? false : ( 1 === $this->mode ? null : 0 );
        }
        if ( 3 === $this->mode ) { return false; }
        return 4 === $this->mode ? 0 : 1;
    }
    public function get_results( $sql, $format = ARRAY_A ) {
        $this->reads[] = $sql;
        if ( 2 === $this->mode ) { return false; }
        if ( 4 === $this->mode ) { return array(); }
        return array( array( 'id'=>41,'public_id'=>'11111111-1111-4111-8111-111111111111','row_version'=>7,'status'=>'running' ) );
    }
    public function prepare( $sql ) { return $sql; }
    public function query( $sql ) { return 8 === $this->mode ? 0 : 1; }
}
class GCU_Install {
    public static function tables() { return array( 'experiments'=>'wp_gcu_experiments' ); }
}
class GCU_Future_Intelligence {
    const LAST_ANOMALY_OPTION = 'gcu_future_last_anomaly';
    public static function parity_status() { global $wpdb; return array( 'ok' => in_array( $wpdb->mode, array( 3, 4 ), true ) ); }
    public static function tables() { return array( 'reports'=>'wp_gcu_future_reports' ); }
}
class R37Repository {
    public $audits = 0;
    public function begin_owned_transaction() { global $wpdb; return 7 !== $wpdb->mode; }
    public function rollback_owned_transaction() { return true; }
    public function commit_owned_transaction() { global $wpdb; return 9 !== $wpdb->mode || $wpdb->audit_failure; }
    public function audit( $action, $type, $id, $purpose, $reason, $before, $after ) {
        global $wpdb;
        if ( 9 === $wpdb->mode && $wpdb->audit_failure ) { $this->audits++; return false; }
        $this->audits++;
        return true;
    }
}
class R37Contracts {
    public function all_destination_health() { return array( array( 'available' => true ) ); }
}
class GCU_Plugin {
    private static $instance;
    public $repo;
    public $contract;
    public function __construct() { $this->repo = new R37Repository(); $this->contract = new R37Contracts(); }
    public static function instance() { if ( ! self::$instance ) { self::$instance = new self(); } return self::$instance; }
    public function repository() { return $this->repo; }
    public function contracts() { return $this->contract; }
}
class GCU_Hardening {
    public static function acquire_db_lock( $key, $wait = 0 ) { global $wpdb; return 6 === $wpdb->mode ? false : 'r37_safe_lock'; }
    public static function release_db_lock( $lock ) { return true; }
}
class GCU_Experiment_Baseline {
    public static function reconcile_after_stop( $row ) { return array( 'restored' => true, 'reason'=>'approved_baseline_unchanged' ); }
}
class GCU_Observability {
    public static $logs = array();
    public static function log( $level, $name, $props = array() ) { self::$logs[] = array( 'name'=>$name, 'props'=>$props ); }
}
$wpdb = new R37DB();
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-fifth-review-hardening.php';

// Existing R37 harness defines the real class plus test-only WP/SQL stubs.
// Override only database reads to exercise list/count agreement and 50-limit.
class R38DB extends R37DB {
    public $expiry_count = 0;
    public $scenario_rows = array();
    public $selection_count = 0;
    public function get_var( $sql ) {
        $this->reads[] = $sql;
        if ( false !== strpos( $sql, 'gcu_future_reports' ) ) { return 0; }
        return $this->expiry_count;
    }
    public function get_results( $sql, $format = ARRAY_A ) {
        $this->selection_count++;
        $this->reads[] = $sql;
        if ( false === strpos( $sql, 'LIMIT 51' ) ) {
            throw new RuntimeException( 'Safety select must inspect 51 records (one overflow sentinel).' );
        }
        return $this->scenario_rows;
    }
    public function query( $sql ) { return 1; }
}
function r38_rows( $size, $iteration ) {
    $rows = array();
    for ( $i=0; $i<$size; $i++ ) {
        $rows[] = array(
            'id' => $i + 1,
            'public_id' => 'r38-' . $iteration . '-' . $i,
            'row_version' => 7,
            'status' => 'running'
        );
    }
    return $rows;
}
$wpdb = new R38DB();
$errors = array();
for ( $round=1; $round<=40; $round++ ) {
    $family = ( $round-1 ) % 5;
    $variation = (int) floor( ( $round-1 ) / 5 );
    $wpdb->reads = array();
    $wpdb->selection_count = 0;
    GCU_Observability::$logs = array();
    GCU_Plugin::instance()->repo = new R37Repository();
    $wpdb->audit_failure = false;
    $wpdb->mode = 5; // Business parity breach: all running experiments must stop.
    $expected_code = null;
    $expected_count = null;
    $expected_audits = 0;
    if ( 0 === $family ) {
        // Expiry COUNT>0 but SELECT empty must never report "0 stopped".
        $wpdb->mode = 4; // good central parity, hence count-scoped stop.
        $wpdb->expiry_count = 1 + $variation;
        $wpdb->scenario_rows = array();
        $expected_code = 'gcu_experiment_expiry_list_mismatch';
    } elseif ( 1 === $family ) {
        // 51..58 running rows. Process only 50, disclose the safety backlog.
        $wpdb->scenario_rows = r38_rows( 51+$variation, $round );
        // Stub honors SQL LIMIT 51 by returning the first 51 records.
        $wpdb->scenario_rows = array_slice( $wpdb->scenario_rows, 0, 51 );
        $expected_code = 'gcu_experiment_stop_batch_incomplete';
        $expected_audits = 50;
    } elseif ( 2 === $family ) {
        // Exactly 50 is NOT an overflow: no fabricated alert.
        $wpdb->scenario_rows = r38_rows( 50, $round );
        $expected_count = 50;
        $expected_audits = 50;
    } elseif ( 3 === $family ) {
        // Bounded healthy workload.
        $wpdb->scenario_rows = r38_rows( 1+$variation, $round );
        $expected_count = 1+$variation;
        $expected_audits = 1+$variation;
    } else {
        // No expired experiments. Do not touch experiment list at all.
        $wpdb->mode = 4; $wpdb->expiry_count = 0;
        $wpdb->scenario_rows = r38_rows( 3+$variation, $round );
        $expected_count = 0;
    }
    $result = GCU_Fifth_Review_Hardening::transactional_early_stop_guard();
    $actual_code = is_wp_error( $result ) ? $result->get_error_code() : null;
    $last = end( GCU_Observability::$logs );
    $pass = $actual_code === $expected_code
        && $expected_audits === GCU_Plugin::instance()->repo->audits
        && ( null === $expected_count || $result === $expected_count )
        && ( 4 !== $family || 0 === $wpdb->selection_count )
        && ( null === $expected_code || count( GCU_Observability::$logs ) > 0 )
        && ( 1 !== $family || ( isset($last['props']['backlog_detected']) && true === $last['props']['backlog_detected'] && 50 === $last['props']['stopped_count'] ) )
        && ( 2 !== $family || 0 === count( GCU_Observability::$logs ) );
    if ( ! $pass ) { $errors[] = 'Round ' . $round . ': got ' . ( $actual_code ?: var_export($result,true) ) . ' after ' . GCU_Plugin::instance()->repo->audits . ' audits'; }
    echo sprintf( 'R38 batch completeness case %02d/40: %s (%s)' . PHP_EOL, $round, $pass ? 'PASS' : 'FAIL', $family );
}
if ( $errors ) { fwrite( STDERR, implode(PHP_EOL,$errors).PHP_EOL ); exit(1); }
echo 'R38: 40/40 direct method scenarios PASS: count/list mismatch; 51+ backlog; exactly 50; bounded normal; zero expiry. SQL-mocked, not WP staging.' . PHP_EOL;
