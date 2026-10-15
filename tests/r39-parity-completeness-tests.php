<?php
/**
 * R39: 40 actual-method negative and boundary scenarios for F14-FUT-05/08.
 * SQL is mocked; no claim of WordPress staging or live execution.
 */
define( 'ABSPATH', __DIR__ . '/' );
define( 'ARRAY_A', 'ARRAY_A' );
function wp_strip_all_tags( $text ) { return strip_tags( (string) $text ); }
$GLOBALS['r39_last_parity'] = null;
function update_option( $key, $value, $autoload = false ) {
    if ( 'gcu_future_last_parity' === $key ) { $GLOBALS['r39_last_parity'] = $value; }
    return true;
}
class GCU_Install {
    public static function tables() { return array( 'claims' => 'wp_gcu_claims', 'blocks' => 'wp_gcu_blocks' ); }
}
class GCU_Policy {
    public static function canonical_claims() {
        return array(
            'zero_platform_commission' => array( 'text' => 'Zero platform commission' ),
            'free_approved_core' => array( 'text' => 'Approved core is free' ),
            'optional_support_no_ranking' => array( 'text' => 'Optional support does not affect rankings' ),
        );
    }
    public static function business_rules() {
        return array( 'platform_commission_percent' => 0, 'approved_core_tier' => 'free', 'support_affects_visibility' => false );
    }
}
class GCU_Future_Policy {
    public static $scans = 0;
    public static function dark_pattern_scan( $text ) {
        self::$scans++;
        return array( 'flags' => false !== strpos( $text, 'injected-deception' ) ? array( 'test_deception' ) : array() );
    }
}
class R39DB {
    public $prefix = 'wp_';
    public $mode = 0;
    public $rows = 0;
    public $queries = array();
    public function prepare( $sql, $arg ) { return str_replace( '%s', "'" . (string) $arg . "'", $sql ); }
    public function get_row( $sql, $format = ARRAY_A ) {
        $claims = GCU_Policy::canonical_claims();
        foreach ( $claims as $key => $claim ) {
            if ( false !== strpos( $sql, "'" . $key . "'" ) ) {
                return array(
                    'claim_text' => $claim['text'],
                    'status' => 'active',
                    'is_public' => 1,
                    'review_due_at' => null,
                    'expires_at' => null,
                );
            }
        }
        return null;
    }
    public function get_results( $sql, $format = ARRAY_A ) {
        $this->queries[] = $sql;
        if ( false === strpos( $sql, 'LIMIT 501' ) ) {
            throw new RuntimeException( 'The active-copy scan must use the 501-row overflow sentinel.' );
        }
        if ( 0 === $this->mode ) { return false; }
        if ( 1 === $this->mode ) { return null; }
        $count = 2 === $this->mode ? 501 : $this->rows;
        $list = array();
        for ( $i = 0; $i < $count; $i++ ) {
            $unsafe = 4 === $this->mode && $i === $count - 1;
            $list[] = array(
                'title' => 'Approved title',
                'body' => $unsafe ? 'injected-deception' : 'Source-linked safety statement',
                'cta_label' => 'Read more',
            );
        }
        return $list;
    }
}
$wpdb = new R39DB();
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php';
$errors = array();
for ( $n = 1; $n <= 40; $n++ ) {
    $family = ( $n - 1 ) % 5;
    $variation = (int) floor( ( $n - 1 ) / 5 );
    $wpdb->mode = $family;
    $wpdb->rows = 3 + 6 * $variation;
    $wpdb->queries = array();
    GCU_Future_Policy::$scans = 0;
    $GLOBALS['r39_last_parity'] = null;
    $result = GCU_Future_Intelligence::parity_status();
    $expected = array(
        'active_copy_scan_unverified',
        'active_copy_scan_unverified',
        'active_copy_scan_limit_exceeded',
        null,
        'active_copy:test_deception',
    )[ $family ];
    $issues = isset( $result['issues'] ) && is_array( $result['issues'] ) ? $result['issues'] : array();
    $pass = ( null === $expected ? ( true === $result['ok'] && empty( $issues ) )
                                 : ( false === $result['ok'] && in_array( $expected, $issues, true ) ) )
        && $GLOBALS['r39_last_parity'] === $result
        && 1 === count( $wpdb->queries )
        && GCU_Future_Policy::$scans <= 500
        && ( $family < 2 ? 0 === GCU_Future_Policy::$scans : true )
        && ( 2 === $family ? 500 === GCU_Future_Policy::$scans : true );
    if ( ! $pass ) {
        $errors[] = sprintf( 'R39 scenario %02d family %d FAILED: %s; scans=%d', $n, $family, json_encode( $result ), GCU_Future_Policy::$scans );
    }
    printf( "R39 parity scenario %02d/40: %s (family %d)\n", $n, $pass ? 'PASS' : 'FAIL', $family );
}
if ( $errors ) {
    fwrite( STDERR, implode( "\n", $errors ) . "\n" );
    exit( 1 );
}
echo "R39 40/40 policy-parity boundary cases PASS (mocked WPDB, not staging/live).\n";
