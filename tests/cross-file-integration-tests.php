<?php

$root = dirname( __DIR__ );
$p = $root . '/14-global-clinic-usp-integration';
$failures = array();

function xfit( $condition, $message ) {
	global $failures;
	if ( ! $condition ) { $failures[] = $message; }
}
function xft( $path ) { return file_exists( $path ) ? file_get_contents( $path ) : ''; }

$main = xft( $p . '/global-clinic-usp-integration.php' );
$comp = xft( $p . '/includes/class-gcu-companion-adapters.php' );
$caps = xft( $p . '/includes/class-gcu-capabilities.php' );
$con  = xft( $p . '/includes/class-gcu-contracts.php' );
$front= xft( $p . '/includes/class-gcu-frontend.php' );
$obs  = xft( $p . '/includes/class-gcu-observability.php' );

xfit( false !== strpos( $main, "GCU_VERSION', '1.4.5" ), 'Release version is not 1.4.5.' );
xfit( false !== strpos( $main, "GCU_CANONICAL_REPOSITORY', '14-global-clinic-usp-and-conversion-sections" ), 'Current repository identity is not exact.' );
xfit( false !== strpos( $main, 'class-gcu-companion-adapters.php' ), 'Companion adapter bootstrap missing.' );

xfit( false !== strpos( $comp, 'smc_membership_assertions' ) && false !== strpos( $comp, 'SMC_Contracts' ), 'File 00 current assertions contract missing.' );
xfit( false !== strpos( $caps, 'GCU_Companion_Adapters::authorize' ) && false === strpos( $caps, "return (bool) apply_filters( 'gcu_authorize', true" ), 'Authorization still depends on obsolete File14-only provider hook.' );

xfit( false !== strpos( $comp, 'DDD_Contracts::dependency_health' ) && false !== strpos( $comp, "home_url( '/doctors/' )" ), 'File 07 current directory contract missing.' );
xfit( false !== strpos( $comp, 'WCA_Contracts::contract_manifest' ) && false !== strpos( $comp, "home_url( '/appointments/' )" ), 'File 08 current clinic contract missing.' );
xfit( false !== strpos( $comp, 'GDO_Operations::health' ) && false !== strpos( $comp, 'GDO_Plugin::application_url' ), 'File 09 current onboarding contract missing.' );

xfit( false !== strpos( $comp, 'sabri_file20_navigation_items' ) && false !== strpos( $comp, 'sabri_file20_module_health' ), 'File 20 current shell contracts missing.' );
xfit( false !== strpos( $comp, 'sabri_shell_context_navigation_fallback_url' ), 'File 20 contextual navigation contract missing.' );
xfit( false === strpos( $front, 'sabri_shell_back_home_controls' ), 'Obsolete File 20 back/home filter remains.' );
xfit( false === strpos( $con, 'sabri_shell_slot_ready_v1' ) && false !== strpos( $con, 'gcu_file14_placement_ready_v1' ), 'File14 semantic placement still depends on obsolete File20 slot filter.' );

xfit( false !== strpos( $comp, 'Sabri\\PublicExperience\\Components' ) && false !== strpos( $front, 'sabri-ui-card' ), 'File 25 current reusable presentation contract missing.' );
xfit( false !== strpos( $comp, 'spcrc/module_manifests' ) && false !== strpos( $comp, "'canonical_data_owner'" ), 'File 24 assurance manifest bridge missing.' );

xfit( false !== strpos( $con, 'GCU_Companion_Adapters::destination_probe' ), 'Request-time owner destination probe missing.' );
xfit( false !== strpos( $con, "'source'=>'owner_event'" ), 'Legacy owner event compatibility path missing.' );
xfit( false !== strpos( $con, "$url='';" ), 'Unavailable destination does not clear its delivery URL.' );

xfit( false !== strpos( $obs, 'GCU_Companion_Adapters::dependency_health' ), 'Observability does not report actual companion health.' );
xfit( false !== strpos( $comp, "'file24_assurance'" ) && false !== strpos( $comp, "'file25_visual'" ), 'Assurance/visual dependencies are not observable.' );

xfit( false === strpos( $comp, '$wpdb->prefix' ) && false === strpos( $comp, 'INSERT INTO' ) && false === strpos( $comp, 'UPDATE ' ), 'Cross-file adapter must not write companion owner tables.' );

if ( $failures ) {
	fwrite( STDERR, "Cross-file integration tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Cross-file integration tests: PASS\n";
