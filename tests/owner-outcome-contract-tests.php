<?php
/** Isolated source/policy contract tests. Not a real WordPress E2E test. */
define( 'ABSPATH', __DIR__ . '/' );
function wp_json_encode( $x ) { return json_encode( $x ); }
class GCU_Policy {
	public static $consent = true;
	public static function analytics_consent() { return self::$consent; }
}
class GCU_Privacy {
	public static $gpc = false;
	public static $low = false;
	public static function global_privacy_control_requested() { return self::$gpc; }
	public static function low_bandwidth_requested() { return self::$low; }
}
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-owner-outcomes.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
$f = array();
function check_owner( $ok, $message ) { global $f; if ( ! $ok ) { $f[] = $message; } }
check_owner( GCU_Owner_Outcomes::consented(), 'Explicit analytics consent should permit optional aggregate observation.' );
GCU_Policy::$consent = false;
check_owner( ! GCU_Owner_Outcomes::consented(), 'No analytic consent must deny event.' );
GCU_Policy::$consent = true; GCU_Privacy::$gpc = true;
check_owner( ! GCU_Owner_Outcomes::consented(), 'GPC must deny event.' );
GCU_Privacy::$gpc = false; GCU_Privacy::$low = true;
check_owner( ! GCU_Owner_Outcomes::consented(), 'Low-data choice must deny optional measurement.' );
check_owner( GCU_Owner_Outcomes::reportable( 'File08.BookingStarted.v1', 'booking_started' ), 'Canonical booking fact should classify.' );
check_owner( GCU_Owner_Outcomes::reportable( 'File09.ApplicationStarted.v1', 'application_started' ), 'Canonical onboarding fact should classify.' );
check_owner( GCU_Owner_Outcomes::reportable( 'File07.DirectoryServed.v1', 'destination_served' ), 'Owner-served response should classify separately from browser-loaded.' );
check_owner( ! GCU_Owner_Outcomes::reportable( 'ClinicUSPFunnelEvent', 'booking_started' ), 'Browser events can never verify booking.' );
check_owner( ! GCU_Owner_Outcomes::reportable( 'File08.BookingStarted.v1', 'application_started' ), 'Cross-owner stage must not classify.' );
check_owner( ! GCU_Future_Policy::experiment_requires_baseline_restore( array( 'headline A', 'headline B' ) ), 'Nonplacement copy variants should not imply placements changed.' );
check_owner( GCU_Future_Policy::experiment_requires_baseline_restore( array( array( 'placement_key' => 'hero' ) ) ), 'Placement-mutating variant requires approved restore.' );
check_owner( GCU_Future_Policy::experiment_requires_baseline_restore( array( 'change layout', 'headline B' ) ), 'Unstructured layout variant must fail closed.' );
check_owner( ! GCU_Future_Policy::experiment_baseline_restoration_ready( array( 'status'=>'approved' ) ), 'An undocumented snapshot must not become approved merely through flags.' );
$repo = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-repository.php' );
check_owner( false !== strpos( $repo, 'gcu_experiment_baseline_restore_unverified' ), 'Native experiment transition lacks fail-closed baseline gate.' );
$main = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/global-clinic-usp-integration.php' );
check_owner( false !== strpos( $main, 'GCU_Owner_Outcomes' ), 'Owner consumer is not bootstrapped.' );
if ( $f ) { fwrite( STDERR, implode( PHP_EOL, $f ) . PHP_EOL ); exit( 1 ); }
echo "File14 owner outcomes and baseline containment: PASS (source-only; two release gates still open)\n";
