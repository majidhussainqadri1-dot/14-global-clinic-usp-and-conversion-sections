<?php
$root=dirname(__DIR__);$repo=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-repository.php');$bootstrap=file_get_contents($root.'/14-global-clinic-usp-integration/global-clinic-usp-integration.php');$bounds=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-round16-bounds.php');$future=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php');$fail=array();
function r10ck($c,$m){global$fail;if(!$c){$fail[]=$m;}}
r10ck(substr_count($repo,'$wpdb->last_error=\'\';')>=2,'Public repository reads must clear DB last_error.');
r10ck(false!==strpos($repo,'active_blocks_read_failed'),'active_blocks DB failure observability missing.');
r10ck(false!==strpos($repo,'public_claims_read_failed'),'public_claims DB failure observability missing.');
r10ck(false!==strpos($bootstrap,'includes/class-gcu-round16-bounds.php'),'Round 16 bounds class must be loaded.');
r10ck(false!==strpos($bounds,'MAX_QUESTION_SIGNALS = 500'),'FAQ signal ceiling missing.');
r10ck(false!==strpos($bounds,'MAX_FAQ_TITLES = 500'),'FAQ catalog ceiling missing.');
r10ck(false!==strpos($bounds,'MAX_CONSISTENCY_ROWS = 1000'),'Consistency scan ceiling missing.');
r10ck(false!==strpos($bounds,'future_faq_gap_scan_suppressed'),'FAQ fail-safe observability missing.');
r10ck(false===strpos($bounds,'safe_future_html'),'Round 20 report overlay must not remain in Round 16 bounds.');
r10ck(false===strpos($bounds,'report_identity_guard'),'Round 20 report identity must live in Future Intelligence.');
r10ck(false!==strpos($future,"WHERE status='active' ORDER BY id ASC LIMIT 501"),'Global parity one-row-over scan missing.');
r10ck(false!==strpos($future,"'active_copy_scan_failed'"),'Global parity DB failure gate missing.');
r10ck(false!==strpos($future,"'active_copy_scan_ceiling_exceeded'"),'Global parity ceiling gate missing.');
foreach(array('report_identity_matches','gcu_future_report_identity_conflict','gcu_future_report_identity_query_failed','gcu_future_reports_page_query_failed','gcu_future_records_page_query_failed','gcu_future_reports_query_failed','gcu_future_report_read_failed','name="report_id"',"'report_id' => isset( \$_POST['report_id'] )",'gcu_report_update','is_wp_error($reports)')as$marker){r10ck(false!==strpos($future,$marker),'Round 20 root marker missing: '.$marker);}
foreach(array('.github/workflows/file14-round20-root-apply.yml','scripts/apply-file14-round20-root-min.py')as$temp){r10ck(!file_exists($root.'/'.$temp),'Temporary Round 20 root machinery remains: '.$temp);}
if($fail){fwrite(STDERR,"Eleventh review regression tests failed:\n- ".implode("\n- ",$fail)."\n");exit(1);}echo "Eleventh review regression tests: PASS\n";