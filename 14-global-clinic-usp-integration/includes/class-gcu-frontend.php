<?php

defined( 'ABSPATH' ) || exit;

final class GCU_Frontend {
	private $current_route = '';
	private $degraded_destination = '';
	private static $instance_counter = 0;

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
		$redirect_map = array(
			'find_doctor'  => 'doctor_directory',
			'start_clinic' => 'doctor_onboarding',
		);
		if ( isset( $redirect_map[ $this->current_route ] ) ) {
			$health = GCU_Plugin::instance()->contracts()->destination( $redirect_map[ $this->current_route ] );
			if ( ! empty( $health['available'] ) && ! empty( $health['url'] ) ) {
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
		$has_shortcode = $post instanceof WP_Post && (
			has_shortcode( $post->post_content, 'gcu_global_clinic' ) ||
			has_shortcode( $post->post_content, 'gcu_how_it_works' ) ||
			has_shortcode( $post->post_content, 'gcu_block' )
		);
		if ( ! $route && ! $has_shortcode ) {
			return;
		}

		wp_enqueue_style( 'gcu-public', GCU_URL . 'assets/css/global-clinic-usp-integration.css', array(), GCU_VERSION );

		if ( GCU_Privacy::measurement_allowed() && ! GCU_Privacy::low_bandwidth_requested() ) {
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
		$locale = $this->current_locale();
		$root   = $this->root_id( 'global-clinic' );
		$token  = $this->event_token();
		$html   = $this->root_open( $root, $locale, $token );
		$html  .= $this->navigation_controls( 'global_clinic', $locale );
		$html  .= '<header class="gcu-page__header">';
		$html  .= '<span class="gcu-eyebrow">' . $this->icon( 'globe' ) . esc_html( GCU_I18n::text( 'worldwide_clinic', $locale ) ) . '</span>';
		$html  .= '<h1>' . esc_html( GCU_I18n::text( 'hero_title', $locale ) ) . '</h1>';
		$html  .= '<p>' . esc_html( GCU_I18n::text( 'hero_body', $locale ) ) . '</p></header>';
		$html  .= $this->render_blocks( 'global_clinic_primary', 'all' );
		$html  .= $this->render_blocks( 'global_clinic_trust', 'all' );
		$html  .= $this->render_blocks( 'global_clinic_steps', 'all' );
		$html  .= $this->render_faqs( $root, $locale );
		$html  .= $this->emergency_notice( $locale );
		$html  .= '</div>';
		return $html;
	}

	public function render_how_it_works() {
		$locale    = $this->current_locale();
		$root      = $this->root_id( 'how-it-works' );
		$contracts = GCU_Plugin::instance()->contracts();
		$patient   = $contracts->destination( 'doctor_directory' );
		$doctor    = $contracts->destination( 'doctor_onboarding' );
		$html      = $this->root_open( $root, $locale, '' );
		$html     .= $this->navigation_controls( 'how_it_works', $locale );
		$html     .= '<header class="gcu-page__header"><span class="gcu-eyebrow">' . $this->icon( 'route' ) . esc_html( GCU_I18n::text( 'transparent_process', $locale ) ) . '</span><h1>' . esc_html( GCU_I18n::text( 'how_title', $locale ) ) . '</h1></header>';
		$html     .= '<section class="gcu-journeys" aria-label="' . esc_attr( GCU_I18n::text( 'journeys_label', $locale ) ) . '">';
		$html     .= $this->journey_card(
			GCU_I18n::text( 'for_patients', $locale ),
			array(
				GCU_I18n::text( 'patient_step_1', $locale ),
				GCU_I18n::text( 'patient_step_2', $locale ),
				GCU_I18n::text( 'patient_step_3', $locale ),
				GCU_I18n::text( 'patient_step_4', $locale ),
			),
			$patient,
			GCU_I18n::text( 'find_doctor', $locale ),
			'patient',
			$locale
		);
		$html     .= $this->journey_card(
			GCU_I18n::text( 'for_doctors', $locale ),
			array(
				GCU_I18n::text( 'doctor_step_1', $locale ),
				GCU_I18n::text( 'doctor_step_2', $locale ),
				GCU_I18n::text( 'doctor_step_3', $locale ),
				GCU_I18n::text( 'doctor_step_4', $locale ),
			),
			$doctor,
			GCU_I18n::text( 'start_clinic', $locale ),
			'doctor',
			$locale
		);
		$html .= '</section>' . $this->render_faqs( $root, $locale ) . $this->emergency_notice( $locale ) . '</div>';
		return $html;
	}

	public function render_blocks( $slot, $audience ) {
		$locale = $this->current_locale();
		$rows   = GCU_Plugin::instance()->repository()->active_blocks( $slot, $audience, $locale );
		if ( empty( $rows ) && 'en-US' !== $locale ) {
			$rows = GCU_Plugin::instance()->repository()->active_blocks( $slot, $audience, 'en-US' );
		}
		if ( empty( $rows ) ) {
			return '<section class="gcu-state gcu-state--empty" role="status" aria-live="polite"><h2>' . esc_html( GCU_I18n::text( 'content_review_title', $locale ) ) . '</h2><p>' . esc_html( GCU_I18n::text( 'content_review_body', $locale ) ) . '</p></section>';
		}

		$html = '<section class="gcu-grid gcu-grid--' . esc_attr( $slot ) . '">';
		foreach ( $rows as $row ) {
			$health = ! empty( $row['cta_destination'] ) ? GCU_Plugin::instance()->contracts()->destination( $row['cta_destination'] ) : array( 'available' => false, 'url' => '' );
			$claims = GCU_Plugin::instance()->repository()->public_claims( $row['claim_keys'] );
			$html  .= '<article class="gcu-card gcu-card--' . esc_attr( $row['block_type'] ) . '" data-block-version="' . esc_attr( $row['content_version'] ) . '">';
			$html  .= '<div class="gcu-card__icon" aria-hidden="true">' . $this->icon( 'patient' === $row['audience'] ? 'search' : ( 'doctor' === $row['audience'] ? 'clinic' : 'shield' ) ) . '</div>';
			$html  .= '<h2>' . esc_html( $row['title'] ) . '</h2><div class="gcu-card__body">' . wp_kses_post( wpautop( $row['body'] ) ) . '</div>';
			if ( $row['cta_label'] ) {
				if ( ! empty( $health['available'] ) && ! empty( $health['url'] ) ) {
					$token = $this->event_token();
					$html .= '<a class="gcu-button" href="' . esc_url( $health['url'] ) . '" data-gcu-destination="' . esc_attr( $row['cta_destination'] ) . '"' . ( $token ? ' data-gcu-event-token="' . esc_attr( $token ) . '"' : '' ) . '>' . $this->icon( 'arrow', true ) . '<span>' . esc_html( $row['cta_label'] ) . '</span></a>';
				} else {
					$html .= '<p class="gcu-destination-state" role="status" aria-live="polite">' . $this->icon( 'info' ) . '<span>' . esc_html( GCU_I18n::text( 'destination_unavailable', $locale ) ) . '</span></p>';
				}
			}
			if ( $claims ) {
				$html .= '<details class="gcu-claims"><summary>' . esc_html( GCU_I18n::text( 'trust_policy_details', $locale ) ) . '</summary><ul>';
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
		return GCU_I18n::current_locale();
	}

	private function render_faqs( $root, $locale ) {
		$items = GCU_Plugin::instance()->repository()->active_blocks( 'global_clinic_faq', 'all', $locale );
		if ( empty( $items ) && 'en-US' !== $locale ) {
			$items = GCU_Plugin::instance()->repository()->active_blocks( 'global_clinic_faq', 'all', 'en-US' );
		}
		if ( empty( $items ) ) {
			return '';
		}
		$title_id = $root . '-faq-title';
		$html = '<section class="gcu-faq" aria-labelledby="' . esc_attr( $title_id ) . '"><h2 id="' . esc_attr( $title_id ) . '">' . $this->icon( 'question' ) . esc_html( GCU_I18n::text( 'faq_title', $locale ) ) . '</h2>';
		foreach ( $items as $item ) {
			$html .= '<details data-block-version="' . esc_attr( $item['content_version'] ) . '"><summary>' . esc_html( $item['title'] ) . '</summary><div>' . wp_kses_post( wpautop( $item['body'] ) ) . '</div></details>';
		}
		return $html . '</section>';
	}

	private function journey_card( $title, array $steps, array $health, $label, $audience, $locale ) {
		$html = '<article class="gcu-card gcu-card--journey"><div class="gcu-card__icon" aria-hidden="true">' . $this->icon( 'patient' === $audience ? 'search' : 'clinic' ) . '</div><h2>' . esc_html( $title ) . '</h2><ol>';
		foreach ( $steps as $step ) {
			$html .= '<li>' . esc_html( $step ) . '</li>';
		}
		$html .= '</ol>';
		if ( ! empty( $health['available'] ) && ! empty( $health['url'] ) ) {
			$token = $this->event_token();
			$html .= '<a class="gcu-button" href="' . esc_url( $health['url'] ) . '" data-gcu-destination="' . esc_attr( $health['key'] ) . '"' . ( $token ? ' data-gcu-event-token="' . esc_attr( $token ) . '"' : '' ) . '>' . $this->icon( 'arrow', true ) . '<span>' . esc_html( $label ) . '</span></a>';
		} else {
			$html .= '<p class="gcu-destination-state" role="status" aria-live="polite">' . $this->icon( 'info' ) . '<span>' . esc_html( GCU_I18n::text( 'owner_unavailable', $locale ) ) . '</span></p>';
		}
		return $html . '</article>';
	}

	private function render_degraded( $destination ) {
		$locale = $this->current_locale();
		$root   = $this->root_id( 'degraded' );
		$html   = $this->root_open( $root, $locale, '' );
		$html  .= $this->navigation_controls( 'degraded', $locale );
		$html  .= '<section class="gcu-state gcu-state--error" role="alert" aria-live="assertive"><div class="gcu-state__icon" aria-hidden="true">' . $this->icon( 'info' ) . '</div><h1>' . esc_html( GCU_I18n::text( 'service_unavailable', $locale ) ) . '</h1><p>' . esc_html( GCU_I18n::text( 'degraded_body', $locale ) ) . '</p><p><code>' . esc_html( sanitize_key( $destination ) ) . '</code></p></section></div>';
		return $html;
	}

	private function emergency_notice( $locale ) {
		return '<aside class="gcu-emergency" role="note">' . $this->icon( 'alert' ) . '<div><strong>' . esc_html( GCU_I18n::text( 'emergency_title', $locale ) ) . '</strong><p>' . esc_html( GCU_I18n::text( 'emergency_body', $locale ) ) . '</p></div></aside>';
	}

	private function navigation_controls( $context, $locale ) {
		$shared = apply_filters(
			'sabri_shell_back_home_controls',
			'',
			array(
				'owner'        => 'File 14',
				'home_url'     => home_url( '/' ),
				'fallback_url' => home_url( '/global-clinic/' ),
				'direction'    => GCU_I18n::direction( $locale ),
			)
		);
		if ( is_string( $shared ) && '' !== trim( $shared ) ) {
			return $shared;
		}

		$back_url = 'global_clinic' === $context ? home_url( '/' ) : home_url( '/global-clinic/' );
		return '<nav class="gcu-context-nav" data-gcu-shell-fallback="true" aria-label="' . esc_attr( GCU_I18n::text( 'page_navigation', $locale ) ) . '"><a href="' . esc_url( $back_url ) . '">' . $this->icon( 'back', true ) . '<span>' . esc_html( GCU_I18n::text( 'back', $locale ) ) . '</span></a><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . $this->icon( 'home' ) . '<span>' . esc_html( GCU_I18n::text( 'home', $locale ) ) . '</span></a></nav>';
	}

	private function event_token() {
		if ( ! GCU_Privacy::measurement_allowed() || GCU_Privacy::low_bandwidth_requested() ) {
			return '';
		}
		$id  = wp_generate_uuid4();
		$key = 'gcu_event_token_' . str_replace( '-', '', $id );
		set_transient( $key, 1, GCU_Policy::EVENT_TOKEN_TTL );
		return $id . '.' . hash_hmac( 'sha256', $id, wp_salt( 'nonce' ) );
	}

	private function root_open( $root, $locale, $token ) {
		$attrs = ' id="' . esc_attr( $root ) . '" lang="' . esc_attr( GCU_I18n::language( $locale ) ) . '" dir="' . esc_attr( GCU_I18n::direction( $locale ) ) . '" data-gcu-module-version="' . esc_attr( GCU_VERSION ) . '"';
		if ( GCU_Privacy::low_bandwidth_requested() ) {
			$attrs .= ' data-gcu-low-bandwidth="true"';
		}
		if ( $token ) {
			$attrs .= ' data-gcu-impression-token="' . esc_attr( $token ) . '"';
		}
		return '<div class="gcu-page"' . $attrs . '>';
	}

	private function root_id( $context ) {
		self::$instance_counter++;
		return 'gcu-' . sanitize_html_class( $context ) . '-' . self::$instance_counter;
	}

	private function icon( $name, $directional = false ) {
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
		$path  = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['info'];
		$class = 'gcu-icon' . ( $directional ? ' gcu-icon--directional' : '' );
		return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
	}
}
