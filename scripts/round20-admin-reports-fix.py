from pathlib import Path
p=Path('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
s=p.read_text()
old='\t\treturn $wpdb->get_results( $wpdb->prepare( "SELECT public_id,report_type,route_key,block_key,locale,reason_code,message,status,resolution,row_version,created_at,updated_at FROM {$t[\'reports\']} WHERE status=%s ORDER BY created_at ASC LIMIT %d", $status, $limit ), ARRAY_A );'
new='\t\t$wpdb->last_error = \'\';\n\t\t$rows = $wpdb->get_results( $wpdb->prepare( "SELECT public_id,report_type,route_key,block_key,locale,reason_code,message,status,resolution,row_version,created_at,updated_at FROM {$t[\'reports\']} WHERE status=%s ORDER BY created_at ASC LIMIT %d", $status, $limit ), ARRAY_A );\n\t\tif ( \'\' !== (string) $wpdb->last_error || ! is_array( $rows ) ) { return new WP_Error( \'gcu_future_reports_query_failed\', __( \'Reports could not be read safely.\', \'global-clinic-usp-integration\' ), array( \'status\' => 503 ) ); }\n\t\treturn $rows;'
if s.count(old)!=1: raise SystemExit('admin reports exact pattern mismatch')
p.write_text(s.replace(old,new,1))
