<?php
/**
 * R48: 40 direct REAL consistency_graph()/rest_consistency()/daily_governance()
 * PHP method tests with mocked WordPress SQL. Not staging or deployed WordPress.
 */
require __DIR__ . '/r41-faq-consistency-governance-tests.php';

function r48_row( $key='primary_usp', $locale='en-US', $claims='["zero_platform_commission"]' ) {
    return array( 'block_key'=>$key, 'locale'=>$locale,
        'title'=>'Safe title', 'body'=>'Approved explanatory text',
        'cta_label'=>'Read details', 'claim_keys'=>$claims );
}
function r48_healthy( $key='primary_usp' ) {
    return array(
        r48_row( $key, 'en-US' ),
        r48_row( $key, 'ur-PK' ),
        r48_row( $key, 'ar-SA' )
    );
}
$failed = array();
for( $case=1; $case<=40; $case++ ) {
    $family = ( $case-1 ) % 10;
    $v = intdiv( $case-1, 10 );
    $wpdb->graph = r48_healthy();
    $wpdb->faq = array();
    $wpdb->copy = array( array( 'title'=>'Safe','body'=>'Approved','cta_label'=>'Continue' ) );
    $wpdb->queries = array();
    $wpdb->writes = 0;
    GCU_Observability::$events = array();
    $GLOBALS['r41_signals'] = array();
    $ok = false;
    if ( 0 === $family ) {
        $wpdb->graph = r48_healthy('safe_block_' . $v);
        if ( 1 === $v ) $wpdb->graph[0]['claim_keys']='[]';
        if ( 2 === $v ) { foreach($wpdb->graph as &$node){ $node['claim_keys']='[]';} unset($node); }
        if ( 3 === $v ) {
            foreach($wpdb->graph as &$node){$node['claim_keys']='["free_approved_core","zero_platform_commission"]';} unset($node);
        }
        $r = GCU_Future_Intelligence::consistency_graph();
        $sql = implode("\n",$wpdb->queries);
        $expected = $v===1;
        $ok = ( $r['verified']===true )
            && ( $r['ok']===!$expected )
            && ( $expected === in_array('claim_set_drift:safe_block_1',$r['issues'],true) )
            && strpos($sql,'LIMIT 501')!==false;
    } elseif ( 1 === $family ) {
        $bad = array( '{"broken"', 'null', '{}', '"one_claim"' )[$v];
        $wpdb->graph[0]['claim_keys'] = $bad;
        $r = GCU_Future_Intelligence::consistency_graph();
        $rest = GCU_Future_Intelligence::rest_consistency();
        $ok = !$r['ok'] && !$r['verified']
            && in_array('active_block_consistency_claims_unverified',$r['issues'],true)
            && is_wp_error($rest) && $rest->get_error_code()==='gcu_future_consistency_read_failed';
    } elseif ( 2 === $family ) {
        $wpdb->graph[1]['claim_keys']=array('["unknown"]','[12]','[{}]','[[]]')[$v];
        $r=GCU_Future_Intelligence::consistency_graph();
        $ok=!$r['verified'] && !$r['ok']
            && in_array('active_block_consistency_claims_unverified',$r['issues'],true);
    } elseif ( 3 === $family ) {
        $duplicate = r48_row('primary_usp','ur-PK',$v%2 ? '["free_approved_core"]' : '["zero_platform_commission"]');
        $wpdb->graph[]=$duplicate;
        if($v>1)$wpdb->graph[]=r48_row('primary_usp','en-US','[]');
        $r=GCU_Future_Intelligence::consistency_graph();
        $ok=!$r['verified'] && !$r['ok']
            && in_array('active_block_consistency_duplicate_locale',$r['issues'],true)
            && count($r['nodes']['primary_usp'])===3;
    } elseif ( 4 === $family ) {
        if($v===0)$wpdb->graph[0]['title']=array('not-string');
        if($v===1)$wpdb->graph[0]['body']=false;
        if($v===2)$wpdb->graph[0]['cta_label']=array('invalid');
        if($v===3)$wpdb->graph[0]['claim_keys']=array('zero_platform_commission');
        $r=GCU_Future_Intelligence::consistency_graph();
        $ok=!$r['verified'] && !$r['ok']
            && in_array('active_block_consistency_row_invalid',$r['issues'],true);
    } elseif ( 5 === $family ) {
        if($v===0)$wpdb->graph[0]['block_key']='';
        if($v===1)$wpdb->graph[0]['block_key']='CLINIC Not Canonical';
        if($v===2)$wpdb->graph[0]['locale']='de-DE';
        if($v===3)$wpdb->graph[0]['locale']=array('en-US');
        $r=GCU_Future_Intelligence::consistency_graph();
        $ok=!$r['verified'] && !$r['ok']
            && in_array('active_block_consistency_row_invalid',$r['issues'],true);
    } elseif ( 6 === $family ) {
        $wpdb->graph=array();
        for($j=0;$j<501+$v;$j++)$wpdb->graph[]=r48_row('block_' . $j, 'en-US','[]');
        // The mocked DB returns all rows: verify real SQL has 501 sentinel
        // and fail closed before accepting any scan >500.
        $r=GCU_Future_Intelligence::consistency_graph();
        $ok=!$r['verified'] && !$r['ok'] && $r['nodes']===array()
            && in_array('active_blocks_consistency_scan_limit_exceeded',$r['issues'],true)
            && strpos(implode(' ',$wpdb->queries),'LIMIT 501')!==false
            && count(GCU_Observability::$events)===1;
    } elseif ( 7 === $family ) {
        $wpdb->graph=array();
        for($j=0;$j<497+$v;$j++)$wpdb->graph[]=r48_row('block_' . $j,'en-US','[]');
        $r=GCU_Future_Intelligence::consistency_graph();
        $ok=$r['verified'] && !$r['ok'] && count($r['nodes'])===497+$v
            && in_array('missing_locale:block_0:ur-PK',$r['issues'],true);
    } elseif ( 8 === $family ) {
        $wpdb->graph = r48_healthy();
        if ( $v===0 ) $wpdb->graph=array_slice($wpdb->graph,0,2);
        if ( $v===1 ) $wpdb->graph[2]['claim_keys']='["free_approved_core"]';
        if ( $v===2 ) $wpdb->graph[0]['claim_keys']='[]';
        if ( $v===3 ) $wpdb->graph[1]['claim_keys']='["free_approved_core"]';
        $r=GCU_Future_Intelligence::consistency_graph();
        $ok=$r['verified'] && !$r['ok']
            && ( $v===0
                ? in_array('missing_locale:primary_usp:ar-SA',$r['issues'],true)
                : in_array('claim_set_drift:primary_usp',$r['issues'],true) );
    } else {
        if($v===0)$wpdb->graph[0]['claim_keys']='not-json';
        if($v===1)$wpdb->graph[]=r48_row('primary_usp','ar-SA');
        if($v===2)$wpdb->graph[1]['locale']=array('not-canonical');
        if($v===3){
            $wpdb->graph=array();
            for($j=0;$j<501;$j++)$wpdb->graph[]=r48_row('block_'.$j,'en-US','[]');
        }
        $r=GCU_Future_Intelligence::daily_governance();
        $ok=is_wp_error($r) && $r->get_error_code()==='gcu_daily_consistency_failed'
            && $wpdb->writes===0;
    }
    printf("R48 actual-method consistency governance %02d/40: %s (family %d)\n",
        $case, $ok?'PASS':'FAIL', $family );
    if(!$ok)$failed[]=sprintf('R48 case %02d family %d failed',$case,$family);
}
if($failed){fwrite(STDERR,implode("\n",$failed)."\n");exit(1);}
echo "R48 40/40 REAL consistency PHP-method SQL-mocked cases PASS, NOT staging/live.\n";
