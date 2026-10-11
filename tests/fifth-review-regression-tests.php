<?php

$root = dirname( __DIR__ );
$plugin = $root . '/14-global-clinic-usp-integration';
$main = file_get_contents( $plugin . '/global-clinic-usp-integration.php' );
$hardening = file_get_contents( $plugin . '/includes/class-gcu-fifth-review-hardening.php' );
$readme = file_get_contents( $plugin . '/readme.txt' );
$status = file_get_contents( $root . '/STATUS.md' );
$rest = file_get_contents( $plugin . '/includes/class-gcu-rest.php' );
$future_policy = file_get_contents( $plugin . '/includes/class-gcu-future-policy.php' );
$failures = array();

function fifth_assert( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures[] = $message;
	}
}

fifth_assert( false !== strpos( $main, 'Version: 1.4.25' ), 'Plugin header must be v1.4.25.' );
fifth_assert( false !== strpos( $main, "define( 'GCU_VERSION', '1.4.25' )" ), 'GCU_VERSION must be v1.4.25.' );
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
fifth_assert( false !== strpos( $hardening, 'begin_owned_transaction()' ), 'Early-stop must start the shared owner transaction.' );
fifth_assert( false !== strpos( $hardening, 'experiment_early_stopped' ), 'Early-stop audit event must remain mandatory.' );
fifth_assert( false !== strpos( $hardening, 'if ( false === $audit )' ), 'Audit failure must trigger rollback.' );
fifth_assert( false !== strpos( $hardening, 'rollback_owned_transaction()' ) && false !== strpos( $hardening, 'commit_owned_transaction()' ), 'Early-stop must have explicit rollback/commit paths.' );

fifth_assert( false !== strpos( $readme, 'Stable tag: 1.4.25' ), 'Readme stable tag must match v1.4.25.' );
fifth_assert( false !== strpos( $readme, 'File 20 remains the sole owner of the global shell/navigation' ) && false !== strpos( $readme, 'only when File 20 is absent' ), 'File 20 sole global shell ownership and bounded recovery behavior must be stated accurately.' );
fifth_assert( false === strpos( $readme, 'future record/report updates' ), 'Readme must not falsely claim generic future record/report transactionality.' );
fifth_assert( false !== strpos( $status, 'Historical eighty-pass ledgers:' ) && substr_count( $status, 'docs/REVIEW-80-' ) >= 6, 'Status must retain all six historical review ledgers accurately.' );
fifth_assert( false !== strpos( $status, 'REVIEW-80-FIFTH-LEDGER-v1.4.3.md' ), 'Fifth ledger must remain part of release truth with its historical release identity.' );

// R19: ensure legacy REST paths invoke the exact typed validator before DB/state operations.
fifth_assert( false !== strpos( $rest, "GCU_Future_Policy::bounded_query_integer(\$r->get_param('days'),30,90)" ), 'Base funnel must use bounded days rather than absint.' );
fifth_assert( false !== strpos( $rest, "GCU_Future_Policy::strict_mutation_version(\$r->get_param('expected_version'))" ), 'Base workflows and claim withdrawal must validate optimistic versions.' );
fifth_assert( false !== strpos( $rest, "GCU_Future_Policy::safe_workflow_target(\$r->get_param('target'))" ), 'Workflow target must be exact valid text.' );
fifth_assert( false !== strpos( $rest, 'gcu_workflow_route_invalid' ) && false !== strpos( $rest, 'gcu_claim_route_invalid' ), 'Owner route authorization must reject invalid route identity.' );
fifth_assert( false !== strpos( $future_policy, 'function strict_mutation_version' ), 'Strict pure mutation validator must ship.' );
fifth_assert( false !== strpos( $hardening, 'gcu_fifth_ai_input_type_invalid' ) && false !== strpos( $hardening, 'candidate_text_invalid' ), 'AI input/output guards must reject non-text values.' );

// R20 regression: public filters/events and pre-callback governance are type-gated.
fifth_assert( false !== strpos( $rest, 'gcu_public_blocks_query_invalid' ) && false !== strpos( $rest, 'public_blocks_query_is_typed' ), 'Public block filters must be validated.' );
fifth_assert( false !== strpos( $rest, 'gcu_public_event_input_invalid' ) && false !== strpos( $rest, 'acquisition_event_input_is_typed' ), 'Public acquisition fields must be validated.' );
fifth_assert( false !== strpos( $hardening, 'gcu_fifth_governance_input_type_invalid' ) && false !== strpos( $hardening, 'validate_future_record_input_types' ), 'Founder pre-callback guard must validate types first.' );
fifth_assert( false !== strpos( $future_policy, 'function public_blocks_query_is_typed' ) && false !== strpos( $future_policy, 'function acquisition_event_input_is_typed' ), 'Pure typed public input helpers must be present.' );

// R21: suppressed duplicate INSERT cannot be reported as success without same-identity SELECT FOR UPDATE.
$repository = file_get_contents( $plugin . '/includes/class-gcu-repository.php' );
fifth_assert( false !== strpos( $repository, 'if(0===$ins)' ) && false !== strpos( $repository, 'FOR UPDATE' ) && false !== strpos( $repository, 'same_acquisition_event_identity' ), 'Zero-row duplicate event requires locked exact-identity proof.' );
fifth_assert( false !== strpos( $repository, 'gcu_conversion_event_identity_conflict' ) && false !== strpos( $repository, 'gcu_event_dedup_unverified' ), 'Conflicting or unprovable duplicate event must fail closed.' );
fifth_assert( false !== strpos( $repository, 'gcu_public_event_uuid_invalid' ) && false !== strpos( $repository, 'strtolower($d[\'event_id\'])' ), 'Invalid explicit UUID must reject; accepted UUIDs must be canonicalized.' );
fifth_assert( false !== strpos( $future_policy, 'same_acquisition_event_identity' ) && false !== strpos( $future_policy, "'ur-PK', 'ur_PK', 'ar', 'ar-SA'" ), 'Identity helper and exact approved locale guard must ship.' );

if ( $failures ) {
	fwrite( STDERR, "Fifth review regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Fifth review regression tests: PASS\n";
