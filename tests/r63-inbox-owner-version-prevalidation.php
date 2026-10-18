<?php
/**
 * R63: 40 direct native GCU_Repository::validate_inbound_event() cases.
 * PHP reflection executes exact File14 validation source, without a DB
 * or pretending this establishes producer signatures or deployed parity.
 */
require __DIR__.'/r61-inbound-owner-contract-integrity.php';
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-repository.php';
$repository=new GCU_Repository();
$validator=new ReflectionMethod('GCU_Repository','validate_inbound_event');
$validator->setAccessible(true);
$events=array('DoctorDirectoryAvailable.v1','ClinicBookingAvailable.v1','DoctorOnboardingAvailable.v1','BusinessPolicyChanged.v1');
$errors=array();
for($i=0;$i<40;$i++){
  $group=intdiv($i,10);$type=$i%10;
  $payload=array('event_id'=>sprintf('%08d-1111-4111-8111-123456789abc',3000+$i),'contract_version'=>1);
  if($group<3){$payload['available']=true;$payload['url']='https://sabrihomeopathy.com/doctors/';}
  if($group===3){$payload['policy_version']='approved-policy-1';$payload['changed_keys']=array('zero_commission');}
  $valid=$type<=2;
  if($type===1)$payload['contract_version']='1';
  if($type===2)unset($payload['contract_version']);
  if($type===3)$payload['contract_version']=2;
  if($type===4)$payload['contract_version']='2';
  if($type===5)$payload['contract_version']=999;
  if($type===6)$payload['contract_version']='01';
  if($type===7)$payload['contract_version']=true;
  if($type===8)$payload['contract_version']=1.0;
  if($type===9)$payload['event_id']='unverified-event';
  $result=$validator->invoke($repository,$events[$group],$payload);
  $expectedCode=$type===9?'gcu_inbound_schema_invalid':'gcu_inbound_version_unverified';
  $pass=$valid ? $result===true
     : $result instanceof WP_Error && $result->code===$expectedCode;
  printf("R63 pre-inbox owner .v1 schema %02d/40: %s\n",$i+1,$pass?'PASS':'FAIL');
  if(!$pass)$errors[]='R63 repository first-boundary schema case '.($i+1);
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R63 40/40 direct native repository owner event schema tests PASS; no deployed WordPress/outbox evidence.\n";
