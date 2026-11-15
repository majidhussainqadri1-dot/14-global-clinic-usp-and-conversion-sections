<?php
/**
 * R75: Forty direct File14 source-method adversarial cases.
 * Numeric coercion is not authority: SMC_Contracts must never be called with
 * a synthesized ID. This is a PHP test double, NOT a WordPress staging test.
 */
define( 'ABSPATH', __DIR__ );
define( 'SMC_VERSION', '1.2.44' );
define( 'SMC_CONTRACT_VERSION', '1.2.3' );
class WP_Error {
    private $code;
    public function __construct( $code, $message = '' ) { $this->code = $code; }
    public function get_error_code() { return $this->code; }
}
function __( $message, $domain = '' ) { return $message; }
function is_wp_error( $v ) { return $v instanceof WP_Error; }
function absint( $n ) { return abs( (int) $n ); }
function apply_filters( $key, $claim, $subject = null ) { return $claim; }
class SMC_Contracts {
    public static $calls = 0;
    public static $seen = array();
    public static function assertions( $user_id ) {
        ++self::$calls;
        self::$seen[] = $user_id;
        return array(
            'contract_version' => '1.2.3',
            'user_id' => $user_id, 'status' => 'approved',
            'eligible' => true, 'suspended' => false,
        );
    }
}
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-companion-adapters.php';

$invalid = array(
    '42', '042', '42junk', ' 42', '42 ', '+42', '-42',
    '4.2', '42.0', '4.2e1', '0x2a', '0b101010', '042.00',
    42.0, 42.9, -42, 0, -1, null, false, true,
    array(42), array(), new stdClass(), '0', '1e2',
    '42\n', "42\n", "\t42", '00', ' 0 ',
    '1000000000000000000000000000000',
    PHP_INT_MAX . '0', -0.1, 0.0, 1.0, 0.5,
    '00042', '۴۲', '٤٢',
);
if ( count( $invalid ) !== 40 ) { fwrite( STDERR, "Invalid fixture count\n" ); exit( 1 ); }
$failed = array();
foreach ( $invalid as $i => $candidate ) {
    $before = SMC_Contracts::$calls;
    $result = GCU_Companion_Adapters::file00_assertions( $candidate );
    if ( ! is_wp_error( $result ) || 'gcu_file00_subject_invalid' !== $result->get_error_code()
        || $before !== SMC_Contracts::$calls ) {
        $failed[] = 'R75 case ' . ( $i + 1 ) . ' permitted coercion into owner';
    }
}
$valid = GCU_Companion_Adapters::file00_assertions( 42 );
if ( ! is_array( $valid ) || 42 !== ( $valid['user_id'] ?? null )
    || 1 !== SMC_Contracts::$calls || SMC_Contracts::$seen !== array( 42 ) ) {
    $failed[] = 'R75 valid WordPress integer subject did not survive';
}
if ( $failed ) {
    fwrite( STDERR, implode( "\n", $failed ) . "\n" );
    exit( 1 );
}
echo "R75 40/40 direct invalid-subject PHP tests PASS; canonical integer 42 PASS; source-only, no staging/live assertion.\n";
