<?php
/** Isolated source/policy contract tests. Not a real WordPress E2E test. */
define( 'ABSPATH', __DIR__ . '/' );
function wp_json_encode( $x ) { return json_encode( $x ); }
function get_option( $key, $default = false ) { return $default; }
function sanitize_key( $key ) { return preg_replace( '/[^a-z0-9_\\-]/', '', strtolower( (string) $key ) ); }
class GCU_Policy {
	public static $consent = true;
	public static function analytics_consent() { return self::$consent; }
}
class GCU_Privacy {
	public static $gpc = false;
	public static $low = false;
	public static $sensitive = false;
	public static function measurement_allowed() { return GCU_Policy::analytics_consent() && ! self::$gpc && ! self::$sensitive; }
	public static function global_privacy_control_requested() { return self::$gpc; }
	public static function low_bandwidth_requested() { return self::$low; }
}
define( 'ARRAY_A', 'ARRAY_A' );
class GCU_Install { public static function tables() { return array( 'events' => 'wp_gcu_events' ); } }
class OwnerOutcomeTestDB {
	public $rows = array();
	public function get_results( $sql, $return_type = ARRAY_A ) { return $this->rows; }
}
$wpdb = new OwnerOutcomeTestDB();
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-owner-outcomes.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-experiment-baseline.php';
$f = array();
function check_owner( $ok, $message ) { global $f; if ( ! $ok ) { $f[] = $message; } }
check_owner( GCU_Owner_Outcomes::consented(), 'Explicit analytics consent should permit optional aggregate observation.' );
GCU_Policy::$consent = false;
check_owner( ! GCU_Owner_Outcomes::consented(), 'No analytic consent must deny event.' );
GCU_Policy::$consent = true; GCU_Privacy::$gpc = true;
check_owner( ! GCU_Owner_Outcomes::consented(), 'GPC must deny event.' );
GCU_Privacy::$gpc = false; GCU_Privacy::$low = true;
check_owner( ! GCU_Owner_Outcomes::consented(), 'Low-data choice must deny optional measurement.' );
GCU_Privacy::$low = false; GCU_Privacy::$sensitive = true;
check_owner( ! GCU_Owner_Outcomes::consented(), 'A sensitive booking/onboarding path must fail closed despite opt-in.' );
GCU_Privacy::$sensitive = false;
check_owner( GCU_Owner_Outcomes::consented(), 'Nonsensitive opted-in owner route may record a bounded event.' );
check_owner( GCU_Owner_Outcomes::reportable( 'File08.BookingStarted.v1', 'booking_started' ), 'Canonical booking fact should classify.' );
check_owner( GCU_Owner_Outcomes::reportable( 'File09.ApplicationStarted.v1', 'application_started' ), 'Canonical onboarding fact should classify.' );
check_owner( GCU_Owner_Outcomes::reportable( 'File07.DirectoryServed.v1', 'destination_served' ), 'Owner-served response should classify separately from browser-loaded.' );
check_owner( ! GCU_Owner_Outcomes::reportable( 'ClinicUSPFunnelEvent', 'booking_started' ), 'Browser events can never verify booking.' );
check_owner( ! GCU_Owner_Outcomes::reportable( 'File08.BookingStarted.v1', 'application_started' ), 'Cross-owner stage must not classify.' );
check_owner( ! GCU_Future_Policy::experiment_requires_baseline_restore( array( 'headline A', 'headline B' ) ), 'Nonplacement copy variants should not imply placements changed.' );
check_owner( GCU_Future_Policy::experiment_requires_baseline_restore( array( array( 'placement_key' => 'hero' ) ) ), 'Placement-mutating variant requires approved restore.' );
check_owner( GCU_Future_Policy::experiment_requires_baseline_restore( array( 'change layout', 'headline B' ) ), 'Unstructured layout variant must fail closed.' );
check_owner( ! GCU_Future_Policy::experiment_baseline_restoration_ready( array( 'status'=>'approved' ) ), 'An undocumented snapshot must not become approved merely through flags.' );
check_owner( array( 'primary' ) === GCU_Experiment_Baseline::placement_keys( array( array( 'placement_key'=>'primary' ), array( 'placement_key'=>'primary' ) ) ), 'Snapshot scope must deduplicate exact placement keys.' );
check_owner( null === GCU_Experiment_Baseline::placement_keys( array( array( 'layout'=>'header' ), array( 'placement_key'=>'primary' ) ) ), 'Ambiguous layout effects must not silently receive a baseline.' );
check_owner( null === GCU_Experiment_Baseline::placement_keys( array( array( 'placement_key'=>'Primary!' ), array( 'placement_key'=>'primary' ) ) ), 'Noncanonical placement identity must be rejected.' );
// The detector only describes opt-in volume when both independent windows
// meet minimum cohorts. It MUST NOT manufacture a linked conversion rate.
$wpdb->rows = array( array( 'event_type' => 'File08.BookingStarted.v1', 'funnel_stage' => 'booking_started', 'current_count' => 11, 'previous_seven_days' => 210 ) );
$observed = GCU_Owner_Outcomes::observed_anomaly();
check_owner( 'consented_owner_volume_observed' === $observed['status'], 'Mature owner-only cohort should permit independent volume analysis.' );
check_owner( 'high' === $observed['severity'], 'A >=60% native-volume drop should register high severity.' );
check_owner( null === $observed['booking_success_rate'] && false === $observed['owner_correlated_funnel'], 'Owner volume cannot masquerade as a booking conversion rate.' );
$wpdb->rows = array( array( 'event_type' => 'File08.BookingStarted.v1', 'funnel_stage' => 'booking_started', 'current_count' => 9, 'previous_seven_days' => 140 ) );
$small = GCU_Owner_Outcomes::observed_anomaly();
check_owner( true === $small['suppressed'] && empty( $small['signals'] ), 'Small native cohort must suppress detailed anomalies.' );
$wpdb->rows = array( array( 'event_type' => 'File08.BookingStarted.v1', 'funnel_stage' => 'application_started', 'current_count' => 35, 'previous_seven_days' => 200 ) );
$spoofed = GCU_Owner_Outcomes::observed_anomaly();
check_owner( empty( $spoofed['signals'] ), 'Cross-owner stage confusion must fail closed.' );
$repo = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-repository.php' );
check_owner( false !== strpos( $repo, 'gcu_experiment_baseline_restore_unverified' ), 'Native experiment transition lacks fail-closed baseline gate.' );
$main = file_get_contents( __DIR__ . '/../14-global-clinic-usp-integration/global-clinic-usp-integration.php' );
check_owner( false !== strpos( $main, 'GCU_Owner_Outcomes' ), 'Owner consumer is not bootstrapped.' );
if ( $f ) { fwrite( STDERR, implode( PHP_EOL, $f ) . PHP_EOL ); exit( 1 ); }
echo "File14 owner outcomes and baseline containment: PASS (source-only; two release gates still open)\n";
