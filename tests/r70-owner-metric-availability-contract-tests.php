<?php
/**
 * R70: 40 actual File14 owner-stage-summary calls with independent owner
 * event volume results. The capability metadata must not represent a missing
 * File08 sensitive REST booking aggregate as a measured zero or success rate.
 * WP DB/contract hooks are MOCKS; this is not live/staging acceptance.
 */
define('ABSPATH',__DIR__);
define('ARRAY_A','ARRAY_A');
function is_wp_error($x){return $x instanceof WP_Error;}
function absint($x){return abs((int)$x);}
function __($s,$domain=''){return $s;}
class WP_Error {public function __construct($a='',$b='',$c=array()){}}
class GCU_Capabilities {
 const VIEW_ANALYTICS='view_analytics';
 public static function require_capability($c,$o=null,$p=''){return true;}
}
class GCU_Install {public static function tables(){return array('events'=>'wp_gcu_events');}}
class GCU_Future_Policy {
 const MIN_COHORT=10;
 public static function valid_aggregate_count($v) {return is_int($v) ? $v>=0 : (is_string($v)&&preg_match('/^(0|[1-9][0-9]*)$/D',$v));}
}
class GCU_Observability {public static function log($a,$b,$c){}}
class R70MockDB {
 public $rows=array();
 public function prepare($sql){return $sql;}
 public function get_results($sql,$format=ARRAY_A){return $this->rows;}
}
$wpdb=new R70MockDB();
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-owner-outcomes.php';
$count=0;
for($sample=0;$sample<40;$sample++){
 $wpdb->rows=$sample?array(array(
  'event_type'=>'File08.BookingStarted.v1',
  'funnel_stage'=>'booking_started',
  'total'=>(string)$sample,
 )):array();
 $value=GCU_Owner_Outcomes::owner_stage_summary(30);
 if(is_wp_error($value)){fwrite(STDERR,'R70 summary failed '.$sample.PHP_EOL);exit(1);}
 $metadata=isset($value['measurement_capabilities'])?$value['measurement_capabilities']:array();
 $valid=is_array($metadata)
   && 'unavailable_sensitive_rest_aggregate_unapproved'===($metadata['booking_started']??null)
   && 'unavailable_owner_attested_arrival'===($metadata['destination_loaded']??null)
   && 'unverified_signed_owner_contract'===($metadata['application_started']??null)
   && array_key_exists('booking_owner_outcome_count',$metadata)
   && null===$metadata['booking_owner_outcome_count']
   && null===$metadata['booking_success_rate']
   && null===$metadata['cross_person_conversion_rate']
   && false===$metadata['zero_is_proven']
   && false===$metadata['owner_confirmation_accepted']
   && false===$metadata['staging_accepted']
   && false===$metadata['live_verified']
   && null===$value['success_rate']
   && false===$value['linked_journey']
   && false===$value['live_accepted'];
 if(!$valid){fwrite(STDERR,'R70 metadata FAIL sample '.$sample.' '.var_export($value,true).PHP_EOL);exit(1);}
 $count++;
 echo 'R70 owner-availability case '.sprintf('%02d',$count).'/40: PASS (mocked row-count '.$sample.', no false booking success)'.PHP_EOL;
}
if($count!==40)exit(2);
echo 'R70 40/40 truthful owner unavailable metrics source behavior PASS; WordPress owner contract NOT accepted.'.PHP_EOL;
