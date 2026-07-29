<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'sgc_placements' );
delete_option( 'sgc_page_map' );
delete_option( 'sgc_version' );

// Managed pages are preserved to prevent unintended content loss.
