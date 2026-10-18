<?php
/**
 * R59: 40 direct File14 quality_score() SQL identity cases.
 * Includes prior real method test harness, not WordPress live runtime.
 */
require __DIR__ . '/r47-quality-provenance-and-sql-evidence-tests.php';
$errors = array();
for ($i=0;$i<40;$i++) {
  $v=intdiv($i,10);$family=$i%10;
  $wpdb=new R47DB();
  $wpdb->stale='0';$wpdb->complaints='0';
  $wpdb->events=array(array('funnel_stage'=>'cta_selected','total'=>(string)(20+$v)));
  $bad=$family>=2 && $family<=8;
  if($family===1) $wpdb->events[]=array('funnel_stage'=>'unused_stage','total'=>'30');
  if($family===2) $wpdb->events[]=array('funnel_stage'=>'','total'=>'30');
  if($family===3) $wpdb->events[]=array('funnel_stage'=>'   ','total'=>'30');
  if($family===4) $wpdb->events[]=array('funnel_stage'=>"\t",'total'=>'30');
  if($family===5) $wpdb->events[]=array('funnel_stage'=>null,'total'=>'30');
  if($family===6) $wpdb->events[]=array('funnel_stage'=>array('cta_selected'),'total'=>'30');
  if($family===7) $wpdb->events[]=array('funnel_stage'=>101,'total'=>'30');
  if($family===8) $wpdb->events[]=array('funnel_stage'=>'destination_loaded','total'=>'1junk');
  if($family===9) $wpdb->events[]=array('funnel_stage'=>'destination_loaded','total'=>'30');
  GCU_Observability::$errors=array();
  $r=GCU_Future_Intelligence::quality_score();
  $pass=$bad
    ? $r['sample_count']===null && $r['score']===null && $r['provisional']
      && in_array('quality_funnel_read_unverified',$r['missing_evidence'],true)
      && count(array_filter(GCU_Observability::$errors,static function($x){return $x[1]==='future_quality_funnel_read_unverified';}))===1
    : $r['sample_count']===20+$v && $r['score']===null && $r['provisional']
      && !in_array('quality_funnel_read_unverified',$r['missing_evidence'],true);
  printf("R59 native quality stage SQL evidence %02d/40: %s\n",$i+1,$pass?'PASS':'FAIL');
  if(!$pass)$errors[]='R59 quality source identity case '.($i+1).' failed';
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R59 40/40 actual funnel quality SQL identity cases PASS; not deployed/site acceptance.\n";
