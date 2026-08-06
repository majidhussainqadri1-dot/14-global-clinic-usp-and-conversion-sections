<?php

defined( 'ABSPATH' ) || exit;

final class GCU_Admin {
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_post_gcu_repair', array( $this, 'repair' ) );
		add_action( 'admin_post_gcu_rollback', array( $this, 'rollback' ) );
		add_action( 'admin_post_gcu_toggle', array( $this, 'toggle' ) );
		add_action( 'admin_notices', array( $this, 'migration_notice' ) );
	}

	public function menu() {
		add_options_page(
			__( 'Global Clinic USP Governance', 'global-clinic-usp-integration' ),
			__( 'Global Clinic USP', 'global-clinic-usp-integration' ),
			GCU_Capabilities::SYSTEM_CHECK,
			'global-clinic-usp-integration',
			array( $this, 'page' )
		);
	}

	public function page() {
		if ( ! GCU_Capabilities::can( GCU_Capabilities::SYSTEM_CHECK, null, 'admin_page' ) ) {
			wp_die( esc_html__( 'You are not authorized to view this page.', 'global-clinic-usp-integration' ) );
		}
		$report = GCU_Plugin::instance()->observability()->health_report();
		$notice = isset( $_GET['gcu_notice'] ) ? sanitize_key( wp_unslash( $_GET['gcu_notice'] ) ) : '';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Global Clinic USP Governance', 'global-clinic-usp-integration' ); ?></h1>
			<p><?php esc_html_e( 'File 14 owns approved value-proposition content, placement governance, claim evidence, ethical conversion measurement and destination contracts. It does not own doctor, clinic, appointment, payment or clinical truth.', 'global-clinic-usp-integration' ); ?></p>
			<?php if ( $notice ) : ?><div class="notice notice-info inline"><p><?php echo esc_html( str_replace( '-', ' ', $notice ) ); ?></p></div><?php endif; ?>

			<h2><?php esc_html_e( 'Release and System Check', 'global-clinic-usp-integration' ); ?></h2>
			<table class="widefat striped" style="max-width:1100px">
				<tbody>
				<tr><th><?php esc_html_e( 'Plugin version', 'global-clinic-usp-integration' ); ?></th><td><?php echo esc_html( $report['version'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'Schema version', 'global-clinic-usp-integration' ); ?></th><td><?php echo esc_html( $report['schema_version'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'Operational state', 'global-clinic-usp-integration' ); ?></th><td><?php echo esc_html( $report['enabled'] ? 'enabled' : 'safe-mode disabled' ); ?></td></tr>
				<tr><th><?php esc_html_e( 'Missing tables', 'global-clinic-usp-integration' ); ?></th><td><?php echo esc_html( $report['missing_tables'] ? implode( ', ', $report['missing_tables'] ) : 'none' ); ?></td></tr>
				<tr><th><?php esc_html_e( 'Claims past review date', 'global-clinic-usp-integration' ); ?></th><td><?php echo esc_html( $report['stale_claims'] ); ?></td></tr>
				</tbody>
			</table>

			<h2><?php esc_html_e( 'Destination Contracts', 'global-clinic-usp-integration' ); ?></h2>
			<table class="widefat striped" style="max-width:1100px"><thead><tr><th><?php esc_html_e( 'Destination', 'global-clinic-usp-integration' ); ?></th><th><?php esc_html_e( 'Owner', 'global-clinic-usp-integration' ); ?></th><th><?php esc_html_e( 'State', 'global-clinic-usp-integration' ); ?></th><th><?php esc_html_e( 'URL', 'global-clinic-usp-integration' ); ?></th></tr></thead><tbody>
			<?php foreach ( $report['destinations'] as $key => $destination ) : ?>
				<tr><td><?php echo esc_html( $key ); ?></td><td><?php echo esc_html( $destination['owner'] ); ?></td><td><?php echo esc_html( $destination['reason'] ); ?></td><td><?php echo $destination['url'] ? '<a href="' . esc_url( $destination['url'] ) . '" target="_blank" rel="noopener">' . esc_html__( 'Open', 'global-clinic-usp-integration' ) . '</a>' : esc_html__( 'Unavailable', 'global-clinic-usp-integration' ); ?></td></tr>
			<?php endforeach; ?>
			</tbody></table>

			<h2><?php esc_html_e( 'Approved Business Integrity', 'global-clinic-usp-integration' ); ?></h2>
			<ul>
				<li><strong>0%</strong> <?php esc_html_e( 'platform commission on approved clinic flows.', 'global-clinic-usp-integration' ); ?></li>
				<li><?php esc_html_e( 'One currently approved free tier; optional support does not buy ranking, visibility or verification.', 'global-clinic-usp-integration' ); ?></li>
				<li><?php esc_html_e( 'No cure, income, approval, scarcity or success guarantee.', 'global-clinic-usp-integration' ); ?></li>
				<li><?php esc_html_e( 'Measurement is disabled unless consent exists and is aggregated with a disclosure threshold.', 'global-clinic-usp-integration' ); ?></li>
			</ul>

			<h2><?php esc_html_e( 'Safe Operations', 'global-clinic-usp-integration' ); ?></h2>
			<div style="display:flex;gap:12px;flex-wrap:wrap">
				<?php $this->action_form( 'gcu_repair', __( 'Run owner-scoped safe repair', 'global-clinic-usp-integration' ) ); ?>
				<?php $this->action_form( 'gcu_rollback', __( 'Restore plugin option snapshot', 'global-clinic-usp-integration' ) ); ?>
				<?php $this->action_form( 'gcu_toggle', $report['enabled'] ? __( 'Enter safe mode', 'global-clinic-usp-integration' ) : __( 'Leave safe mode', 'global-clinic-usp-integration' ), array( 'enabled' => $report['enabled'] ? '0' : '1' ) ); ?>
			</div>

			<h2><?php esc_html_e( 'Canonical API Contracts', 'global-clinic-usp-integration' ); ?></h2>
			<p><code>GET /wp-json/gcu/v1/blocks</code> · <code>GET /wp-json/gcu/v1/destinations</code> · <code>POST /wp-json/gcu/v1/events</code> · <code>POST /wp-json/gcu/v1/content</code> · <code>PATCH /wp-json/gcu/v1/workflow/{machine}/{id}</code></p>
			<p><?php esc_html_e( 'All mutating commands re-check capability, record state and expected row version. Companion modules receive no direct writes.', 'global-clinic-usp-integration' ); ?></p>
		</div>
		<?php
	}

	public function repair() {
		$this->authorize( 'gcu_repair', GCU_Capabilities::SYSTEM_CHECK );
		GCU_Install::capture_snapshot( true );
		GCU_Install::schema();
		GCU_Capabilities::install();
		GCU_Install::seed_governed_content();
		GCU_Install::schedule();
		GCU_Plugin::instance()->repository()->audit( 'safe_repair', 'module', 'file14', 'operations', 'Owner-scoped repair requested', array(), array( 'schema' => GCU_SCHEMA_VERSION ) );
		$this->redirect( 'safe-repair-completed' );
	}

	public function rollback() {
		$this->authorize( 'gcu_rollback', GCU_Capabilities::SYSTEM_CHECK );
		$result = GCU_Install::rollback_options();
		$this->redirect( is_wp_error( $result ) ? 'rollback-unavailable' : 'snapshot-restored' );
	}

	public function toggle() {
		$this->authorize( 'gcu_toggle', GCU_Capabilities::SYSTEM_CHECK );
		$enabled = isset( $_POST['enabled'] ) && '1' === sanitize_key( wp_unslash( $_POST['enabled'] ) );
		update_option( 'gcu_enabled', $enabled ? 1 : 0, false );
		GCU_Plugin::instance()->repository()->audit( 'safe_mode_changed', 'module', 'file14', 'operations', '', array(), array( 'enabled' => $enabled ) );
		$this->redirect( $enabled ? 'module-enabled' : 'safe-mode-enabled' );
	}

	public function migration_notice() {
		if ( ! GCU_Capabilities::can( GCU_Capabilities::SYSTEM_CHECK, null, 'migration_notice' ) ) {
			return;
		}
		$log = get_option( GCU_Install::MIGRATION_LOG, array() );
		if ( empty( $log['detected'] ) || get_user_meta( get_current_user_id(), 'gcu_legacy_notice_seen', true ) ) {
			return;
		}
		update_user_meta( get_current_user_id(), 'gcu_legacy_notice_seen', 1 );
		echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'File 14 detected the legacy SGC package. It performed a read-only inventory and did not overwrite or delete legacy pages, options or companion records.', 'global-clinic-usp-integration' ) . '</p></div>';
	}

	private function action_form( $action, $label, array $fields = array() ) {
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
			<?php foreach ( $fields as $name => $value ) : ?><input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"><?php endforeach; ?>
			<?php wp_nonce_field( $action ); ?>
			<?php submit_button( $label, 'secondary', 'submit', false ); ?>
		</form>
		<?php
	}

	private function authorize( $action, $capability ) {
		check_admin_referer( $action );
		if ( ! GCU_Capabilities::can( $capability, null, $action ) ) {
			wp_die( esc_html__( 'You are not authorized to perform this action.', 'global-clinic-usp-integration' ) );
		}
	}

	private function redirect( $notice ) {
		wp_safe_redirect( add_query_arg( 'gcu_notice', sanitize_key( $notice ), admin_url( 'options-general.php?page=global-clinic-usp-integration' ) ) );
		exit;
	}
}
