<?php
/**
 * R64: forty native GCU_Repository::verified_inbox_payload() source tests.
 * Tests recorded digest, row/JSON event identity, owner .v1 schema and one
 * actual process_inbox() corrupt-message side-effect guard. SQL mocks, not
 * owner signatures or deployed WordPress.
 */
require __DIR__.'/r61-inbound-owner-contract-integrity.php';
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-repository.php';
if(!defined('ARRAY_A'))define('ARRAY_A','ARRAY_A');
function is_wp_error($v){return $v instanceof WP_Error;}
function current_time($what='mysql',$utc=false){return '2026-10-19 04:00:00';}
class GCU_Install {public static function tables(){return array('inbox'=>'wp_gcu_inbox');}}
class GCU_Plugin {
    private static $self;
    public static function instance(){if(!self::$self)self::$self=new self();return self::$self;}
    public function contracts(){return $GLOBALS['r64_consumer'];}
}
class R64FakeInboxDB {
    public $rows=array(),$updates=array();
    public function get_results($sql,$mode=null){return $this->rows;}
    public function prepare($sql){return $sql;}
    public function query($sql){
      $this->updates[]=$sql;
      return 1;
    }
}
$repository=new GCU_Repository();
$method=new ReflectionMethod('GCU_Repository','verified_inbox_payload');
$method->setAccessible(true);
$errors=array();
$names=array('DoctorDirectoryAvailable.v1','ClinicBookingAvailable.v1',
             'DoctorOnboardingAvailable.v1','BusinessPolicyChanged.v1');
for($i=0;$i<40;$i++){
  $group=intdiv($i,10);$mode=$i%10;
  $uuid=sprintf('%08d-1111-4111-8111-123456789abc',5000+$i);
  $payload=array('event_id'=>$uuid,'contract_version'=>1);
  if($group<3){$payload['available']=true;$payload['url']='https://sabrihomeopathy.com/doctors/';}
  else{$payload['policy_version']='approved-r64';$payload['changed_keys']=array('zero_commission');}
  if($mode===1)$payload['contract_version']='1';
  if($mode===2)unset($payload['contract_version']);
  $raw=json_encode($payload);
  $row=array('id'=>100+$i,'attempts'=>0,'event_id'=>$uuid,'event_name'=>$names[$group],
    'payload_hash'=>hash('sha256',$raw),'payload'=>$raw);
  $valid=$mode<=2;
  if($mode===3)$row['payload_hash']=str_repeat('0',64);
  if($mode===4)$row['payload']=substr($raw,0,-1).' ';
  if($mode===5)$row['event_id']=sprintf('%08d-1111-4111-8111-123456789abc',7000+$i);
  if($mode===6){
    $altered=$payload;$altered['event_id']=sprintf('%08d-1111-4111-8111-123456789abc',8000+$i);
    $row['payload']=json_encode($altered);$row['payload_hash']=hash('sha256',$row['payload']);
  }
  if($mode===7){$row['payload']='null';$row['payload_hash']=hash('sha256',$row['payload']);}
  if($mode===8)$row['event_name']='UnsupportedOwnerEvent.v1';
  if($mode===9){
    $payload['contract_version']=2;$row['payload']=json_encode($payload);
    $row['payload_hash']=hash('sha256',$row['payload']);
  }
  $result=$method->invoke($repository,$row);
  $pass=$valid ? is_array($result)&&$result===$payload
    : $result instanceof WP_Error && $result->code==='gcu_inbox_payload_unverified';
  printf("R64 native inbox hash and owner identity %02d/40: %s\n",$i+1,$pass?'PASS':'FAIL');
  if(!$pass)$errors[]='R64 inbox integrity method case '.($i+1);
}
// Real process_inbox side-effect containment: a claimed corrupt stored
// inbox row must be retried/dead-lettered, never write owner readiness option.
$GLOBALS['r64_consumer']=$consumer;
$GLOBALS['r61_options']=array();$GLOBALS['r61_writes']=0;
$uuid='00003000-1111-4111-8111-123456789abc';
$event=array('event_id'=>$uuid,'available'=>true,'contract_version'=>1,
 'url'=>'https://sabrihomeopathy.com/doctors/');
$encoded=json_encode($event);
$wpdb=new R64FakeInboxDB();
$wpdb->rows=array(array('id'=>100,'attempts'=>0,'event_id'=>$uuid,
  'event_name'=>'DoctorDirectoryAvailable.v1','payload'=>$encoded,
  'payload_hash'=>str_repeat('a',64),'status'=>'pending'));
$count=$repository->process_inbox('',1);
$contained=$count===1 && $GLOBALS['r61_writes']===0
 && count($wpdb->updates)===2
 && strpos($wpdb->updates[1],"last_error_code='process_exception'")!==false;
if(!$contained)$errors[]='R64 actual process_inbox failed to contain corrupt owner event';
echo 'R64 direct process_inbox corrupt hash side-effect guard: '.($contained?'PASS':'FAIL')."\n";
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R64 40/40 native inbox evidence vectors + 1 process containment PASS; no live source/deployed signature evidence.\n";
