<?php
/**
 * R74: 40 direct calls to File14's real GCU_Companion_Adapters::authorize().
 * SMC_Contracts and WordPress filter infrastructure are test doubles.
 * No WordPress staging, external site or deployed plugin is implied.
 */
define( 'ABSPATH', __DIR__ );
define( 'SMC_VERSION', '1.2.44' );
define( 'SMC_CONTRACT_VERSION', '1.2.3' );
class WP_Error {
    public function __construct( $code, $message = '' ) {}
}
function __( $message, $domain = '' ) { return $message; }
function absint( $n ) { return abs( (int) $n ); }
function is_wp_error( $v ) { return $v instanceof WP_Error; }
function get_current_user_id() { return 42; }
function sanitize_key( $v ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $v ) ); }
function has_filter( $key ) { return false; }
$GLOBALS['r74_owner_filter'] = null;
function apply_filters( $name, $claim, $user_id = null ) {
    if ( 'smc_assertions_v1' !== $name ) { return $claim; }
    $callback = $GLOBALS['r74_owner_filter'];
    return $callback ? $callback( $claim ) : $claim;
}
class SMC_Contracts {
    public static $original;
    public static function assertions( $uid ) { return self::$original; }
}
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-companion-adapters.php';

$errors = array();
for ( $n = 1; $n <= 40; $n++ ) {
    $family = ( $n - 1 ) % 10;
    $v = intdiv( $n - 1, 10 );
    $native = array(
        'contract_version'=>'1.2.3', 'user_id'=>42, 'status'=>'approved',
        'eligible'=>true, 'suspended'=>false,
    );
    $filter = static function( $claim ) { return $claim; };
    $expected = false;
    switch ( $family ) {
        case 0: // Native deny must NOT be elevated by compatibility filter.
            $native['eligible'] = false;
            $filter = static function( $c ) { $c['eligible'] = true; return $c; };
            break;
        case 1: // Native suspension is not cancellable by filtered claim.
            $native['suspended'] = true;
            $filter = static function( $c ) { $c['suspended'] = false; return $c; };
            break;
        case 2: // Canonical blocked statuses cannot be fabricated away.
            $native['status'] = array('suspended','rejected','expired','appeal_review')[$v];
            $filter = static function( $c ) { $c['status']='approved'; return $c; };
            break;
        case 3: // Native subject must be canonical, even if filter repairs it.
            $native['user_id'] = array('42',42.0,43,-42)[$v];
            $filter = static function( $c ) { $c['user_id']=42; return $c; };
            break;
        case 4: // Invalid original contract must not be upgraded by filter.
            $native['contract_version'] = array('0.9.0','2.0.0','',42)[$v];
            $filter = static function( $c ) { $c['contract_version']='1.2.3'; return $c; };
            break;
        case 5: // Filter cannot swap native claimant.
            $values = array(43,'42',42.0,null);
            $filter = static function( $c ) use ( $values,$v ) { $c['user_id']=$values[$v]; return $c; };
            break;
        case 6: // Filter cannot rewrite native contract version.
            $values = array('2.0.0','1.2.4',false,null);
            $filter = static function( $c ) use ( $values,$v ) { $c['contract_version']=$values[$v]; return $c; };
            break;
        case 7: // Filter must preserve strict boolean eligibility.
            $values = array('true',1,0,null);
            $filter = static function( $c ) use ( $values,$v ) { $c['eligible']=$values[$v]; return $c; };
            break;
        case 8: // Valid vetoes and invalid status rewriting must deny.
            if (0===$v) { $filter=static function($c){ $c['eligible']=false;return $c; }; }
            if (1===$v) { $filter=static function($c){ $c['suspended']=true;return $c; }; }
            if (2===$v) { $filter=static function($c){ $c['status']='suspended';return $c; }; }
            if (3===$v) { $filter=static function($c){ return null; }; }
            break;
        case 9: // Valid owner approvals may survive only restriction-only filters.
            $expected = true;
            if (0===$v) { $filter=static function($c){return $c;}; }
            if (1===$v) { $filter=static function($c){$c['note']='a bounded hint';return $c;}; }
            if (2===$v) { $filter=static function($c){$c['mfa_required']=true;return $c;}; }
            if (3===$v) { $filter=static function($c){$c['guard']='strict';return $c;}; }
            break;
    }
    SMC_Contracts::$original = $native;
    $GLOBALS['r74_owner_filter'] = $filter;
    $actual = GCU_Companion_Adapters::authorize('gcu_manage',null,'test_'.$n);
    $pass = $actual === $expected;
    printf("R74 native assertion case %02d/40: %s (family %d)\n",$n,$pass?'PASS':'FAIL',$family);
    if (!$pass) { $errors[] = sprintf('R74 %02d unexpected %s',$n,var_export($actual,true)); }
}
if ($errors) { fwrite(STDERR,implode("\n",$errors)."\n"); exit(1); }
echo "R74 40/40 actual File14 native-assertion authorization PHP mock cases PASS; staging and production unverified.\n";
