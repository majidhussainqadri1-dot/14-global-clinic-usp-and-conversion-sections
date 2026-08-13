from pathlib import Path

path = Path('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
text = path.read_text()
old = '''\t\t$blocks = $wpdb->get_results( "SELECT title,body,cta_label FROM {$t['blocks']} WHERE status='active' LIMIT 500", ARRAY_A );\n\t\tforeach ( is_array( $blocks ) ? $blocks : array() as $block ) {\n\t\t\t$scan = GCU_Future_Policy::dark_pattern_scan( implode( ' ', array( $block['title'], wp_strip_all_tags( $block['body'] ), $block['cta_label'] ) ) );\n\t\t\tforeach ( $scan['flags'] as $flag ) {\n\t\t\t\t$issues[] = 'active_copy:' . $flag;\n\t\t\t}\n\t\t}'''
new = '''\t\t$wpdb->last_error = '';\n\t\t$blocks = $wpdb->get_results( "SELECT title,body,cta_label FROM {$t['blocks']} WHERE status='active' ORDER BY id ASC LIMIT 501", ARRAY_A );\n\t\tif ( '' !== (string) $wpdb->last_error || ! is_array( $blocks ) ) {\n\t\t\t$issues[] = 'active_copy_scan_failed';\n\t\t} elseif ( count( $blocks ) > 500 ) {\n\t\t\t$issues[] = 'active_copy_scan_ceiling_exceeded';\n\t\t} else {\n\t\t\tforeach ( $blocks as $block ) {\n\t\t\t\t$scan = GCU_Future_Policy::dark_pattern_scan( implode( ' ', array( $block['title'], wp_strip_all_tags( $block['body'] ), $block['cta_label'] ) ) );\n\t\t\t\tforeach ( $scan['flags'] as $flag ) {\n\t\t\t\t\t$issues[] = 'active_copy:' . $flag;\n\t\t\t\t}\n\t\t\t}\n\t\t}'''
if old not in text:
    raise SystemExit('Expected parity scan block not found; refusing blind correction')
path.write_text(text.replace(old, new, 1))
