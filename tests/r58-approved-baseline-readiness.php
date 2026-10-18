<?php
/**
 * R58 40 direct approved baseline::ready() + snapshot proof tests.
 * Uses R56 native-method WP/SQL test harness, never deployed WordPress.
 */
require __DIR__ . '/r56-approved-snapshot-reconciliation.php';
$failures=array();
for ($i=0;$i<40;$i++) {
  $family=$i%10;$variation=intdiv($i,10);
  $id=sprintf('%08d-1111-4111-8111-123456789abc',$i+200);
  $v=22+$variation;$by=5+$variation;
  $experiment=array(
    'public_id'=>$id,'status'=>'approved','row_version'=>$v,
    'approved_by'=>$by,'variants'=>json_encode(array('copy A','copy B'))
  );
  $snap=array(
    'contract'=>GCU_Experiment_Baseline::CONTRACT,
    'experiment'=>$id,'approval_version'=>$v,'approved_by'=>$by,
    'approved_at'=>'2026-10-18 12:00:00','placements'=>array(),
    'requires_manual_review_after_stop'=>true
  );
  $created_by=$by;
  $allow=$family===0||$family===1;
  if($family===1) { $experiment['row_version']=(string)$v; $experiment['approved_by']=(string)$by; $snap['approval_version']=(string)$v; $snap['approved_by']=(string)$by; $created_by=(string)$by; }
  if($family===2) $experiment['row_version']=$v.'junk';
  if($family===3) $experiment['row_version']=$v+0.5;
  if($family===4) $experiment['approved_by']=$by.'junk';
  if($family===5) $experiment['approved_by']=true;
  if($family===6) $snap['approved_by']=$by.'junk';
  if($family===7) $snap['approval_version']=$v.'junk';
  if($family===8) $created_by=$by.'junk';
  if($family===9) $experiment['variants']=array('copy A','copy B');
  $json=json_encode($snap);
  $wpdb=new R56DB();
  $wpdb->snapshot_row=array('payload'=>$json,'payload_hash'=>hash('sha256',$json),'created_by'=>$created_by);
  $result=GCU_Experiment_Baseline::ready($experiment);
  $ok=$result===$allow;
  if(!$ok) $failures[]='R58 ready scenario '.($i+1).' unexpected '.var_export($result,true);
  printf("R58 approved baseline readiness %02d/40: %s\n",$i+1,$ok?'PASS':'FAIL');
}
if($failures){fwrite(STDERR,implode("\n",$failures)."\n");exit(1);}
echo "R58 40/40 native approved baseline original-value/actor/version checks PASS; mocks only.\n";
