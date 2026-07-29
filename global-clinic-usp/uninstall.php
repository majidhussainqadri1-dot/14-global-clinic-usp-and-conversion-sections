<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'sgc_placements' );
delete_option( 'sgc_page_map' );
delete_option( 'sgc_version' );
delete_option( 'sgc_schema_version' );
delete_option( 'sgc_setup_errors' );
delete_option( 'sgc_activation_snapshot' );
delete_transient( 'sgc_activation_notice' );

// Managed pages and all third-party content are preserved to prevent unintended data loss.
