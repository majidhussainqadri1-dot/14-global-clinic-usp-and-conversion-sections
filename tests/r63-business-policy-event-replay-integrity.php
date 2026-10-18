<?php
/**
 * R63: 40 actual GCU_Contracts BusinessPolicyChanged.v1 replay scenarios.
 * Reuses WordPress wp_options mocks; no signed File01 registry or Live proof.
 */
require __DIR__.'/r61-inbound-owner-contract-integrity.php';
$errors=array();
for($i=0;$i<40;$i++){
  $v=intdiv($i,10);$mode=$i%10;
  $GLOBALS['r61_options']=array();$GLOBALS['r61_writes']=0;
  $GLOBALS['r63_policy_actions']=array();
  $id=sprintf('%08d-1111-4111-8111-123456789abc',2200+$i);
  $event=array('event_id'=>$id,'contract_version'=>1,
    'policy_version'=>'approved-policy-'.($v+1),
    'changed_keys'=>array('zero_commission','emergency_service'));
  $allowed=$mode<=2;
  if($mode===1)$event['contract_version']='1';
  if($mode===2)unset($event['contract_version']);
  if($mode===3)$event['event_id']='not-a-uuid';
  if($mode===4)$event['event_id']='';
  if($mode===5)$event['event_id']=array('uuid'=>$id);
  if($mode===6)$event['event_id']=" ".$id;
  if($mode===7)$event['contract_version']=2;
  if($mode===8)$event['contract_version']=true;
  if($mode===9)$event['contract_version']='01';
  $r=$consumer->apply_inbound_event('BusinessPolicyChanged.v1',$event);
  $saved=isset($GLOBALS['r61_options'][GCU_Contracts::REVALIDATION_OPTION])
    ?$GLOBALS['r61_options'][GCU_Contracts::REVALIDATION_OPTION]:null;
  $expectedCode=$mode>=7?'gcu_owner_event_contract_version_unverified':'gcu_policy_event_invalid';
  $pass=$allowed
    ? $r===true && $GLOBALS['r61_writes']===1 && is_array($saved)
      && count($saved)===1 && isset($saved[$id])
      && $saved[$id]['event_id']===$id && $saved[$id]['policy_version']===$event['policy_version']
      && count($GLOBALS['r63_policy_actions'])===1
    : $r instanceof WP_Error && $r->code===$expectedCode && $GLOBALS['r61_writes']===0
      && null===$saved && count($GLOBALS['r63_policy_actions'])===0;
  printf("R63 policy replay original UUID %02d/40: %s\n",$i+1,$pass?'PASS':'FAIL');
  if(!$pass)$errors[]='R63 BusinessPolicyChanged.v1 policy inbox case '.($i+1);
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R63 40/40 direct BusinessPolicyChanged.v1 policy option/UUID source tests PASS; actual signed owner event unverified.\n";
