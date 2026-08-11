<?php

defined( 'ABSPATH' ) || exit;

/** Shared defensive primitives for File 14. */
final class GCU_Hardening {
	private static $locks = array();

	public static function strict_same_origin_url( $url ) {
		$url = trim( (string) $url );
		if ( '' === $url ) { return ''; }
		$home = wp_parse_url( home_url( '/' ) );
		if ( 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ) { $url = home_url( $url ); }
		$target = wp_parse_url( esc_url_raw( $url ) );
		if ( ! is_array( $home ) || ! is_array( $target ) || empty( $target['host'] ) ) { return ''; }
		$home_scheme = isset( $home['scheme'] ) ? strtolower( $home['scheme'] ) : '';
		$target_scheme = isset( $target['scheme'] ) ? strtolower( $target['scheme'] ) : '';
		if ( ! in_array( $target_scheme, array( 'http', 'https' ), true ) || $home_scheme !== $target_scheme ) { return ''; }
		if ( strtolower( (string) $home['host'] ) !== strtolower( (string) $target['host'] ) ) { return ''; }
		if ( self::effective_port( $home_scheme, isset( $home['port'] ) ? (int) $home['port'] : 0 ) !== self::effective_port( $target_scheme, isset( $target['port'] ) ? (int) $target['port'] : 0 ) ) { return ''; }
		if ( isset( $target['user'] ) || isset( $target['pass'] ) ) { return ''; }
		return esc_url_raw( $url );
	}

	private static function effective_port( $scheme, $port ) {
		if ( $port > 0 ) { return $port; }
		return 'https' === $scheme ? 443 : 80;
	}

	public static function acquire_db_lock( $scope, $timeout = 2 ) {
		global $wpdb;
		$scope = preg_replace( '/[^a-zA-Z0-9:_\-]/', '', (string) $scope );
		$name = substr( $wpdb->prefix . 'gcu:' . $scope, 0, 64 );
		if ( '' === $name ) { return false; }
		$ok = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, %d)', $name, max( 0, min( 5, (int) $timeout ) ) ) );
		if ( 1 === $ok ) { self::$locks[ $name ] = true; return $name; }
		return false;
	}

	public static function release_db_lock( $name ) {
		global $wpdb;
		$name = (string) $name;
		if ( '' !== $name && isset( self::$locks[ $name ] ) ) {
			$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $name ) );
			unset( self::$locks[ $name ] );
		}
	}

	public static function sanitize_structured_value( $value, $depth = 0 ) {
		if ( $depth > 4 ) { return null; }
		if ( is_array( $value ) ) {
			$out = array(); $count = 0;
			foreach ( $value as $key => $child ) {
				if ( $count >= 50 ) { break; }
				$clean_key = is_int( $key ) ? $key : sanitize_key( (string) $key );
				$out[ $clean_key ] = self::sanitize_structured_value( $child, $depth + 1 );
				$count++;
			}
			return $out;
		}
		if ( is_bool( $value ) || is_int( $value ) || is_float( $value ) || null === $value ) { return $value; }
		return self::bounded_text( sanitize_text_field( (string) $value ), 500 );
	}

	public static function bounded_text( $text, $max ) {
		$text = (string) $text; $max = max( 1, (int) $max );
		return function_exists( 'mb_substr' ) ? mb_substr( $text, 0, $max ) : substr( $text, 0, $max );
	}

	public static function normalized_request_path() {
		$raw = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
		$path = wp_parse_url( (string) $raw, PHP_URL_PATH );
		$path = rawurldecode( (string) $path );
		$path = '/' . ltrim( preg_replace( '#/+#', '/', $path ), '/' );
		return strtolower( trailingslashit( $path ) );
	}

	public static function is_sensitive_path() {
		$path = self::normalized_request_path();
		$prefixes = array( '/wp-admin/', '/wp-login.php/', '/account/', '/doctor/apply/', '/doctor/application/', '/doctor/verification/', '/checkout/', '/messages/', '/appointments/', '/settings/', '/profile/edit/' );
		foreach ( $prefixes as $prefix ) { if ( 0 === strpos( $path, $prefix ) ) { return true; } }
		return false;
	}

	public static function request_fingerprint( $value ) {
		$normalize = static function ( $v ) use ( &$normalize ) {
			if ( is_array( $v ) ) {
				if ( array_keys( $v ) !== range( 0, count( $v ) - 1 ) ) { ksort( $v, SORT_STRING ); }
				foreach ( $v as $k => $child ) { $v[ $k ] = $normalize( $child ); }
			}
			return $v;
		};
		$encoded = wp_json_encode( $normalize( self::sanitize_structured_value( $value ) ) );
		return false === $encoded ? '' : hash( 'sha256', $encoded );
	}

	public static function command_key( $name, $supplied = '' ) {
		$supplied = sanitize_text_field( (string) $supplied );
		if ( '' !== $supplied ) { return hash( 'sha256', sanitize_key( $name ) . '|' . get_current_user_id() . '|' . self::bounded_text( $supplied, 191 ) ); }
		return hash( 'sha256', sanitize_key( $name ) . '|' . get_current_user_id() . '|' . wp_generate_uuid4() );
	}
}
