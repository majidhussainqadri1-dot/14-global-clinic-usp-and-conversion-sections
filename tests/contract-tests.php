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
check( false === strpos( $main, "define( 'SGC_" ), 'Legacy SGC constants must not remain in canonical package.' );
check( false === strpos( $all, 'wp_insert_post(' ), 'File 14 must not create or overwrite WordPress pages.' );
check( false === strpos( $all, 'doctor-portal' ), 'Legacy duplicate doctor portal must not be owned by File 14.' );

foreach ( array( 'gcu_claims', 'gcu_content_blocks', 'gcu_placements', 'gcu_experiments', 'gcu_conversion_events', 'gcu_audit_log', 'gcu_event_outbox', 'gcu_event_inbox' ) as $table ) {
	check( false !== strpos( $install, $table ), 'Missing canonical table: ' . $table );
}
check( false !== strpos( $install, 'read-only inventory' ), 'Legacy migration must be read-only.' );
check( false !== strpos( $install, 'gcu_install_lock' ), 'Concurrent installation lock missing.' );
check( false !== strpos( $install, 'dbDelta' ), 'Idempotent schema migration missing.' );

foreach ( array( 'ClinicUSPCTASelected.v1', 'ClinicUSPContentPublished.v1', 'DoctorDirectoryAvailable.v1', 'ClinicBookingAvailable.v1', 'DoctorOnboardingAvailable.v1', 'BusinessPolicyChanged.v1' ) as $event ) {
	check( false !== strpos( $repo . $contracts, $event ), 'Missing event contract: ' . $event );
}
check( false !== strpos( $repo, 'row_version' ), 'Optimistic concurrency control missing.' );
check( false !== strpos( $repo, 'INSERT IGNORE' ), 'Event idempotency missing.' );
check( false !== strpos( $repo, 'dispatch_outbox' ), 'Reliable outbox dispatch missing.' );
check( false !== strpos( $repo, 'accept_inbound_event' ), 'Inbound event deduplication missing.' );
check( false !== strpos( $repo, 'withdraw_claim' ), 'Claim withdrawal command missing.' );
check( false !== strpos( $repo, 'create_experiment' ), 'Experiment governance command missing.' );
check( false !== strpos( $repo, "'threshold' => 10" ), 'Analytics small-number suppression missing.' );
check( false !== strpos( $repo, 'gcu_version_conflict' ), 'Version conflict response missing.' );

foreach ( array( '/blocks', '/destinations', '/events', '/content', '/placements', '/experiments', '/claims/', '/workflow/', '/analytics/funnel', '/health' ) as $route ) {
	check( false !== strpos( $rest, $route ), 'Missing REST contract: ' . $route );
}
check( false !== strpos( $rest, 'X-GCU-Event-Token' ), 'Public event anti-replay token missing.' );
check( false !== strpos( $rest, 'delete_transient' ), 'Event token must be single-use.' );

foreach ( array( '^global-clinic/?$', '^clinic/how-it-works/?$', '^find-a-global-doctor/?$', '^start-your-global-clinic/?$' ) as $route ) {
	check( false !== strpos( $front, $route ), 'Missing canonical route: ' . $route );
}
check( false !== strpos( $front, 'sabri_shell_back_home_controls' ), 'File 20 Back/Home contract missing.' );
check( false !== strpos( $front, 'wp_safe_redirect' ), 'Safe destination redirect missing.' );
check( false !== strpos( $front, 'GCU_I18n::text' ), 'Runtime localized interface chrome missing.' );
check( false !== strpos( $front, 'lang=\"' ) && false !== strpos( $front, 'dir=\"' ), 'Per-root language/direction attributes missing.' );
check( false === stripos( $front, 'onclick=' ), 'Inline onclick is forbidden by the central CSP-safe UI rule.' );
check( false === stripos( $front, 'javascript:' ), 'javascript: URLs are forbidden.' );
check( false === strpos( $front, '<main class="gcu-page"' ), 'File 14 must not introduce a duplicate main landmark inside File 20/the theme shell.' );
check( false !== strpos( $front, '<svg' ), 'Mandatory semantic icon system missing.' );
check( false !== strpos( $install, 'canonical_block_sets' ), 'Urdu/Arabic locale seed sets missing.' );
check( false !== strpos( $install, 'global_clinic_faq' ), 'Versioned FAQ placement missing.' );
check( false !== strpos( $install, 'success_metric' ), 'Experiment success metric missing.' );
check( false !== strpos( $install, 'sample_policy' ), 'Experiment sample policy missing.' );
check( false !== strpos( $install, 'privacy_policy' ), 'Experiment privacy policy missing.' );

foreach ( array( "'en-US'", "'ur-PK'", "'ar-SA'", "'back'", "'home'", "'emergency_body'" ) as $needle ) {
	check( false !== strpos( $i18n, $needle ), 'Localization catalogue is incomplete: ' . $needle );
}
check( false !== strpos( $i18n, 'missing_keys' ) && false !== strpos( $i18n, 'is_complete' ), 'Localization parity gate missing.' );

check( false !== strpos( $privacy, 'GCU_Policy::analytics_consent' ), 'Consent gate missing.' );
check( false !== strpos( $privacy, 'HTTP_SEC_GPC' ), 'Global Privacy Control support missing.' );
check( false !== strpos( $privacy, 'HTTP_SAVE_DATA' ), 'Save-Data low-bandwidth support missing.' );
check( false !== strpos( $privacy, 'is_file14_acquisition_route' ), 'Attribution must be scoped to File 14 acquisition routes.' );
check( false !== strpos( $privacy, 'hash_hmac' ), 'Signed attribution payload missing.' );
check( false !== strpos( $privacy, 'ATTRIBUTION_TTL' ), 'Bounded attribution retention missing.' );
check( false !== strpos( $privacy, 'wp_privacy_personal_data_exporters' ), 'Privacy exporter missing.' );
check( false !== strpos( $privacy, 'wp_privacy_personal_data_erasers' ), 'Privacy eraser missing.' );

check( false !== strpos( $observability, 'localization_complete' ), 'Localization health evidence missing.' );
check( false !== strpos( $observability, 'brand_primary' ), 'Brand health evidence missing.' );

check( false !== strpos( $css, '--gcu-brand-primary: #087A4E' ), 'Exact Sabri Green #087A4E must be the primary brand token.' );
check( false !== strpos( $css, 'min-height: 44px' ), '44px interaction target missing.' );
check( false !== strpos( $css, 'prefers-reduced-motion' ), 'Reduced motion support missing.' );
check( false !== strpos( $css, 'prefers-reduced-data' ), 'Reduced-data support missing.' );
check( false !== strpos( $css, 'forced-colors' ), 'Forced-colors support missing.' );
check( false !== strpos( $css, '[dir="rtl"]' ), 'RTL support missing.' );
check( false !== strpos( $css, '@media (max-width: 360px)' ), '320px-class mobile reflow coverage missing.' );
check( false !== strpos( $js, 'globalPrivacyControl' ), 'Client-side GPC guard missing.' );
check( false !== strpos( $js, 'saveData' ), 'Client-side reduced-data guard missing.' );
check( false !== strpos( $js, 'keepalive: true' ), 'Non-blocking measurement transport missing.' );
check( false !== strpos( $js, 'Measurement is non-blocking' ), 'Measurement failure boundary missing.' );

check( false !== strpos( $uninstall, 'GCU_ALLOW_PURGE' ), 'Guarded purge constant missing.' );
check( false !== strpos( $uninstall, 'gcu_purge_on_uninstall' ), 'Explicit purge option missing.' );
check( false !== strpos( $uninstall, 'return;' ), 'Default uninstall must be non-destructive.' );

foreach ( array( '0% commission', 'free tier', 'does not purchase ranking', 'does not guarantee', 'not an emergency service' ) as $phrase ) {
	check( false !== stripos( $all, $phrase ), 'Missing or inconsistent approved policy phrase: ' . $phrase );
}
foreach ( array( 'guaranteed income', 'guaranteed cure', 'limited spots', 'act now', 'instant verification' ) as $forbidden ) {
	check( false === stripos( $all, $forbidden ), 'Forbidden deceptive claim present: ' . $forbidden );
}

if ( $failures ) {
	fwrite( STDERR, "Contract tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Contract tests: PASS\n";
