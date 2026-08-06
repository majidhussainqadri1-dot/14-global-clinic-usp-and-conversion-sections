<?php
/**
 * Plugin Name: Global Clinic USP and Conversion Integration
 * Plugin URI: https://sabrihomeopathy.com/
 * Description: Canonical, policy-governed Worldwide Clinic value proposition, ethical conversion journeys, destination contracts and privacy-minimized measurement for the Sabri Social Homeopathy Platform.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Dr. Allamah Majid Hussain Sabri Muhaddith Mursheed
 * License: GPL-2.0-or-later
 * Text Domain: global-clinic-usp-integration
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'GCU_VERSION', '1.0.0' );
define( 'GCU_SCHEMA_VERSION', 10001 );
define( 'GCU_FILE', __FILE__ );
define( 'GCU_DIR', plugin_dir_path( __FILE__ ) );
define( 'GCU_URL', plugin_dir_url( __FILE__ ) );
define( 'GCU_BASENAME', plugin_basename( __FILE__ ) );

$gcu_files = array(
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
);

foreach ( $gcu_files as $gcu_file ) {
	require_once GCU_DIR . $gcu_file;
}
unset( $gcu_files, $gcu_file );

register_activation_hook( GCU_FILE, array( 'GCU_Install', 'activate' ) );
register_deactivation_hook( GCU_FILE, array( 'GCU_Install', 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function () {
		GCU_Plugin::instance()->run();
	},
	90
);
