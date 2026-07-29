<?php

defined( 'ABSPATH' ) || exit;

final class SGC_Plugin {
	public function run() {
		load_plugin_textdomain( 'global-clinic-usp', false, dirname( plugin_basename( SGC_FILE ) ) . '/languages' );
		SGC_Activator::maybe_upgrade();

		( new SGC_Frontend() )->hooks();

		if ( is_admin() ) {
			( new SGC_Admin() )->hooks();
		}
	}
}
