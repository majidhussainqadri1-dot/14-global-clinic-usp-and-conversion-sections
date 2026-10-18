<?php
/** R56: forty actual owner-stage SQL row-identity validation scenarios. Mocked WordPress, NOT live. */
function __( $text, $domain = '' ) { return $text; }
class GCU_Observability {
    public static $entries = array();
    public static function log( $level, $code, $fields = array() ) {
        self::$entries[] = array( $level, $code, $fields );
    }
}
require __DIR__ . '/r36-owner-cohort-privacy-tests.php';
GCU_Capabilities::$allowed = true;
$failures = array();
for ( $i=0; $i<40; ++$i ) {
    $mode=$i%10; $variation=intdiv($i,10);
    $wpdb->rows=array(
        array('event_type'=>'File08.BookingStarted.v1','funnel_stage'=>'booking_started','total'=>(string)(20+$variation))
    );
    $bad=false;
    if ($mode===1) { $wpdb->rows[]=array('event_type'=>array('File07.DirectoryServed.v1'),'funnel_stage'=>'destination_served','total'=>'30');$bad=true; }
    if ($mode===2) { $wpdb->rows[]=array('event_type'=>42,'funnel_stage'=>'destination_served','total'=>'30');$bad=true; }
    if ($mode===3) { $wpdb->rows[]=array('event_type'=>'','funnel_stage'=>'destination_served','total'=>'30');$bad=true; }
    if ($mode===4) { $wpdb->rows[]=array('event_type'=>'   ','funnel_stage'=>'destination_served','total'=>'30');$bad=true; }
    if ($mode===5) { $wpdb->rows[]=array('event_type'=>'File07.DirectoryServed.v1','funnel_stage'=>array('destination_served'),'total'=>'30');$bad=true; }
    if ($mode===6) { $wpdb->rows[]=array('event_type'=>'File07.DirectoryServed.v1','funnel_stage'=>100,'total'=>'30');$bad=true; }
    if ($mode===7) { $wpdb->rows[]=array('event_type'=>'File07.DirectoryServed.v1','funnel_stage'=>'','total'=>'30');$bad=true; }
    if ($mode===8) { $wpdb->rows[]=array('event_type'=>'File07.DirectoryServed.v1','funnel_stage'=>null,'total'=>'30');$bad=true; }
    if ($mode===9) { // Well-typed but unapproved pair remains deliberately excluded.
        $wpdb->rows[]=array('event_type'=>'File07.DirectoryServed.v1','funnel_stage'=>'booking_started','total'=>'30');
    }
    GCU_Observability::$entries=array();
    $result=GCU_Owner_Outcomes::owner_stage_summary(30);
    $ok=$bad ?
        is_wp_error($result) && $result->code==='gcu_owner_stage_row_unverified'
            && count(GCU_Observability::$entries)===1 && GCU_Observability::$entries[0][2]===array()
        : is_array($result) && !$result['suppressed'] && count($result['items'])===1
            && $result['items'][0]['count']===20+$variation && $result['success_rate']===null
            && GCU_Observability::$entries===array();
    if(!$ok){$failures[]='R56 owner identity source scenario '.($i+1);}
    printf("R56 owner SQL identity %02d/40: %s\n",$i+1,$ok?'PASS':'FAIL');
}
if($failures){fwrite(STDERR,implode("\n",$failures)."\n");exit(1);}
echo "R56: 40/40 native owner SQL typed evidence source checks PASS; not installed WordPress.\n";
