<?php

defined( 'ABSPATH' ) || exit;

final class SGC_Plugin {
	public function run() {
		( new SGC_Frontend() )->hooks();

		if ( is_admin() ) {
			( new SGC_Admin() )->hooks();
		}
	}
}
