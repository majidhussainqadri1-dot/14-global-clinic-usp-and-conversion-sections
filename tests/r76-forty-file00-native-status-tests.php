<?php
/**
 * R76: 40 native original status denial mocks and two canonical approvals.
 * File00 smc_membership_state grants approved (regular) and verified
 * (institutional), not an open-ended list of future or malformed statuses.
 * No running WordPress installation is used by this PHP harness.
 */
define( 'ABSPATH', __DIR__ );
define( 'SMC_VERSION', '1.2.44' );
define( 'SMC_CONTRACT_VERSION', '1.2.3' );
class WP_Error {
    public function __construct( $code, $message = '' ) {}
}
function __( $message, $domain = '' ) { return $message; }
function absint( $value ) { return abs( (int) $value ); }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function get_current_user_id() { return 42; }
function has_filter( $key ) { return false; }
function sanitize_key( $key ) {
    return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $key ) );
}
function apply_filters( $key, $claim, $subject = null ) { return $claim; }
class SMC_Contracts {
    public static $status = 'approved';
    public static function assertions( $user_id ) {
        return array(
            'contract_version' => '1.2.3',
            'user_id' => $user_id,
            'status' => self::$status,
            'eligible' => true,
            'suspended' => false,
        );
    }
}
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-companion-adapters.php';
$base = array(
    'not_enrolled', 'draft', 'guardian_pending', 'submitted',
    'under_review', 'more_information', 'resubmitted', 'approval_pending',
    'rejected', 'suspended', 'expired', 'appeal_review', 'erasure_pending',
    'invalid_application', 'effects_reconciliation', 'active', 'pending',
    'approved ', ' verified', 'unknown',
);
if ( 20 !== count( $base ) ) { fwrite( STDERR, "Expected 20 status fixtures\n" ); exit( 1 ); }
$errors = array();
for ( $n = 1; $n <= 40; ++$n ) {
    $raw = $base[ ( $n - 1 ) % 20 ];
    SMC_Contracts::$status = $n <= 20 ? $raw : $raw . "\n";
    $result = GCU_Companion_Adapters::authorize( 'gcu_manage', null, 'native_owner_status' );
    if ( false !== $result ) {
        $errors[] = sprintf( 'R76 native status case %02d incorrectly granted', $n );
    }
    printf( "R76 canonical status %02d/40 %s\n", $n, false === $result ? 'PASS' : 'FAIL' );
}
foreach ( array( 'approved', 'verified' ) as $valid ) {
    SMC_Contracts::$status = $valid;
    if ( true !== GCU_Companion_Adapters::authorize( 'gcu_manage', null, 'native_owner_status' ) ) {
        $errors[] = 'R76 rejected actual File00 canonical status ' . $valid;
    }
}
if ( $errors ) { fwrite( STDERR, implode( "\n", $errors ) . "\n" ); exit( 1 ); }
echo "R76 40/40 strict original File00 status checks PASS; approved and verified allowed; source PHP mock only.\n";
