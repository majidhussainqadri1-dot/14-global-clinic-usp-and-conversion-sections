<?php

defined( 'ABSPATH' ) || exit;

final class SGC_Frontend {
	/** @var array<string,bool> */
	private static $rendered = array();

	public function hooks() {
		add_shortcode( 'sgc_home_hero', array( $this, 'home_hero' ) );
		add_shortcode( 'sgc_doctor_portal', array( $this, 'doctor_portal' ) );
		add_shortcode( 'sgc_patient_banner', array( $this, 'patient_banner' ) );
		add_shortcode( 'sgc_our_mission', array( $this, 'mission' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_filter( 'the_content', array( $this, 'front_page_hero' ), 8 );
		add_filter( 'the_content', array( $this, 'directory_banner' ), 9 );
		add_action( 'wp_footer', array( $this, 'footer_mission' ), 8 );
		add_filter( 'sabri_shell_navigation_destinations', array( $this, 'shell_destinations' ) );
		add_filter( 'sabri_shell_system_check_report', array( $this, 'shell_system_check' ) );
	}

	public function assets() {
		wp_register_style( 'global-clinic-usp', SGC_URL . 'assets/css/global-clinic-usp.css', array(), SGC_VERSION );
		wp_register_style( 'global-clinic-usp-footer', SGC_URL . 'assets/css/footer-mission.css', array(), SGC_VERSION );

		if ( $this->component_expected_on_request() ) {
			wp_enqueue_style( 'global-clinic-usp' );
		}
		if ( $this->footer_allowed() ) {
			wp_enqueue_style( 'global-clinic-usp-footer' );
		}
	}

	public function home_hero() {
		return $this->once(
			'home_hero',
			SGC_Helpers::template(
				'home-hero',
				array(
					'title_id'    => SGC_Helpers::instance_id( 'sgc-home-title' ),
					'search_id'   => SGC_Helpers::instance_id( 'sgc-home-search' ),
					'doctors_url' => SGC_Helpers::doctors_url(),
					'portal_url'  => SGC_Helpers::page_url( 'portal' ),
				)
			)
		);
	}

	public function doctor_portal() {
		return $this->once(
			'doctor_portal',
			SGC_Helpers::template(
				'doctor-portal',
				array(
					'section_id'      => SGC_Helpers::instance_id( 'sgc-doctor-portal' ),
					'benefits_id'     => SGC_Helpers::instance_id( 'sgc-doctor-benefits' ),
					'process_id'      => SGC_Helpers::instance_id( 'sgc-doctor-process' ),
					'application_url' => SGC_Helpers::application_url(),
					'doctors_url'     => SGC_Helpers::doctors_url(),
					'clinic_url'      => SGC_Helpers::clinic_url(),
				)
			)
		);
	}

	public function patient_banner() {
		return $this->once(
			'patient_banner',
			SGC_Helpers::template(
				'patient-banner',
				array(
					'title_id'    => SGC_Helpers::instance_id( 'sgc-patient-title' ),
					'doctors_url' => SGC_Helpers::doctors_url(),
					'clinic_url'  => SGC_Helpers::clinic_url(),
				)
			)
		);
	}

	public function mission() {
		return $this->once(
			'mission',
			SGC_Helpers::template(
				'mission',
				array(
					'section_id'  => SGC_Helpers::instance_id( 'sgc-mission' ),
					'title_id'    => SGC_Helpers::instance_id( 'sgc-mission-title' ),
					'portal_url'  => SGC_Helpers::page_url( 'portal' ),
					'doctors_url' => SGC_Helpers::doctors_url(),
				)
			)
		);
	}

	public function front_page_hero( $content ) {
		if ( ! SGC_Helpers::enabled( 'home_hero' ) || ! SGC_Helpers::public_request_allowed() || ! is_front_page() || ! is_page() || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}
		if ( has_shortcode( $content, 'sgc_home_hero' ) ) {
			return $content;
		}
		return $this->home_hero() . $content;
	}

	public function directory_banner( $content ) {
		if ( ! SGC_Helpers::enabled( 'patient_banner' ) || ! SGC_Helpers::public_request_allowed() || ! is_page() || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}
		if ( has_shortcode( $content, 'sgc_patient_banner' ) ) {
			return $content;
		}
		$compatible = array( 'sdd_doctors_directory', 'swc_worldwide_clinic', 'sabri_doctor_directory', 'sabri_worldwide_clinic' );
		if ( ! SGC_Helpers::content_has_any_shortcode( $content, $compatible ) ) {
			return $content;
		}
		return $this->patient_banner() . $content;
	}

	public function shell_destinations( $destinations ) {
		if ( ! is_array( $destinations ) || ! SGC_Helpers::enabled( 'doctor_portal' ) ) {
			return $destinations;
		}
		$destinations['doctor_portal'] = array(
			'label'      => __( 'Doctor Portal', 'global-clinic-usp' ),
			'group'      => 'doctors',
			'slugs'      => array( 'doctor-portal' ),
			'shortcodes' => array( 'sgc_doctor_portal' ),
			'post_type'  => '',
			'order'      => 65,
		);
		return $destinations;
	}

	public function shell_system_check( $rows ) {
		if ( ! is_array( $rows ) ) {
			$rows = array();
		}
		$health = SGC_Activator::health_report();
		$rows[] = array(
			'label'  => __( 'File 14 Doctor Portal', 'global-clinic-usp' ),
			'value'  => $health['portal']['status'],
			'status' => 'pass' === $health['portal']['status'] ? 'pass' : 'warn',
		);
		$rows[] = array(
			'label'  => __( 'File 14 Mission Page', 'global-clinic-usp' ),
			'value'  => $health['mission']['status'],
			'status' => 'pass' === $health['mission']['status'] ? 'pass' : 'warn',
		);
		$rows[] = array(
			'label'  => __( 'File 14 Business Policy Copy', 'global-clinic-usp' ),
			'value'  => __( 'Neutral copy enforced pending approved platform-wide policy', 'global-clinic-usp' ),
			'status' => 'pass',
		);
		return $rows;
	}

	public function footer_mission() {
		if ( ! $this->footer_allowed() || isset( self::$rendered['footer_mission'] ) ) {
			return;
		}
		$mission_url = SGC_Helpers::page_url( 'mission' );
		if ( ! $mission_url ) {
			return;
		}
		self::$rendered['footer_mission'] = true;
		echo SGC_Helpers::template( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'footer-mission',
			array( 'mission_url' => $mission_url )
		);
	}

	private function once( $key, $output ) {
		if ( isset( self::$rendered[ $key ] ) ) {
			return '';
		}
		self::$rendered[ $key ] = true;
		return $output;
	}

	private function component_expected_on_request() {
		if ( ! SGC_Helpers::public_request_allowed() ) {
			return false;
		}
		if ( is_front_page() && SGC_Helpers::enabled( 'home_hero' ) ) {
			return true;
		}
		if ( ! is_singular( 'page' ) ) {
			return false;
		}
		global $post;
		$content = $post instanceof WP_Post ? (string) $post->post_content : '';
		return SGC_Helpers::content_has_any_shortcode(
			$content,
			array( 'sgc_home_hero', 'sgc_doctor_portal', 'sgc_patient_banner', 'sgc_our_mission', 'sdd_doctors_directory', 'swc_worldwide_clinic', 'sabri_doctor_directory', 'sabri_worldwide_clinic' )
		);
	}

	private function footer_allowed() {
		if ( ! SGC_Helpers::enabled( 'footer_mission' ) || ! SGC_Helpers::public_request_allowed() ) {
			return false;
		}
		$mission_url = SGC_Helpers::page_url( 'mission' );
		if ( ! $mission_url ) {
			return false;
		}
		$current_id = is_singular() ? get_queried_object_id() : 0;
		$map        = (array) get_option( 'sgc_page_map', array() );
		if ( $current_id && ! empty( $map['mission'] ) && $current_id === absint( $map['mission'] ) ) {
			return false;
		}
		return (bool) apply_filters( 'sgc_footer_mission_allowed', true );
	}
}
