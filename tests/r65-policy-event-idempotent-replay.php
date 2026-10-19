<?php
/**
 * R65-D02: forty actual File14 BusinessPolicyChanged.v1 at-least-once
 * replay/idempotence/concurrent owner-identity conflict SOURCE method tests.
 * No signed File01 events, genuine WordPress DB or live staging.
 */
require __DIR__.'/r63-business-policy-event-replay-integrity.php';
$errors=array();
for($i=0;$i<40;$i++){
    $group=intdiv($i,10);$mode=$i%10;
    $id=sprintf('%08d-1111-4111-8111-123456789abc',7200+$i);
    $first=array('event_id'=>$id,'contract_version'=>1,
      'policy_version'=>'approved-policy-'.$group,
      'changed_keys'=>array('zero_commission','donation_optional'));
    $GLOBALS['r61_options']=array();$GLOBALS['r61_writes']=0;
    $GLOBALS['r63_policy_actions']=array();
    $result=$consumer->apply_inbound_event('BusinessPolicyChanged.v1',$first);
    $stored=isset($GLOBALS['r61_options'][GCU_Contracts::REVALIDATION_OPTION])
      ?$GLOBALS['r61_options'][GCU_Contracts::REVALIDATION_OPTION]:null;
    if($result!==true||$GLOBALS['r61_writes']!==1
       ||count($GLOBALS['r63_policy_actions'])!==1||!is_array($stored)
       ||!isset($stored[$id])){
        $errors[]='R65 first-delivery setup '.($i+1);
        continue;
    }
    $originalEncoded=json_encode($stored);$originalTime=$stored[$id]['received_at'];
    $replay=$first;
    $allowed=$mode<=2||$mode===9;
    $code='';
    if($mode===1)$replay['changed_keys']=array('donation_optional','zero_commission');
    if($mode===2)unset($replay['contract_version']);
    if($mode===3){$replay['policy_version']='approved-policy-CONFLICT';$code='gcu_policy_event_identity_conflict';}
    if($mode===4){$replay['changed_keys']=array('zero_commission');$code='gcu_policy_event_identity_conflict';}
    if($mode===5){$replay['changed_keys']=array(array('unexpected'));$code='gcu_policy_keys_unverified';}
    if($mode===6){unset($replay['policy_version']);$code='gcu_policy_version_unverified';}
    if($mode===7){$replay['policy_version']=true;$code='gcu_policy_version_unverified';}
    if($mode===8){$replay['contract_version']=2;$code='gcu_owner_event_contract_version_unverified';}
    if($mode===9){
        // A different event ID is a new fact: it must queue exactly once.
        $replay['event_id']=sprintf('%08d-1111-4111-8111-123456789abc',8200+$i);
    }
    $result2=$consumer->apply_inbound_event('BusinessPolicyChanged.v1',$replay);
    $current=$GLOBALS['r61_options'][GCU_Contracts::REVALIDATION_OPTION];
    $ok=$allowed
      ? ($result2===true && $GLOBALS['r61_writes']===($mode===9?2:1)
          && count($GLOBALS['r63_policy_actions'])===($mode===9?2:1)
          && $current[$id]['received_at']===$originalTime
          && ($mode===9
              ? count($current)===2 && isset($current[$replay['event_id']])
              : json_encode($current)===$originalEncoded))
      : ($result2 instanceof WP_Error && $result2->code===$code
          && $GLOBALS['r61_writes']===1 && count($GLOBALS['r63_policy_actions'])===1
          && json_encode($current)===$originalEncoded);
    printf("R65 policy event exact replay and conflict %02d/40: %s\n",$i+1,$ok?'PASS':'FAIL');
    if(!$ok)$errors[]='R65 original event ID idempotence test '.($i+1)
        .' result='.($result2 instanceof WP_Error?$result2->code:var_export($result2,true));
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R65 40/40 actual policy replay and conflict source method tests PASS; real signed producer/staging unverified.\n";
