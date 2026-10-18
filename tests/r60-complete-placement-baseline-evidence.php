<?php
/**
 * R60: 40 actual File14 baseline capture/snapshot ready/reconcile source
 * scenarios with malformed incomplete File20-style placement SQL evidence.
 * Includes R56 40 pre-existing native method cases. NO live WordPress.
 */
require __DIR__.'/r56-approved-snapshot-reconciliation.php';
class R60PlacementDB extends R56DB {
    public $writes=array();
    public function insert($table,$data,$format=null){$this->writes[]=$data;return 1;}
}
$errors=array();
for($i=0;$i<40;$i++) {
    $v=intdiv($i,10);$family=$i%10;
    $key='clinic_hero';
    $placement=array(
      'id'=>100+$v,
      'public_id'=>sprintf('%08d-2222-4222-8222-123456789abc',200+$i),
      'placement_key'=>$key, 'block_key'=>'clinic_message',
      'route_key'=>'worldwide-clinic-directory', 'slot_key'=>'hero',
      'audience'=>'patient', 'priority'=>10+$v,
      'status'=>'active', 'starts_at'=>null, 'ends_at'=>null,
      'row_version'=>3+$v
    );
    $variants=json_encode(array(array('placement_key'=>$key,'copy'=>'A'),array('placement_key'=>$key,'copy'=>'B')));
    $exp=array(
      'public_id'=>sprintf('%08d-1111-4111-8111-123456789abc',500+$i),
      'row_version'=>20+$v,'approved_by'=>9+$v,'variants'=>$variants,'status'=>'approved'
    );
    $db=new R60PlacementDB();
    $db->placement=$placement;
    $wpdb=$db;
    $expected=true;
    if($i<20){
      if($family===1)$db->placement['row_version']=(string)$placement['row_version'];
      if($family===2)$db->placement['extra_column']='harmless owner field';
      if($family===3){unset($db->placement['route_key']);$expected=false;}
      if($family===4){unset($db->placement['ends_at']);$expected=false;}
      if($family===5){unset($db->placement['row_version']);$expected=false;}
      if($family===6){$db->placement['row_version']='3junk';$expected=false;}
      if($family===7){$db->placement['public_id']='not-uuid';$expected=false;}
      if($family===8){$db->placement['placement_key']='wrong_key';$expected=false;}
      if($family===9){$db->placement['row_version']=0;$expected=false;}
      $result=GCU_Experiment_Baseline::capture_approved($exp,$exp['approved_by']);
      $pass=$expected
        ? $result===true && count($db->writes)===1
        : $result instanceof WP_Error && $result->code==='gcu_baseline_placement_unverified' && count($db->writes)===0;
      if($pass && $expected){
        $db->snapshot_row=array(
          'payload'=>$db->writes[0]['payload'],
          'payload_hash'=>$db->writes[0]['payload_hash'],
          'created_by'=>$exp['approved_by']
        );
        $exp['row_version']++;
        $pass=GCU_Experiment_Baseline::ready($exp)===true;
      }
    }else{
      $result=GCU_Experiment_Baseline::capture_approved($exp,$exp['approved_by']);
      if($result!==true){$errors[]='R60 setup capture '.$i;continue;}
      $original=json_decode($db->writes[0]['payload'],true);
      if($family===1)unset($original['placements'][$key]['route_key']);
      if($family===2)unset($original['placements'][$key]['starts_at']);
      if($family===3)unset($original['placements'][$key]['row_version']);
      if($family===4)$original['placements'][$key]['row_version']='3junk';
      if($family===5)$original['placements'][$key]['public_id']='bad-uuid';
      if($family===6)$original['placements'][$key]['placement_key']='wrong_key';
      if($family===7)$original['placements'][$key]['unexpected']='unsigned-extra-field';
      if($family===8)$original['placements'][$key]='bad scalar';
      if($family===9)$original['placements'][$key]['row_version']=0;
      $expected=$family===0;
      $json=json_encode($original);
      // Rehash tampered evidence to ensure shape contract, not digest,
      // is what rejects an otherwise hash-consistent malformed record.
      $db->snapshot_row=array('payload'=>$json,'payload_hash'=>hash('sha256',$json),'created_by'=>$exp['approved_by']);
      $exp['row_version']++;
      $ready=GCU_Experiment_Baseline::ready($exp);
      $reconciled=GCU_Experiment_Baseline::reconcile_after_stop($exp);
      $pass=$ready===$expected && is_array($reconciled)
        && $reconciled['restored']===$expected
        && $reconciled['manual_review']===true;
    }
    if(!$pass)$errors[]='R60 placement shape case '.($i+1).' family '.$family;
    printf("R60 immutable owner placement proof %02d/40: %s\n",$i+1,$pass?'PASS':'FAIL');
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R60 placement row and owner baseline snapshot 40/40 real PHP method/mocked SQL tests PASS; actual CAS/live unverified.\n";
