<?php

defined( 'ABSPATH' ) || exit;

final class SGC_Helpers {
	public static function defaults() {
		return array(
			'home_hero'       => 1,
			'doctor_portal'   => 1,
			'patient_banner'  => 1,
			'footer_mission'  => 1,
		);
	}

	public static function placements() {
		return wp_parse_args( (array) get_option( 'sgc_placements', array() ), self::defaults() );
	}

	public static function enabled( $key ) {
		$settings = self::placements();
		return ! empty( $settings[ $key ] );
	}

	public static function page_url( $key ) {
		$map = (array) get_option( 'sgc_page_map', array() );
		if ( ! empty( $map[ $key ] ) && 'publish' === get_post_status( absint( $map[ $key ] ) ) ) {
			return get_permalink( absint( $map[ $key ] ) );
		}

		$fallbacks = array(
			'portal'  => 'doctor-portal',
			'mission' => 'our-mission',
		);

		return home_url( '/' . ( isset( $fallbacks[ $key ] ) ? $fallbacks[ $key ] : sanitize_title( $key ) ) . '/' );
	}

	public static function doctors_url() {
		$sdd = (array) get_option( 'sdd_page_map', array() );
		$spf = (array) get_option( 'spf_page_map', array() );
		$id  = ! empty( $sdd['directory'] ) ? absint( $sdd['directory'] ) : ( ! empty( $spf['doctors'] ) ? absint( $spf['doctors'] ) : 0 );
		return $id ? get_permalink( $id ) : home_url( '/homeopathy-doctors/' );
	}

	public static function application_url() {
		$map = (array) get_option( 'gdo_page_map', array() );
		return ! empty( $map['apply'] ) ? get_permalink( absint( $map['apply'] ) ) : home_url( '/doctor-application/' );
	}

	public static function clinic_url() {
		$swc = (array) get_option( 'swc_page_map', array() );
		$spf = (array) get_option( 'spf_page_map', array() );
		$id  = ! empty( $swc['clinic'] ) ? absint( $swc['clinic'] ) : ( ! empty( $spf['clinic'] ) ? absint( $spf['clinic'] ) : 0 );
		return $id ? get_permalink( $id ) : home_url( '/worldwide-clinic/' );
	}

	public static function template( $name, array $vars = array() ) {
		$allowed = array( 'home-hero', 'doctor-portal', 'patient-banner', 'mission', 'footer-mission' );
		if ( ! in_array( $name, $allowed, true ) ) {
			return '';
		}

		$path = SGC_DIR . 'templates/' . $name . '.php';
		if ( ! file_exists( $path ) ) {
			return '';
		}

		extract( $vars, EXTR_SKIP );
		ob_start();
		include $path;
		return (string) ob_get_clean();
	}
}
