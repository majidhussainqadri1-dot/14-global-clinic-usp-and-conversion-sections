<?php
/**
 * File 14 static corrective contract tests.
 */

$root     = dirname( __DIR__ );
$plugin   = $root . '/global-clinic-usp';
$failures = array();

function sgc_assert( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures[] = $message;
	}
}

function sgc_read( $path ) {
	$content = file_get_contents( $path );
	if ( false === $content ) {
		throw new RuntimeException( 'Unable to read ' . $path );
	}
	return $content;
}

$bootstrap = sgc_read( $plugin . '/global-clinic-usp.php' );
$frontend  = sgc_read( $plugin . '/includes/class-sgc-frontend.php' );
$activator = sgc_read( $plugin . '/includes/class-sgc-activator.php' );
$helpers   = sgc_read( $plugin . '/includes/class-sgc-helpers.php' );
$css       = sgc_read( $plugin . '/assets/css/global-clinic-usp.css' );
$templates = '';
foreach ( glob( $plugin . '/templates/*.php' ) as $template ) {
	$templates .= "\n" . sgc_read( $template );
}

sgc_assert( false === stripos( $templates, '<main' ), 'Shortcode templates must not create a main landmark.' );
sgc_assert( false === strpos( $frontend, 'preg_replace' ), 'Frontend must not mutate companion HTML with regex.' );
sgc_assert( false === strpos( $frontend, "'</nav>'" ), 'Frontend must not insert content by closing-nav string surgery.' );
sgc_assert( false === stripos( $templates, 'zero platform commission' ), 'Public templates must not promise zero platform commission while policy is unresolved.' );
sgc_assert( false !== strpos( $bootstrap, 'Version: 0.1.1' ) && false !== strpos( $bootstrap, "define( 'SGC_VERSION', '0.1.1' )" ), 'Header and runtime version must match 0.1.1.' );
sgc_assert( false !== strpos( $bootstrap, 'Dr. Allamah Majid Hussain Sabri Muhaddith Mursheed' ), 'Approved Founder metadata must be present.' );
sgc_assert( false !== strpos( $frontend, 'sabri_shell_navigation_destinations' ), 'Unified Application Shell destination filter must be used.' );
sgc_assert( false !== strpos( $frontend, 'private static $rendered' ), 'Duplicate-output guard must be present.' );
sgc_assert( false !== strpos( $helpers, 'validated_page_url' ), 'Strict destination validation must be present.' );
sgc_assert( false !== strpos( $activator, 'capture_snapshot' ) && false !== strpos( $activator, 'rollback' ) && false !== strpos( $activator, 'repair' ), 'Snapshot, rollback, and repair contracts must be present.' );
sgc_assert( false === strpos( $activator, 'wp_update_post' ), 'Activation and repair must not overwrite existing page content.' );
sgc_assert( false !== strpos( $css, 'min-height: 44px' ), 'Main stylesheet must enforce a 44px minimum interactive target.' );
sgc_assert( is_file( $plugin . '/assets/css/footer-mission.css' ), 'Scoped footer stylesheet must exist.' );

if ( $failures ) {
	fwrite( STDERR, "Corrective contract tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "All File 14 corrective contract tests passed.\n";
