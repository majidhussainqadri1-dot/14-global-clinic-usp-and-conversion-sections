from pathlib import Path

policy = Path('14-global-clinic-usp-integration/includes/class-gcu-future-policy.php')
p = policy.read_text(encoding='utf-8')
anchor = "\tpublic static function dark_pattern_scan( $text ) {"
method = r'''	public static function business_policy_contradiction_scan( $text ) {
		$text = self::normalize_text( $text );
		$flags = array();
		$patterns = array(
			'nonzero_platform_commission' => '/\b(?:[1-9]\d?(?:\.\d+)?|100)\s*%\s*(?:platform\s*)?commission\b|\b(?:platform\s*)?commission\s*(?:is|of|:)\s*(?:[1-9]\d?(?:\.\d+)?|100)\s*%/i',
			'paid_core_tier' => '/\b(?:paid|premium|pro|subscription)\s+(?:core\s+)?tier\b|\b(?:core|basic)\s+(?:features?|service)\s+(?:require|requires|need|needs)\s+(?:payment|subscription|fee)/i',
			'support_buys_status' => '/\b(?:donat(?:e|ion)|support|payment|pay)\b[^.!?]{0,80}\b(?:buys?|improves?|increases?|boosts?|gets?)\b[^.!?]{0,60}\b(?:rank|ranking|visibility|verification|priority|approval)\b/i',
			'approval_guarantee' => '/\b(?:guaranteed|automatic|instant|certain)\s+(?:doctor\s+)?(?:approval|verification|activation)\b|\b(?:approval|verification|activation)\s+(?:is\s+)?guaranteed\b/i',
			'outcome_guarantee' => '/\b(?:guaranteed|certain|assured)\s+(?:cure|income|outcome|result)\b|\b(?:cure|income|outcome|result)\s+(?:is\s+)?guaranteed\b/i',
		);
		foreach ( $patterns as $key => $pattern ) {
			if ( preg_match( $pattern, $text ) ) { $flags[] = 'business_policy_contradiction:' . $key; }
		}
		return array( 'safe' => empty( $flags ), 'flags' => $flags );
	}

'''
if p.count(anchor) != 1:
    raise SystemExit(f'policy method anchor count={p.count(anchor)}')
p = p.replace(anchor, method + anchor, 1)
old = "\t\t$dark = self::dark_pattern_scan( $current );\n\t\t$semantic = $prior ? self::semantic_risk_scan( $prior, $current ) : array( 'safe' => true, 'risk' => 'low', 'flags' => array() );\n\t\t$flags = array_merge( $dark['flags'], $semantic['flags'] );\n\t\treturn array( 'safe' => empty( $flags ), 'flags' => array_values( array_unique( $flags ) ), 'dark_pattern' => $dark, 'semantic' => $semantic );"
new = "\t\t$dark = self::dark_pattern_scan( $current );\n\t\t$business = self::business_policy_contradiction_scan( $current );\n\t\t$semantic = $prior ? self::semantic_risk_scan( $prior, $current ) : array( 'safe' => true, 'risk' => 'low', 'flags' => array() );\n\t\t$flags = array_merge( $dark['flags'], $business['flags'], $semantic['flags'] );\n\t\treturn array( 'safe' => empty( $flags ), 'flags' => array_values( array_unique( $flags ) ), 'dark_pattern' => $dark, 'business_policy' => $business, 'semantic' => $semantic );"
if p.count(old) != 1:
    raise SystemExit(f'copy preflight anchor count={p.count(old)}')
p = p.replace(old, new, 1)
policy.write_text(p, encoding='utf-8')

future = Path('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
f = future.read_text(encoding='utf-8')
old2 = "\t\t\tforeach ( $blocks as $block ) {\n\t\t\t\t$scan = GCU_Future_Policy::dark_pattern_scan( implode( ' ', array( $block['title'], wp_strip_all_tags( $block['body'] ), $block['cta_label'] ) ) );\n\t\t\t\tforeach ( $scan['flags'] as $flag ) {\n\t\t\t\t\t$issues[] = 'active_copy:' . $flag;\n\t\t\t\t}\n\t\t\t}"
new2 = "\t\t\tforeach ( $blocks as $block ) {\n\t\t\t\t$active_text = implode( ' ', array( $block['title'], wp_strip_all_tags( $block['body'] ), $block['cta_label'] ) );\n\t\t\t\t$scan = GCU_Future_Policy::dark_pattern_scan( $active_text );\n\t\t\t\t$business = GCU_Future_Policy::business_policy_contradiction_scan( $active_text );\n\t\t\t\tforeach ( array_merge( $scan['flags'], $business['flags'] ) as $flag ) {\n\t\t\t\t\t$issues[] = 'active_copy:' . $flag;\n\t\t\t\t}\n\t\t\t}"
if f.count(old2) != 1:
    raise SystemExit(f'parity anchor count={f.count(old2)}')
f = f.replace(old2, new2, 1)
future.write_text(f, encoding='utf-8')

test = Path('tests/twelfth-cycle-round06-regression-tests.php')
test.write_text(r'''<?php
$root = dirname( __DIR__ );
$policy = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-policy.php' );
$future = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
$failures = array();
function r12r06_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
r12r06_assert( false !== strpos( $policy, 'business_policy_contradiction_scan' ), 'Business-policy contradiction scanner must exist.' );
r12r06_assert( false !== strpos( $policy, 'nonzero_platform_commission' ), 'Non-zero platform commission copy must be blocked.' );
r12r06_assert( false !== strpos( $policy, '$business = self::business_policy_contradiction_scan( $current );' ), 'Copy preflight must include business-policy contradiction flags.' );
r12r06_assert( false !== strpos( $future, 'GCU_Future_Policy::business_policy_contradiction_scan( $active_text )' ), 'Active-copy parity sentinel must scan business-policy contradictions.' );
if ( $failures ) { fwrite( STDERR, "Twelfth-cycle Round 06 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Twelfth-cycle Round 06 regression tests: PASS\n";
''', encoding='utf-8')

quality = Path('scripts/quality.sh')
q = quality.read_text(encoding='utf-8')
anchorq = 'php "$ROOT/tests/twelfth-cycle-round05-regression-tests.php"\n'
if q.count(anchorq) != 1:
    raise SystemExit(f'quality anchor count={q.count(anchorq)}')
q = q.replace(anchorq, anchorq + 'php "$ROOT/tests/twelfth-cycle-round06-regression-tests.php"\n', 1)
quality.write_text(q, encoding='utf-8')
print('Round 06 business-policy semantic hardening applied.')
