<?php

defined( 'ABSPATH' ) || exit;

final class SGC_Helpers {
	/**
	 * Component instance counters.
	 *
	 * @var array<string,int>
	 */
	private static $instances = array();

	public static function defaults() {
		return array(
			'home_hero'      => 1,
			'doctor_portal'  => 1,
			'patient_banner' => 1,
			'footer_mission' => 1,
		);
	}

	public static function placements() {
		return wp_parse_args( (array) get_option( 'sgc_placements', array() ), self::defaults() );
	}

	public static function enabled( $key ) {
		$settings = self::placements();
		return ! empty( $settings[ $key ] );
	}

	public static function instance_id( $prefix ) {
		$prefix = sanitize_html_class( $prefix );
		if ( ! isset( self::$instances[ $prefix ] ) ) {
			self::$instances[ $prefix ] = 0;
		}
		self::$instances[ $prefix ]++;
		return $prefix . '-' . self::$instances[ $prefix ];
	}

	public static function page_url( $key ) {
		$map        = (array) get_option( 'sgc_page_map', array() );
		$shortcodes = array(
			'portal'  => 'sgc_doctor_portal',
			'mission' => 'sgc_our_mission',
		);
		$slugs      = array(
			'portal'  => array( 'doctor-portal' ),
			'mission' => array( 'our-mission' ),
		);

		$id = ! empty( $map[ $key ] ) ? absint( $map[ $key ] ) : 0;
		if ( $id ) {
			$url = self::validated_page_url( $id, isset( $shortcodes[ $key ] ) ? array( $shortcodes[ $key ] ) : array() );
			if ( $url ) {
				return $url;
			}
		}

		return self::find_page_url_by_slugs(
			isset( $slugs[ $key ] ) ? $slugs[ $key ] : array(),
			isset( $shortcodes[ $key ] ) ? array( $shortcodes[ $key ] ) : array()
		);
	}

	public static function doctors_url() {
		$sdd = (array) get_option( 'sdd_page_map', array() );
		$spf = (array) get_option( 'spf_page_map', array() );
		$candidates = array(
			array( ! empty( $sdd['directory'] ) ? absint( $sdd['directory'] ) : 0, array( 'sdd_doctors_directory' ) ),
			array( ! empty( $spf['doctors'] ) ? absint( $spf['doctors'] ) : 0, array( 'sabri_doctor_directory', 'sabri_platform_module' ) ),
		);

		return self::first_valid_candidate_url( $candidates, array( 'homeopathy-doctors', 'doctors' ), array( 'sdd_doctors_directory', 'sabri_doctor_directory', 'sabri_platform_module' ) );
	}

	public static function application_url() {
		$map = (array) get_option( 'gdo_page_map', array() );
		$candidates = array(
			array( ! empty( $map['apply'] ) ? absint( $map['apply'] ) : 0, array( 'gdo_doctor_application' ) ),
		);
		return self::first_valid_candidate_url( $candidates, array( 'doctor-application', 'apply-as-doctor' ), array( 'gdo_doctor_application' ) );
	}

	public static function clinic_url() {
		$swc = (array) get_option( 'swc_page_map', array() );
		$spf = (array) get_option( 'spf_page_map', array() );
		$candidates = array(
			array( ! empty( $swc['clinic'] ) ? absint( $swc['clinic'] ) : 0, array( 'swc_worldwide_clinic' ) ),
			array( ! empty( $spf['clinic'] ) ? absint( $spf['clinic'] ) : 0, array( 'sabri_platform_module' ) ),
		);

		return self::first_valid_candidate_url( $candidates, array( 'worldwide-clinic', 'global-clinic', 'clinic-directory' ), array( 'swc_worldwide_clinic', 'sabri_platform_module' ) );
	}

	public static function validated_page_url( $page_id, array $expected_shortcodes = array() ) {
		$page_id = absint( $page_id );
		$page    = $page_id ? get_post( $page_id ) : null;

		if ( ! $page instanceof WP_Post || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
			return '';
		}

		if ( function_exists( 'is_post_publicly_viewable' ) && ! is_post_publicly_viewable( $page ) ) {
			return '';
		}

		if ( $expected_shortcodes && ! self::content_has_any_shortcode( (string) $page->post_content, $expected_shortcodes ) ) {
			return '';
		}

		$url = get_permalink( $page_id );
		return is_string( $url ) && '' !== $url ? $url : '';
	}

	public static function find_page_url_by_slugs( array $slugs, array $expected_shortcodes = array() ) {
		foreach ( $slugs as $slug ) {
			$page = get_page_by_path( sanitize_title( $slug ), OBJECT, 'page' );
			if ( $page instanceof WP_Post ) {
				$url = self::validated_page_url( $page->ID, $expected_shortcodes );
				if ( $url ) {
					return $url;
				}
			}
		}
		return '';
	}

	public static function content_has_any_shortcode( $content, array $shortcodes ) {
		foreach ( $shortcodes as $shortcode ) {
			if ( $shortcode && has_shortcode( $content, $shortcode ) ) {
				return true;
			}
		}
		return false;
	}

	public static function public_request_allowed() {
		if ( is_admin() || is_feed() || is_robots() || is_trackback() || is_embed() || is_preview() || is_404() || is_search() ) {
			return false;
		}
		if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
			return false;
		}
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}
		return ! self::shell_is_minimal();
	}

	public static function shell_is_minimal() {
		$class = '\\Sabri\\UnifiedShell\\Layout';
		if ( class_exists( $class ) && method_exists( $class, 'current_mode' ) ) {
			return 'minimal' === call_user_func( array( $class, 'current_mode' ) );
		}
		return false;
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

	private static function first_valid_candidate_url( array $candidates, array $slugs, array $shortcodes ) {
		foreach ( $candidates as $candidate ) {
			$id       = isset( $candidate[0] ) ? absint( $candidate[0] ) : 0;
			$expected = isset( $candidate[1] ) && is_array( $candidate[1] ) ? $candidate[1] : array();
			if ( ! $id ) {
				continue;
			}
			$url = self::validated_page_url( $id, $expected );
			if ( $url ) {
				return $url;
			}
		}

		$url = self::find_page_url_by_slugs( $slugs, $shortcodes );
		if ( $url ) {
			return $url;
		}

		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => 50,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			)
		);
		foreach ( $pages as $page ) {
			if ( self::content_has_any_shortcode( (string) $page->post_content, $shortcodes ) ) {
				$url = self::validated_page_url( $page->ID, $shortcodes );
				if ( $url ) {
					return $url;
				}
			}
		}

		return '';
	}

}
