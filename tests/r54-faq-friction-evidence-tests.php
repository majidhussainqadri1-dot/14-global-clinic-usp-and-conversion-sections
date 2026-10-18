<?php
/**
 * R54: 40 actual FUT18 FAQ policy/public projection tests, plus 40 actual
 * FUT13 friction_summary() SQL-evidence method tests. WordPress/SQL mocks;
 * NOT installed staging/browser/live proof.
 */
define( 'ABSPATH', __DIR__ . '/' );
define( 'ARRAY_A', 'ARRAY_A' );
function __( $text, $domain = '' ) { return $text; }
function sanitize_text_field( $v ) { return trim( (string) $v ); }
function sanitize_key( $v ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $v ) ); }
function apply_filters( $name, $default ) {
    return 'gcu_future_question_aggregates' === $name ? $GLOBALS['r54_signals'] : $default;
}
function is_wp_error( $x ) { return $x instanceof WP_Error; }
class WP_Error {
    private $code;
    public function __construct( $code, $message = '', $data = array() ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
class GCU_Install {
    public static function tables() { return array( 'blocks' => 'wp_gcu_blocks', 'events' => 'wp_gcu_events' ); }
}
class GCU_Observability {
    public static $messages = array();
    public static function log( $level, $name, $fields = array() ) {
        self::$messages[] = array( 'name' => $name, 'fields' => $fields );
    }
}
class R54_DB {
    public $rows = array();
    public $faq = array();
    public function prepare( $sql, $n ) { return str_replace( '%d', (string) (int) $n, $sql ); }
    public function get_col( $sql ) { return $this->faq; }
    public function get_results( $sql, $format = ARRAY_A ) { return $this->rows; }
}
require_once dirname( __DIR__ ) . '/14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
require_once dirname( __DIR__ ) . '/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php';
$errors = array();
for ( $i = 0; $i < 40; $i++ ) {
    $v = intdiv( $i, 10 );
    $family = $i % 10;
    $raw = 10 + $v;
    $question = 'Uncovered patient guide request';
    $expected = $family === 0 || $family === 1;
    switch ( $family ) {
        case 0: $raw = 10 + $v; break;
        case 1: $raw = (string) ( 10 + $v ); break;
        case 2: $raw = str_pad( (string) ( 10 + $v ), 12, '0', STR_PAD_LEFT ); break;
        case 3: $raw = ( 10 + $v ) . '.5'; break;
        case 4: $raw = '1e2'; break;
        case 5: $raw = '-' . ( 10 + $v ); break;
        case 6: $raw = ( 10 + $v ) . 'invalid'; break;
        case 7: $raw = array( 10 + $v ); break;
        case 8: $raw = (string) ( 8 - $v ); break;
        case 9: $question = ''; break;
    }
    $signal = array( 'question' => $question, 'count' => $raw );
    $policy = GCU_Future_Policy::valid_faq_aggregate_signal( $signal );
    $wpdb = new R54_DB();
    $GLOBALS['r54_signals'] = array( $signal );
    GCU_Observability::$messages = array();
    $public = GCU_Future_Intelligence::faq_gap_candidates();
    $ok = $policy === $expected && is_array( $public )
        && count( $public ) === ( $expected ? 1 : 0 )
        && ( ! $expected || $public[0]['count'] === 10 + $v );
    if ( ! $ok ) { $errors[] = 'R54 FAQ scenario ' . ( $i + 1 ); }
    echo 'R54 FAQ method ' . ( $i + 1 ) . '/40: ' . ( $ok ? 'PASS' : 'FAIL' ) . PHP_EOL;
}
for ( $i = 0; $i < 40; $i++ ) {
    $v = intdiv( $i, 10 );
    $family = $i % 10;
    $wpdb = new R54_DB();
    $wpdb->rows = array( array( 'funnel_stage' => 'cta_selected', 'total' => (string) ( 20 + $v ) ) );
    $bad = false;
    $expected_suppressed = false;
    if ( 1 === $family ) {
        $wpdb->rows[] = array( 'funnel_stage' => 'destination_loaded', 'total' => (string) ( 1 + $v ) );
        $expected_suppressed = true;
    } elseif ( 2 === $family ) { $wpdb->rows[] = array( 'funnel_stage' => array( 'cta_selected' ), 'total' => '40' ); $bad = true; }
    elseif ( 3 === $family ) { $wpdb->rows[] = array( 'funnel_stage' => 123, 'total' => '40' ); $bad = true; }
    elseif ( 4 === $family ) { $wpdb->rows[] = array( 'funnel_stage' => false, 'total' => '40' ); $bad = true; }
    elseif ( 5 === $family ) { $wpdb->rows[] = array( 'funnel_stage' => null, 'total' => '40' ); $bad = true; }
    elseif ( 6 === $family ) { $wpdb->rows[] = array( 'funnel_stage' => '', 'total' => '40' ); $bad = true; }
    elseif ( 7 === $family ) { $wpdb->rows[] = array( 'funnel_stage' => '   ', 'total' => '40' ); $bad = true; }
    elseif ( 8 === $family ) {
        $invalid = array( '-1', '10.5', null, '1e2' );
        $wpdb->rows[] = array( 'funnel_stage' => 'destination_loaded', 'total' => $invalid[$v] );
        $bad = true;
    } elseif ( 9 === $family ) {
        $wpdb->rows[] = array( 'funnel_stage' => 'unknown_stage', 'total' => '30' );
    }
    GCU_Observability::$messages = array();
    $actual = GCU_Future_Intelligence::friction_summary( 30 );
    $ok = $bad
        ? is_wp_error( $actual )
            && $actual->get_error_code() === 'gcu_friction_stage_row_unverified'
            && count( GCU_Observability::$messages ) === 1
            && GCU_Observability::$messages[0]['fields'] === array()
        : is_array( $actual ) && isset( $actual['stages']['cta_selected'] )
            && $actual['stages']['cta_selected'] === 20 + $v
            && $actual['suppressed'] === $expected_suppressed
            && ! isset( $actual['stages']['unknown_stage'] )
            && ! isset( $actual['stages']['destination_loaded'] );
    if ( ! $ok ) { $errors[] = 'R54 friction scenario ' . ( $i + 1 ); }
    echo 'R54 friction method ' . ( $i + 1 ) . '/40: ' . ( $ok ? 'PASS' : 'FAIL' ) . PHP_EOL;
}
if ( $errors ) { fwrite( STDERR, implode( PHP_EOL, $errors ) . PHP_EOL ); exit( 1 ); }
echo "R54: 40/40 direct FAQ + 40/40 direct friction source SQL/WP-mocked cases PASS; no staging/live acceptance." . PHP_EOL;
