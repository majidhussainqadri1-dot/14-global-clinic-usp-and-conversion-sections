<?php
/**
 * File 14 V12.3: read-only deployed-state freeze for WP-CLI on the target host.
 * Usage: wp eval-file tools/file14-readonly-deployed-freeze.php
 * No patient data, row values, credentials, option contents or logs are output.
 * Running this file against staging does NOT prove the state of production.
 */
if ( PHP_SAPI !== 'cli' || ! defined( 'ABSPATH' ) || ! defined( 'WP_PLUGIN_DIR' ) || ! function_exists( 'get_option' ) ) {
    fwrite( STDERR, "WordPress CLI context required.\n" );
    exit( 2 );
}
$root = WP_PLUGIN_DIR . '/14-global-clinic-usp-integration';
$entry = $root . '/global-clinic-usp-integration.php';
$exists = is_file( $entry );
$plugins = get_option( 'active_plugins', array() );
$active = is_array( $plugins ) && in_array(
    '14-global-clinic-usp-integration/global-clinic-usp-integration.php', $plugins, true
);
$version = null;
if ( $exists && function_exists( 'get_file_data' ) ) {
    $meta = get_file_data( $entry, array( 'Version' => 'Version' ), 'plugin' );
    $version = isset( $meta['Version'] ) ? (string) $meta['Version'] : null;
}
$hashes = array();
$failures = array();
if ( is_dir( $root ) ) {
    try {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS )
        );
        foreach ( $it as $item ) {
            if ( count( $hashes ) >= 2000 ) { $failures[] = 'file_limit_2000'; break; }
            if ( $item->isLink() || ! $item->isFile() || ! $item->isReadable() ) {
                $failures[] = 'unreadable_or_symlink'; continue;
            }
            $rel = substr( $item->getPathname(), strlen( $root ) + 1 );
            $hash = hash_file( 'sha256', $item->getPathname() );
            if ( ! is_string( $hash ) || 64 !== strlen( $hash ) ) {
                $failures[] = 'hash_failed'; continue;
            }
            $hashes[ str_replace( DIRECTORY_SEPARATOR, '/', $rel ) ] = $hash;
        }
    } catch ( Exception $e ) {
        $failures[] = 'directory_scan_exception';
    }
}
ksort( $hashes, SORT_STRING );
$ctx = hash_init( 'sha256' );
foreach ( $hashes as $name => $hash ) {
    hash_update( $ctx, $name . "\0" . $hash . "\n" );
}
$tree_hash = $hashes && ! $failures ? hash_final( $ctx ) : null;
$allowed = array(
    'gcu_version', 'gcu_schema_version', 'gcu_future_schema_version',
    'gcu_migration_log', 'gcu_upgrade_error', 'gcu_install_lock', 'gcu_enabled'
);
$options = array();
foreach ( $allowed as $key ) {
    $value = get_option( $key, null );
    $options[ $key ] = array(
        'present' => null !== $value,
        'value_type' => gettype( $value ),
        'scalar_version' => in_array( $key, array(
            'gcu_version', 'gcu_schema_version', 'gcu_future_schema_version'
        ), true ) && is_scalar( $value ) ? (string) $value : null,
        'boolean_state' => 'gcu_enabled' === $key && is_scalar( $value ) ? (bool) $value : null,
    );
}
$schema = array();
if ( isset( $GLOBALS['wpdb'] ) && is_object( $GLOBALS['wpdb'] ) ) {
    $wpdb = $GLOBALS['wpdb'];
    $prefix = $wpdb->prefix . 'gcu_';
    $like = $wpdb->esc_like( $prefix ) . '%';
    $tables = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLE STATUS LIKE %s', $like ), ARRAY_A );
    if ( is_array( $tables ) ) {
        foreach ( $tables as $table ) {
            if ( ! is_array( $table ) || empty( $table['Name'] ) ) { continue; }
            $name = (string) $table['Name'];
            if ( 0 !== strpos( $name, $prefix ) ) { continue; }
            $quoted = '`' . str_replace( '`', '``', $name ) . '`';
            $columns = $wpdb->get_results( 'SHOW COLUMNS FROM ' . $quoted, ARRAY_A );
            $schema[ $name ] = array(
                'engine' => isset( $table['Engine'] ) ? (string) $table['Engine'] : null,
                'columns' => is_array( $columns ) ? array_map(
                    static function ( $col ) {
                        return array(
                            'name' => isset( $col['Field'] ) ? $col['Field'] : '',
                            'type' => isset( $col['Type'] ) ? $col['Type'] : '',
                            'nullable' => isset( $col['Null'] ) ? $col['Null'] : '',
                        );
                    }, $columns
                ) : null,
            );
        }
    }
}
$report = array(
    'scope' => 'exact-wordpress-execution-context-read-only',
    'site_url' => function_exists( 'home_url' ) ? home_url( '/' ) : null,
    'captured_at_utc' => gmdate( 'c' ),
    'plugin_active' => $active,
    'plugin_entry_exists' => $exists,
    'plugin_version_header' => $version,
    'plugin_runtime_version' => defined( 'GCU_VERSION' ) ? GCU_VERSION : null,
    'plugin_main_sha256' => $exists ? hash_file( 'sha256', $entry ) : null,
    'deployed_tree_sha256' => $tree_hash,
    'deployed_tree_file_count' => count( $hashes ),
    'tree_capture_complete' => ! $failures && $exists,
    'tree_scan_failures' => array_values( array_unique( $failures ) ),
    'wordpress_version' => function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : null,
    'php_version' => PHP_VERSION,
    'wp_options_selected_states' => $options,
    'gcu_table_structure' => $schema,
    'migration_verified' => false,
    'deployment_parity_verified' => false,
    'live_retest_verified' => false,
    'note' => 'Verify migration logs, runtime errors and package provenance separately.',
);
echo wp_json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
