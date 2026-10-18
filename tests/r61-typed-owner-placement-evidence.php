<?php
/**
 * R61: 40 direct File14 placement capture/ready/stop source-method tests.
 * Underlying R60 mock suite also runs. No deployed WP, real File20 CAS,
 * or backend database claims.
 */
require __DIR__ . '/r60-complete-placement-baseline-evidence.php';
$errors=array();
for($i=0;$i<40;$i++){
    $v=intdiv($i,10);$family=$i%10;
    $key='clinic_hero';
    $owner=array(
        'id'=>100+$i,
        'public_id'=>sprintf('%08d-2222-4222-8222-123456789abc',600+$i),
        'placement_key'=>$key,'block_key'=>'approved_clinic_copy',
        'route_key'=>'worldwide-clinic-directory','slot_key'=>'hero',
        'audience'=>'patient','priority'=>10+$v,'status'=>'active',
        'starts_at'=>null,'ends_at'=>null,'row_version'=>3+$v
    );
    $valid=$family<4;
    if($family===1)$owner['priority']=-3-$v;
    if($family===2)$owner['priority']=(string)(-3-$v);
    if($family===3){$owner['priority']='0';$owner['starts_at']='2026-10-18 10:00:00';}
    if($family===4)$owner['priority']='3junk';
    if($family===5)$owner['priority']=true;
    if($family===6)$owner['priority']='2147483648';
    if($family===7)$owner['route_key']='  ';
    if($family===8)$owner['audience']=array('patient');
    if($family===9)$owner['starts_at']=array('2026-10-18 10:00:00');
    $db=new R60PlacementDB();
    // Half the vectors exercise live SELECT/capture. The other half use
    // an independently rehashed corrupt snapshot to test read/reconcile.
    $db->placement=$i<20?$owner:array(
        'id'=>100+$i,
        'public_id'=>sprintf('%08d-2222-4222-8222-123456789abc',600+$i),
        'placement_key'=>$key,'block_key'=>'approved_clinic_copy',
        'route_key'=>'worldwide-clinic-directory','slot_key'=>'hero',
        'audience'=>'patient','priority'=>10+$v,'status'=>'active',
        'starts_at'=>null,'ends_at'=>null,'row_version'=>3+$v
    );
    $wpdb=$db;
    $experiment=array(
        'public_id'=>sprintf('%08d-1111-4111-8111-123456789abc',700+$i),
        'row_version'=>21+$v,'approved_by'=>9+$v,'status'=>'approved',
        'variants'=>json_encode(array(array('placement_key'=>$key,'copy'=>'A'),array('placement_key'=>$key,'copy'=>'B')))
    );
    $capture=GCU_Experiment_Baseline::capture_approved($experiment,$experiment['approved_by']);
    $pass=false;
    if($i<20){
       $pass=$valid ? $capture===true && count($db->writes)===1 :
          $capture instanceof WP_Error && $capture->code==='gcu_baseline_placement_unverified' && count($db->writes)===0;
    }else{
       if($capture!==true){$errors[]='R61 test setup capture case '.($i+1);continue;}
       $snapshot=json_decode($db->writes[0]['payload'],true);
       $snapshot['placements'][$key]=$owner;
       unset($snapshot['placements'][$key]['id']);
       // Valid SQL case can retain its canonical keys and fields.
       $encoded=json_encode($snapshot);
       $db->snapshot_row=array('payload'=>$encoded,'payload_hash'=>hash('sha256',$encoded),'created_by'=>$experiment['approved_by']);
       $experiment['row_version']++;
       $ready=GCU_Experiment_Baseline::ready($experiment);
       $stop=GCU_Experiment_Baseline::reconcile_after_stop($experiment);
       $pass=($ready===$valid) && is_array($stop)
          && $stop['restored']===$valid && $stop['manual_review']===true;
    }
    if(!$pass)$errors[]='R61 typed File20 owner evidence vector '.($i+1);
    printf("R61 typed owner placement evidence %02d/40: %s\n",$i+1,$pass?'PASS':'FAIL');
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R61 40/40 signed priority, typed File20 fields, complete snapshot SOURCE-PHP SQL mock regressions PASS.\n";
