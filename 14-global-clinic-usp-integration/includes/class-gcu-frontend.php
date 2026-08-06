<?php

defined( 'ABSPATH' ) || exit;

final class GCU_Frontend {
	private $current_route = '';
	private $degraded_destination = '';

	public function hooks() {
		add_action( 'init', array( $this, 'rewrites' ) );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_action( 'template_redirect', array( $this, 'route_actions' ), 1 );
		add_filter( 'template_include', array( $this, 'template' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'wp_head', array( $this, 'head_meta' ), 1 );
		add_shortcode( 'gcu_global_clinic', array( $this, 'shortcode_global_clinic' ) );
		add_shortcode( 'gcu_how_it_works', array( $this, 'shortcode_how_it_works' ) );
		add_shortcode( 'gcu_block', array( $this, 'shortcode_block' ) );
	}

	public function rewrites() {
		add_rewrite_rule( '^global-clinic/?$', 'index.php?gcu_route=global_clinic', 'top' );
		add_rewrite_rule( '^clinic/how-it-works/?$', 'index.php?gcu_route=how_it_works', 'top' );
		add_rewrite_rule( '^find-a-global-doctor/?$', 'index.php?gcu_route=find_doctor', 'top' );
		add_rewrite_rule( '^start-your-global-clinic/?$', 'index.php?gcu_route=start_clinic', 'top' );
	}

	public function query_vars( $vars ) {
		$vars[] = 'gcu_route';
		return $vars;
	}

	public function route_actions() {
		$this->current_route = sanitize_key( (string) get_query_var( 'gcu_route' ) );
		if ( ! $this->current_route ) {
			return;
		}
		if ( ! get_option( 'gcu_enabled', 1 ) ) {
			status_header( 503 );
			$this->degraded_destination = 'module_disabled';
			return;
		}
		$redirect_map = array( 'find_doctor' => 'doctor_directory', 'start_clinic' => 'doctor_onboarding' );
		if ( isset( $redirect_map[ $this->current_route ] ) ) {
			$health = GCU_Plugin::instance()->contracts()->destination( $redirect_map[ $this->current_route ] );
			if ( $health['available'] && $health['url'] ) {
				wp_safe_redirect( $health['url'], 302, 'File 14 canonical destination bridge' );
				exit;
			}
			status_header( 503 );
			$this->degraded_destination = $redirect_map[ $this->current_route ];
		}
	}

	public function template( $template ) {
		if ( ! $this->current_route ) {
			$this->current_route = sanitize_key( (string) get_query_var( 'gcu_route' ) );
		}
		if ( $this->current_route ) {
			return GCU_DIR . 'templates/public-page.php';
		}
		return $template;
	}

	public function assets() {
		$route = sanitize_key( (string) get_query_var( 'gcu_route' ) );
		global $post;
		$has_shortcode = $post instanceof WP_Post && ( has_shortcode( $post->post_content, 'gcu_global_clinic' ) || has_shortcode( $post->post_content, 'gcu_how_it_works' ) || has_shortcode( $post->post_content, 'gcu_block' ) );
		if ( ! $route && ! $has_shortcode ) {
			return;
		}
		wp_enqueue_style( 'gcu-public', GCU_URL . 'assets/css/global-clinic-usp-integration.css', array(), GCU_VERSION );
		if ( GCU_Policy::analytics_consent() ) {
			wp_enqueue_script( 'gcu-public', GCU_URL . 'assets/js/global-clinic-usp-integration.js', array(), GCU_VERSION, true );
			wp_localize_script(
				'gcu-public',
				'GCU_PUBLIC',
				array(
					'endpoint' => esc_url_raw( rest_url( 'gcu/v1/events' ) ),
					'consent'  => true,
				)
			);
		}
	}

	public function head_meta() {
		$route = sanitize_key( (string) get_query_var( 'gcu_route' ) );
		if ( ! $route ) {
			return;
		}
		if ( $this->degraded_destination ) {
			echo "<meta name=\"robots\" content=\"noindex,nofollow\">\n";
		}
		$canonical = 'how_it_works' === $route ? home_url( '/clinic/how-it-works/' ) : home_url( '/global-clinic/' );
		echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
	}

	public function render_route() {
		$route = $this->current_route ? $this->current_route : sanitize_key( (string) get_query_var( 'gcu_route' ) );
		if ( $this->degraded_destination ) {
			return $this->render_degraded( $this->degraded_destination );
		}
		return 'how_it_works' === $route ? $this->render_how_it_works() : $this->render_global_clinic();
	}

	public function shortcode_global_clinic() {
		return $this->render_global_clinic();
	}

	public function shortcode_how_it_works() {
		return $this->render_how_it_works();
	}

	public function shortcode_block( $atts ) {
		$atts = shortcode_atts( array( 'slot' => 'global_clinic_primary', 'audience' => 'all' ), $atts, 'gcu_block' );
		return $this->render_blocks( sanitize_key( $atts['slot'] ), GCU_Policy::sanitize_audience( $atts['audience'] ) );
	}

	public function render_primary_slot() {
		return $this->render_blocks( 'global_clinic_primary', 'all' );
	}

	public function render_trust_slot() {
		return $this->render_blocks( 'global_clinic_trust', 'all' );
	}

	public function render_global_clinic() {
		$impression = $this->event_token();
		$html  = '<main class="gcu-page" id="gcu-main" data-gcu-impression-token="' . esc_attr( $impression ) . '">';
		$html .= $this->navigation_controls();
		$html .= '<header class="gcu-page__header"><span class="gcu-eyebrow">' . $this->icon( 'globe' ) . esc_html__( 'Worldwide Clinic', 'global-clinic-usp-integration' ) . '</span>';
		$html .= '<h1>' . esc_html__( 'Global Homeopathic Care and Professional Presence — Connected with Trust', 'global-clinic-usp-integration' ) . '</h1>';
		$html .= '<p>' . esc_html__( 'One clear entry point for patients seeking verified doctors and for qualified doctors beginning the approval journey for a worldwide clinic.', 'global-clinic-usp-integration' ) . '</p></header>';
		$html .= $this->render_blocks( 'global_clinic_primary', 'all' );
		$html .= $this->render_blocks( 'global_clinic_trust', 'all' );
		$html .= $this->render_blocks( 'global_clinic_steps', 'all' );
		$html .= $this->render_faqs();
		$html .= $this->emergency_notice();
		$html .= '</main>';
		return $html;
	}

	public function render_how_it_works() {
		$contracts = GCU_Plugin::instance()->contracts();
		$patient = $contracts->destination( 'doctor_directory' );
		$doctor  = $contracts->destination( 'doctor_onboarding' );
		$html  = '<main class="gcu-page" id="gcu-main">' . $this->navigation_controls();
		$html .= '<header class="gcu-page__header"><span class="gcu-eyebrow">' . $this->icon( 'route' ) . esc_html__( 'Transparent Process', 'global-clinic-usp-integration' ) . '</span><h1>' . esc_html__( 'How the Global Clinic Journey Works', 'global-clinic-usp-integration' ) . '</h1></header>';
		$html .= '<section class="gcu-journeys" aria-label="' . esc_attr__( 'Patient and doctor journeys', 'global-clinic-usp-integration' ) . '">';
		$html .= $this->journey_card( __( 'For Patients', 'global-clinic-usp-integration' ), array( __( 'Search the verified public directory.', 'global-clinic-usp-integration' ), __( 'Review the canonical doctor profile and clinic information.', 'global-clinic-usp-integration' ), __( 'Sign in for protected contact, save or appointment actions.', 'global-clinic-usp-integration' ), __( 'Use the clinic owner for availability, consent and booking status.', 'global-clinic-usp-integration' ) ), $patient, __( 'Find a Global Doctor', 'global-clinic-usp-integration' ), 'patient' );
		$html .= $this->journey_card( __( 'For Doctors', 'global-clinic-usp-integration' ), array( __( 'Create a high-trust account and accept platform rules.', 'global-clinic-usp-integration' ), __( 'Submit identity and professional evidence to File 09.', 'global-clinic-usp-integration' ), __( 'Complete review, additional-information or appeal steps where required.', 'global-clinic-usp-integration' ), __( 'After approval, configure the canonical profile and clinic owners.', 'global-clinic-usp-integration' ) ), $doctor, __( 'Start Your Global Clinic', 'global-clinic-usp-integration' ), 'doctor' );
		$html .= '</section>' . $this->render_faqs() . $this->emergency_notice() . '</main>';
		return $html;
	}

	public function render_blocks( $slot, $audience ) {
		$locale = $this->current_locale();
		$rows   = GCU_Plugin::instance()->repository()->active_blocks( $slot, $audience, $locale );
		if ( empty( $rows ) && 'en-US' !== $locale ) {
			$rows = GCU_Plugin::instance()->repository()->active_blocks( $slot, $audience, 'en-US' );
		}
		if ( empty( $rows ) ) {
			return '<section class="gcu-state gcu-state--empty" role="status"><h2>' . esc_html__( 'Content is being reviewed', 'global-clinic-usp-integration' ) . '</h2><p>' . esc_html__( 'The approved version is temporarily unavailable. Please use the Home link or return later.', 'global-clinic-usp-integration' ) . '</p></section>';
		}
		$html = '<section class="gcu-grid gcu-grid--' . esc_attr( $slot ) . '">';
		foreach ( $rows as $row ) {
			$health = $row['cta_destination'] ? GCU_Plugin::instance()->contracts()->destination( $row['cta_destination'] ) : array( 'available' => false, 'url' => '' );
			$claims = GCU_Plugin::instance()->repository()->public_claims( $row['claim_keys'] );
			$html .= '<article class="gcu-card gcu-card--' . esc_attr( $row['block_type'] ) . '" data-block-version="' . esc_attr( $row['content_version'] ) . '">';
			$html .= '<div class="gcu-card__icon" aria-hidden="true">' . $this->icon( 'patient' === $row['audience'] ? 'search' : ( 'doctor' === $row['audience'] ? 'clinic' : 'shield' ) ) . '</div>';
			$html .= '<h2>' . esc_html( $row['title'] ) . '</h2><div class="gcu-card__body">' . wp_kses_post( wpautop( $row['body'] ) ) . '</div>';
			if ( $row['cta_label'] ) {
				if ( $health['available'] && $health['url'] ) {
					$html .= '<a class="gcu-button" href="' . esc_url( $health['url'] ) . '" data-gcu-destination="' . esc_attr( $row['cta_destination'] ) . '" data-gcu-event-token="' . esc_attr( $this->event_token() ) . '">' . $this->icon( 'arrow' ) . '<span>' . esc_html( $row['cta_label'] ) . '</span></a>';
				} else {
					$html .= '<p class="gcu-destination-state" role="status">' . $this->icon( 'info' ) . esc_html__( 'This destination is temporarily unavailable. No application, booking or approval has been inferred.', 'global-clinic-usp-integration' ) . '</p>';
				}
			}
			if ( $claims ) {
				$html .= '<details class="gcu-claims"><summary>' . esc_html__( 'Trust and policy details', 'global-clinic-usp-integration' ) . '</summary><ul>';
				foreach ( $claims as $claim ) {
					$html .= '<li>' . esc_html( GCU_Policy::localized_claim_text( $claim['claim_key'], $locale, $claim['claim_text'] ) ) . '</li>';
				}
				$html .= '</ul></details>';
			}
			$html .= '</article>';
		}
		$html .= '</section>';
		return $html;
	}

	public function current_locale() {
		$locale = determine_locale();
		$map = array( 'en_US' => 'en-US', 'ur' => 'ur-PK', 'ur_PK' => 'ur-PK', 'ar' => 'ar-SA', 'ar_SA' => 'ar-SA' );
		return isset( $map[ $locale ] ) ? $map[ $locale ] : GCU_Policy::sanitize_locale( str_replace( '_', '-', $locale ) );
	}

	private function render_faqs() {
		$locale = $this->current_locale();
		$items  = GCU_Plugin::instance()->repository()->active_blocks( 'global_clinic_faq', 'all', $locale );
		if ( empty( $items ) && 'en-US' !== $locale ) {
			$items = GCU_Plugin::instance()->repository()->active_blocks( 'global_clinic_faq', 'all', 'en-US' );
		}
		if ( empty( $items ) ) {
			return '';
		}
		$html = '<section class="gcu-faq" aria-labelledby="gcu-faq-title"><h2 id="gcu-faq-title">' . $this->icon( 'question' ) . esc_html__( 'Frequently Asked Questions', 'global-clinic-usp-integration' ) . '</h2>';
		foreach ( $items as $item ) {
			$html .= '<details data-block-version="' . esc_attr( $item['content_version'] ) . '"><summary>' . esc_html( $item['title'] ) . '</summary><div>' . wp_kses_post( wpautop( $item['body'] ) ) . '</div></details>';
		}
		return $html . '</section>';
	}

	private function journey_card( $title, array $steps, array $health, $label, $audience ) {
		$html = '<article class="gcu-card gcu-card--journey"><div class="gcu-card__icon">' . $this->icon( 'patient' === $audience ? 'search' : 'clinic' ) . '</div><h2>' . esc_html( $title ) . '</h2><ol>';
		foreach ( $steps as $step ) {
			$html .= '<li>' . esc_html( $step ) . '</li>';
		}
		$html .= '</ol>';
		if ( $health['available'] && $health['url'] ) {
			$html .= '<a class="gcu-button" href="' . esc_url( $health['url'] ) . '" data-gcu-destination="' . esc_attr( $health['key'] ) . '" data-gcu-event-token="' . esc_attr( $this->event_token() ) . '">' . $this->icon( 'arrow' ) . esc_html( $label ) . '</a>';
		} else {
			$html .= '<p class="gcu-destination-state" role="status">' . esc_html__( 'Owner destination unavailable; no action was created.', 'global-clinic-usp-integration' ) . '</p>';
		}
		return $html . '</article>';
	}

	private function render_degraded( $destination ) {
		return '<main class="gcu-page" id="gcu-main">' . $this->navigation_controls() . '<section class="gcu-state gcu-state--error" role="alert"><div class="gcu-state__icon">' . $this->icon( 'info' ) . '</div><h1>' . esc_html__( 'Service temporarily unavailable', 'global-clinic-usp-integration' ) . '</h1><p>' . esc_html__( 'The requested owner destination is unavailable or incompatible. No booking, application, verification or clinical action has been created.', 'global-clinic-usp-integration' ) . '</p><p><code>' . esc_html( sanitize_key( $destination ) ) . '</code></p></section></main>';
	}

	private function emergency_notice() {
		return '<aside class="gcu-emergency" role="note">' . $this->icon( 'alert' ) . '<div><strong>' . esc_html__( 'Emergency limitation', 'global-clinic-usp-integration' ) . '</strong><p>' . esc_html__( 'This platform is not an emergency service. For urgent or life-threatening symptoms, contact local emergency services immediately.', 'global-clinic-usp-integration' ) . '</p></div></aside>';
	}

	private function navigation_controls() {
		$shared = apply_filters( 'sabri_shell_back_home_controls', '', array( 'owner' => 'File 14', 'home_url' => home_url( '/' ), 'fallback_url' => home_url( '/global-clinic/' ) ) );
		if ( is_string( $shared ) && '' !== trim( $shared ) ) {
			return $shared;
		}
		return '<nav class="gcu-context-nav" aria-label="' . esc_attr__( 'Page navigation', 'global-clinic-usp-integration' ) . '"><button type="button" class="gcu-context-nav__back" onclick="if(history.length>1){history.back()}else{location.href=\'' . esc_js( home_url( '/global-clinic/' ) ) . '\'}">' . $this->icon( 'back' ) . '<span>' . esc_html__( 'Back', 'global-clinic-usp-integration' ) . '</span></button><a href="' . esc_url( home_url( '/' ) ) . '">' . $this->icon( 'home' ) . '<span>' . esc_html__( 'Home', 'global-clinic-usp-integration' ) . '</span></a></nav>';
	}

	private function event_token() {
		if ( ! GCU_Policy::analytics_consent() ) {
			return '';
		}
		$id  = wp_generate_uuid4();
		$key = 'gcu_event_token_' . str_replace( '-', '', $id );
		set_transient( $key, 1, GCU_Policy::EVENT_TOKEN_TTL );
		return $id . '.' . hash_hmac( 'sha256', $id, wp_salt( 'nonce' ) );
	}

	private function icon( $name ) {
		$paths = array(
			'globe'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>',
			'route'    => '<circle cx="6" cy="18" r="2"/><circle cx="18" cy="6" r="2"/><path d="M8 18h3a3 3 0 0 0 3-3v-6a3 3 0 0 1 3-3"/>',
			'search'   => '<circle cx="10" cy="10" r="6"/><path d="m15 15 5 5"/>',
			'clinic'   => '<path d="M4 21V8l8-5 8 5v13M9 21v-6h6v6M9 10h6M12 7v6"/>',
			'shield'   => '<path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/>',
			'arrow'    => '<path d="M5 12h14M14 7l5 5-5 5"/>',
			'info'     => '<circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01"/>',
			'question' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.7 2.7 0 1 1 4.1 2.3c-1 .6-1.6 1.1-1.6 2.2M12 17h.01"/>',
			'alert'    => '<path d="M12 3 2.8 20h18.4L12 3Z"/><path d="M12 9v5M12 17h.01"/>',
			'back'     => '<path d="m15 18-6-6 6-6"/>',
			'home'     => '<path d="m3 11 9-8 9 8v10h-6v-6H9v6H3V11Z"/>',
		);
		$path = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['info'];
		return '<svg class="gcu-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
	}
}
