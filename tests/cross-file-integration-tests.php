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

xfit( false !== strpos( $main, "GCU_VERSION', '1.4.10" ), 'Release version is not 1.4.10.' );
xfit( false !== strpos( $main, "GCU_CANONICAL_REPOSITORY', '14-global-clinic-usp-and-conversion-sections" ), 'Current repository identity is not exact.' );
xfit( false !== strpos( $main, 'class-gcu-companion-adapters.php' ), 'Companion adapter bootstrap missing.' );

xfit( false !== strpos( $comp, 'FILE00_MIN_VERSION' ) && false !== strpos( $comp, 'FILE00_MIN_CONTRACT' ) && false !== strpos( $comp, 'SMC_VERSION' ) && false !== strpos( $comp, 'SMC_CONTRACT_VERSION' ) && false !== strpos( $comp, 'SMC_Contracts' ) && false !== strpos( $comp, 'smc_assertions_v1' ) && false !== strpos( $comp, 'smc_restricted_capabilities' ), 'File 00 current versioned assertions/hardening contract missing.' );
xfit( false !== strpos( $caps, 'GCU_Companion_Adapters::authorize' ) && false === strpos( $caps, "return (bool) apply_filters( 'gcu_authorize', true" ), 'Authorization still depends on obsolete File14-only provider hook.' );

xfit( false !== strpos( $comp, "FILE07_MIN_VERSION = '1.2.1'") && false !== strpos( $comp, "FILE07_MIN_CONTRACT = '1.2.1'") && false !== strpos( $comp, 'DDD_VERSION' ) && false !== strpos( $comp, 'DDD_CONTRACT_VERSION' ) && false !== strpos( $comp, "array( 'DDD_Contracts', 'dependency_health' )" ) && false !== strpos( $comp, 'DDD_Observability' ) && false !== strpos( $comp, 'system_check' ) && false !== strpos( $comp, "home_url( '/doctors/' )" ), 'File 07 current versioned directory/runtime-health contract missing.' );
xfit( false !== strpos( $comp, 'FILE08_MIN_VERSION' ) && false !== strpos( $comp, 'FILE08_MIN_API' ) && false !== strpos( $comp, 'WCA_VERSION' ) && false !== strpos( $comp, "array( 'WCA_Contracts', 'contract_manifest' )" ) && false !== strpos( $comp, 'WCA_Observability' ) && false !== strpos( $comp, 'runtime_health' ) && false !== strpos( $comp, "'/appointments'" ) && false !== strpos( $comp, 'commission_percent' ) && false !== strpos( $comp, 'donation_visibility_link' ), 'File 08 current versioned clinic/runtime-health contract missing.' );
xfit( false !== strpos( $comp, 'FILE09_MIN_VERSION' ) && false !== strpos( $comp, 'FILE09_MIN_CONTRACT' ) && false !== strpos( $comp, 'GDO_VERSION' ) && false !== strpos( $comp, 'gdo_file14_onboarding_destination' ) && false !== strpos( $comp, "'file09'" ) && false !== strpos( $comp, "'file14'" ) && false !== strpos( $comp, 'writes_data' ) && false !== strpos( $comp, 'automatic_enrollment' ) && false !== strpos( $comp, 'automatic_verification' ), 'File 09 current onboarding contract invariants missing.' );

xfit( false !== strpos( $comp, 'FILE01_MIN_VERSION' ) && false !== strpos( $comp, 'FILE01_MIN_CONTRACT' ) && false !== strpos( $comp, 'SPF_VERSION' ) && false !== strpos( $comp, 'SPF_CONTRACT_VERSION' ) && false !== strpos( $comp, 'SPF_Registry' ) && false !== strpos( $comp, 'file01_route_registry_state' ) && false !== strpos( $comp, 'placement_contract_ready' ) && false !== strpos( $comp, 'GCU_Companion_Adapters::file01_health' ) && false !== strpos( $comp, 'contracts_ready' ) && false !== strpos( $comp, 'missing_contracts' ) && false !== strpos( $comp, 'incompatible_contracts' ) && false !== strpos( $comp, "registered', 'compatible', 'active" ), 'File 01 canonical lifecycle/route/contract readiness missing.' );
xfit( false !== strpos( $comp, 'file01_manifest_current( $module, $expected_manifest )' ) && false !== strpos( $comp, 'expected_routes' ) && false !== strpos( $comp, 'contract-drift' ) && false !== strpos( $comp, "'route_key'" ) && false !== strpos( $comp, "'redirects'" ), 'R5 File 01 full-manifest route semantics missing.' );
xfit( false !== strpos( $comp, 'sync_file01_registry' ) && false !== strpos( $comp, 'SPF_Registry::register_manifest' ) && false !== strpos( $comp, 'SPF_Registry::map_route' ) && false !== strpos( $comp, 'SPF_Registry::register_contract' ), 'File 01 authorized manifest/route/API-event registry sync missing.' );
xfit( false !== strpos( $comp, "'file14-global-clinic'" ) && false !== strpos( $comp, "'/global-clinic/'" ) && false !== strpos( $comp, "'/find-a-global-doctor/'" ) && false !== strpos( $comp, "'/start-your-global-clinic/'" ) && false !== strpos( $comp, "'/clinic/how-it-works/'" ), 'File 14 canonical File 01 route declarations incomplete.' );

xfit( false !== strpos( $comp, 'FILE20_MIN_VERSION' ) && false !== strpos( $comp, 'FILE20_MIN_CONTRACT' ) && false !== strpos( $comp, 'FILE20_MAX_CONTRACT_EXCLUSIVE' ) && false !== strpos( $comp, 'SABRI_SHELL_VERSION' ) && false !== strpos( $comp, 'CentralPlanContract' ) && false !== strpos( $comp, 'canonical_contracts' ) && false !== strpos( $comp, 'approved-clinic-cta' ) && false !== strpos( $comp, 'slots-only' ) && false !== strpos( $comp, 'cta-hidden' ) && false !== strpos( $comp, 'owner-aware-bounded' ) && false !== strpos( $comp, 'sabri_shell_route_result_allowed' ) && false !== strpos( $comp, 'sabri_shell_system_check_sections' ), 'File 20 owner-native semantic shell contract missing.' );
xfit( false !== strpos( $comp, 'sabri_shell_context_navigation_fallback_url' ), 'File 20 contextual navigation contract missing.' );
xfit( false === strpos( $front, 'sabri_shell_back_home_controls' ), 'Obsolete File 20 back/home filter remains.' );
xfit( false === strpos( $con, 'sabri_shell_slot_ready_v1' ) && false !== strpos( $con, 'gcu_file14_placement_ready_v1' ) && false !== strpos( $con, 'GCU_Companion_Adapters::placement_contract_ready' ), 'File14 placement does not enforce current File01/File20 readiness.' );

xfit( false !== strpos( $comp, 'FILE25_MIN_CONTRACT' ) && false !== strpos( $comp, 'FILE25_MIN_COMPONENT_CONTRACT' ) && false !== strpos( $comp, 'sabri_visual_experience_contract' ) && false !== strpos( $comp, 'sabri_visual_experience_render_state' ) && false !== strpos( $comp, 'visual_system_owner' ) && false !== strpos( $comp, 'global_shell_owner' ) && false === strpos( $comp, 'Sabri\\PublicExperience\\Components::render_state' ) && false !== strpos( $front, 'sabri-ui-card' ), 'File 25 current public versioned presentation contract missing.' );
xfit( false !== strpos( $comp, 'FILE24_MIN_VERSION' ) && false !== strpos( $comp, 'SPCRC_VERSION' ) && false !== strpos( $comp, "did_action( 'spcrc/booted' )" ) && false !== strpos( $comp, 'spcrc/governed_artifact_registry' ) && false !== strpos( $comp, 'spcrc/module_manifests' ) && false !== strpos( $comp, "'canonical_data_owner'" ), 'File 24 successful-boot/versioned assurance bridge missing.' );
xfit( false !== strpos( $comp, "'posture'                => 'unassessed'" ) && false !== strpos( $comp, "'last_security_test'     => ''" ), 'File 24 manifest must not fabricate repository security acceptance.' );

xfit( false !== strpos( $con, 'GCU_Companion_Adapters::destination_probe' ), 'Request-time owner destination probe missing.' );
xfit( false !== strpos( $con, "'source'=>'owner_event'" ), 'Legacy owner event compatibility path missing.' );
xfit( false !== strpos( $con, '$url=\'\';' ), 'Unavailable destination does not clear its delivery URL.' );

xfit( false !== strpos( $obs, 'GCU_Companion_Adapters::dependency_health' ), 'Observability does not report actual companion health.' );
xfit( false !== strpos( $comp, 'FILE19_MIN_VERSION' ) && false !== strpos( $comp, 'SUN_VERSION' ) && false !== strpos( $comp, "'file19_notifications'" ) && false !== strpos( $comp, 'sun_register_notification_producer' ) && false !== strpos( $comp, 'sun_ingest_domain_event' ) && false !== strpos( $comp, 'gcu_operational_notification_recipients' ), 'File 19 versioned explicit-recipient notification integration missing.' );
xfit( false !== strpos( $comp, 'private static function owner_call' ) && false !== strpos( $comp, 'catch ( Throwable $exception )' ) && false !== strpos( $comp, 'companion_contract_exception' ) && false !== strpos( $comp, 'return $fallback' ), 'Companion owner calls are not exception-isolated/fail-closed.' );
xfit( false !== strpos( $comp, "'file24_assurance'" ) && false !== strpos( $comp, "'file25_visual'" ), 'Assurance/visual dependencies are not observable.' );

xfit( false === strpos( $comp, '$wpdb->prefix' ) && false === strpos( $comp, 'INSERT INTO' ) && false === strpos( $comp, 'UPDATE ' ), 'Cross-file adapter must not write companion owner tables.' );

if ( $failures ) {
	fwrite( STDERR, "Cross-file integration tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Cross-file integration tests: PASS\n";
