<?php
/** R64: 40 real GCU_Contracts::persist_option_exact source method cases.
 * SQL/WordPress option mocks, not live WordPress. Invalid UTF-8/NAN/INF
 * reproduce PHP json_encode boolean false and prevent false hash success.
 */
require __DIR__.'/r61-inbound-owner-contract-integrity.php';
$check=new ReflectionMethod('GCU_Contracts','persist_option_exact');
$check->setAccessible(true);
$errors=array();
for($i=0;$i<40;$i++){
  $v=intdiv($i,10);$mode=$i%10;
  $GLOBALS['r61_options']=array();$GLOBALS['r61_writes']=0;
  $value=array('event_id'=>sprintf('%08d-1111-4111-8111-123456789abc',4000+$i),
    'available'=>true,'count'=>10+$v);
  $valid=$mode<=3;
  if($mode===1)$value['copy']='درست اردو';
  if($mode===2)$value['nested']=array('route'=>'clinic','locale'=>'en-US');
  if($mode===3)$value['available']=false;
  if($mode===4)$value['broken_utf8']="\xB1";
  if($mode===5)$value['nested']=array('bad'=>"\xC3\x28");
  if($mode===6)$value['count']=INF;
  if($mode===7)$value['count']=NAN;
  if($mode===8)$value['copy']="valid".$v."\xFF";
  if($mode===9)$value['nested']=array('one'=>"\x80",'two'=>'valid');
  $actual=$check->invoke($consumer,'r64_option_evidence',$value);
  $encoded=wp_json_encode($value);
  $pass=($actual===$valid) && $GLOBALS['r61_writes']===1 &&
     ($valid?is_string($encoded):false===$encoded);
  printf("R64 exact option JSON evidence %02d/40: %s\n",$i+1,$pass?'PASS':'FAIL');
  if(!$pass)$errors[]='R64 real method JSON evidence '.$i.' '.var_export($actual,true);
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R64 40/40 direct WordPress option original/stored JSON verification cases PASS; live not tested.\n";
