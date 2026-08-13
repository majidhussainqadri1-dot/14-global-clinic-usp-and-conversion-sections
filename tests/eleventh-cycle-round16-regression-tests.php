<?php
/** File 14 eleventh fresh cycle — Round 16 resource-bound regression gate. */
$root = dirname( __DIR__ );
$bootstrap = file_get_contents( $root . '/14-global-clinic-usp-integration/global-clinic-usp-integration.php' );
$bounds = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-round16-bounds.php' );
$future = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );

if ( false === $bootstrap || false === $bounds || false === $future ) {
	fwrite( STDERR, "Round 16 source could not be read.\n" );
	exit( 1 );
}

$markers = array(
	"includes/class-gcu-round16-bounds.php",
	"GCU_Round16_Bounds', 'bootstrap",
	"MAX_QUESTION_SIGNALS = 500",
	"MAX_FAQ_TITLES = 500",
	"MAX_CONSISTENCY_ROWS = 1000",
	"gcu_future_question_aggregates",
	"/gcu/v1/future/consistency",
	"/gcu/v1/future/scenarios",
	"gcu_future_daily_governance",
	"settings_page_global-clinic-usp-future",
	"future_faq_gap_scan_suppressed",
	"future_consistency_scan_suppressed",
);

$haystack = $bootstrap . "\n" . $bounds;
foreach ( $markers as $marker ) {
	if ( false === strpos( $haystack, $marker ) ) {
		fwrite( STDERR, "Round 16 hard-bound marker missing: {$marker}\n" );
		exit( 1 );
	}
}

$filter_pos = strpos( $future, "apply_filters( 'gcu_future_question_aggregates'" );
$type_pos = strpos( $future, "if ( ! is_array( \$signals ) )", $filter_pos );
$query_pos = strpos( $future, 'SELECT LOWER(title)', $filter_pos );
if ( false === $filter_pos || false === $type_pos || false === $query_pos || ! ( $filter_pos < $type_pos && $type_pos < $query_pos ) ) {
	fwrite( STDERR, "Round 16 FAQ suppression cannot fail closed before the catalog query.\n" );
	exit( 1 );
}

echo "Eleventh-cycle Round 16 resource-bound regression gate: PASS\n";
