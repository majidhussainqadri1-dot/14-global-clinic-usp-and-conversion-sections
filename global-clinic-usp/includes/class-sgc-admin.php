<?php

defined( 'ABSPATH' ) || exit;

final class SGC_Admin {
	public function hooks() {
		add_action( 'admin_init', array( $this, 'settings' ) );
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_notices', array( $this, 'activation_notice' ) );
	}

	public function settings() {
		register_setting(
			'sgc_settings',
			'sgc_placements',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => SGC_Helpers::defaults(),
			)
		);
	}

	public function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();
		$out   = array();
		foreach ( array_keys( SGC_Helpers::defaults() ) as $key ) {
			$out[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
		}
		return $out;
	}

	public function menu() {
		add_options_page(
			__( 'Global Clinic USP', 'global-clinic-usp' ),
			__( 'Global Clinic USP', 'global-clinic-usp' ),
			'manage_options',
			'global-clinic-usp',
			array( $this, 'page' )
		);
	}

	public function page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = SGC_Helpers::placements();
		$labels   = array(
			'home_hero'      => 'Show the Global Clinic hero on the public home page',
			'doctor_portal'  => 'Show Doctor Portal in compatible platform navigation',
			'patient_banner' => 'Show the global-access banner above doctor search',
			'footer_mission' => 'Show the Our Mission link in the public footer',
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Global Clinic USP', 'global-clinic-usp' ); ?></h1>
			<p><?php esc_html_e( 'Manage the four approved placements for the Global Cloud Clinic Network proposition.', 'global-clinic-usp' ); ?></p>
			<form action="options.php" method="post">
				<?php settings_fields( 'sgc_settings' ); ?>
				<table class="form-table" role="presentation">
					<?php foreach ( $labels as $key => $label ) : ?>
						<tr><th scope="row"><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></th><td><label><input type="checkbox" name="sgc_placements[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( ! empty( $settings[ $key ] ) ); ?>> <?php echo esc_html( $label ); ?></label></td></tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button(); ?>
			</form>
			<p><a class="button button-primary" href="<?php echo esc_url( SGC_Helpers::page_url( 'portal' ) ); ?>" target="_blank" rel="noopener">View Doctor Portal</a> <a class="button" href="<?php echo esc_url( SGC_Helpers::page_url( 'mission' ) ); ?>" target="_blank" rel="noopener">View Our Mission</a></p>
		</div>
		<?php
	}

	public function activation_notice() {
		if ( ! current_user_can( 'manage_options' ) || ! get_transient( 'sgc_activation_notice' ) ) {
			return;
		}

		delete_transient( 'sgc_activation_notice' );
		?>
		<div class="notice notice-success is-dismissible"><p><strong>Global Clinic USP activated.</strong> The Home Hero, Doctor Portal, patient search banner, and Our Mission placements are ready. <a href="<?php echo esc_url( admin_url( 'options-general.php?page=global-clinic-usp' ) ); ?>">Review settings</a>.</p></div>
		<?php
	}
}
