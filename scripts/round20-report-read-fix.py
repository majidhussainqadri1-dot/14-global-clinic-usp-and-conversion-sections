from pathlib import Path
p=Path('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
s=p.read_text()
old='\t\tglobal$wpdb;$t=self::tables();$row=$wpdb->get_row($wpdb->prepare("SELECT * FROM {$t[\'reports\']} WHERE public_id=%s",$id),ARRAY_A);if(!$row){return new WP_Error(\'gcu_future_report_not_found\',__(\'Report not found.\',\'global-clinic-usp-integration\'));}'
new='\t\tglobal$wpdb;$t=self::tables();$wpdb->last_error=\'\';$row=$wpdb->get_row($wpdb->prepare("SELECT * FROM {$t[\'reports\']} WHERE public_id=%s",$id),ARRAY_A);if(\'\'!==(string)$wpdb->last_error){return new WP_Error(\'gcu_future_report_read_failed\',__(\'The report could not be read safely.\',\'global-clinic-usp-integration\'),array(\'status\'=>503));}if(!$row){return new WP_Error(\'gcu_future_report_not_found\',__(\'Report not found.\',\'global-clinic-usp-integration\'));}'
if s.count(old)!=1: raise SystemExit('report read exact pattern mismatch')
s=s.replace(old,new,1)
a="self::resolve_report_record( $id, $expected, $status, $resolution );"
b="GCU_Round16_Bounds::resolve_report_action( $id, $expected, $status, $resolution );"
c="$reports = self::reports( 'open', 50 );"
d="$reports = GCU_Round16_Bounds::reports_for_admin();"
if s.count(a)!=1 or s.count(c)!=1: raise SystemExit('admin wrapper marker mismatch')
p.write_text(s.replace(a,b,1).replace(c,d,1))
