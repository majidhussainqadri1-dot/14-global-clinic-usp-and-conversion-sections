<?php
/** R62: 40 actual native owner v1 availability consumer contract cases.
 * Simulated File07/08/09 owner envelopes + wp_options, NOT signed producers
 * or deployed WordPress. R61 source-mode event tests also run first.
 */
require __DIR__ . '/r61-inbound-owner-contract-integrity.php';
$errors=array();
$names=array('DoctorDirectoryAvailable.v1','ClinicBookingAvailable.v1','DoctorOnboardingAvailable.v1','DoctorDirectoryAvailable.v1');
$keys=array('doctor_directory','clinic','doctor_onboarding','doctor_directory');
$urls=array('doctors','worldwide-clinic','doctor/apply','doctors');
for($i=0;$i<40;$i++){
  $group=intdiv($i,10);$type=$i%10;
  $GLOBALS['r61_options']=array();$GLOBALS['r61_writes']=0;
  $owner=array('event_id'=>sprintf('%08d-1111-4111-8111-123456789abc',1800+$i),
    'available'=>true,'url'=>'https://sabrihomeopathy.com/'.$urls[$group].'/','contract_version'=>1);
  $approved=$type<=2;
  if($type===1)$owner['contract_version']='1';
  if($type===2)unset($owner['contract_version']);
  if($type===3)$owner['contract_version']=2;
  if($type===4)$owner['contract_version']='2';
  if($type===5)$owner['contract_version']=999;
  if($type===6)$owner['contract_version']='01';
  if($type===7)$owner['contract_version']=true;
  if($type===8)$owner['contract_version']=-1;
  if($type===9)$owner['contract_version']=1.0;
  $r=$consumer->apply_inbound_event($names[$group],$owner);
  $key='gcu_destination_state_'.$keys[$group];
  $state=isset($GLOBALS['r61_options'][$key])?$GLOBALS['r61_options'][$key]:null;
  $pass=$approved
    ? $r===true && $GLOBALS['r61_writes']===1 && is_array($state) &&
      $state['contract_version']===1 && $state['available']===true
    : $r instanceof WP_Error && $r->code==='gcu_owner_event_contract_version_unverified' &&
      $GLOBALS['r61_writes']===0 && $state===null;
  printf("R62 native v1 owner event contract %02d/40: %s\n",$i+1,$pass?'PASS':'FAIL');
  if(!$pass)$errors[]='R62 v1 schema negotiation case '.($i+1).' mismatch';
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R62: 40/40 true File07/08/09 v1 owner event consumer PHP/option mocks PASS; signed registry/staging absent.\n";
