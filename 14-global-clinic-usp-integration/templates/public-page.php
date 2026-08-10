<?php
/** Public route template. */
defined( 'ABSPATH' ) || exit;
get_header();
$route = sanitize_key( (string) get_query_var( 'gcu_route' ) );
$html  = GCU_Plugin::instance()->frontend()->render_route();
echo apply_filters( 'gcu_public_route_html', $html, $route ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
get_footer();
