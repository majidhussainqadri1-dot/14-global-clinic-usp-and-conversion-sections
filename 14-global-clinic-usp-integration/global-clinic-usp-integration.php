<?php
/**
 * Plugin Name: Global Clinic USP and Conversion Integration
 * Plugin URI: https://sabrihomeopathy.com/
 * Description: Canonical, policy-governed Worldwide Clinic value proposition, ethical conversion journeys, destination contracts, trust intelligence and privacy-minimized measurement for the Sabri Social Homeopathy Platform.
 * Version: 1.4.6
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Dr. Allamah Majid Hussain Sabri Muhaddith Mursheed
 * License: GPL-2.0-or-later
 * Text Domain: global-clinic-usp-integration
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'GCU_VERSION', '1.4.6' );
define( 'GCU_SCHEMA_VERSION', 10005 );
define( 'GCU_PLAN_VERSION', 'SSH-F14-PLAN-2026-v1.0' );
define( 'GCU_FUTURE_PLAN_VERSION', 'SSH-F14-FUTURE-CTI-2026-v2.0' );
define( 'GCU_FUTURE_SCHEMA_VERSION', 1 );
define( 'GCU_CENTRAL_PLAN_BASELINE', '2026-08-10' );
define( 'GCU_CANONICAL_REPOSITORY', '14-global-clinic-usp-and-conversion-integration' );
define( 'GCU_CURRENT_REPOSITORY_ALIAS', '14-global-clinic-usp-and-conversion-sections' );
define( 'GCU_BRAND_PRIMARY', '#087A4E' );
define( 'GCU_FILE', __FILE__ );
define( 'GCU_DIR', plugin_dir_path( __FILE__ ) );
define( 'GCU_URL', plugin_dir_url( __FILE__ ) );
define( 'GCU_BASENAME', plugin_basename( __FILE__ ) );

$gcu_files = array(
	'includes/class-gcu-i18n.php',
	'includes/class-gcu-policy.php',
	'includes/class-gcu-hardening.php',
	'includes/class-gcu-integrity.php',
	'includes/class-gcu-future-policy.php',
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
	'includes/class-gcu-future-intelligence.php',
	'includes/class-gcu-future-i18n.php',
	'includes/class-gcu-future-guards.php',
	'includes/class-gcu-review80-hardening.php',
	'includes/class-gcu-fifth-review-hardening.php',
);

foreach ( $gcu_files as $gcu_file ) {
	require_once GCU_DIR . $gcu_file;
}
unset( $gcu_files, $gcu_file );

add_filter( 'cron_schedules', array( 'GCU_Install', 'cron_schedules' ) );
register_activation_hook( GCU_FILE, array( 'GCU_Install', 'activate' ) );
register_deactivation_hook( GCU_FILE, array( 'GCU_Install', 'deactivate' ) );
register_deactivation_hook(
	GCU_FILE,
	static function () {
		wp_clear_scheduled_hook( 'gcu_future_daily_governance' );
		wp_clear_scheduled_hook( 'gcu_future_hourly_intelligence' );
	}
);
add_action( 'plugins_loaded', static function () { GCU_Plugin::instance()->run(); }, 90 );
add_action( 'plugins_loaded', array( 'GCU_Future_Intelligence', 'bootstrap' ), 95 );
add_action( 'plugins_loaded', array( 'GCU_Future_I18n', 'bootstrap' ), 96 );
add_action( 'plugins_loaded', array( 'GCU_Future_Guards', 'bootstrap' ), 97 );
add_action( 'plugins_loaded', array( 'GCU_Review80_Hardening', 'bootstrap' ), 98 );
add_action( 'plugins_loaded', array( 'GCU_Fifth_Review_Hardening', 'bootstrap' ), 99 );
