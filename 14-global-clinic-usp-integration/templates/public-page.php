<?php
/** Public route template. */
defined( 'ABSPATH' ) || exit;
get_header();
echo GCU_Plugin::instance()->frontend()->render_route(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
get_footer();
