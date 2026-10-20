<?php
/**
 * R73: Forty direct REAL GCU_Companion_Adapters method cases.
 * WordPress and File00 are mocked solely at the public contract boundary.
 * This proves fail-closed PHP behavior, NOT staging or deployed parity.
 */
define('ABSPATH', __DIR__);
define('SMC_VERSION', '1.2.44');
define('SMC_CONTRACT_VERSION', '1.2.3');
class WP_Error {
    public function __construct($code, $message = '') {}
}
function __($value, $textdomain = '') { return $value; }
function absint($value) { return abs((int) $value); }
function is_wp_error($value) { return $value instanceof WP_Error; }
function get_current_user_id() { return 42; }
function sanitize_key($value) { return preg_replace('/[^a-z0-9_-]/', '', strtolower((string) $value)); }
$GLOBALS['r73_filter'] = null;
$GLOBALS['r73_has_filter'] = false;
function has_filter($name) { return 'gcu_authorize' === $name && $GLOBALS['r73_has_filter'] ? 10 : false; }
function apply_filters($name, $value) {
    return 'gcu_authorize' === $name ? $GLOBALS['r73_filter'] : $value;
}
class SMC_Contracts {
    public static $claim = array();
    public static function assertions($user_id) { return self::$claim; }
}
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-companion-adapters.php';

$values = array(
    2 => array(false, 'false', 'true', 1),
    3 => array(0, '0', null, array()),
    4 => array(true, 'false', 'true', 1),
    5 => array(0, '0', null, array()),
    6 => array('42', 42.0, 43, -42),
    7 => array('false', 'true', 1, array('yes')),
    8 => array(0, null, false, new stdClass()),
    9 => array('suspended', 'rejected', 'erasure_pending', 'invalid_application'),
);
$errors = array();
for ($case = 1; $case <= 40; $case++) {
    $family = ($case - 1) % 10;
    $variant = intdiv($case - 1, 10);
    SMC_Contracts::$claim = array(
        'user_id' => 42, 'contract_version' => '1.2.3',
        'eligible' => true, 'suspended' => false,
        'status' => 'approved',
    );
    $GLOBALS['r73_filter'] = null;
    $GLOBALS['r73_has_filter'] = false;
    $expected = in_array($family, array(0, 1), true);
    if (1 === $family) {
        $GLOBALS['r73_filter'] = true;
        $GLOBALS['r73_has_filter'] = true;
    } elseif (2 === $family || 3 === $family) {
        SMC_Contracts::$claim['eligible'] = $values[$family][$variant];
    } elseif (4 === $family || 5 === $family) {
        SMC_Contracts::$claim['suspended'] = $values[$family][$variant];
    } elseif (6 === $family) {
        SMC_Contracts::$claim['user_id'] = $values[$family][$variant];
    } elseif (7 === $family || 8 === $family) {
        $GLOBALS['r73_filter'] = $values[$family][$variant];
        $GLOBALS['r73_has_filter'] = true;
    } elseif (9 === $family) {
        SMC_Contracts::$claim['status'] = $values[$family][$variant];
    }
    $result = GCU_Companion_Adapters::authorize('gcu_manage', null, 'case_' . $case);
    $valid = $result === $expected;
    printf("R73 File00 authorization case %02d/40 %s (family %d)\n", $case, $valid ? 'PASS' : 'FAIL', $family);
    if (!$valid) { $errors[] = 'R73 case ' . $case . ' failed'; }
}
if ($errors) {
    fwrite(STDERR, implode("\n", $errors) . "\n");
    exit(1);
}
echo "R73: 40/40 direct File14 File00-claim/filter authorization cases PASS; staging/live unverified.\n";
