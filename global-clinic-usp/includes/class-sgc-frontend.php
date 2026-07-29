<?php

defined( 'ABSPATH' ) || exit;

final class SGC_Frontend {
	public function hooks() {
		add_shortcode( 'sgc_home_hero', array( $this, 'home_hero' ) );
		add_shortcode( 'sgc_doctor_portal', array( $this, 'doctor_portal' ) );
		add_shortcode( 'sgc_patient_banner', array( $this, 'patient_banner' ) );
		add_shortcode( 'sgc_our_mission', array( $this, 'mission' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_filter( 'do_shortcode_tag', array( $this, 'augment_shortcode' ), 30, 2 );
		add_filter( 'the_content', array( $this, 'front_page_hero' ), 8 );
		add_action( 'wp_footer', array( $this, 'footer_mission' ), 8 );
	}

	public function assets() {
		if ( is_admin() || is_feed() || is_robots() ) {
			return;
		}

		wp_enqueue_style( 'global-clinic-usp', SGC_URL . 'assets/css/global-clinic-usp.css', array(), SGC_VERSION );
	}

	public function home_hero() {
		return SGC_Helpers::template(
			'home-hero',
			array(
				'doctors_url' => SGC_Helpers::doctors_url(),
				'portal_url'  => SGC_Helpers::page_url( 'portal' ),
			)
		);
	}

	public function doctor_portal() {
		return SGC_Helpers::template(
			'doctor-portal',
			array(
				'application_url' => SGC_Helpers::application_url(),
				'doctors_url'     => SGC_Helpers::doctors_url(),
				'clinic_url'      => SGC_Helpers::clinic_url(),
			)
		);
	}

	public function patient_banner() {
		return SGC_Helpers::template(
			'patient-banner',
			array(
				'doctors_url' => SGC_Helpers::doctors_url(),
				'clinic_url'  => SGC_Helpers::clinic_url(),
			)
		);
	}

	public function mission() {
		return SGC_Helpers::template(
			'mission',
			array(
				'portal_url'  => SGC_Helpers::page_url( 'portal' ),
				'doctors_url' => SGC_Helpers::doctors_url(),
			)
		);
	}

	public function augment_shortcode( $output, $tag ) {
		$platform_tags = array( 'sabri_platform_home', 'sabri_platform_module', 'sdd_doctors_directory', 'swc_worldwide_clinic' );

		if ( SGC_Helpers::enabled( 'doctor_portal' ) && in_array( $tag, $platform_tags, true ) ) {
			$output = $this->add_portal_to_navigation( $output );
		}

		if ( 'sabri_platform_home' === $tag && SGC_Helpers::enabled( 'home_hero' ) ) {
			$output = preg_replace( '/<header class="spf-hero">.*?<\/header>/s', '', $output, 1 );
			$output = $this->insert_after_first_navigation( $output, $this->home_hero() );
		}

		if ( 'sdd_doctors_directory' === $tag && SGC_Helpers::enabled( 'patient_banner' ) ) {
			$output = $this->insert_after_first_navigation( $output, $this->patient_banner() );
		}

		return $output;
	}

	public function front_page_hero( $content ) {
		if ( ! SGC_Helpers::enabled( 'home_hero' ) || ! is_front_page() || ! is_page() || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		if ( has_shortcode( $content, 'sabri_platform_home' ) || has_shortcode( $content, 'sgc_home_hero' ) ) {
			return $content;
		}

		return $this->home_hero() . $content;
	}

	public function footer_mission() {
		if ( is_admin() || ! SGC_Helpers::enabled( 'footer_mission' ) || is_feed() || is_robots() ) {
			return;
		}

		echo SGC_Helpers::template( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'footer-mission',
			array( 'mission_url' => SGC_Helpers::page_url( 'mission' ) )
		);
	}

	private function add_portal_to_navigation( $output ) {
		if ( false !== strpos( $output, 'sgc-nav-portal' ) ) {
			return $output;
		}

		$link = '<a class="sgc-nav-portal" href="' . esc_url( SGC_Helpers::page_url( 'portal' ) ) . '">Doctor Portal</a>';
		return preg_replace( '/<\/nav>/', $link . '</nav>', $output, 1 );
	}

	private function insert_after_first_navigation( $output, $section ) {
		$position = strpos( $output, '</nav>' );
		if ( false === $position ) {
			return $section . $output;
		}

		$position += strlen( '</nav>' );
		return substr( $output, 0, $position ) . $section . substr( $output, $position );
	}
}
