<?php

$root = dirname( __DIR__ );
$plugin = $root . '/14-global-clinic-usp-integration';
$failures = array();

function check( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures[] = $message;
	}
}

function file_text( $path ) {
	return file_exists( $path ) ? file_get_contents( $path ) : '';
}

$required = array(
	'global-clinic-usp-integration.php',
	'includes/class-gcu-i18n.php',
	'includes/class-gcu-policy.php',
	'includes/class-gcu-capabilities.php',
	'includes/class-gcu-install.php',
	'includes/class-gcu-repository.php',
	'includes/class-gcu-contracts.php',
	'includes/class-gcu-observability.php',
	'includes/class-gcu-privacy.php',
	'includes/class-gcu-rest.php',
	'includes/class-gcu-frontend.php',
	'includes/class-gcu-admin.php',
	'includes/class-gcu-plugin.php',
	'assets/css/global-clinic-usp-integration.css',
	'assets/js/global-clinic-usp-integration.js',
	'templates/public-page.php',
	'readme.txt',
	'uninstall.php',
);
foreach ( $required as $relative ) {
	check( file_exists( $plugin . '/' . $relative ), 'Missing required file: ' . $relative );
}

$all = '';
$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $plugin, FilesystemIterator::SKIP_DOTS ) );
foreach ( $iterator as $file ) {
	if ( $file->isFile() && preg_match( '/\.(php|js|css|txt)$/', $file->getFilename() ) ) {
		$all .= "\n" . file_get_contents( $file->getPathname() );
	}
}

$main = file_text( $plugin . '/global-clinic-usp-integration.php' );
$i18n = file_text( $plugin . '/includes/class-gcu-i18n.php' );
$install = file_text( $plugin . '/includes/class-gcu-install.php' );
$repo = file_text( $plugin . '/includes/class-gcu-repository.php' );
$contracts = file_text( $plugin . '/includes/class-gcu-contracts.php' );
$rest = file_text( $plugin . '/includes/class-gcu-rest.php' );
$front = file_text( $plugin . '/includes/class-gcu-frontend.php' );
$privacy = file_text( $plugin . '/includes/class-gcu-privacy.php' );
$observability = file_text( $plugin . '/includes/class-gcu-observability.php' );
$css = file_text( $plugin . '/assets/css/global-clinic-usp-integration.css' );
$js = file_text( $plugin . '/assets/js/global-clinic-usp-integration.js' );
$uninstall = file_text( $plugin . '/uninstall.php' );

check( false !== strpos( $main, 'Version: 1.3.0' ), 'Software version must be 1.3.0.' );
check( false !== strpos( $main, "define( 'GCU_VERSION', '1.3.0' )" ), 'Runtime version constant must be 1.3.0.' );
check( false !== strpos( $main, "define( 'GCU_BRAND_PRIMARY', '#087A4E' )" ), 'Central Sabri Green constant missing.' );
check( false !== strpos( $main, "define( 'GCU_CANONICAL_REPOSITORY', '14-global-clinic-usp-and-conversion-integration' )" ), 'Canonical repository identity missing.' );
check( false !== strpos( $main, 'Text Domain: global-clinic-usp-integration' ), 'Canonical text domain missing.' );
check( false === strpos( $all, 'wp_insert_post(' ), 'File 14 must not create or overwrite WordPress pages.' );
check( false === strpos( $all, 'doctor-portal' ), 'Legacy duplicate doctor portal must not be owned by File 14.' );

foreach ( array( 'gcu_claims', 'gcu_content_blocks', 'gcu_placements', 'gcu_experiments', 'gcu_conversion_events', 'gcu_audit_log', 'gcu_event_outbox', 'gcu_event_inbox' ) as $table ) {
	check( false !== strpos( $install, $table ), 'Missing canonical table: ' . $table );
}
foreach ( array( 'gcu_install_lock', 'dbDelta', 'canonical_block_sets', 'global_clinic_faq', 'success_metric', 'sample_policy', 'privacy_policy' ) as $needle ) {
	check( false !== strpos( $install, $needle ), 'Install/governance contract missing: ' . $needle );
}
check( false !== strpos( $install, 'read-only inventory' ), 'Legacy migration must be read-only.' );

foreach ( array( 'ClinicUSPCTASelected.v1', 'ClinicUSPContentPublished.v1', 'DoctorDirectoryAvailable.v1', 'ClinicBookingAvailable.v1', 'DoctorOnboardingAvailable.v1', 'BusinessPolicyChanged.v1' ) as $event ) {
	check( false !== strpos( $repo . $contracts, $event ), 'Missing event contract: ' . $event );
}
foreach ( array( 'row_version', 'INSERT IGNORE', 'dispatch_outbox', 'accept_inbound_event', 'withdraw_claim', 'create_experiment', 'gcu_version_conflict' ) as $needle ) {
	check( false !== strpos( $repo, $needle ), 'Repository reliability/governance contract missing: ' . $needle );
}
check( false !== strpos( $repo, "'threshold' => 10" ), 'Analytics small-number suppression missing.' );

foreach ( array( '/blocks', '/destinations', '/events', '/content', '/placements', '/experiments', '/claims/', '/workflow/', '/analytics/funnel', '/health' ) as $route ) {
	check( false !== strpos( $rest, $route ), 'Missing REST contract: ' . $route );
}
check( false !== strpos( $rest, 'X-GCU-Event-Token' ) && false !== strpos( $rest, 'delete_transient' ), 'Single-use public event anti-replay contract missing.' );

foreach ( array( '^global-clinic/?$', '^clinic/how-it-works/?$', '^find-a-global-doctor/?$', '^start-your-global-clinic/?$' ) as $route ) {
	check( false !== strpos( $front, $route ), 'Missing canonical route: ' . $route );
}
check( false !== strpos( $front, 'sabri_shell_back_home_controls' ), 'File 20 Back/Home contract missing.' );
check( false !== strpos( $front, 'wp_safe_redirect' ), 'Safe destination redirect missing.' );
check( false !== strpos( $front, 'GCU_I18n::language' ) && false !== strpos( $front, 'GCU_I18n::direction' ), 'Per-root language/direction handling missing.' );
check( false !== strpos( $front, 'data-gcu-module-version' ), 'Versioned semantic root metadata missing.' );
check( false === stripos( $front, 'onclick=' ) && false === stripos( $front, 'javascript:' ), 'Inline executable navigation is forbidden.' );
check( false === strpos( $front, '<main class="gcu-page"' ), 'File 14 must not introduce a duplicate main landmark.' );
check( false !== strpos( $front, '<svg' ), 'Semantic icon system missing.' );

foreach ( array( "'en-US'", "'ur-PK'", "'ar-SA'", "'back'", "'home'", "'emergency_body'", 'missing_keys', 'is_complete' ) as $needle ) {
	check( false !== strpos( $i18n, $needle ), 'Localization catalogue/parity gate is incomplete: ' . $needle );
}

foreach ( array( 'GCU_Policy::analytics_consent', 'HTTP_SEC_GPC', 'HTTP_SAVE_DATA', 'is_file14_acquisition_route', 'hash_hmac', 'ATTRIBUTION_TTL', 'wp_privacy_personal_data_exporters', 'wp_privacy_personal_data_erasers' ) as $needle ) {
	check( false !== strpos( $privacy, $needle ), 'Privacy contract missing: ' . $needle );
}
check( false !== strpos( $observability, 'localization_complete' ) && false !== strpos( $observability, 'brand_primary' ), 'Observability must expose localization/brand governance health.' );

foreach ( array( '--gcu-brand-primary: #087A4E', 'min-height: 44px', 'prefers-reduced-motion', 'prefers-reduced-data', 'forced-colors', '[dir="rtl"]', '@media (max-width: 360px)' ) as $needle ) {
	check( false !== strpos( $css, $needle ), 'CSS central/accessibility contract missing: ' . $needle );
}
foreach ( array( 'globalPrivacyControl', 'saveData', 'keepalive: true', 'Measurement is non-blocking' ) as $needle ) {
	check( false !== strpos( $js, $needle ), 'Client measurement boundary missing: ' . $needle );
}

check( false !== strpos( $uninstall, 'GCU_ALLOW_PURGE' ) && false !== strpos( $uninstall, 'gcu_purge_on_uninstall' ) && false !== strpos( $uninstall, 'return;' ), 'Non-destructive uninstall/guarded purge contract missing.' );

foreach ( array( '0% commission', 'free tier', 'does not purchase ranking', 'does not guarantee', 'not an emergency service' ) as $phrase ) {
	check( false !== stripos( $all, $phrase ), 'Missing approved policy phrase: ' . $phrase );
}
foreach ( array( 'guaranteed income', 'guaranteed cure', 'limited spots', 'act now', 'instant verification' ) as $forbidden ) {
	check( false === stripos( $all, $forbidden ), 'Forbidden deceptive claim present: ' . $forbidden );
}

if ( $failures ) {
	fwrite( STDERR, "Contract tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Contract tests: PASS\n";
