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

xfit( false !== strpos( $comp, 'SMC_Contracts' ) && false !== strpos( $comp, 'smc_assertions_v1' ) && false !== strpos( $comp, 'smc_restricted_capabilities' ), 'File 00 current assertions/hardening contract missing.' );
xfit( false !== strpos( $caps, 'GCU_Companion_Adapters::authorize' ) && false === strpos( $caps, "return (bool) apply_filters( 'gcu_authorize', true" ), 'Authorization still depends on obsolete File14-only provider hook.' );

xfit( false !== strpos( $comp, 'DDD_Contracts::dependency_health' ) && false !== strpos( $comp, "home_url( '/doctors/' )" ), 'File 07 current directory contract missing.' );
xfit( false !== strpos( $comp, 'WCA_Contracts::contract_manifest' ) && false !== strpos( $comp, "home_url( '/appointments/' )" ), 'File 08 current clinic contract missing.' );
xfit( false !== strpos( $comp, 'gdo_file14_onboarding_destination' ), 'File 09 current onboarding contract missing.' );

xfit( false !== strpos( $comp, 'SPF_Registry' ) && false !== strpos( $comp, 'file01_route_registry_state' ) && false !== strpos( $comp, 'placement_contract_ready' ) && false !== strpos( $comp, "'file-01', '2.0.1'" ) && false !== strpos( $comp, 'GCU_Companion_Adapters::file01_health' ), 'File 01 canonical registry/health contract missing.' );
xfit( false !== strpos( $comp, 'sync_file01_registry' ) && false !== strpos( $comp, 'SPF_Registry::register_manifest' ) && false !== strpos( $comp, 'SPF_Registry::map_route' ) && false !== strpos( $comp, 'SPF_Registry::register_contract' ), 'File 01 authorized manifest/route/API-event registry sync missing.' );
xfit( false !== strpos( $comp, "'file14-global-clinic'" ) && false !== strpos( $comp, "'/global-clinic/'" ) && false !== strpos( $comp, "'/find-a-global-doctor/'" ) && false !== strpos( $comp, "'/start-your-global-clinic/'" ) && false !== strpos( $comp, "'/clinic/how-it-works/'" ), 'File 14 canonical File 01 route declarations incomplete.' );

xfit( false !== strpos( $comp, 'sabri_shell_route_result_allowed' ) && false !== strpos( $comp, 'sabri_shell_system_check_sections' ), 'File 20 current shell contracts missing.' );
xfit( false !== strpos( $comp, 'sabri_shell_context_navigation_fallback_url' ), 'File 20 contextual navigation contract missing.' );
xfit( false === strpos( $front, 'sabri_shell_back_home_controls' ), 'Obsolete File 20 back/home filter remains.' );
xfit( false === strpos( $con, 'sabri_shell_slot_ready_v1' ) && false !== strpos( $con, 'gcu_file14_placement_ready_v1' ) && false !== strpos( $con, 'GCU_Companion_Adapters::placement_contract_ready' ), 'File14 placement does not enforce current File01/File20 readiness.' );

xfit( false !== strpos( $comp, 'Sabri\\PublicExperience\\Components' ) && false !== strpos( $front, 'sabri-ui-card' ), 'File 25 current reusable presentation contract missing.' );
xfit( false !== strpos( $comp, 'spcrc/module_manifests' ) && false !== strpos( $comp, "'canonical_data_owner'" ), 'File 24 assurance manifest bridge missing.' );
xfit( false !== strpos( $comp, "'posture'                => 'unassessed'" ) && false !== strpos( $comp, "'last_security_test'     => ''" ), 'File 24 manifest must not fabricate repository security acceptance.' );

xfit( false !== strpos( $con, 'GCU_Companion_Adapters::destination_probe' ), 'Request-time owner destination probe missing.' );
xfit( false !== strpos( $con, "'source'=>'owner_event'" ), 'Legacy owner event compatibility path missing.' );
xfit( false !== strpos( $con, '$url=\'\';' ), 'Unavailable destination does not clear its delivery URL.' );

xfit( false !== strpos( $obs, 'GCU_Companion_Adapters::dependency_health' ), 'Observability does not report actual companion health.' );
xfit( false !== strpos( $comp, "'file19_notifications'" ) && false !== strpos( $comp, 'sun_register_notification_producer' ) && false !== strpos( $comp, 'sun_ingest_domain_event' ) && false !== strpos( $comp, 'gcu_operational_notification_recipients' ), 'File 19 explicit-recipient notification integration missing.' );
xfit( false !== strpos( $comp, "'file24_assurance'" ) && false !== strpos( $comp, "'file25_visual'" ), 'Assurance/visual dependencies are not observable.' );

xfit( false === strpos( $comp, '$wpdb->prefix' ) && false === strpos( $comp, 'INSERT INTO' ) && false === strpos( $comp, 'UPDATE ' ), 'Cross-file adapter must not write companion owner tables.' );

if ( $failures ) {
	fwrite( STDERR, "Cross-file integration tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Cross-file integration tests: PASS\n";
