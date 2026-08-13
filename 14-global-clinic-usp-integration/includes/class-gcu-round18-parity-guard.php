<?php

defined( 'ABSPATH' ) || exit;

final class GCU_Round18_Parity_Guard {
	const ACTIVE_COPY_SCAN_CEILING = 500;

	public static function bootstrap() {
		add_filter( 'gcu_authorize', array( __CLASS__, 'guard' ), PHP_INT_MAX, 4 );
	}

	public static function guard( $allowed, $capability, $object, $purpose ) {
		return $allowed;
	}
}
