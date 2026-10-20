<?php
/**
 * R71: Forty distinct real GCU_Contracts::destination() method calls with
 * typed and malformed owner probes / external veto-filter responses.
 * Runs on mocked WordPress input, NOT an installed WordPress or owner plugin.
 */
error_reporting(E_ALL);
define('ABSPATH', __DIR__);
$GLOBALS['r71_probe'] = array();
$GLOBALS['r71_filter_override'] = false;
$GLOBALS['r71_filter'] = null;
$GLOBALS['r71_option_reads'] = 0;
function sanitize_key($v){return strtolower(preg_replace('/[^a-z0-9_-]/i','',(string)$v));}
function home_url($v='/'){return 'https://example.test'.$v;}
function get_option($v,$default=false){$GLOBALS['r71_option_reads']++;return $default;}
function apply_filters($name,$value){
    return 'gcu_destination_contract_v1'===$name && $GLOBALS['r71_filter_override']
        ? $GLOBALS['r71_filter'] : $value;
}
class GCU_Companion_Adapters {
 public static function destination_probe($key){return $GLOBALS['r71_probe'];}
}
class GCU_Hardening {
 public static function strict_same_origin_url($url){
  if(!is_string($url)||''===$url)return '';
  $p=parse_url($url);
  return is_array($p)&&isset($p['scheme'],$p['host'])
   &&'https'===$p['scheme']&&'example.test'===$p['host']?$url:'';
 }
}
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-contracts.php';
$healthy=array('available'=>true,'url'=>'https://example.test/doctors/','reason'=>'owner_runtime_ready','contract'=>'v1','source'=>'owner_runtime_probe');
$cases=array();
function r71_case($label,$base,$filtered,$override,$expected){
 global $cases;$cases[]=array($label,$base,$filtered,$override,$expected);
}
r71_case('canonical owner and untouched filter',$healthy,null,false,true);
r71_case('native owner explicitly false',array_merge($healthy,array('available'=>false)),null,false,false);
r71_case('owner ready string true',array_merge($healthy,array('available'=>'true')),null,false,false);
r71_case('owner ready string false',array_merge($healthy,array('available'=>'false')),null,false,false);
r71_case('owner ready integer one',array_merge($healthy,array('available'=>1)),null,false,false);
r71_case('owner ready integer zero',array_merge($healthy,array('available'=>0)),null,false,false);
r71_case('owner ready null',array_merge($healthy,array('available'=>null)),null,false,false);
r71_case('owner ready array',array_merge($healthy,array('available'=>array(true))),null,false,false);
$no_available=$healthy;unset($no_available['available']);
r71_case('owner ready missing',$no_available,null,false,false);
$no_url=$healthy;unset($no_url['url']);
r71_case('owner URL missing',$no_url,null,false,false);
r71_case('owner URL external',array_merge($healthy,array('url'=>'https://other.test/doctors/')),null,false,false);
r71_case('native false with URL',array_merge($healthy,array('available'=>false)),null,false,false);
r71_case('owner URL number',array_merge($healthy,array('url'=>1)),null,false,false);
r71_case('owner URL array',array_merge($healthy,array('url'=>array('x'))),null,false,false);
r71_case('owner URL null',array_merge($healthy,array('url'=>null)),null,false,false);
r71_case('owner URL empty',array_merge($healthy,array('url'=>'')),null,false,false);
$canonical=array('available'=>true,'url'=>'https://example.test/doctors/','reason'=>'owner_confirmed');
r71_case('filter veto boolean false',$healthy,array_merge($canonical,array('available'=>false)),true,false);
r71_case('filter veto integer zero',$healthy,array_merge($canonical,array('available'=>0)),true,false);
r71_case('filter veto string false',$healthy,array_merge($canonical,array('available'=>'false')),true,false);
r71_case('filter veto string zero',$healthy,array_merge($canonical,array('available'=>'0')),true,false);
r71_case('filter available string true',$healthy,array_merge($canonical,array('available'=>'true')),true,false);
r71_case('filter available integer one',$healthy,array_merge($canonical,array('available'=>1)),true,false);
r71_case('filter available null',$healthy,array_merge($canonical,array('available'=>null)),true,false);
r71_case('filter available empty',$healthy,array_merge($canonical,array('available'=>'')),true,false);
r71_case('filter returns empty array',$healthy,array(),true,false);
r71_case('filter returns scalar false',$healthy,false,true,false);
r71_case('filter returns scalar true',$healthy,true,true,false);
r71_case('filter returns numeric zero',$healthy,0,true,false);
r71_case('filter returns null',$healthy,null,true,false);
r71_case('filter returns arbitrary text',$healthy,'ready',true,false);
$f=$canonical;unset($f['available']);
r71_case('filter missing available',$healthy,$f,true,false);
$f=$canonical;unset($f['url']);
r71_case('filter missing URL',$healthy,$f,true,false);
r71_case('filter URL null',$healthy,array_merge($canonical,array('url'=>null)),true,false);
r71_case('filter URL false',$healthy,array_merge($canonical,array('url'=>false)),true,false);
r71_case('filter URL integer',$healthy,array_merge($canonical,array('url'=>1)),true,false);
r71_case('filter URL array',$healthy,array_merge($canonical,array('url'=>array('bad'))),true,false);
r71_case('filter URL external',$healthy,array_merge($canonical,array('url'=>'https://other.test/doctors/')),true,false);
r71_case('filter URL same-origin swap',$healthy,array_merge($canonical,array('url'=>'https://example.test/account/')),true,false);
r71_case('filter URL empty',$healthy,array_merge($canonical,array('url'=>'')),true,false);
r71_case('filter harmless reason edit',$healthy,array_merge($canonical,array('reason'=>'changed')),true,true);
if(count($cases)!==40){fwrite(STDERR,'R71 cases count '.count($cases).' != 40'.PHP_EOL);exit(2);}
$contracts=new GCU_Contracts();
foreach($cases as $n=>$v){
 list($label,$base,$filtered,$override,$expected)=$v;
 $GLOBALS['r71_probe']=$base;
 $GLOBALS['r71_filter_override']=$override;
 $GLOBALS['r71_filter']=$filtered;
 $result=$contracts->destination('doctor_directory');
 $good=is_array($result)&&isset($result['available'])&&$result['available']===$expected
    &&($expected ? $result['url']==='https://example.test/doctors/' : $result['url']==='');
 if(!$good){fwrite(STDERR,'R71 case '.($n+1).' failed '.$label.' '.var_export($result,true).PHP_EOL);exit(1);}
 echo 'R71 filter behavior case '.sprintf('%02d',$n+1).'/40 PASS: '.$label.PHP_EOL;
}
if($GLOBALS['r71_option_reads']!==0){fwrite(STDERR,'R71 leaked stale owner option'.PHP_EOL);exit(1);}
echo 'R71 40/40 exact File14 owner destination/filter behavior tests PASS; staging/live unverified.'.PHP_EOL;
