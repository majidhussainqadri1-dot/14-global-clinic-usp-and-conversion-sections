<?php
/**
 * R65-D01: forty actual GCU_Repository::validate_inbound_event() and
 * GCU_Contracts::apply_inbound_event() policy-version/changed-keys tests.
 * Mocked WordPress owner inbox only; does NOT authenticate File01 producers.
 */
require __DIR__.'/r63-inbox-owner-version-prevalidation.php';
$preflight=new ReflectionMethod('GCU_Repository','validate_inbound_event');
$preflight->setAccessible(true);
$errors=array();
for($i=0;$i<40;$i++){
    $group=intdiv($i,10);$mode=$i%10;
    $id=sprintf('%08d-1111-4111-8111-123456789abc',6200+$i);
    $policy=array('event_id'=>$id,'contract_version'=>1,
      'policy_version'=>'central-approved-policy-'.($group+1),
      'changed_keys'=>array('zero_commission','donation_optional'));
    $allowed=($mode===0||$mode===1);
    $repoCode='gcu_inbound_policy_version_unverified';
    $consumerCode='gcu_policy_version_unverified';
    if($mode===1)$policy['policy_version']='V'.($group+1);
    if($mode===2)unset($policy['policy_version']);
    if($mode===3)$policy['policy_version']='';
    if($mode===4)$policy['policy_version']='    ';
    if($mode===5)$policy['policy_version']=array('policy'=>'1');
    if($mode===6)$policy['policy_version']=1;
    if($mode===7)$policy['policy_version']=str_repeat('P',65);
    if($mode===8){$policy['changed_keys']='commission';$repoCode='gcu_inbound_policy_keys_unverified';$consumerCode='gcu_policy_keys_unverified';}
    if($mode===9){$policy['changed_keys']=array(array('zero_commission'));$repoCode='gcu_inbound_policy_keys_unverified';$consumerCode='gcu_policy_keys_unverified';}
    $GLOBALS['r61_options']=array();$GLOBALS['r61_writes']=0;$GLOBALS['r63_policy_actions']=array();
    $atRepo=$preflight->invoke($repository,'BusinessPolicyChanged.v1',$policy);
    $atConsumer=$consumer->apply_inbound_event('BusinessPolicyChanged.v1',$policy);
    $state=isset($GLOBALS['r61_options'][GCU_Contracts::REVALIDATION_OPTION])
      ?$GLOBALS['r61_options'][GCU_Contracts::REVALIDATION_OPTION]:null;
    $ok=$allowed
      ? ($atRepo===true && $atConsumer===true && $GLOBALS['r61_writes']===1
          && count($GLOBALS['r63_policy_actions'])===1 && is_array($state)
          && isset($state[$id]) && $state[$id]['policy_version']===$policy['policy_version'])
      : ($atRepo instanceof WP_Error && $atRepo->code===$repoCode
          && $atConsumer instanceof WP_Error && $atConsumer->code===$consumerCode
          && $GLOBALS['r61_writes']===0 && null===$state
          && count($GLOBALS['r63_policy_actions'])===0);
    printf("R65 owner policy source version and keys %02d/40: %s\n",$i+1,$ok?'PASS':'FAIL');
    if(!$ok)$errors[]='R65 required central owner policy truth case '.($i+1)
      .' repo='.($atRepo instanceof WP_Error?$atRepo->code:var_export($atRepo,true))
      .' consumer='.($atConsumer instanceof WP_Error?$atConsumer->code:var_export($atConsumer,true));
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R65 40/40 original owner policy version and key checks at both actual source-method boundaries PASS.\n";
