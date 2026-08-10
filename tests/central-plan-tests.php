<?php

$root = dirname( __DIR__ );
$plugin = $root . '/14-global-clinic-usp-integration';
$failures = array();

function central_check( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures[] = $message;
	}
}

function central_text( $path ) {
	return file_exists( $path ) ? file_get_contents( $path ) : '';
}

$main = central_text( $plugin . '/global-clinic-usp-integration.php' );
$front = central_text( $plugin . '/includes/class-gcu-frontend.php' );
$privacy = central_text( $plugin . '/includes/class-gcu-privacy.php' );
$i18n = central_text( $plugin . '/includes/class-gcu-i18n.php' );
$css = central_text( $plugin . '/assets/css/global-clinic-usp-integration.css' );
$trace = central_text( $root . '/docs/REQUIREMENTS-TRACEABILITY.md' );
$readme = central_text( $plugin . '/readme.txt' );

central_check( false !== strpos( $main, "GCU_CENTRAL_PLAN_BASELINE', '2026-08-10" ), 'Fresh central-plan baseline is not frozen in runtime metadata.' );
central_check( false !== strpos( $main, "GCU_PLAN_VERSION', 'SSH-F14-PLAN-2026-v1.0" ), 'Fresh File 14 plan identity missing.' );
central_check( false !== strpos( $main, "GCU_CANONICAL_REPOSITORY', '14-global-clinic-usp-and-conversion-integration" ), 'Fresh File 14 canonical repository identity missing.' );
central_check( false !== strpos( $css, '#087A4E' ), 'CEN-BRAND-001 exact Sabri Green missing.' );
central_check( false === strpos( $css, '--gcu-brand-primary: #ff' ) && false === strpos( $css, '--gcu-brand-primary: #FF' ), 'Orange may not be the primary brand token.' );
central_check( false !== strpos( $front, 'sabri_shell_back_home_controls' ), 'CEN-NAV-001 File 20 navigation contract missing.' );
central_check( false !== strpos( $front, 'data-gcu-shell-fallback' ), 'Safe local fallback navigation is missing.' );
foreach ( array( 'onclick=', 'onload=', 'onerror=', 'onchange=', 'onfocus=', 'onmouseover=', 'onmouseenter=', 'onmouseleave=', 'javascript:' ) as $inline_executable ) {
	central_check( false === stripos( $front, $inline_executable ), 'Inline executable markup is forbidden: ' . $inline_executable );
}
central_check( false === stripos( $front, '<script' ), 'Inline script elements are forbidden.' );
central_check( false !== strpos( $front, "'doctor_directory'" ) && false !== strpos( $front, "'doctor_onboarding'" ), 'File 07/File 09 destination contracts missing.' );
central_check( false !== strpos( $front, 'GCU_Plugin::instance()->contracts()->destination' ), 'CTA destinations must resolve through owner contracts.' );
central_check( false !== strpos( $privacy, 'is_file14_acquisition_route' ), 'Measurement is not bounded to File 14 acquisition routes.' );
central_check( false !== strpos( $privacy, 'global_privacy_control_requested' ), 'Anti-surveillance GPC boundary missing.' );
central_check( false !== strpos( $privacy, 'low_bandwidth_requested' ), 'Low-bandwidth boundary missing.' );
central_check( false !== strpos( $i18n, "'en-US'" ) && false !== strpos( $i18n, "'ur-PK'" ) && false !== strpos( $i18n, "'ar-SA'" ), 'Required localization set incomplete.' );
central_check( false !== strpos( $i18n, 'missing_keys' ), 'Mixed/partial translation release gate missing.' );
central_check( false !== strpos( $css, 'prefers-reduced-motion' ) && false !== strpos( $css, 'prefers-reduced-data' ) && false !== strpos( $css, 'forced-colors' ), 'Accessibility/reduced-data media contracts incomplete.' );
central_check( false !== strpos( $css, 'max-width: 360px' ), 'Small-screen reflow guard missing.' );
central_check( false !== strpos( $readme, 'Stable tag: 1.3.0' ), 'Readme release identity drift.' );

foreach ( array( 'CEN-GOV-001', 'CEN-OWN-001', 'CEN-BIZ-001', 'CEN-DON-001', 'CEN-BRAND-001', 'CEN-NAV-001', 'F14-FR-001', 'F14-FR-016', 'F14-NFR-010', 'DoD-11', 'DoD-13' ) as $id ) {
	central_check( false !== strpos( $trace, $id ), 'Traceability missing governing identity: ' . $id );
}

central_check( ! is_dir( $root . '/.file14-v110-bootstrap' ), 'Obsolete v1.1 bootstrap fragments must not remain in the release tree.' );
central_check( ! file_exists( $root . '/.github/workflows/file14-v110-expand.yml' ), 'Obsolete bootstrap expansion workflow must be removed.' );

if ( $failures ) {
	fwrite( STDERR, "Central-plan tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Central-plan tests: PASS\n";
