<?php
/** File 14 eleventh fresh cycle — Round 20 root-method regression gate. */
$root=dirname(__DIR__);$future=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php');$bounds=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-round16-bounds.php');
if(false===$future||false===$bounds){fwrite(STDERR,"Round 20 source could not be read.\n");exit(1);}
$required=array('report_identity_matches','gcu_future_report_identity_conflict','gcu_future_report_identity_query_failed','gcu_future_reports_page_query_failed','gcu_future_records_page_query_failed','gcu_future_reports_query_failed','gcu_future_report_read_failed','name="report_id"',"'report_id' => isset( \$_POST['report_id'] )",'gcu_report_update','is_wp_error($reports)');
foreach($required as$marker){if(false===strpos($future,$marker)){fwrite(STDERR,"Round 20 root marker missing: {$marker}\n");exit(1);}}
if(false!==strpos($bounds,'safe_future_html')||false!==strpos($bounds,'report_identity_guard')){fwrite(STDERR,"Round 20 patch stacking remains in Round 16 bounds.\n");exit(1);}
foreach(array('.github/workflows/file14-round20-root-apply.yml','scripts/apply-file14-round20-root-min.py','.github/workflows/file14-round20-apply.yml','scripts/round20-admin-reports-fix.py','scripts/round20-report-read-fix.py','scripts/round20-report-reliability-fix.py')as$temp){if(file_exists($root.'/'.$temp)){fwrite(STDERR,"Temporary Round 20 machinery remains: {$temp}\n");exit(1);}}
echo "Eleventh-cycle Round 20 root-method regression gate: PASS\n";