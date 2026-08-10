<?php
/** Non-destructive uninstall boundary for File 14. */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

foreach ( array(
	'gcu_daily_governance_check',
	'gcu_process_outbox',
	'gcu_process_inbox',
	'gcu_lifecycle_cleanup',
	'gcu_future_daily_governance',
	'gcu_future_hourly_intelligence',
) as $hook ) {
	wp_clear_scheduled_hook( $hook );
}

$allow = defined( 'GCU_ALLOW_PURGE' ) && true === GCU_ALLOW_PURGE && (bool) get_option( 'gcu_purge_on_uninstall', false );
if ( ! $allow ) {
	return;
}

global $wpdb;
$tables = array(
	$wpdb->prefix . 'gcu_claim_history',
	$wpdb->prefix . 'gcu_claims',
	$wpdb->prefix . 'gcu_content_blocks',
	$wpdb->prefix . 'gcu_placements',
	$wpdb->prefix . 'gcu_experiments',
	$wpdb->prefix . 'gcu_conversion_events',
	$wpdb->prefix . 'gcu_audit_log',
	$wpdb->prefix . 'gcu_event_outbox',
	$wpdb->prefix . 'gcu_event_inbox',
	$wpdb->prefix . 'gcu_event_tokens',
	$wpdb->prefix . 'gcu_rate_limits',
	$wpdb->prefix . 'gcu_commands',
	$wpdb->prefix . 'gcu_future_records',
	$wpdb->prefix . 'gcu_future_reports',
);
foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS `$table`" );
}

$options = array(
	'gcu_install_lock', 'gcu_version', 'gcu_schema_version', 'gcu_rollback_snapshot', 'gcu_migration_log', 'gcu_upgrade_error',
	'gcu_enabled', 'gcu_settings', 'gcu_legacy_migrated', 'gcu_last_health_report', 'gcu_policy_revalidation_required',
	'gcu_purge_on_uninstall', 'gcu_destination_state_doctor_directory', 'gcu_destination_state_clinic',
	'gcu_destination_state_doctor_onboarding', 'gcu_future_schema_version', 'gcu_future_safe_mode',
	'gcu_future_last_anomaly', 'gcu_future_last_parity',
);
foreach ( $options as $option ) {
	delete_option( $option );
}
$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => '_gcu_measurement_subject_v1' ) );
$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'gcu_legacy_notice_seen' ) );
