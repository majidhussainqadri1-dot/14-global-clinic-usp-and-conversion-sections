<?php
/**
 * R62: 40 direct GCU_Experiment_Baseline capture / snapshot ready / stop
 * scenarios: valid SQL UTC DATETIME, null, corrupt and inverted placement
 * windows. SQL mocks only; no File20 CAS/production WordPress claims.
 */
require __DIR__ . '/r61-typed-owner-placement-evidence.php';
$errors=array();
for($i=0;$i<40;$i++){
  $group=intdiv($i,10);$type=$i%10;
  $key='clinic_hero';
  $before=array(
    'id'=>1000+$i,'public_id'=>sprintf('%08d-2222-4222-8222-123456789abc',1000+$i),
    'placement_key'=>$key,'block_key'=>'approved_clinic_copy','route_key'=>'global_clinic',
    'slot_key'=>'hero','audience'=>'all','priority'=>100,'status'=>'active',
    'starts_at'=>null,'ends_at'=>null,'row_version'=>4+$group
  );
  $subject=$before;
  $valid=$type<=3;
  if($type===1){$subject['starts_at']='2026-10-18 10:00:00';$subject['ends_at']='2026-10-20 10:00:00';}
  if($type===2){$subject['starts_at']='2026-10-18 10:00:00';}
  if($type===3){$subject['ends_at']='2026-10-20 10:00:00';}
  if($type===4){$subject['starts_at']='tomorrow';}
  if($type===5){$subject['ends_at']='2026-02-31 10:00:00';}
  if($type===6){$subject['starts_at']='2026-10-18 25:61:00';}
  if($type===7){$subject['starts_at']='2026-10-20 10:00:00';$subject['ends_at']='2026-10-18 10:00:00';}
  if($type===8){$subject['ends_at']='    ';}
  if($type===9){$subject['starts_at']=array('2026-10-18 10:00:00');}
  $db=new R60PlacementDB();
  $db->placement=$group<2?$subject:$before;
  $wpdb=$db;
  $experiment=array(
    'public_id'=>sprintf('%08d-1111-4111-8111-123456789abc',1100+$i),
    'row_version'=>21+$group,'approved_by'=>9+$group,'status'=>'approved',
    'variants'=>json_encode(array(array('placement_key'=>$key,'copy'=>'A'),array('placement_key'=>$key,'copy'=>'B')))
  );
  $capture=GCU_Experiment_Baseline::capture_approved($experiment,$experiment['approved_by']);
  if($group<2){
    $ok=$valid ? $capture===true && count($db->writes)===1
      : $capture instanceof WP_Error && $capture->code==='gcu_baseline_placement_unverified' && count($db->writes)===0;
    if($valid && $ok){
      $db->snapshot_row=array('payload'=>$db->writes[0]['payload'],
        'payload_hash'=>$db->writes[0]['payload_hash'],'created_by'=>$experiment['approved_by']);
      $experiment['row_version']++;
      $ok=GCU_Experiment_Baseline::ready($experiment)===true;
    }
  }else{
    if($capture!==true){$errors[]='R62 setup approved owner baseline '.($i+1);continue;}
    $snapshot=json_decode($db->writes[0]['payload'],true);
    $snapshot['placements'][$key]=array_intersect_key($subject,array_fill_keys(array(
      'public_id','placement_key','block_key','route_key','slot_key','audience','priority','status','starts_at','ends_at','row_version'
    ),true));
    // The checksum remains valid after simulated corrupted row mutation:
    // cryptographic consistency alone cannot certify actual SQL datetimes.
    $json=json_encode($snapshot);
    $db->snapshot_row=array('payload'=>$json,'payload_hash'=>hash('sha256',$json),'created_by'=>$experiment['approved_by']);
    if($valid){$db->placement=$subject;}
    $experiment['row_version']++;
    $ready=GCU_Experiment_Baseline::ready($experiment);
    $result=GCU_Experiment_Baseline::reconcile_after_stop($experiment);
    $ok=($ready===$valid) && is_array($result) &&
      $result['restored']===$valid && $result['manual_review']===true;
  }
  printf("R62 placement datetime proof %02d/40: %s\n",$i+1,$ok?'PASS':'FAIL');
  if(!$ok)$errors[]='R62 native owner time-window validation '.$i;
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R62: 40/40 real baseline native-method schedule and rehashed-snapshot SQL mocks PASS, live unknown.\n";
