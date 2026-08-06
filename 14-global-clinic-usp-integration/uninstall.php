<?php
/**
 * Non-destructive by default. Purge requires both the GCU_ALLOW_PURGE constant
 * and the explicit gcu_purge_on_uninstall option.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

wp_clear_scheduled_hook( 'gcu_daily_governance_check' );
wp_cache_flush_group( 'gcu' );

if ( ! defined( 'GCU_ALLOW_PURGE' ) || true !== GCU_ALLOW_PURGE || ! get_option( 'gcu_purge_on_uninstall', false ) ) {
	return;
}

global $wpdb;
$tables = array(
	$wpdb->prefix . 'gcu_claims',
	$wpdb->prefix . 'gcu_content_blocks',
	$wpdb->prefix . 'gcu_placements',
	$wpdb->prefix . 'gcu_experiments',
	$wpdb->prefix . 'gcu_conversion_events',
	$wpdb->prefix . 'gcu_audit_log',
);
foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS `$table`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

foreach ( array(
	'gcu_version',
	'gcu_schema_version',
	'gcu_enabled',
	'gcu_settings',
	'gcu_legacy_migrated',
	'gcu_migration_log',
	'gcu_rollback_snapshot',
	'gcu_install_lock',
	'gcu_last_health_report',
	'gcu_policy_revalidation_required',
	'gcu_purge_on_uninstall',
) as $option ) {
	delete_option( $option );
}

$role = get_role( 'administrator' );
if ( $role ) {
	foreach ( array( 'gcu_manage_content', 'gcu_approve_claims', 'gcu_manage_placements', 'gcu_manage_experiments', 'gcu_view_analytics', 'gcu_system_check' ) as $capability ) {
		$role->remove_cap( $capability );
	}
}
