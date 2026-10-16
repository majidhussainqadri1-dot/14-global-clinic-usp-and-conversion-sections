<?php
/**
 * R42: F14-FUT13/14, 40 direct REAL production-method tests using
 * SQL and WordPress-isolated mocks; absolutely NOT staging/live E2E.
 */
define( 'ABSPATH', __DIR__ . '/' );
define( 'ARRAY_A', 'ARRAY_A' );
function __( $v, $domain = '' ) { return $v; }
function is_wp_error( $v ) { return $v instanceof WP_Error; }
function get_option( $key, $default = false ) {
    return 'gcu_future_schema_version' === $key ? 1 : $default;
}
function update_option( $key, $v, $autoload = false ) {
    $GLOBALS['r42_options'][$key] = $v;
    return true;
}
function absint( $n ) { return abs( (int) $n ); }
class WP_Error {
    private $code;
    public function __construct( $code, $message = '', $details = array() ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
class WP_REST_Request {
    public function get_param( $key ) { return 'days' === $key ? 30 : null; }
}
class WP_REST_Response {
    public $data;
    public function __construct( $data, $status = 200 ) { $this->data = $data; }
    public function header( $name, $value ) {}
}
class GCU_Capabilities {
    const VIEW_ANALYTICS = 'view_analytics';
    public static function require_capability( $capability, $target = null, $purpose = '' ) { return true; }
}
class GCU_Future_Policy {
    const MIN_COHORT = 10;
    public static function owner_confirmation_contract_ready() { return false; }
    public static function acquisition_stage_reportable( $stage ) {
        return in_array( $stage, array( 'cta_selected', 'destination_loaded' ), true );
    }
    public static function valid_aggregate_count( $v ) {
        if ( is_int( $v ) ) { return $v >= 0; }
        if ( ! is_string( $v ) || ! preg_match( '/^(0|[1-9][0-9]*)$/D', $v ) ) { return false; }
        $limit = (string) PHP_INT_MAX;
        return strlen( $v ) < strlen( $limit ) || ( strlen( $v ) === strlen( $limit ) && strcmp( $v, $limit ) <= 0 );
    }
    public static function cohort_allowed( $n ) { return $n >= self::MIN_COHORT; }
    public static function bounded_query_integer( $n, $default, $maximum ) {
        return null === $n ? $default : min( $maximum, max( 1, (int) $n ) );
    }
}
class GCU_Install {
    public static function tables() {
        return array( 'events' => 'wp_gcu_events', 'claims' => 'wp_gcu_claims' );
    }
    public static function ready_for_runtime() { return true; }
}
class GCU_Observability {
    public static $logs = array();
    public static function log( $severity, $code, $fields = array() ) {
        self::$logs[] = array( $severity, $code, $fields );
    }
}
class R42_DB {
    public $prefix = 'wp_';
    public $anomaly = array();
    public $owner = array();
    public $friction = array();
    public $called = array();
    public function prepare( $sql, ...$args ) {
        foreach ( $args as $a ) {
            $sql = preg_replace( '/%d|%s/', is_numeric( $a ) ? (string) $a : "'" . (string) $a . "'", $sql, 1 );
        }
        return $sql;
    }
    public function esc_like( $s ) { return $s; }
    public function get_var( $sql ) {
        if ( strpos( $sql, 'SHOW TABLES LIKE' ) !== false ) {
            return strpos( $sql, 'gcu_future_records' ) !== false
                ? 'wp_gcu_future_records' : 'wp_gcu_future_reports';
        }
        throw new RuntimeException( 'Unexpected scalar DB query: ' . $sql );
    }
    public function get_row( $sql, $format = ARRAY_A ) {
        if ( strpos( $sql, 'SHOW TABLE STATUS' ) !== false ) { return array( 'Engine' => 'InnoDB' ); }
        throw new RuntimeException( 'Unexpected row DB query: ' . $sql );
    }
    public function get_col( $sql, $col = 0 ) {
        if ( strpos( $sql, 'SHOW COLUMNS' ) !== false ) {
            return array( 'record_type', 'record_key', 'locale', 'region', 'status',
                'is_public', 'payload', 'payload_hash', 'row_version', 'review_due_at',
                'public_id', 'report_type', 'route_key', 'reason_code', 'actor_hash',
                'created_at', 'updated_at' );
        }
        throw new RuntimeException( 'Unexpected column DB query: ' . $sql );
    }
    public function get_results( $sql, $format = ARRAY_A ) {
        $this->called[] = $sql;
        if ( false !== strpos( $sql, 'previous_seven_days' ) ) { return $this->anomaly; }
        if ( false !== strpos( $sql, 'COUNT(*) AS total' ) && false !== strpos( $sql, 'event_type,funnel_stage' ) ) { return $this->owner; }
        if ( false !== strpos( $sql, 'SELECT funnel_stage,COUNT(*) total' ) ) { return $this->friction; }
        throw new RuntimeException( 'Unexpected analytics query: ' . $sql );
    }
}
$wpdb = new R42_DB();
$GLOBALS['r42_options'] = array();
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-owner-outcomes.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php';

$failed = array();
for ( $case = 1; $case <= 40; $case++ ) {
    $family = ( $case-1 ) % 10;
    $v = intdiv( $case-1, 10 );
    $GLOBALS['r42_options'] = array();
    GCU_Observability::$logs = array();
    $wpdb->called = array();
    $wpdb->anomaly = array( array(
        'event_type' => 'File08.BookingStarted.v1', 'funnel_stage' => 'booking_started',
        'current_count' => 10+$v, 'previous_seven_days' => 91+$v*7 ) );
    $wpdb->owner = array( array(
        'event_type' => 'File09.ApplicationStarted.v1', 'funnel_stage' => 'application_started',
        'total' => 20+$v ) );
    $wpdb->friction = array( array( 'funnel_stage'=>'cta_selected', 'total'=>20+$v ) );
    $pass = false;
    if ( 0 === $family ) {
        $wpdb->anomaly = $v % 2 ? false : null;
        $r = GCU_Owner_Outcomes::observed_anomaly();
        $c = GCU_Future_Intelligence::anomaly_detector();
        $pass = $r['status'] === 'owner_event_volume_read_unverified'
            && $c['status'] === $r['status'] && $r['suppressed'] === true
            && $r['booking_success_rate'] === null
            && count( GCU_Observability::$logs ) === 2
            && $GLOBALS['r42_options'][GCU_Future_Intelligence::LAST_ANOMALY_OPTION]['status'] === $r['status'];
    } elseif ( 1 === $family ) {
        $wpdb->anomaly = array();
        $r = GCU_Owner_Outcomes::observed_anomaly();
        $pass = $r['status'] === 'insufficient_owner_event_evidence'
            && $r['suppressed'] === true && GCU_Observability::$logs === array();
    } elseif ( 2 === $family ) {
        $r = GCU_Owner_Outcomes::observed_anomaly();
        $pass = $r['status'] === 'consented_owner_volume_observed'
            && count($r['signals']) === 1
            && $r['booking_success_rate'] === null && $r['owner_correlated_funnel'] === false;
    } elseif ( 3 === $family ) {
        $wpdb->owner = $v % 2 ? false : null;
        $r = GCU_Owner_Outcomes::owner_stage_summary(30);
        $pass = is_wp_error($r) && $r->get_error_code() === 'gcu_owner_stage_read_unverified'
            && count(GCU_Observability::$logs) === 1;
    } elseif ( 4 === $family ) {
        $wpdb->owner = $v % 2 ? array(array('event_type'=>'File09.ApplicationStarted.v1'))
            : array(array('event_type'=>'File09.ApplicationStarted.v1','funnel_stage'=>'application_started','total'=>'n/a'));
        $r = GCU_Owner_Outcomes::owner_stage_summary(30);
        $pass = is_wp_error($r) && $r->get_error_code() === 'gcu_owner_stage_row_unverified';
    } elseif ( 5 === $family ) {
        if ( $v % 2 ) { $wpdb->owner[0]['total'] = 5; }
        $r = GCU_Owner_Outcomes::owner_stage_summary(30);
        $pass = is_array($r) && $r['success_rate'] === null
            && ( $v % 2 ? $r['suppressed'] && !$r['items'] : !$r['suppressed'] && count($r['items'])===1 );
    } elseif ( 6 === $family ) {
        $wpdb->friction = $v % 2 ? false : null;
        $r = GCU_Future_Intelligence::friction_summary(30);
        $pass = is_wp_error($r) && $r->get_error_code() === 'gcu_friction_stage_read_unverified'
            && count(GCU_Observability::$logs) === 1;
    } elseif ( 7 === $family ) {
        $wpdb->friction = $v % 2 ? array(array('funnel_stage'=>'cta_selected'))
            : array(array('funnel_stage'=>'cta_selected','total'=>'invalid'));
        $r = GCU_Future_Intelligence::friction_summary(30);
        $pass = is_wp_error($r) && $r->get_error_code() === 'gcu_friction_stage_row_unverified';
    } elseif ( 8 === $family ) {
        if ( $v % 2 ) { $wpdb->friction[0]['total'] = 5; }
        $r = GCU_Future_Intelligence::friction_summary(30);
        $pass = is_array($r) && $r['dropoffs'] === array()
            && ( $v % 2 ? $r['suppressed'] === true : $r['suppressed'] === false );
    } else {
        if ( 0 === $v ) { $wpdb->owner = false; }
        if ( 1 === $v ) { $wpdb->friction = false; }
        if ( 2 === $v ) { $wpdb->owner[0]['total'] = 'bad'; }
        $r = GCU_Future_Intelligence::rest_friction( new WP_REST_Request() );
        $expected = 0 === $v ? 'gcu_owner_stage_read_unverified'
            : ( 1 === $v ? 'gcu_friction_stage_read_unverified'
            : ( 2 === $v ? 'gcu_owner_stage_row_unverified' : '' ) );
        $pass = $expected
            ? is_wp_error($r) && $r->get_error_code()===$expected
            : $r instanceof WP_REST_Response && isset($r->data['owner_native_observations']['contract'])
                && $r->data['owner_native_observations']['success_rate'] === null;
    }
    if ( ! $pass ) { $failed[] = sprintf( 'R42 scenario %02d family %d variation %d FAILED', $case, $family, $v ); }
    printf( "R42 direct actual-method test %02d/40: %s (family %d)\n", $case, $pass ? 'PASS' : 'FAIL', $family );
}
if ( $failed ) { fwrite(STDERR, implode("\n",$failed)."\n"); exit(1); }
echo "R42 40/40 actual PHP-method SQL mocked privacy/error/REST/owner anomaly checks PASS. This is NOT staging/live E2E.\n";
