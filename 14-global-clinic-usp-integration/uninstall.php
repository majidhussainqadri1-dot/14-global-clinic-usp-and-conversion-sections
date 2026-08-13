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

$approval = get_option( 'gcu_purge_approval_v1', array() );
$now = time();
$approval_ok = is_array( $approval )
	&& ! empty( $approval['approved_by'] )
	&& user_can( absint( $approval['approved_by'] ), 'manage_options' )
	&& ! empty( $approval['approved_at'] )
	&& (int) $approval['approved_at'] <= $now
	&& (int) $approval['approved_at'] >= $now - DAY_IN_SECONDS
	&& ! empty( $approval['backup_verified_at'] )
	&& (int) $approval['backup_verified_at'] <= $now
	&& ! empty( $approval['restore_verified_at'] )
	&& (int) $approval['restore_verified_at'] <= $now;
$allow = defined( 'GCU_ALLOW_PURGE' ) && true === GCU_ALLOW_PURGE && (bool) get_option( 'gcu_purge_on_uninstall', false ) && $approval_ok;
if ( ! $allow ) {
	return;
}

$receipt = array(
	'file' => 14,
	'status' => 'started',
	'approved_by' => absint( $approval['approved_by'] ),
	'approved_at' => (int) $approval['approved_at'],
	'backup_verified_at' => (int) $approval['backup_verified_at'],
	'restore_verified_at' => (int) $approval['restore_verified_at'],
	'approval_digest' => hash( 'sha256', wp_json_encode( $approval ) ),
	'started_at' => $now,
);
update_option( 'gcu_purge_receipt_v1', $receipt, false );
$stored_receipt = get_option( 'gcu_purge_receipt_v1', array() );
if ( ! is_array( $stored_receipt ) || empty( $stored_receipt['approval_digest'] ) || ! hash_equals( $receipt['approval_digest'], (string) $stored_receipt['approval_digest'] ) ) {
	return;
}
do_action( 'gcu_destructive_purge_authorized_v1', $receipt );

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
$purge_ok = true;
foreach ( $tables as $table ) {
	if ( false === $wpdb->query( "DROP TABLE IF EXISTS `$table`" ) ) {
		$purge_ok = false;
		break;
	}
}
if ( ! $purge_ok ) {
	$receipt['status'] = 'incomplete';
	$receipt['failed_at'] = time();
	update_option( 'gcu_purge_receipt_v1', $receipt, false );
	return;
}

$options = array(
	'gcu_install_lock', 'gcu_version', 'gcu_schema_version', 'gcu_rollback_snapshot', 'gcu_migration_log', 'gcu_upgrade_error',
	'gcu_enabled', 'gcu_settings', 'gcu_legacy_migrated', 'gcu_last_health_report', 'gcu_policy_revalidation_required',
	'gcu_purge_on_uninstall', 'gcu_purge_approval_v1', 'gcu_destination_state_doctor_directory', 'gcu_destination_state_clinic',
	'gcu_destination_state_doctor_onboarding', 'gcu_future_schema_version', 'gcu_future_safe_mode',
	'gcu_future_last_anomaly', 'gcu_future_last_parity',
	'gcu_audit_hmac_key_v1', 'gcu_privacy_hmac_key_v1', 'gcu_integrity_key_migration_v1',
);
foreach ( $options as $option ) {
	delete_option( $option );
}
$meta_subject = $wpdb->delete( $wpdb->usermeta, array( 'meta_key' => '_gcu_measurement_subject_v1' ) );
$meta_notice = $wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'gcu_legacy_notice_seen' ) );
$receipt['status'] = ( false === $meta_subject || false === $meta_notice ) ? 'incomplete' : 'completed';
$receipt['completed_at'] = time();
update_option( 'gcu_purge_receipt_v1', $receipt, false );
do_action( 'gcu_destructive_purge_completed_v1', $receipt );
