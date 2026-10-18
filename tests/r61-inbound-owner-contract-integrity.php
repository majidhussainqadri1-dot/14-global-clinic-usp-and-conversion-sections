<?php
/**
 * R61: forty REAL GCU_Contracts::apply_inbound_event() source cases.
 * Simulated wp_options, no external File07/08/09, signed registry, staging
 * or production WordPress identity acceptance.
 */
define('ABSPATH',__DIR__.'/');
function __( $s,$domain='' ){return $s;}
function wp_is_uuid($v){return is_string($v) && (bool)preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/Di',$v);}
function sanitize_text_field($v){return trim((string)$v);}
function wp_json_encode($v){return json_encode($v);}
function get_option($k,$default=false){return array_key_exists($k,$GLOBALS['r61_options'])?$GLOBALS['r61_options'][$k]:$default;}
function update_option($k,$value,$autoload=false){$GLOBALS['r61_options'][$k]=$value;$GLOBALS['r61_writes']++;return true;}
class WP_Error {
    public $code;
    public function __construct($code,$message=''){ $this->code=$code; }
    public function get_error_code(){return $this->code;}
}
class GCU_Hardening {
    public static function strict_same_origin_url($url){
        return is_string($url) && 0===strpos($url,'https://sabrihomeopathy.com/') ? $url : '';
    }
}
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-contracts.php';
$errors=array();
$consumer=new GCU_Contracts();
for($i=0;$i<40;$i++){
    $v=intdiv($i,10);$mode=$i%10;
    $GLOBALS['r61_options']=array();$GLOBALS['r61_writes']=0;
    $event=array(
       'event_id'=>sprintf('%08d-1111-4111-8111-123456789abc',900+$i),
       'available'=>true,'url'=>'https://sabrihomeopathy.com/doctors/',
       'contract_version'=>1
    );
    $expected=$mode<=3; $error='gcu_owner_event_contract_version_unverified';
    if($mode===1)$event['contract_version']='1';
    if($mode===2)unset($event['contract_version']);
    if($mode===3){$event['contract_version']='1';$event['available']=false;$event['url']='';}
    if($mode===4)$event['contract_version']=-1;
    if($mode===5)$event['contract_version']='0';
    if($mode===6)$event['contract_version']='01';
    if($mode===7)$event['contract_version']=array('1junk',1.5,true,array(1))[$v];
    if($mode===8){$event['available']='true';$error='gcu_owner_event_envelope_unverified';}
    if($mode===9){$event['event_id']='not-a-uuid';$error='gcu_owner_event_envelope_unverified';}
    $r=$consumer->apply_inbound_event('DoctorDirectoryAvailable.v1',$event);
    $state=isset($GLOBALS['r61_options']['gcu_destination_state_doctor_directory'])
       ?$GLOBALS['r61_options']['gcu_destination_state_doctor_directory']:null;
    $ok=$expected
       ? $r===true && $GLOBALS['r61_writes']===1 && is_array($state)
          && $state['available']===$event['available']
          && $state['contract_version']===(isset($event['contract_version'])?(int)$event['contract_version']:1)
       : $r instanceof WP_Error && $r->code===$error && $GLOBALS['r61_writes']===0 && null===$state;
    if(!$ok)$errors[]='R61 inbound owner version '.($i+1).' unexpected '.(is_object($r)?$r->code:var_export($r,true));
    printf("R61 inbound owner event evidence %02d/40: %s\n",$i+1,$ok?'PASS':'FAIL');
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R61 40/40 source-mode owner File07/08/09 readiness event version + envelope cases PASS. Live unverified.\n";
