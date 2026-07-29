<?php
/**
 * Plugin Name: Global Clinic USP and Conversion Sections
 * Plugin URI: https://www.sabrihomeopathy.com/
 * Description: Provides policy-safe Global Clinic conversion sections, a Doctor Portal, patient discovery guidance, and mission content for the Sabri Social Homeopathy Platform.
 * Version: 0.1.1
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Dr. Allamah Majid Hussain Sabri Muhaddith Mursheed
 * License: GPL-2.0-or-later
 * Text Domain: global-clinic-usp
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'SGC_VERSION', '0.1.1' );
define( 'SGC_SCHEMA_VERSION', 2 );
define( 'SGC_FILE', __FILE__ );
define( 'SGC_DIR', plugin_dir_path( __FILE__ ) );
define( 'SGC_URL', plugin_dir_url( __FILE__ ) );

require_once SGC_DIR . 'includes/class-sgc-helpers.php';
require_once SGC_DIR . 'includes/class-sgc-activator.php';
require_once SGC_DIR . 'includes/class-sgc-frontend.php';
require_once SGC_DIR . 'includes/class-sgc-admin.php';
require_once SGC_DIR . 'includes/class-sgc-plugin.php';

register_activation_hook( SGC_FILE, array( 'SGC_Activator', 'activate' ) );

add_action(
	'plugins_loaded',
	static function () {
		( new SGC_Plugin() )->run();
	},
	80
);
