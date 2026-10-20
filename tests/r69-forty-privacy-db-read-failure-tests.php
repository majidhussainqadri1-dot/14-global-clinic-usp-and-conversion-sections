<?php
/**
 * R69: Forty behavior calls into the ACTUAL File14 GCU_Privacy WordPress privacy
 * callbacks under mocked DB success/failure conditions. Not a real WordPress DB.
 * Source-plan scope: F14 FR-010/FR-011, NFR-002/003, central consent/privacy
 * constitution; owner files remain separate and are not changed by these tests.
 */
error_reporting(E_ALL);
define('ABSPATH',__DIR__);
define('ARRAY_A','ARRAY_A');
$GLOBALS['r69_user_meta_deleted']=0;
$GLOBALS['r69_event_rows']=array();
$GLOBALS['r69_report_rows']=array();
$GLOBALS['r69_event_count']='0';
$GLOBALS['r69_report_count']='0';
$GLOBALS['r69_event_delete']=0;
$GLOBALS['r69_report_update']=0;
function absint($n){return abs((int)$n);}
function sanitize_email($v){return (string)$v;}
function sanitize_key($v){return strtolower(preg_replace('/[^a-z0-9_-]/i','',(string)$v));}
function sanitize_text_field($v){return (string)$v;}
function __($v,$domain=''){return $v;}
function get_user_by($field,$email){return (object)array('ID'=>12);}
function get_user_meta($id,$key,$single){return str_repeat('a',64);}
function delete_user_meta($id,$key){$GLOBALS['r69_user_meta_deleted']++;return true;}
class GCU_Install {public static function tables(){return array('events'=>'wp_gcu_events');}}
class GCU_Future_Intelligence {public static function tables(){return array('reports'=>'wp_gcu_future_reports');}}
class GCU_Integrity {
 public static function user_subject_hash($subject){return str_repeat('b',64);}
 public static function future_actor_hash($id){return str_repeat('c',64);}
}
class R69MockDB {
 public function prepare($sql){return $sql;}
 public function get_results($sql,$format=ARRAY_A){
    if(strpos($sql,'wp_gcu_events')!==false)return $GLOBALS['r69_event_rows'];
    if(strpos($sql,'wp_gcu_future_reports')!==false)return $GLOBALS['r69_report_rows'];
    return false;
 }
 public function get_var($sql){
    if(strpos($sql,'wp_gcu_events')!==false)return $GLOBALS['r69_event_count'];
    if(strpos($sql,'wp_gcu_future_reports')!==false)return $GLOBALS['r69_report_count'];
    return null;
 }
 public function query($sql){
    if(strpos($sql,'wp_gcu_events')!==false)return $GLOBALS['r69_event_delete'];
    if(strpos($sql,'wp_gcu_future_reports')!==false)return $GLOBALS['r69_report_update'];
    return false;
 }
}
$wpdb=new R69MockDB();
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-privacy.php';
$privacy=new GCU_Privacy();
$checks=0;
function r69_reset(){
 $GLOBALS['r69_event_rows']=array();$GLOBALS['r69_report_rows']=array();
 $GLOBALS['r69_event_count']='0';$GLOBALS['r69_report_count']='0';
 $GLOBALS['r69_event_delete']=0;$GLOBALS['r69_report_update']=0;
 $GLOBALS['r69_user_meta_deleted']=0;
}
function r69_expect($label,$result,$predicate){
 global $checks;$checks++;
 if(!$predicate){fwrite(STDERR,"R69 ".sprintf('%02d',$checks)." FAIL: ".$label.
    " result=".var_export($result,true).PHP_EOL);exit(1);}
 echo "R69 privacy behavior ".sprintf('%02d',$checks)."/40 PASS: ".$label.PHP_EOL;
}
$invalid=array(false,null,'',true,3.5,new stdClass(),'SQL_ERROR',-1);
// 01–08 event-export query failed: never report an empty export as complete.
foreach($invalid as $i=>$bad){
 r69_reset();$GLOBALS['r69_event_rows']=$bad;
 $out=$privacy->export_data('patient@example.test',1);
 r69_expect('event export invalid database response '.($i+1),$out,
  is_array($out)&&isset($out['done'])&&false===$out['done']&&isset($out['data'])&&array()===$out['data']);
}
// 09–16 reports-export query failed: do not return partial events as complete.
foreach($invalid as $i=>$bad){
 r69_reset();$GLOBALS['r69_event_rows']=array(array('event_id'=>'event1'));
 $GLOBALS['r69_report_rows']=$bad;
 $out=$privacy->export_data('patient@example.test',1);
 r69_expect('report export invalid database response '.($i+1),$out,
  is_array($out)&&isset($out['done'])&&false===$out['done']&&isset($out['data'])&&array()===$out['data']);
}
// 17–24 erasure of events: no cast of SQL COUNT errors to zero.
foreach($invalid as $i=>$bad){
 r69_reset();$GLOBALS['r69_event_count']=$bad;
 $out=$privacy->erase_data('patient@example.test',1);
 r69_expect('event count unverified '.($i+1),$out,
  is_array($out)&&isset($out['done'],$out['items_retained'])&&false===$out['done']
  &&true===$out['items_retained']&&count($out['messages'])>0
  &&0===$GLOBALS['r69_user_meta_deleted']);
}
// 25–32 anonymization of reports: identical fail-closed behavior.
foreach($invalid as $i=>$bad){
 r69_reset();$GLOBALS['r69_report_count']=$bad;
 $out=$privacy->erase_data('patient@example.test',1);
 r69_expect('report count unverified '.($i+1),$out,
  is_array($out)&&isset($out['done'],$out['items_retained'])&&false===$out['done']
  &&true===$out['items_retained']&&count($out['messages'])>0
  &&0===$GLOBALS['r69_user_meta_deleted']);
}
// 33–40 positive/partial paths must remain operational and truthful.
r69_reset();$out=$privacy->export_data('patient@example.test',1);
r69_expect('empty verified export completes',$out,$out['done']===true&&count($out['data'])===0);
r69_reset();$GLOBALS['r69_event_rows']=array(array('event_id'=>'evt1'));
$out=$privacy->export_data('patient@example.test',1);
r69_expect('one verified event exports',$out,$out['done']===true&&count($out['data'])===1);
r69_reset();$GLOBALS['r69_report_rows']=array(array('public_id'=>'rep1'));
$out=$privacy->export_data('patient@example.test',1);
r69_expect('one verified report exports',$out,$out['done']===true&&count($out['data'])===1);
r69_reset();$GLOBALS['r69_event_rows']=array_fill(0,201,array('event_id'=>'e'));
$out=$privacy->export_data('patient@example.test',1);
r69_expect('more than 200 events paginated',$out,$out['done']===false&&count($out['data'])===200);
r69_reset();$GLOBALS['r69_report_rows']=array_fill(0,201,array('public_id'=>'r'));
$out=$privacy->export_data('patient@example.test',1);
r69_expect('more than 200 reports paginated',$out,$out['done']===false&&count($out['data'])===200);
r69_reset();$out=$privacy->erase_data('patient@example.test',1);
r69_expect('both canonical zero counts permit verified completion',$out,$out['done']===true&&$GLOBALS['r69_user_meta_deleted']===1);
r69_reset();$GLOBALS['r69_event_count']='1';$out=$privacy->erase_data('patient@example.test',1);
r69_expect('remaining events preserve repeat erasure',$out,$out['done']===false&&$GLOBALS['r69_user_meta_deleted']===0);
r69_reset();$GLOBALS['r69_report_count']=1;$out=$privacy->erase_data('patient@example.test',1);
r69_expect('remaining reports preserve repeat anonymization',$out,$out['done']===false&&$GLOBALS['r69_user_meta_deleted']===0);
if(40!==$checks){fwrite(STDERR,'R69 wrong case count: '.$checks.PHP_EOL);exit(1);}
echo "R69 40/40 native File14 privacy export/erasure mock behavior tests PASS. Deployed DB unverified.".PHP_EOL;
