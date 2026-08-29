from pathlib import Path

p = Path('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
s = p.read_text(encoding='utf-8')

repls = [
("register_rest_route( $ns, '/future/claims/(?P<claim_key>[a-z0-9_\\-]+)/revalidate', array( 'methods' => WP_REST_Server::EDITABLE, 'callback' => array( __CLASS__, 'rest_revalidate_claim' ), 'permission_callback' => array( __CLASS__, 'can_approve_claims' ) ) );",
 "register_rest_route( $ns, '/future/claims/(?P<claim_key>[a-z0-9_\\-]+)/revalidate', array( 'methods' => WP_REST_Server::EDITABLE, 'callback' => array( __CLASS__, 'rest_revalidate_claim' ), 'permission_callback' => array( __CLASS__, 'can_revalidate_claim' ) ) );"),
("\tpublic static function revalidate_claim( $key, $expected, $reason ) {\n\t\t$reason = trim( sanitize_textarea_field( $reason ) );",
 "\tpublic static function revalidate_claim( $key, $expected, $reason ) {\n\t\t$key = sanitize_key( $key );\n\t\t$auth = GCU_Capabilities::require_capability( GCU_Capabilities::APPROVE_CLAIMS, $key, 'future_intelligence_claims' );\n\t\tif ( is_wp_error( $auth ) ) { return $auth; }\n\t\t$reason = trim( sanitize_textarea_field( $reason ) );"),
("\tpublic static function resolve_report_record( $id, $expected, $status, $resolution ) {\n\t\t$ready=self::runtime_ready();if(is_wp_error($ready)){return$ready;}",
 "\tpublic static function resolve_report_record( $id, $expected, $status, $resolution ) {\n\t\t$ready=self::runtime_ready();if(is_wp_error($ready)){return$ready;}\n\t\t$id=sanitize_text_field((string)$id);$auth=GCU_Capabilities::require_capability(GCU_Capabilities::MANAGE_CONTENT,$id,'future_intelligence_content');if(is_wp_error($auth)){return$auth;}"),
("\tpublic static function resolve_report() {\n\t\tcheck_admin_referer( 'gcu_future_resolve_report' );\n\t\tif ( ! self::can_manage_content() ) {\n\t\t\twp_die( esc_html__( 'You are not authorized to review File 14 reports.', 'global-clinic-usp-integration' ) );\n\t\t}\n\t\t$id = isset( $_POST['report_id'] ) ? sanitize_text_field( wp_unslash( $_POST['report_id'] ) ) : '';",
 "\tpublic static function resolve_report() {\n\t\tcheck_admin_referer( 'gcu_future_resolve_report' );\n\t\t$id = isset( $_POST['report_id'] ) ? sanitize_text_field( wp_unslash( $_POST['report_id'] ) ) : '';\n\t\tif ( ! GCU_Capabilities::can( GCU_Capabilities::MANAGE_CONTENT, $id, 'future_intelligence_content' ) ) {\n\t\t\twp_die( esc_html__( 'You are not authorized to review this File 14 report.', 'global-clinic-usp-integration' ) );\n\t\t}"),
("\tpublic static function can_approve_claims() {\n\t\treturn GCU_Capabilities::can( GCU_Capabilities::APPROVE_CLAIMS, null, 'future_intelligence_claims' );\n\t}",
 "\tpublic static function can_approve_claims() {\n\t\treturn GCU_Capabilities::can( GCU_Capabilities::APPROVE_CLAIMS, null, 'future_intelligence_claims' );\n\t}\n\tpublic static function can_revalidate_claim( WP_REST_Request $request ) {\n\t\t$key = sanitize_key( (string) $request['claim_key'] );\n\t\treturn GCU_Capabilities::can( GCU_Capabilities::APPROVE_CLAIMS, $key, 'future_intelligence_claims' );\n\t}")
]

for old, new in repls:
    count = s.count(old)
    if count != 1:
        raise SystemExit(f'Expected exactly one match, found {count}: {old[:100]!r}')
    s = s.replace(old, new, 1)

p.write_text(s, encoding='utf-8')
print('Round 02 authorization corrections applied.')
