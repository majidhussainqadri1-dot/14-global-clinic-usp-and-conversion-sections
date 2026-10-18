<?php
/** R58: 40 new direct File14 observed_anomaly() SQL/WordPress mock cases. */
require __DIR__ . '/r42-analytics-read-integrity-tests.php';
$errors = array();
for ($i=0;$i<40;$i++) {
  $family=$i%10; $v=intdiv($i,10);
  $wpdb->anomaly = array(array(
    'event_type'=>'File08.BookingStarted.v1','funnel_stage'=>'booking_started',
    'current_count'=>(string)(11+$v),'previous_seven_days'=>'84'
  ));
  $valid=($family===0 || $family===7);
  $unregistered=($family===6 || $family===8);
  if ($family===1) $wpdb->anomaly[0]['event_type']='';
  if ($family===2) $wpdb->anomaly[0]['event_type']='  ';
  if ($family===3) $wpdb->anomaly[0]['funnel_stage']='';
  if ($family===4) $wpdb->anomaly[0]['funnel_stage']=" \t";
  if ($family===5) $wpdb->anomaly[0]['current_count']='11.5';
  if ($family===6) $wpdb->anomaly[0]['event_type']='File08.Unknown.v1';
  if ($family===7) $wpdb->anomaly[0]['previous_seven_days']='91';
  if ($family===8) $wpdb->anomaly[0]['funnel_stage']='unregistered_stage';
  if ($family===9) $wpdb->anomaly[0]['funnel_stage']=array('booking_started');
  GCU_Observability::$logs=array();
  $r=GCU_Owner_Outcomes::observed_anomaly();
  $ok= $valid
    ? $r['status']==='consented_owner_volume_observed' && count($r['signals'])===1 && !$r['suppressed']
    : ($unregistered
      ? $r['status']==='insufficient_owner_event_evidence' && $r['suppressed'] && $r['signals']===array()
      : $r['status']==='owner_event_volume_read_unverified' && $r['severity']==='unverified'
        && $r['suppressed'] && $r['signals']===array()
        && count(GCU_Observability::$logs)===1 && GCU_Observability::$logs[0][2]===array()
    );
  if (!$ok) $errors[]='R58 anomaly native case '.($i+1).' mismatch';
  printf("R58 native owner anomaly identity %02d/40: %s\n",$i+1,$ok?'PASS':'FAIL');
}
if ($errors) {fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R58 40/40 native anomaly identity/error cases PASS; SQL mock, not installed WordPress.\n";
