<?php
/**
 * R46 forty REAL PHP method checks with simulated WordPress SQL rows.
 * R39’s pre-existing 40 direct-method checks also execute via require.
 * NOT a WordPress staging browser test or evidence of live deployed code.
 */
require __DIR__ . '/r39-parity-completeness-tests.php';

class R46DB extends R39DB {
    public $date_override = null;
    public $block_rows = array();
    public function get_row( $sql, $format = ARRAY_A ) {
        $row = parent::get_row($sql, $format);
        if ( is_array($row) && is_array($this->date_override) ) {
            foreach ($this->date_override as $key=>$value) { $row[$key] = $value; }
        }
        return $row;
    }
    public function get_results($sql,$format=ARRAY_A) {
        $this->queries[] = $sql;
        if (strpos($sql, 'LIMIT 501') === false) { throw new RuntimeException('R46 requires bounded active block scan'); }
        return $this->block_rows;
    }
}
$wpdb = new R46DB();
$fresh = new ReflectionMethod('GCU_Future_Intelligence','claim_freshness');
$fresh->setAccessible(true);
$errors = array();
$invalidDates = array('tomorrow', '2099-02-30 00:00:00', '2099-13-01 00:00:00', '2099-01-01 00:00:00 ');
$future = gmdate('Y-m-d H:i:s', time()+86400*12);
$past = gmdate('Y-m-d H:i:s', time()-86400*12);
$clean = array('title'=>'Clinic visit','body'=>'Approved information','cta_label'=>'Read details');
for ($i=1;$i<=40;$i++) {
    $family=($i-1)%10; $v=intdiv($i-1,10);
    $wpdb->queries = array();
    $wpdb->date_override = null;
    $wpdb->block_rows = array($clean);
    $GLOBALS['r39_last_parity'] = null;
    GCU_Future_Policy::$scans = 0;
    $ok=false;
    if ($family === 0 || $family === 1) {
        $row=array('review_due_at'=>null,'expires_at'=>null);
        $row[$family===0?'review_due_at':'expires_at']=$invalidDates[$v];
        $res=$fresh->invoke(null,$row);
        $ok=is_array($res) && $res['fresh']===false;
    } elseif ($family === 2 || $family === 3) {
        $row=array('review_due_at'=>null,'expires_at'=>null);
        $row[$family===2?'review_due_at':'expires_at']=$past;
        $res=$fresh->invoke(null,$row);
        $ok=is_array($res) && $res['fresh']===false;
    } elseif ($family === 4) {
        $row=array('review_due_at'=>null,'expires_at'=>null);
        if($v===1) $row['review_due_at']=$future;
        if($v===2) $row['expires_at']=$future;
        if($v===3) { $row['review_due_at']=$future; $row['expires_at']=$future; }
        $res=$fresh->invoke(null,$row);
        $ok=$res['fresh']===true;
    } elseif ($family === 5) {
        $wpdb->date_override=array('review_due_at'=>$invalidDates[$v]);
        $p=GCU_Future_Intelligence::parity_status();
        $ok=$p['ok']===false
            && in_array('claim_stale:zero_platform_commission',$p['issues'],true)
            && $GLOBALS['r39_last_parity']===$p;
    } elseif ($family === 6) {
        $wpdb->block_rows=array(
            $v===0?null:($v===1?array('title'=>'valid'):($v===2?array('title'=>'valid','body'=>'text'):array('body'=>'text','cta_label'=>'Read')))
        );
        $p=GCU_Future_Intelligence::parity_status();
        $ok=$p['ok']===false && in_array('active_copy_row_unverified',$p['issues'],true)
            && GCU_Future_Policy::$scans===0;
    } elseif ($family === 7) {
        $wpdb->block_rows=array(
            $v===0?array('title'=>array('nested'),'body'=>'good','cta_label'=>'Read'):(
                $v===1?array('title'=>'valid','body'=>array('unsafe'),'cta_label'=>'Read'):(
                $v===2?array('title'=>'valid','body'=>'good','cta_label'=>42):
                array('title'=>'valid','body'=>null,'cta_label'=>'Read')
            ))
        );
        $p=GCU_Future_Intelligence::parity_status();
        $ok=$p['ok']===false && in_array('active_copy_row_unverified',$p['issues'],true)
            && GCU_Future_Policy::$scans===0;
    } elseif ($family === 8) {
        if($v===0) $wpdb->block_rows=array();
        if($v===1) $wpdb->block_rows=array($clean);
        if($v===2) $wpdb->block_rows=array($clean,$clean);
        if($v===3) $wpdb->block_rows=array_merge(array_fill(0,10,$clean),array(array('title'=>'Approved','body'=>'injected-deception','cta_label'=>'Continue')));
        $p=GCU_Future_Intelligence::parity_status();
        $expect=$v===3;
        $ok=$p['ok']===!$expect
            && ($expect===in_array('active_copy:test_deception',$p['issues'],true))
            && GCU_Future_Policy::$scans===count($wpdb->block_rows);
    } else {
        $wpdb->date_override=array('expires_at'=>$invalidDates[$v]);
        $wpdb->block_rows=array(array('title'=>'Public','body'=>array('nested'),'cta_label'=>'Read'));
        $p=GCU_Future_Intelligence::parity_status();
        $ok=$p['ok']===false
            && in_array('active_copy_row_unverified',$p['issues'],true)
            && in_array('claim_stale:free_approved_core',$p['issues'],true)
            && GCU_Future_Policy::$scans===0;
    }
    printf("R46 real PHP method claim/active-copy test %02d/40: %s (family %d)\n",$i,$ok?'PASS':'FAIL',$family);
    if(!$ok)$errors[]=sprintf('R46 test %02d failed family %d',$i,$family);
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R46 forty actual-method SQL mocked date and active-copy fail-closed cases PASS; staging/live NOT tested.\n";
