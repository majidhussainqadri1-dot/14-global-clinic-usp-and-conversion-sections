<?php
/** File 14 eleventh fresh cycle — Round 20 report-workflow regression gate. */
$root = dirname( __DIR__ );
$bounds = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-round16-bounds.php' );
if ( false === $bounds ) {
	fwrite( STDERR, "Round 20 hardening source could not be read.\n" );
	exit( 1 );
}
$required = array(
	"safe_future_html",
	"name=\"report_id\"",
	"report_identity_guard",
	"gcu_future_report_identity_conflict",
	"safe_future_rest_paths",
	"/gcu/v1/future/reports",
	"/gcu/v1/future/records",
	"gcu_future_reports_page_query_failed",
	"gcu_future_records_page_query_failed",
	"future_admin_reports_preflight_failed",
	"gcu_future_report_read_failed",
	"future-report:",
	"gcu_report_update",
	"remove_action( 'admin_post_gcu_future_report'",
	"remove_action( 'admin_post_gcu_future_resolve_report'",
);
foreach ( $required as $marker ) {
	if ( false === strpos( $bounds, $marker ) ) {
		fwrite( STDERR, "Round 20 hardening marker missing: {$marker}\n" );
		exit( 1 );
	}
}
foreach ( array(
	'.github/workflows/file14-round20-apply.yml',
	'scripts/round20-admin-reports-fix.py',
	'scripts/round20-report-read-fix.py',
	'scripts/round20-report-reliability-fix.py',
) as $temporary ) {
	if ( file_exists( $root . '/' . $temporary ) ) {
		fwrite( STDERR, "Temporary Round 20 machinery remains: {$temporary}\n" );
		exit( 1 );
	}
}
echo "Eleventh-cycle Round 20 report-workflow regression gate: PASS\n";
