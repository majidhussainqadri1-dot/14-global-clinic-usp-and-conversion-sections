<?php

defined( 'ABSPATH' ) || exit;

final class SGC_Admin {
	public function hooks() {
		add_action( 'admin_init', array( $this, 'settings' ) );
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_notices', array( $this, 'activation_notice' ) );
		add_action( 'admin_post_sgc_repair', array( $this, 'repair' ) );
		add_action( 'admin_post_sgc_rollback', array( $this, 'rollback' ) );
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
		$health   = SGC_Activator::health_report();
		$labels   = array(
			'home_hero'      => __( 'Show the Global Clinic conversion hero on the public home page', 'global-clinic-usp' ),
			'doctor_portal'  => __( 'Register Doctor Portal in the Unified Application Shell navigation', 'global-clinic-usp' ),
			'patient_banner' => __( 'Show the global-access banner on compatible doctor and clinic pages', 'global-clinic-usp' ),
			'footer_mission' => __( 'Show the Our Mission link on eligible public pages', 'global-clinic-usp' ),
		);
		$notice = isset( $_GET['sgc_notice'] ) ? sanitize_key( wp_unslash( $_GET['sgc_notice'] ) ) : '';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Global Clinic USP', 'global-clinic-usp' ); ?></h1>
			<?php if ( 'repaired' === $notice ) : ?><div class="notice notice-success inline"><p><?php esc_html_e( 'Safe repair completed. Existing page content was not overwritten.', 'global-clinic-usp' ); ?></p></div><?php endif; ?>
			<?php if ( 'rolled-back' === $notice ) : ?><div class="notice notice-success inline"><p><?php esc_html_e( 'Plugin-owned settings were restored from the latest snapshot.', 'global-clinic-usp' ); ?></p></div><?php endif; ?>
			<?php if ( 'rollback-unavailable' === $notice ) : ?><div class="notice notice-warning inline"><p><?php esc_html_e( 'No activation or repair snapshot is available.', 'global-clinic-usp' ); ?></p></div><?php endif; ?>

			<p><?php esc_html_e( 'Manage the approved Global Clinic conversion placements. Public commission wording remains neutral until a single platform-wide business policy is approved and implemented.', 'global-clinic-usp' ); ?></p>
			<form action="options.php" method="post">
				<?php settings_fields( 'sgc_settings' ); ?>
				<table class="form-table" role="presentation">
					<?php foreach ( $labels as $key => $label ) : ?>
						<tr><th scope="row"><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></th><td><label><input type="checkbox" name="sgc_placements[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( ! empty( $settings[ $key ] ) ); ?>> <?php echo esc_html( $label ); ?></label></td></tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button(); ?>
			</form>

			<h2><?php esc_html_e( 'System Health', 'global-clinic-usp' ); ?></h2>
			<table class="widefat striped" style="max-width:900px"><thead><tr><th><?php esc_html_e( 'Component', 'global-clinic-usp' ); ?></th><th><?php esc_html_e( 'Status', 'global-clinic-usp' ); ?></th><th><?php esc_html_e( 'Page', 'global-clinic-usp' ); ?></th></tr></thead><tbody>
			<?php foreach ( array( 'portal' => __( 'Doctor Portal', 'global-clinic-usp' ), 'mission' => __( 'Our Mission', 'global-clinic-usp' ) ) as $key => $label ) : $row = $health[ $key ]; ?>
				<tr><td><?php echo esc_html( $label ); ?></td><td><?php echo esc_html( strtoupper( $row['status'] ) ); ?></td><td><?php echo $row['url'] ? '<a href="' . esc_url( $row['url'] ) . '" target="_blank" rel="noopener">' . esc_html__( 'View', 'global-clinic-usp' ) . '</a>' : esc_html__( 'Unavailable', 'global-clinic-usp' ); ?></td></tr>
			<?php endforeach; ?>
			</tbody></table>

			<?php if ( ! empty( $health['errors'] ) ) : ?>
				<div class="notice notice-warning inline"><p><strong><?php esc_html_e( 'Setup conflicts detected:', 'global-clinic-usp' ); ?></strong></p><ul><?php foreach ( $health['errors'] as $error ) : ?><li><?php echo esc_html( $error['code'] . ' — ' . $error['slug'] . ( $error['page_id'] ? ' — Page ' . $error['page_id'] : '' ) ); ?></li><?php endforeach; ?></ul></div>
			<?php endif; ?>

			<div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:18px">
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'sgc_repair' ); ?><input type="hidden" name="action" value="sgc_repair"><?php submit_button( __( 'Run Safe Repair', 'global-clinic-usp' ), 'secondary', 'submit', false ); ?></form>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'sgc_rollback' ); ?><input type="hidden" name="action" value="sgc_rollback"><?php submit_button( __( 'Restore Plugin Settings Snapshot', 'global-clinic-usp' ), 'secondary', 'submit', false ); ?></form>
			</div>
		</div>
		<?php
	}

	public function repair() {
		$this->authorize( 'sgc_repair' );
		SGC_Activator::repair();
		wp_safe_redirect( add_query_arg( 'sgc_notice', 'repaired', admin_url( 'options-general.php?page=global-clinic-usp' ) ) );
		exit;
	}

	public function rollback() {
		$this->authorize( 'sgc_rollback' );
		$notice = SGC_Activator::rollback() ? 'rolled-back' : 'rollback-unavailable';
		wp_safe_redirect( add_query_arg( 'sgc_notice', $notice, admin_url( 'options-general.php?page=global-clinic-usp' ) ) );
		exit;
	}

	public function activation_notice() {
		if ( ! current_user_can( 'manage_options' ) || ! get_transient( 'sgc_activation_notice' ) ) {
			return;
		}
		delete_transient( 'sgc_activation_notice' );
		?> <div class="notice notice-success is-dismissible"><p><strong><?php esc_html_e( 'Global Clinic USP activated safely.', 'global-clinic-usp' ); ?></strong> <?php esc_html_e( 'Existing page content was preserved. Review mappings and settings before staging acceptance.', 'global-clinic-usp' ); ?> <a href="<?php echo esc_url( admin_url( 'options-general.php?page=global-clinic-usp' ) ); ?>"><?php esc_html_e( 'Review settings', 'global-clinic-usp' ); ?></a>.</p></div> <?php
	}

	private function authorize( $action ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to perform this action.', 'global-clinic-usp' ) );
		}
		check_admin_referer( $action );
	}
}
