from pathlib import Path
p=Path('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
s=p.read_text()
old='\t\tglobal$wpdb;$t=self::tables();$row=$wpdb->get_row($wpdb->prepare("SELECT * FROM {$t[\'reports\']} WHERE public_id=%s",$id),ARRAY_A);if(!$row){return new WP_Error(\'gcu_future_report_not_found\',__(\'Report not found.\',\'global-clinic-usp-integration\'));}'
new='\t\tglobal$wpdb;$t=self::tables();$wpdb->last_error=\'\';$row=$wpdb->get_row($wpdb->prepare("SELECT * FROM {$t[\'reports\']} WHERE public_id=%s",$id),ARRAY_A);if(\'\'!==(string)$wpdb->last_error){return new WP_Error(\'gcu_future_report_read_failed\',__(\'The report could not be read safely.\',\'global-clinic-usp-integration\'),array(\'status\'=>503));}if(!$row){return new WP_Error(\'gcu_future_report_not_found\',__(\'Report not found.\',\'global-clinic-usp-integration\'));}'
if s.count(old)!=1: raise SystemExit('report read exact pattern mismatch')
p.write_text(s.replace(old,new,1))
