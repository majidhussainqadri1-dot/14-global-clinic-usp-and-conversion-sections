<?php
/**
 * R40: forty direct tests of the real FUT-04 claim_freshness_sentinel method.
 * Mocked WPDB/transaction/locks; NOT WordPress staging or live E2E tests.
 */
define( 'ABSPATH', __DIR__ . '/' );
define( 'ARRAY_A', 'ARRAY_A' );
function __( $message, $domain = '' ) { return $message; }
function current_time( $format, $utc = false ) { return '2026-10-15 18:00:00'; }
function wp_json_encode( $value ) { return json_encode( $value ); }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function get_option( $key, $default = false ) {
    return 'gcu_future_schema_version' === $key ? 1 : $default;
}
function sanitize_key( $text ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $text ) ); }
class WP_Error {
    private $code;
    public function __construct( $code, $message = '', $data = array() ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
class GCU_Install {
    public static function tables() { return array( 'claims'=>'wp_gcu_claims', 'claim_history'=>'wp_gcu_claim_history' ); }
    public static function ready_for_runtime() { return true; }
}
class GCU_Observability {
    public static $logs = array();
    public static function log( $level, $name, $fields = array() ) {
        self::$logs[] = array( 'level'=>$level, 'name'=>$name, 'fields'=>$fields );
    }
}
class R40DB {
    public $mode = 0;
    public $variation = 0;
    public $rows = array();
    public $read_sql = '';
    public $current_key = '';
    public $inserts = 0;
    public $updates = 0;
    public function get_results( $sql, $format = ARRAY_A ) {
        $this->read_sql = $sql;
        if ( false === strpos( $sql, 'ORDER BY id ASC LIMIT 101' ) ) {
            throw new RuntimeException( 'FUT-04 must check an ordered 101-row overflow sentinel.' );
        }
        if ( 0 === $this->mode ) {
            return $this->variation % 2 === 0 ? false : null;
        }
        return array_slice( $this->rows, 0, 101 );
    }
    public function prepare( $sql ) { return $sql; }
    public function query( $sql ) {
        if ( false !== strpos( $sql, 'INSERT IGNORE INTO' ) ) {
            $this->inserts++;
            return 6 === $this->mode && $this->current_key === $this->failing_key() ? false : 1;
        }
        if ( false !== strpos( $sql, "SET status='review_required'" ) ) {
            $this->updates++;
            return 7 === $this->mode && $this->current_key === $this->failing_key() ? 0 : 1;
        }
        throw new RuntimeException( 'Unexpected SQL writer in source sentinel.' );
    }
    public function failing_key() { return 'claim_' . sprintf( '%03d', $this->variation + 1 ); }
}
class R40Repo {
    public $audit_calls = 0;
    public $rollbacks = 0;
    public $commits = 0;
    public function begin_owned_transaction() {
        global $wpdb;
        return ! ( 5 === $wpdb->mode && $wpdb->current_key === $wpdb->failing_key() );
    }
    public function audit( $name, $type, $id, $context, $reason, $before, $after ) {
        global $wpdb;
        $this->audit_calls++;
        return ! ( 8 === $wpdb->mode && $wpdb->current_key === $wpdb->failing_key() );
    }
    public function commit_owned_transaction() {
        global $wpdb;
        $this->commits++;
        return ! ( 9 === $wpdb->mode && $wpdb->current_key === $wpdb->failing_key() );
    }
    public function rollback_owned_transaction() { $this->rollbacks++; return true; }
}
class GCU_Plugin {
    private static $instance;
    public $repo;
    public function __construct() { $this->repo = new R40Repo(); }
    public static function instance() {
        if ( ! self::$instance ) { self::$instance = new self(); }
        return self::$instance;
    }
    public function repository() { return $this->repo; }
}
class GCU_Hardening {
    public static function acquire_db_lock( $key, $seconds = 0 ) {
        global $wpdb;
        $wpdb->current_key = substr( $key, strlen( 'future-claim:' ) );
        return 4 === $wpdb->mode && $wpdb->current_key === $wpdb->failing_key() ? false : 'lock';
    }
    public static function release_db_lock( $token ) { return true; }
}
$wpdb = new R40DB();
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php';
$errors = array();
for ( $case = 1; $case <= 40; $case++ ) {
    $family = ( $case - 1 ) % 10;
    $variation = (int) floor( ( $case - 1 ) / 10 );
    $wpdb->mode = $family;
    $wpdb->variation = $variation;
    $wpdb->rows = array();
    $wpdb->read_sql = '';
    $wpdb->inserts = 0;
    $wpdb->updates = 0;
    $wpdb->current_key = '';
    GCU_Observability::$logs = array();
    GCU_Plugin::instance()->repo = new R40Repo();
    $count = 0 === $family ? 0
        : ( 1 === $family ? 101 + $variation
        : ( 2 === $family ? 100
        : ( 3 === $family ? 0 : 5 + $variation ) ) );
    for ( $j = 0; $j < $count; $j++ ) {
        $wpdb->rows[] = array(
            'id' => $j+1,
            'claim_key' => 'claim_' . sprintf( '%03d', $j+1 ),
            'row_version' => 2,
            'status' => 'active',
            'is_public' => 1,
        );
    }
    $res = GCU_Future_Intelligence::claim_freshness_sentinel();
    $expect_error = 3 !== $family && 2 !== $family;
    $expect_code = 0 === $family ? 'gcu_claim_freshness_read_failed' : 'gcu_claim_freshness_batch_incomplete';
    $processed = 0 === $family || 3 === $family ? 0 : ( 1 === $family || 2 === $family ? 100 : $count - 1 );
    $recorded = GCU_Plugin::instance()->repo;
    $error_code = is_wp_error( $res ) ? $res->get_error_code() : '';
    $pass = ( $expect_error ? $error_code === $expect_code : $res === $processed )
        && false !== strpos( $wpdb->read_sql, 'LIMIT 101' )
        && $recorded->commits >= ( $processed > 0 ? 1 : 0 )
        && ( 0 !== $family || ( 0 === $wpdb->inserts && 0 === $wpdb->updates ) )
        && ( 1 !== $family || ( count( GCU_Observability::$logs ) > 0
            && ! empty( end( GCU_Observability::$logs )['fields']['backlog_detected'] ) ) )
        && ( $expect_error || 0 === count( GCU_Observability::$logs ) );
    if ( ! $pass ) {
        $errors[] = sprintf( 'case %02d: family %d returned %s, commits %d, log %s',
            $case, $family, $error_code ?: var_export($res,true), $recorded->commits,
            json_encode(GCU_Observability::$logs) );
    }
    printf( "R40 claim freshness actual-method case %02d/40: %s (family %d)\n", $case, $pass ? 'PASS' : 'FAIL', $family );
}
if ( $errors ) {
    fwrite( STDERR, implode( "\n", $errors ) . "\n" ); exit(1);
}
echo "R40: 40/40 real-method SQL-mocked boundary, lock, history, CAS, audit and commit failure scenarios PASS; staging/live unverified.\n";
