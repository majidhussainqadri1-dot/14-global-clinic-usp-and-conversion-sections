<?php
/**
 * R59: 40 direct GCU_Experiment_Baseline::capture_approved() adversarial
 * original-value Founder actor and experiment version cases.
 * Includes R56 source-only WordPress/SQL method mock; NOT deployed testing.
 */
require __DIR__ . '/r56-approved-snapshot-reconciliation.php';
class R59CaptureDB extends R56DB {
    public $writes = array();
    public function insert($table, $data, $format = null) {
        $this->writes[] = array('table'=>$table,'data'=>$data);
        return 1;
    }
}
$errors = array();
for ($i=0; $i<40; $i++) {
    $v = intdiv($i,10); $family = $i%10;
    $approved = 7+$v; $version = 11+$v;
    $actor = $approved; $rv = $version;
    $variants = json_encode(array('A approved copy','B approved copy'));
    $ok_expected = $family===0 || $family===1;
    if ($family===1) { $actor=(string)$approved; $rv=(string)$version; }
    if ($family===2) $actor=-$approved;
    if ($family===3) $actor=$approved+0.5;
    if ($family===4) $actor='0'.$approved;
    if ($family===5) $actor='18446744073709551616';
    if ($family===6) $rv=$version.'junk';
    if ($family===7) $rv=(float)$version;
    if ($family===8) $rv=PHP_INT_MAX;
    if ($family===9) $variants=array('A approved copy','B approved copy');
    $exp=array(
      'public_id'=>sprintf('%08d-1111-4111-8111-123456789abc',$i+101),
      'row_version'=>$rv, 'variants'=>$variants
    );
    $wpdb=new R59CaptureDB();
    $result=GCU_Experiment_Baseline::capture_approved($exp,$actor);
    $pass=$ok_expected
      ? $result===true && count($wpdb->writes)===1 &&
          $wpdb->writes[0]['data']['created_by']===$approved &&
          $wpdb->writes[0]['data']['row_version']===1
      : $result instanceof WP_Error && count($wpdb->writes)===0;
    printf("R59 Founder capture actor/version %02d/40: %s\n",$i+1,$pass?'PASS':'FAIL');
    if(!$pass)$errors[]='Founder baseline capture case '.($i+1).' bad '.(is_object($result)?$result->code:var_export($result,true));
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R59 40/40 actual Founder baseline capture source method cases PASS; no staging/live owner writes.\n";
