<?php
$root=dirname(__DIR__);$repo=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-repository.php');$bootstrap=file_get_contents($root.'/14-global-clinic-usp-integration/global-clinic-usp-integration.php');$bounds=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-round16-bounds.php');$future=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php');$fail=array();
function r10ck($c,$m){global$fail;if(!$c){$fail[]=$m;}}
r10ck(substr_count($repo,'$wpdb->last_error=\'\';')>=2,'Public repository reads must clear DB last_error.');
r10ck(false!==strpos($repo,'active_blocks_read_failed'),'active_blocks DB failure observability missing.');
r10ck(false!==strpos($repo,'public_claims_read_failed'),'public_claims DB failure observability missing.');
r10ck(false!==strpos($repo,"''!==(string)\$wpdb->last_error||!is_array(\$r)"),'active_blocks DB failure check missing.');
r10ck(false!==strpos($repo,"''!==(string)\$wpdb->last_error||!is_array(\$rows)"),'public_claims DB failure check missing.');
r10ck(false!==strpos($bootstrap,'includes/class-gcu-round16-bounds.php'),'Round 16 bounds class must be loaded.');
r10ck(false!==strpos($bootstrap,"GCU_Round16_Bounds', 'bootstrap"),'Round 16 bounds class must bootstrap after core hardening.');
r10ck(false!==strpos($bounds,'MAX_QUESTION_SIGNALS = 500'),'FAQ signal ceiling missing.');
r10ck(false!==strpos($bounds,'MAX_FAQ_TITLES = 500'),'FAQ catalog ceiling missing.');
r10ck(false!==strpos($bounds,'MAX_CONSISTENCY_ROWS = 1000'),'Consistency scan ceiling missing.');
r10ck(false!==strpos($bounds,'future_faq_gap_scan_suppressed'),'FAQ fail-safe observability missing.');
r10ck(false!==strpos($bounds,"'/gcu/v1/future/consistency'"),'Consistency REST guard missing.');
r10ck(false!==strpos($bounds,"'/gcu/v1/future/scenarios'"),'Scenario REST guard missing.');
r10ck(false!==strpos($bounds,"remove_action( 'gcu_future_daily_governance'"),'Daily consistency fail-safe guard missing.');
$filter_pos=strpos($future,"apply_filters( 'gcu_future_question_aggregates'");$type_pos=false===$filter_pos?false:strpos($future,'if ( ! is_array( $signals ) )',$filter_pos);$query_pos=false===$filter_pos?false:strpos($future,'SELECT LOWER(title)',$filter_pos);r10ck(false!==$filter_pos&&false!==$type_pos&&false!==$query_pos&&$filter_pos<$type_pos&&$type_pos<$query_pos,'FAQ suppression must short-circuit before catalog query.');
r10ck(false!==strpos($future,"WHERE status='active' ORDER BY id ASC LIMIT 501"),'Global parity active-copy scan must use a deterministic one-row-over ceiling query.');
r10ck(false!==strpos($future,"'active_copy_scan_failed'"),'Global parity DB-query failure must block parity.');
r10ck(false!==strpos($future,"'active_copy_scan_ceiling_exceeded'"),'Global parity scan ceiling overflow must block parity.');
$parity_pos=strpos($future,'public static function parity_status()');$clear_pos=false===$parity_pos?false:strpos($future,"\$wpdb->last_error = '';",$parity_pos);$blocks_pos=false===$parity_pos?false:strpos($future,'SELECT title,body,cta_label',$parity_pos);r10ck(false!==$parity_pos&&false!==$clear_pos&&false!==$blocks_pos&&$parity_pos<$clear_pos&&$clear_pos<$blocks_pos,'Global parity active-copy query must clear DB error state before scanning.');
if($fail){fwrite(STDERR,"Eleventh review regression tests failed:\n- ".implode("\n- ",$fail)."\n");exit(1);}echo "Eleventh review regression tests: PASS\n";