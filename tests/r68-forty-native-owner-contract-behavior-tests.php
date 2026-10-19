<?php
/**
 * R68: 40 DISTINCT native method invocations of the exact File14 adapter.
 * Mocked owner producers represent File07/08/09 contracts; not live WordPress.
 * Each assertion checks result and closed URL, not merely a source string.
 */
error_reporting(E_ALL);
define('ABSPATH',__DIR__);
define('DDD_VERSION','1.2.1');
define('DDD_CONTRACT_VERSION','1.2.1');
define('WCA_VERSION','1.2.16');
define('GDO_VERSION','1.3.0');
$GLOBALS['r68_file07_ready']=true;
$GLOBALS['r68_file08_manifest']=array();
$GLOBALS['r68_file08_health']=array('ok'=>true);
$GLOBALS['r68_file09_destination']=array();
function sanitize_key($s){return strtolower((string)preg_replace('/[^a-z0-9_-]/i','',(string)$s));}
function sanitize_text_field($s){return (string)$s;}
function home_url($path){return 'https://example.test'.$path;}
class GCU_Hardening {
    public static function bounded_text($v,$n){return substr((string)$v,0,(int)$n);}
    public static function strict_same_origin_url($v){
        if(!is_string($v)||''===$v){return '';}
        $p=parse_url($v);
        return is_array($p)&&isset($p['scheme'],$p['host'])
            &&'https'===$p['scheme']&&'example.test'===$p['host']?$v:'';
    }
}
class DDD_Contracts {
    public static function dependency_health(){return array('ready'=>$GLOBALS['r68_file07_ready']);}
}
class DDD_Observability {
    public static function system_check(){return array('overall'=>'pass');}
}
class WCA_Contracts {
    public static function contract_manifest(){return $GLOBALS['r68_file08_manifest'];}
}
class WCA_Observability {
    public static function health(){return $GLOBALS['r68_file08_health'];}
}
function gdo_file14_onboarding_destination(){return $GLOBALS['r68_file09_destination'];}
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-companion-adapters.php';
function r68_clinic_manifest(){
    return array('runtime_version'=>'1.2.16','api_version'=>'1.0.0',
       'routes'=>array('appointments'=>array('pattern'=>'/appointments')),
       'commission_percent'=>0,'donation_visibility_link'=>false);
}
function r68_file09_manifest(){
    return array('contract_version'=>'1.1.0','owner'=>'file09','consumer'=>'file14',
        'available'=>true,'writes_data'=>false,
        'automatic_enrollment'=>false,'automatic_verification'=>false,
        'canonical_url'=>'https://example.test/doctor/apply/');
}
$cases=0;
function r68_assert($destination,$label,$expected){
    global $cases;
    $cases++;
    $result=GCU_Companion_Adapters::destination_probe($destination);
    $valid=is_array($result)&&array_key_exists('available',$result)
        &&$result['available']===$expected;
    $url=isset($result['url'])?$result['url']:'';
    $valid=$valid&&($expected?is_string($url)&&''!==$url:''===$url);
    if(!$valid){fwrite(STDERR,'R68 case '.sprintf('%02d',$cases).
       ' FAIL: '.$label.' result='.var_export($result,true).PHP_EOL);exit(1);}
    echo 'R68 native case '.sprintf('%02d',$cases).'/40 PASS: '.$label.PHP_EOL;
}
function r68_commission($name,$value,$expected){
    $GLOBALS['r68_file08_manifest']=r68_clinic_manifest();
    $GLOBALS['r68_file08_manifest']['commission_percent']=$value;
    $GLOBALS['r68_file08_health']=array('ok'=>true);
    r68_assert('clinic',$name,$expected);
}
r68_commission('canonical integer zero',0,true);
r68_commission('string zero must not prove integer zero','0',false);
r68_commission('float zero is untyped',0.0,false);
r68_commission('decimal string zero','0.0',false);
r68_commission('fractional positive commission',0.5,false);
r68_commission('fractional positive string','0.5',false);
r68_commission('negative fraction',-0.5,false);
r68_commission('malformed text commission','garbage',false);
r68_commission('empty commission string','',false);
r68_commission('boolean false commission',false,false);
r68_commission('boolean true commission',true,false);
r68_commission('null commission',null,false);
r68_commission('array commission',array(),false);
r68_commission('positive integer commission',1,false);
r68_commission('negative integer commission',-1,false);
function r68_donation($name,$value,$expected){
    $GLOBALS['r68_file08_manifest']=r68_clinic_manifest();
    $GLOBALS['r68_file08_manifest']['donation_visibility_link']=$value;
    $GLOBALS['r68_file08_health']=array('ok'=>true);
    r68_assert('clinic',$name,$expected);
}
r68_donation('canonical donation-neutral false',false,true);
r68_donation('zero integer is not boolean false',0,false);
r68_donation('zero string is not boolean false','0',false);
r68_donation('null is not boolean false',null,false);
r68_donation('blank text is not boolean false','',false);
r68_donation('array is not boolean false',array(),false);
r68_donation('donation linked to visibility',true,false);
function r68_health($name,$health,$expected){
    $GLOBALS['r68_file08_manifest']=r68_clinic_manifest();
    $GLOBALS['r68_file08_health']=$health;
    r68_assert('clinic',$name,$expected);
}
r68_health('canonical owner health true',array('ok'=>true),true);
r68_health('native health false',array('ok'=>false),false);
r68_health('string false cannot indicate health',array('ok'=>'false'),false);
r68_health('string true cannot attest health',array('ok'=>'true'),false);
r68_health('numeric one not attested',array('ok'=>1),false);
r68_health('array health flag not attested',array('ok'=>array('ok'=>true)),false);
r68_health('missing health flag',array(),false);
function r68_directory($name,$value,$expected){
    $GLOBALS['r68_file07_ready']=$value;
    r68_assert('doctor_directory',$name,$expected);
}
r68_directory('File07 canonical ready true',true,true);
r68_directory('File07 actual false',false,false);
r68_directory('File07 false text cannot attest','false',false);
r68_directory('File07 numeric one cannot attest',1,false);
r68_directory('File07 array cannot attest',array(),false);
function r68_onboard($name,$k,$value,$expected){
    $GLOBALS['r68_file09_destination']=r68_file09_manifest();
    $GLOBALS['r68_file09_destination'][$k]=$value;
    r68_assert('doctor_onboarding',$name,$expected);
}
r68_onboard('File09 canonical contract', 'available',true,true);
r68_onboard('File09 false text not available','available','false',false);
r68_onboard('File09 numeric one not attested','available',1,false);
r68_onboard('File09 numeric zero is not typed no-write','writes_data',0,false);
r68_onboard('File09 text zero is not typed no-enrol','automatic_enrollment','0',false);
r68_onboard('File09 null is not typed no-verification','automatic_verification',null,false);
if(40!==$cases){fwrite(STDERR,'R68 wrong behavioral case count: '.$cases.PHP_EOL);exit(1);}
echo 'R68 40/40 owner manifest source method behavior checks PASS; installed runtime unverified.'.PHP_EOL;
