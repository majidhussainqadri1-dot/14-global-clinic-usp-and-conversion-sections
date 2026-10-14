<?php
/**
 * R36 privacy regression: forty distinct owner-stage grouped result sets.
 * WordPress-free behavior tests of the actual File14 class (not live E2E).
 */
define( 'ABSPATH', __DIR__ . '/' );
define( 'ARRAY_A', 'ARRAY_A' );
function absint( $n ) { return abs( (int) $n ); }
function is_wp_error( $v ) { return $v instanceof WP_Error; }
class WP_Error { public $code; public function __construct( $code ) { $this->code = $code; } }
class GCU_Capabilities {
    const VIEW_ANALYTICS = 'view_analytics';
    public static $allowed = true;
    public static function require_capability( $cap, $record = null, $purpose = '' ) {
        return self::$allowed ? true : new WP_Error( 'gcu_unauthorized' );
    }
}
class GCU_Install {
    public static function tables() { return array( 'events' => 'wp_gcu_events' ); }
}
class R36CohortDB {
    public $rows = array();
    public function prepare( $sql, $days ) { return str_replace( '%d', (int) $days, $sql ); }
    public function get_results( $sql, $format = ARRAY_A ) { return $this->rows; }
}
$wpdb = new R36CohortDB();
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-owner-outcomes.php';
$names = array(
    'dir' => array( 'event_type' => 'File07.DirectoryServed.v1', 'funnel_stage' => 'destination_served' ),
    'book' => array( 'event_type' => 'File08.BookingStarted.v1', 'funnel_stage' => 'booking_started' ),
    'apply' => array( 'event_type' => 'File09.ApplicationStarted.v1', 'funnel_stage' => 'application_started' ),
    'invalid' => array( 'event_type' => 'File08.BookingStarted.v1', 'funnel_stage' => 'destination_served' ),
);
function r36_row( $kind, $count ) {
    global $names;
    return array_merge( $names[ $kind ], array( 'total' => $count ) );
}
$failures = array();
for ( $round = 0; $round < 40; $round++ ) {
    $mode = $round % 4;
    if ( 0 === $mode ) { // exactly >=10 public cohorts
        $wpdb->rows = array( r36_row( 'dir', 10 + $round ), r36_row( 'book', 10 + $round ) );
        $expected_items = 2; $expected_suppressed = false; $forbidden_stage = '';
    } elseif ( 1 === $mode ) { // combination of >=10 plus a rare booking
        $wpdb->rows = array( r36_row( 'dir', 12 + $round ), r36_row( 'book', 1 + $round % 9 ) );
        $expected_items = 1; $expected_suppressed = true; $forbidden_stage = 'booking_started';
    } elseif ( 2 === $mode ) { // ALL cohorts under10, sum under10
        $wpdb->rows = array( r36_row( 'dir', 1 + $round % 3 ), r36_row( 'book', 1 ), r36_row( 'apply', 2 ) );
        $expected_items = 0; $expected_suppressed = true; $forbidden_stage = 'application_started';
    } else { // invalid cross-owner type must not poison group size
        $wpdb->rows = array( r36_row( 'book', 10 + $round ), r36_row( 'invalid', 100 + $round ) );
        $expected_items = 1; $expected_suppressed = false; $forbidden_stage = 'destination_served';
    }
    $r = GCU_Owner_Outcomes::owner_stage_summary( 30 );
    $encoded = json_encode( $r );
    $ok = is_array( $r ) && count( $r['items'] ) === $expected_items
        && $r['suppressed'] === $expected_suppressed
        && ( '' === $forbidden_stage || false === strpos( $encoded, '"stage":"' . $forbidden_stage . '"' ) )
        && $r['success_rate'] === null && $r['linked_journey'] === false;
    if ( ! $ok ) { $failures[] = 'Privacy cohort case ' . ( $round + 1 ) . ': ' . $encoded; }
    echo 'R36 cohort privacy ' . sprintf( '%02d', $round + 1 ) . '/40: ' . ( $ok ? 'PASS' : 'FAIL' ) . PHP_EOL;
}
// Authorization guard must remain in front of all aggregate reads.
GCU_Capabilities::$allowed = false;
$denied = GCU_Owner_Outcomes::owner_stage_summary();
if ( ! is_wp_error( $denied ) ) { $failures[] = 'Owner summary lacks File00 view_analytics authorization'; }
if ( $failures ) { fwrite( STDERR, implode( PHP_EOL, $failures ) . PHP_EOL ); exit( 1 ); }
echo "R36 privacy: forty behavioral cohort tests and denied-authority test PASS. Source-mode only; staging/live not verified." . PHP_EOL;
