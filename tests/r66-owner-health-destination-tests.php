<?php
/**
 * R66 direct native GCU_Contracts::destination() regression tests.
 * These are mock WordPress/owner-contract tests, NOT deployed WP acceptance.
 */
error_reporting(E_ALL);
define('ABSPATH', __DIR__);
define('DAY_IN_SECONDS', 86400);
$GLOBALS['gcu_probe'] = null;
$GLOBALS['gcu_filter'] = 'none';
$GLOBALS['gcu_option_reads'] = 0;
$GLOBALS['gcu_checks'] = 0;
function sanitize_key($v) { return strtolower(preg_replace('/[^a-z0-9_\\-]/i','',(string)$v)); }
function home_url($v = '/') { return 'https://example.test' . $v; }
function get_option($key, $default = false) {
    $GLOBALS['gcu_option_reads']++;
    if (strpos($key, 'gcu_destination_state_') === 0) {
        return array('available'=>true,'verified_at'=>time(),'url'=>'https://example.test/doctors/');
    }
    return $default;
}
function apply_filters($name,$value) {
    if ('gcu_destination_contract_v1' !== $name) { return $value; }
    switch($GLOBALS['gcu_filter']) {
        case 'restrict': $value['available']=false; break;
        case 'rewrite': $value['url']='https://example.test/account/'; break;
        case 'offsite': $value['url']='https://elsewhere.invalid/doctors/'; break;
        case 'empty': $value['url']=''; break;
        case 'type': $value['url']=array('bad'); break;
    }
    return $value;
}
class GCU_Hardening {
    public static function strict_same_origin_url($url) {
        if (!is_string($url) || ''===$url) { return ''; }
        $parts=parse_url($url);
        if (!is_array($parts) || !isset($parts['scheme'],$parts['host']) ||
            'https'!==$parts['scheme'] || 'example.test'!==$parts['host']) { return ''; }
        return $url;
    }
}
class GCU_Companion_Adapters {
    public static function destination_probe($key) { return $GLOBALS['gcu_probe']; }
}
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-contracts.php';
function verify($condition,$message) {
    $GLOBALS['gcu_checks']++;
    if (!$condition) { fwrite(STDERR, "FAIL: ".$message."\n"); exit(1); }
}
$c = new GCU_Contracts();
$GLOBALS['gcu_probe'] = array('available'=>false,'url'=>'','reason'=>'owner_runtime_unhealthy','source'=>'owner_native');
$x=$c->destination('doctor_directory');
verify(false===$x['available'],'Explicit native owner failure must disable CTA.');
verify(''===$x['url'],'Failed owner cannot retain a destination URL.');
verify('owner_runtime_unhealthy'===$x['reason'],'Owner failure reason must survive.');
verify(0===$GLOBALS['gcu_option_reads'],'Legacy cached event must not be read to override native owner.');
$GLOBALS['gcu_probe'] = array('available'=>false,'url'=>'','reason'=>'owner_contract_unavailable_or_incompatible');
$x=$c->destination('doctor_directory');
verify(false===$x['available'],'An incompatible provider cannot be replaced by stale event.');
$GLOBALS['gcu_probe'] = null;
$x=$c->destination('doctor_directory');
verify(false===$x['available'],'Unknown owner health is not an availability grant.');
verify(''===$x['url'],'Unknown owner must not receive a URL.');
$GLOBALS['gcu_probe'] = array('available'=>true,'url'=>'https://example.test/doctors/','source'=>'owner_native');
$x=$c->destination('doctor_directory');
verify(true===$x['available'] && 'https://example.test/doctors/'===$x['url'],'Healthy owner canonical destination remains accessible.');
$GLOBALS['gcu_filter']='restrict';
$x=$c->destination('doctor_directory');
verify(false===$x['available'] && ''===$x['url'],'Filter may veto owner availability.');
foreach(array('rewrite','offsite','empty','type') as $mode) {
    $GLOBALS['gcu_filter']=$mode;
    $x=$c->destination('doctor_directory');
    verify(false===$x['available'] && ''===$x['url'],'Unapproved filter URL alteration must fail closed: '.$mode);
}
$GLOBALS['gcu_filter']='none';
$GLOBALS['gcu_probe']=array('available'=>true,'url'=>'https://elsewhere.invalid/doctors/');
$x=$c->destination('doctor_directory');
verify(false===$x['available'] && ''===$x['url'],'Unsafe owner URL must not be usable.');
verify(0===$GLOBALS['gcu_option_reads'],'Cached owner-event readiness must not be used in any destination path.');
echo 'R66 real GCU_Contracts destination probes: '.$GLOBALS['gcu_checks'].' PASS'.PHP_EOL;
