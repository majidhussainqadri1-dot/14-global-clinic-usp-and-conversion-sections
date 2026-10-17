<?php
/**
 * R41: 40 direct real-method cases, mocked WPDB/WordPress.
 * FUT18 FAQ gap evidence, FUT19 consistency evidence, daily and REST guards.
 * This is NOT an actual WordPress/staging/live acceptance test.
 */
define( 'ABSPATH', __DIR__ . '/' );
define( 'ARRAY_A', 'ARRAY_A' );
function __( $value, $domain = '' ) { return $value; }
function is_wp_error( $x ) { return $x instanceof WP_Error; }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $value ) ); }
function sanitize_text_field( $value ) { return trim( (string) $value ); }
function wp_strip_all_tags( $value ) { return strip_tags( (string) $value ); }
function current_time( $format, $gmt = false ) { return '2026-10-16 12:00:00'; }
function wp_json_encode( $data ) { return json_encode( $data ); }
function apply_filters( $name, $value ) { return 'gcu_future_question_aggregates' === $name ? $GLOBALS['r41_signals'] : $value; }
function get_option( $key, $default = false ) {
    if ( 'gcu_future_schema_version' === $key ) { return 1; }
    return $default;
}
function update_option( $key, $value, $autoload = false ) { return true; }
class WP_Error {
    private $code;
    public function __construct( $code, $message = '', $data = array() ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
class WP_REST_Response {
    public $data;
    public function __construct( $data, $code = 200 ) { $this->data = $data; }
    public function header( $name, $value ) {}
}
class GCU_Capabilities {
    const MANAGE_CONTENT = 'manage_content';
    public static function require_capability( $capability, $object = null, $context = '' ) { return true; }
}
class GCU_Install {
    public static function tables() {
        return array( 'claims'=>'wp_gcu_claims', 'blocks'=>'wp_gcu_blocks',
            'claim_history'=>'wp_gcu_claim_history', 'reports'=>'wp_gcu_future_reports', 'records'=>'wp_gcu_future_records' );
    }
    public static $runtime = true;
    public static function ready_for_runtime() { return self::$runtime; }
}
class GCU_Observability {
    public static $events = array();
    public static function log( $severity, $code, $fields = array() ) {
        self::$events[] = array( 'code'=>$code, 'fields'=>$fields );
    }
}
class GCU_Policy {
    public static function supported_locales() { return array( 'en-US', 'ur-PK', 'ar-SA' ); }
    public static function sanitize_locale( $locale ) {
        return is_string( $locale ) && in_array( $locale, self::supported_locales(), true ) ? $locale : 'en-US';
    }
    public static function canonical_claims() {
        return array(
            'zero_platform_commission'=>array('text'=>'Zero platform commission'),
            'free_approved_core'=>array('text'=>'Core free'),
            'optional_support_no_ranking'=>array('text'=>'Support does not affect ranking'),
        );
    }
    public static function business_rules() {
        return array('platform_commission_percent'=>0,'approved_core_tier'=>'free','support_affects_visibility'=>false);
    }
}
class GCU_Future_Policy {
    public static function terminology_lock() { return array( 'source'=>'approved' ); }
    public static function valid_faq_aggregate_signal( $v ) {
        return isset( $v['question'], $v['count'] ) && is_string( $v['question'] )
            && is_numeric( $v['count'] ) && (int) $v['count'] >= 10;
    }
    public static function valid_aggregate_count( $value ) {
        if ( is_int( $value ) ) { return $value >= 0; }
        if ( ! is_string( $value ) || ! preg_match('/^(0|[1-9][0-9]*)$/D', $value) ) { return false; }
        $max=(string)PHP_INT_MAX;
        return strlen($value)<strlen($max) || (strlen($value)===strlen($max) && strcmp($value,$max)<=0);
    }
    public static function validate_future_read_query( array $input, $kind ) {
        return array('safe'=>true,'type'=>isset($input['type'])?(string)$input['type']:'',
            'status'=>isset($input['status'])?(string)$input['status']:'open',
            'cursor'=>isset($input['cursor'])?(int)$input['cursor']:0,
            'limit'=>isset($input['limit'])?(int)$input['limit']:2);
    }
    public static function dark_pattern_scan( $text ) { return array( 'flags'=>array() ); }
    public static function validate_public_record_payload( $type, $payload ) {
        return array( 'safe' => is_array( $payload ) && ! empty( $payload['title'] ) && ! empty( $payload['effective_date'] ) );
    }
}
class R41DB {
    public $prefix = 'wp_';
    public $faq = array();
    public $graph = array();
    public $copy = array();
    public $queries = array();
    public $writes = 0;
    public function prepare( $sql, ...$args ) {
        // WordPress wpdb::prepare accepts both variadic arguments and a single array.
        if ( count( $args ) === 1 && is_array( $args[0] ) ) { $args = $args[0]; }
        foreach ( $args as $v ) {
            $sql = preg_replace( '/%s|%d/', is_numeric( $v ) ? (string) $v : "'" . (string) $v . "'", $sql, 1 );
        }
        return $sql;
    }
    public function esc_like( $value ) { return $value; }
    public function get_var( $sql ) {
        if ( strpos( $sql, 'SHOW TABLES LIKE' ) !== false ) {
            return strpos( $sql, 'gcu_future_reports' ) !== false ? 'wp_gcu_future_reports' : 'wp_gcu_future_records';
        }
        return 0;
    }
    public function get_row( $sql, $format = ARRAY_A ) {
        if ( strpos( $sql, 'SHOW TABLE STATUS' ) !== false ) { return array('Engine'=>'InnoDB'); }
        if ( strpos( $sql, 'wp_gcu_claims' ) !== false ) {
            foreach ( GCU_Policy::canonical_claims() as $k => $v ) {
                if ( strpos( $sql, $k ) !== false ) {
                    return array('claim_text'=>$v['text'],'status'=>'active','is_public'=>1,
                        'review_due_at'=>null,'expires_at'=>null);
                }
            }
        }
        return null;
    }
    public function get_col( $sql, $column = 0 ) {
        if ( strpos( $sql, 'SHOW COLUMNS FROM' ) !== false ) {
            return array('record_type','record_key','locale','region','status','is_public','payload',
                'payload_hash','row_version','review_due_at','public_id','report_type','route_key',
                'reason_code','actor_hash','created_at','updated_at');
        }
        return $this->faq;
    }
    public function get_results( $sql, $format = ARRAY_A ) {
        $this->queries[] = $sql;
        if ( strpos( $sql, 'SELECT * FROM wp_gcu_claims' ) !== false ) { return array(); }
        if ( strpos( $sql, 'SELECT title,body,cta_label FROM wp_gcu_blocks' ) !== false ) { return $this->copy; }
        if ( strpos( $sql, 'SELECT block_key,locale,title,body,cta_label,claim_keys FROM wp_gcu_blocks' ) !== false ) { return $this->graph; }
        throw new RuntimeException('Unexpected read: '.$sql);
    }
    public function query( $sql ) { $this->writes++; return 0; }
}
$wpdb = new R41DB();
$GLOBALS['r41_signals'] = array();
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php';

function r41_rows( $variant = 'complete' ) {
    $rows = array();
    foreach ( GCU_Policy::supported_locales() as $i => $locale ) {
        if ( 'missing' === $variant && 2 === $i ) { continue; }
        $claim = 'drift' === $variant && 2 === $i ? '["free_approved_core"]' : '["zero_platform_commission"]';
        $rows[] = array('block_key'=>'primary_usp','locale'=>$locale,
            'title'=>'Safe approved content','body'=>'Approved text','cta_label'=>'Read more','claim_keys'=>$claim);
    }
    if ( 'malformed' === $variant ) { $rows[1] = array( 'block_key'=>'primary_usp' ); }
    return $rows;
}
$problems = array();
for ( $case=1; $case<=40; $case++ ) {
    $family = ( $case-1 ) % 10;
    $variation = intdiv( $case-1, 10 );
    $wpdb->faq = array('approved question');
    $wpdb->graph = r41_rows();
    $wpdb->copy = array(array('title'=>'Approved','body'=>'Safe','cta_label'=>'Read'));
    $wpdb->queries = array();
    $wpdb->writes = 0;
    GCU_Observability::$events = array();
    $GLOBALS['r41_signals'] = array(array('question'=>'approved question','count'=>20+$variation));
    $passed = false;
    if ( 0 === $family ) {
        $wpdb->faq = $variation % 2 ? null : false;
        $result = GCU_Future_Intelligence::faq_gap_candidates();
        $passed = is_wp_error($result) && 'gcu_faq_gap_catalog_read_failed' === $result->get_error_code()
            && count(GCU_Observability::$events) === 1;
    } elseif ( 1 === $family ) {
        $result = GCU_Future_Intelligence::faq_gap_candidates();
        $passed = is_array($result) && count($result) === 0;
    } elseif ( 2 === $family ) {
        $wpdb->faq = array('unrelated clinical administration topic');
        $result = GCU_Future_Intelligence::faq_gap_candidates();
        $passed = is_array($result) && count($result) === 1
            && 'suggested_only' === $result[0]['status'] && false === $result[0]['auto_publish'];
    } elseif ( 3 === $family ) {
        $wpdb->graph = $variation % 2 ? null : false;
        $result = GCU_Future_Intelligence::consistency_graph();
        $rest = GCU_Future_Intelligence::rest_consistency();
        $passed = false === $result['ok'] && false === $result['verified']
            && in_array('active_blocks_consistency_read_unverified',$result['issues'],true)
            && is_wp_error($rest) && 'gcu_future_consistency_read_failed' === $rest->get_error_code();
    } elseif ( 4 === $family ) {
        $result = GCU_Future_Intelligence::consistency_graph();
        $passed = true === $result['ok'] && true === $result['verified'] && count($result['issues']) === 0;
    } elseif ( 5 === $family ) {
        $wpdb->graph = r41_rows('missing');
        $result = GCU_Future_Intelligence::consistency_graph();
        $passed = false === $result['ok'] && true === $result['verified']
            && in_array('missing_locale:primary_usp:ar-SA',$result['issues'],true);
    } elseif ( 6 === $family ) {
        $wpdb->graph = r41_rows('drift');
        $result = GCU_Future_Intelligence::consistency_graph();
        $passed = false === $result['ok'] && true === $result['verified']
            && in_array('claim_set_drift:primary_usp',$result['issues'],true);
    } elseif ( 7 === $family ) {
        $wpdb->graph = r41_rows('malformed');
        $result = GCU_Future_Intelligence::consistency_graph();
        $passed = false === $result['ok'] && false === $result['verified']
            && in_array('active_block_consistency_row_invalid',$result['issues'],true);
    } elseif ( 8 === $family ) {
        $wpdb->faq = $variation % 2 ? null : false;
        $result = GCU_Future_Intelligence::rest_faq_gaps();
        $passed = is_wp_error($result) && 'gcu_faq_gap_catalog_read_failed' === $result->get_error_code();
    } else {
        $GLOBALS['r41_signals'] = array();
        if ( 0 === $variation ) { $wpdb->copy = false; }
        if ( 1 === $variation ) { $wpdb->graph = false; }
        if ( 2 === $variation ) { $wpdb->faq = false; }
        $result = GCU_Future_Intelligence::daily_governance();
        $expected = array('gcu_daily_policy_parity_failed','gcu_daily_consistency_failed',
            'gcu_faq_gap_catalog_read_failed',null)[$variation];
        $passed = null === $expected
            ? ( is_array($result) && isset($result['reports'],$result['records']) )
            : ( is_wp_error($result) && $expected === $result->get_error_code()
                && 0 === $wpdb->writes );
    }
    if ( ! $passed ) { $problems[] = 'Case '.$case.' family '.$family.' FAILED'; }
    echo sprintf("R41 direct-source scenario %02d/40: %s (family %d)\n", $case, $passed ? 'PASS':'FAIL', $family);
}
if ( $problems ) { fwrite(STDERR,implode("\n",$problems)."\n"); exit(1); }
echo "R41: 40 direct real-method SQL-mocked cases PASS; not WordPress/staging/live acceptance.\n";
