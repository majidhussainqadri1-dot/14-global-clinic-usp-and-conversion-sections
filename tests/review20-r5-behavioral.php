<?php
/** R5 File 01 canonical registry truth: executable fail-closed regression under PHP 7.4/8.3.
 * Mock only the declared File 01 read DTOs; never claim this proves an installed
 * WordPress database, schema, staging deployment or runtime integration.
 */
define('ABSPATH', __DIR__);
define('GCU_VERSION', '1.4.11');
define('SPF_VERSION', '2.0.1');
define('SPF_CONTRACT_VERSION', '2.0.0');
class WP_Error { public function __construct($code = '') {} }
function is_wp_error($x) { return $x instanceof WP_Error; }
function wp_json_encode($x) { return json_encode($x); }
function sanitize_key($v) { return preg_replace('/[^a-z0-9_-]/', '', strtolower((string) $v)); }
function untrailingslashit($v) { return rtrim((string) $v, '/'); }
function wp_parse_url($url, $part = -1) { return parse_url($url, $part); }
function home_url($path = '/') { return 'https://example.test' . $path; }
class GCU_Capabilities { public static function all() { return array('gcu_manage_content'); } }
class SPF_Registry {
    public static $module;
    public static $routes;
    public static $contracts;
    public static function get_module($key) { return self::$module; }
    public static function list_routes() { return self::$routes; }
    public static function list_contracts($filters) { return self::$contracts; }
}
require dirname(__DIR__) . '/14-global-clinic-usp-integration/includes/class-gcu-companion-adapters.php';
function r5_fixture() {
    SPF_Registry::$module = GCU_Companion_Adapters::file01_manifest();
    SPF_Registry::$routes = GCU_Companion_Adapters::file01_routes();
    SPF_Registry::$contracts = GCU_Companion_Adapters::file01_registry_contracts();
}
function r5_check($title, $expected) {
    $result = GCU_Companion_Adapters::file01_route_registry_state();
    if ((bool) $result['ready'] !== $expected) {
        fwrite(STDERR, "FAIL: $title; state=" . json_encode($result) . "\n");
        exit(1);
    }
    echo "PASS: $title\n";
}
r5_fixture(); r5_check('exact canonical module, routes and contracts', true);
r5_fixture(); SPF_Registry::$module['owner_file'] = '20'; r5_check('foreign owner file denied', false);
r5_fixture(); SPF_Registry::$module['software_version'] = '1.4.8'; r5_check('stale module runtime version denied', false);
r5_fixture(); SPF_Registry::$module['contract_version'] = '0.9.0'; r5_check('stale module contract version denied', false);
r5_fixture(); SPF_Registry::$module['slug'] = 'different-module'; r5_check('incorrect module slug denied', false);
r5_fixture(); SPF_Registry::$module['state'] = 'degraded'; r5_check('degraded module denied', false);
r5_fixture(); SPF_Registry::$module['state'] = 'retired'; r5_check('retired module denied', false);
r5_fixture(); SPF_Registry::$module['required'] = array(); r5_check('altered dependency manifest denied', false);
r5_fixture(); SPF_Registry::$module['global_shell_owner'] = true; r5_check('foreign shell ownership takeover denied', false);
r5_fixture(); SPF_Registry::$routes[0]['route_key'] = 'file14-alias'; r5_check('wrong canonical route key denied', false);
r5_fixture(); SPF_Registry::$routes[0]['status'] = 'redirect'; r5_check('redirect cannot masquerade as canonical route', false);
r5_fixture(); SPF_Registry::$routes[0]['destination'] = 'https://example.test/other/'; r5_check('wrong same-origin destination denied', false);
r5_fixture(); SPF_Registry::$routes[0]['destination'] = 'https://foreign.test/global-clinic/'; r5_check('foreign destination denied', false);
r5_fixture(); SPF_Registry::$routes[0]['layout_context'] = 'other'; r5_check('wrong shell layout denied', false);
r5_fixture(); SPF_Registry::$routes[0]['redirects'] = array('/trap/'); r5_check('unexpected redirect aliases denied', false);
r5_fixture(); SPF_Registry::$routes[0]['status'] = 'registered'; r5_check('genuine registered route remains eligible', true);
r5_fixture(); SPF_Registry::$contracts[0]['status'] = 'deprecated'; r5_check('deprecated API contract denied', false);
r5_fixture(); array_pop(SPF_Registry::$contracts); r5_check('missing events contract denied', false);
r5_fixture(); SPF_Registry::$contracts[0]['schema']['privacy'] = 'unbounded'; r5_check('mismatched privacy contract denied', false);
echo "R5 behavioral File 01 canonical registry truth: PASS — 19 cases\n";
