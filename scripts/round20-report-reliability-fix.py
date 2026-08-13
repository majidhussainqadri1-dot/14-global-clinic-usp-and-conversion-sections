from pathlib import Path

path = Path('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
text = path.read_text()

def one(old, new, label):
    global text
    if text.count(old) != 1:
        raise SystemExit(f'{label}: expected one match, found {text.count(old)}')
    text = text.replace(old, new, 1)

one("\t\t$nonce = wp_nonce_field( 'gcu_future_report', '_wpnonce', true, false );\n\t\treturn '<section","\t\t$nonce = wp_nonce_field( 'gcu_future_report', '_wpnonce', true, false );\n\t\t$report_id = wp_generate_uuid4();\n\t\treturn '<section",'report UUID allocation')
one('<input type="hidden" name="action" value="gcu_future_report"><input type="hidden" name="route_key"','<input type="hidden" name="action" value="gcu_future_report"><input type="hidden" name="report_id" value="\' . esc_attr( $report_id ) . '\"><input type="hidden" name="route_key"','report hidden UUID')
one("\t\t$data = array(\n\t\t\t'reason_code' => isset( $_POST['reason_code'] ) ? wp_unslash( $_POST['reason_code'] ) : '',","\t\t$data = array(\n\t\t\t'report_id' => isset( $_POST['report_id'] ) ? sanitize_text_field( wp_unslash( $_POST['report_id'] ) ) : '',\n\t\t\t'reason_code' => isset( $_POST['reason_code'] ) ? wp_unslash( $_POST['reason_code'] ) : '',",'report submit UUID')
old="$rows=$wpdb->get_results($wpdb->prepare(\"SELECT id,public_id,report_type,route_key,block_key,locale,reason_code,message,status,resolution,row_version,created_at,updated_at FROM {$t['reports']} WHERE status=%s AND id>%d ORDER BY id ASC LIMIT %d\",$status,absint($cursor),$limit+1),ARRAY_A);$rows=is_array($rows)?$rows:array();"
new="$wpdb->last_error='';$rows=$wpdb->get_results($wpdb->prepare(\"SELECT id,public_id,report_type,route_key,block_key,locale,reason_code,message,status,resolution,row_version,created_at,updated_at FROM {$t['reports']} WHERE status=%s AND id>%d ORDER BY id ASC LIMIT %d\",$status,absint($cursor),$limit+1),ARRAY_A);if(''!==(string)$wpdb->last_error||!is_array($rows)){return new WP_Error('gcu_future_reports_page_query_failed',__('Reports could not be read safely.','global-clinic-usp-integration'),array('status'=>503));}"
one(old,new,'reports page DB fail closed')
old="$rows=$wpdb->get_results($wpdb->prepare($sql,$args),ARRAY_A);$rows=is_array($rows)?$rows:array();"
new="$wpdb->last_error='';$rows=$wpdb->get_results($wpdb->prepare($sql,$args),ARRAY_A);if(''!==(string)$wpdb->last_error||!is_array($rows)){return new WP_Error('gcu_future_records_page_query_failed',__('Future records could not be read safely.','global-clinic-usp-integration'),array('status'=>503));}"
one(old,new,'records page DB fail closed')
path.write_text(text)
