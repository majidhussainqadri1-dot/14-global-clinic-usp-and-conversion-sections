<?php

$root = dirname( __DIR__ );
$plugin = $root . '/14-global-clinic-usp-integration';
$main = file_get_contents( $plugin . '/global-clinic-usp-integration.php' );
$hardening = file_get_contents( $plugin . '/includes/class-gcu-fifth-review-hardening.php' );
$readme = file_get_contents( $plugin . '/readme.txt' );
$status = file_get_contents( $root . '/STATUS.md' );
$failures = array();

function fifth_assert( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures[] = $message;
	}
}

fifth_assert( false !== strpos( $main, 'Version: 1.4.3' ), 'Plugin header must be v1.4.3.' );
fifth_assert( false !== strpos( $main, "define( 'GCU_VERSION', '1.4.3' )" ), 'GCU_VERSION must be v1.4.3.' );
fifth_assert( false !== strpos( $main, 'class-gcu-fifth-review-hardening.php' ), 'Fifth-review hardening class must ship.' );
fifth_assert( false !== strpos( $main, "GCU_Fifth_Review_Hardening', 'bootstrap" ), 'Fifth-review hardening must bootstrap.' );

fifth_assert( false !== strpos( $hardening, 'pre_option_gcu_enabled' ), 'Runtime schema truth must gate gcu_enabled reads.' );
fifth_assert( false !== strpos( $hardening, 'GCU_Install::verify_schema()' ), 'Base schema must be verified at runtime.' );
fifth_assert( false !== strpos( $hardening, 'GCU_Future_Intelligence::verify_schema()' ), 'Future schema must be verified at runtime.' );
fifth_assert( false !== strpos( $hardening, 'return self::$schema_gate ? $pre_option : 0;' ), 'Schema drift must fail closed.' );

fifth_assert( false !== strpos( $hardening, "'/gcu/v1/future/records'" ), 'Future record route must be governed.' );
fifth_assert( false !== strpos( $hardening, "'future_public_governance'" ), 'Public Future governance approval purpose must be explicit.' );
fifth_assert( false !== strpos( $hardening, 'GCU_Capabilities::APPROVE_CLAIMS' ), 'Active/public Future governance must require Founder-level approval.' );

fifth_assert( false !== strpos( $hardening, "'/gcu/v1/future/ai-copy'" ), 'AI copy route must be hardened.' );
fifth_assert( false !== strpos( $hardening, 'gcu_fifth_ai_sensitive_input_blocked' ), 'Sensitive AI-copy input must fail closed.' );
fifth_assert( false !== strpos( $hardening, 'question_contains_sensitive_data' ), 'Existing multilingual sensitive-data detector must be reused.' );
fifth_assert( false !== strpos( $hardening, 'multilingual_dark_pattern_scan' ), 'AI output must pass multilingual dark-pattern scanning.' );
fifth_assert( false !== strpos( $hardening, "'multilingual_guard_applied'" ), 'AI response must expose that the multilingual guard ran.' );

fifth_assert( false !== strpos( $hardening, "'/gcu/v1/events'" ), 'Conversion event route must be hardened.' );
fifth_assert( false !== strpos( $hardening, 'gcu_conversion_event_identity_conflict' ), 'Conflicting conversion event UUID reuse must be rejected.' );
fifth_assert( false !== strpos( $hardening, 'funnel_stage,destination_key,subject_hash,source_value,medium_value,campaign_value,ref_value' ), 'Event identity comparison must cover stage, destination, subject and campaign context.' );

fifth_assert( false !== strpos( $hardening, "remove_action( 'gcu_future_hourly_intelligence'" ), 'Legacy non-transactional hourly worker must be replaced.' );
fifth_assert( false !== strpos( $hardening, 'transactional_early_stop_guard' ), 'Transactional early-stop implementation must be active.' );
fifth_assert( false !== strpos( $hardening, 'START TRANSACTION' ), 'Early-stop must start a database transaction.' );
fifth_assert( false !== strpos( $hardening, 'experiment_early_stopped' ), 'Early-stop audit event must remain mandatory.' );
fifth_assert( false !== strpos( $hardening, 'if ( false === $audit )' ), 'Audit failure must trigger rollback.' );
fifth_assert( false !== strpos( $hardening, 'ROLLBACK' ) && false !== strpos( $hardening, 'COMMIT' ), 'Early-stop must have explicit rollback/commit paths.' );

fifth_assert( false !== strpos( $readme, 'Stable tag: 1.4.3' ), 'Readme stable tag must match v1.4.3.' );
fifth_assert( false === strpos( $readme, 'bounded local fallback' ), 'Stale local shell fallback claim must remain removed.' );
fifth_assert( false !== strpos( $readme, 'does not emit a local Back/Home shell fallback' ), 'File 20 sole shell ownership must be stated accurately.' );
fifth_assert( false === strpos( $readme, 'future record/report updates' ), 'Readme must not falsely claim generic future record/report transactionality.' );
fifth_assert( false !== strpos( $status, 'The five repository review ledgers are:' ), 'Status must count all five review ledgers accurately.' );
fifth_assert( false !== strpos( $status, 'REVIEW-80-FIFTH-LEDGER-v1.4.3.md' ), 'Fifth ledger must be part of release truth.' );

if ( $failures ) {
	fwrite( STDERR, "Fifth review regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Fifth review regression tests: PASS\n";
