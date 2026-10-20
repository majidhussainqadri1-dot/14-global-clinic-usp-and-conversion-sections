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

fifth_assert( false !== strpos( $main, 'Version: 1.4.72' ), 'Plugin header must be v1.4.72.' );
fifth_assert( false !== strpos( $main, "define( 'GCU_VERSION', '1.4.72' )" ), 'GCU_VERSION must be v1.4.72.' );
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

fifth_assert( false !== strpos( $readme, 'Stable tag: 1.4.72' ), 'Readme stable tag must match v1.4.72.' );
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

// R22: Future governance REST must not turn malformed versions into legitimate concurrency checks.
$future_source = file_get_contents( $plugin . '/includes/class-gcu-future-intelligence.php' );
fifth_assert( false !== strpos( $future_policy, 'function strict_record_expected_version' ) && false !== strpos( $future_policy, 'null === self::strict_record_expected_version' ), 'R22 Future expected-version typed validation is required.' );
fifth_assert( false !== strpos( $future_source, 'GCU_Future_Policy::strict_record_expected_version( $data[\'expected_version\'] )' ), 'Future record mutation must use strict expected version, never absint.' );
fifth_assert( false !== strpos( $future_source, 'GCU_Future_Policy::strict_mutation_version( $raw_version )' ), 'Future claim revalidation must use bounded exact version.' );
fifth_assert( false !== strpos( $future_source, 'GCU_Future_Policy::safe_workflow_target( $request->get_param( \'target\' ) )' ), 'Workflow early preflight cannot cast nested input to string.' );

// R23: FUT-12 public report cannot coerce nested JSON, manufacture unknown route/locale, or silently replace a client UUID.
$r23_future = file_get_contents( $plugin . '/includes/class-gcu-future-intelligence.php' );
fifth_assert( false !== strpos( $future_policy, 'function validate_copy_quality_report_input_types' ), 'R23 pure copy-report shape/owner validator must ship.' );
fifth_assert( false !== strpos( $r23_future, 'GCU_Future_Policy::validate_copy_quality_report_input_types( $data )' ) && false !== strpos( $r23_future, 'gcu_future_report_input_invalid' ), 'FUT-12 public report command must guard typed request before any sanitizer.' );
fifth_assert( false !== strpos( $r23_future, 'gcu_future_report_uuid_invalid' ), 'Supplied invalid report UUID must return explicit 400.' );
$r34_forbidden_report_route_fallback = <<<'PHP'
if ( ! in_array( $route, array( 'global_clinic', 'how_it_works' ), true ) ) { $route = 'global_clinic'; }
PHP;
fifth_assert( false === strpos( $r23_future, $r34_forbidden_report_route_fallback ), 'Unsupported report route must not silently map into a different public journey.' );

// R23: verify source CV IDs were not confused with 71 locally owned File14 code modules.
$r23_cv_trace = file_get_contents( $root . '/docs/CENTRAL-CV-71-OWNER-CONSUMER-TRACE-v1.4.27-R23.md' );
$r23_cv_ids = array_merge( range( 14, 25 ), range( 188, 199 ), range( 239, 285 ) );
foreach ( $r23_cv_ids as $r23_cv ) {
    fifth_assert( false !== strpos( $r23_cv_trace, '| CV-' . str_pad( (string) $r23_cv, 3, '0', STR_PAD_LEFT ) . ' |' ), 'Missing source-transferred central CV ID ' . $r23_cv . ' in File14 owner/consumer trace.' );
}
preg_match_all( '/^\\| CV-[0-9]{3} \\|/m', $r23_cv_trace, $r23_cv_occurrences );
fifth_assert( 71 === count( $r23_cv_occurrences[0] ), 'The central CV map must contain exactly 71 individual source IDs.' );
fifth_assert( false !== strpos( $r23_cv_trace, 'not a proven deployed owner assignment' ), 'Central CV map must not claim speculative owner mapping as live fact.' );

// R24 source-level checks: endpoints reject wrong shapes; public feedback cannot silently discard evidence.
$r24_future = file_get_contents( $plugin . '/includes/class-gcu-future-intelligence.php' );
fifth_assert( false !== strpos( $future_policy, 'function validate_handoff_context_input' ) && false !== strpos( $r24_future, 'gcu_future_handoff_context_invalid' ), 'FUT-02 must reject malformed explicit handoff context.' );
fifth_assert( false !== strpos( $future_policy, 'function validate_preflight_request_input' ) && false !== strpos( $r24_future, 'gcu_future_copy_preflight_input_invalid' ) && false !== strpos( $r24_future, 'gcu_future_experiment_preflight_input_invalid' ), 'FUT-10/16 must reject malformed preflight shapes.' );
fifth_assert( false !== strpos( $r24_future, 'gcu_future_copy_preflight_body_invalid' ) && false !== strpos( $r24_future, 'gcu_future_experiment_preflight_body_invalid' ), 'Preflight endpoints must reject non-object JSON bodies.' );
fifth_assert( false === strpos( $r24_future, "mb_substr( \$message, 0, 500 ) : substr( \$message, 0, 500 )" ) && false !== strpos( $future_policy, 'preg_match_all' ), 'FUT-12 cannot silently truncate feedback and must preserve UTF-8 character budget.' );

// R25: moderator resolution must be protected at the repository-facing mutation, not only the admin-post route.
$r25_source = file_get_contents( $plugin . '/includes/class-gcu-future-intelligence.php' );
$r25_mutate = substr( $r25_source, strpos( $r25_source, 'public static function resolve_report_record(' ), strpos( $r25_source, 'private static function reports_page(' ) - strpos( $r25_source, 'public static function resolve_report_record(' ) );
$r25_admin = substr( $r25_source, strpos( $r25_source, 'public static function resolve_report()' ), strpos( $r25_source, 'public static function admin_menu()' ) - strpos( $r25_source, 'public static function resolve_report()' ) );
fifth_assert( false !== strpos( $r25_mutate, 'GCU_Capabilities::require_capability(' ) && false !== strpos( $r25_mutate, "'future_intelligence_content'" ), 'R25 native report mutation requires File00 delegated editor authorization using approved purpose.' );
fifth_assert( false !== strpos( $future_policy, 'function validate_report_resolution_input' ) && false !== strpos( $r25_mutate, 'GCU_Future_Policy::validate_report_resolution_input(' ), 'R25 repository-facing report mutation requires exact typed version/identity/status.' );
fifth_assert( false === strpos( $r25_admin, 'absint( $_POST' ) && false !== strpos( $r25_admin, 'is_wp_error( $result )' ), 'R25 admin POST may not normalize negative report versions or silently swallow failed moderation.' );
fifth_assert( false !== strpos( $r25_source, "'gcu_report_review'" ) && false !== strpos( $r25_source, 'notice notice-error' ), 'R25 admin dashboard must show failed report moderation rather than implying success.' );

// R26 — FUT-12/CV-262: unresolved lifecycle remains actionable and diagnostic users cannot view reporter texts.
$r26_source = file_get_contents( $plugin . '/includes/class-gcu-future-intelligence.php' );
$r26_query_start = strpos( $r26_source, 'public static function review_queue_reports(' );
$r26_native_start = strpos( $r26_source, 'public static function resolve_report_record(' );
$r26_queue_source = substr( $r26_source, $r26_query_start, $r26_native_start - $r26_query_start );
$r26_dashboard_start = strpos( $r26_source, 'public static function admin_page()' );
$r26_dashboard_end = strpos( $r26_source, 'public static function can_manage_content()', $r26_dashboard_start );
$r26_dashboard = substr( $r26_source, $r26_dashboard_start, $r26_dashboard_end - $r26_dashboard_start );
fifth_assert( false !== $r26_query_start && false !== strpos( $r26_queue_source, "status IN ('open','reviewing')" ), 'R26 moderator queue must not lose reviewing reports before resolution.' );
fifth_assert( false !== strpos( $r26_queue_source, 'ORDER BY created_at ASC,id ASC LIMIT %d' ), 'R26 queue ordering and maximum bounded rows are required.' );
fifth_assert( false !== strpos( $r26_dashboard, '$can_resolve_reports = self::can_manage_content();' ), 'R26 report read and mutation UI must require delegated content-moderation capability.' );
fifth_assert( false !== strpos( $r26_dashboard, '$reports = $can_resolve_reports ? self::review_queue_reports( 50 ) : array();' ), 'R26 diagnostics-only users must not query reporter text.' );
fifth_assert( false !== strpos( $r26_dashboard, '$can_resolve_reports ? count( $reports ) : \'restricted\'' ), 'R26 diagnostics-only users must not learn report counts.' );
fifth_assert( false !== strpos( $r26_dashboard, '<?php if ( $can_resolve_reports ) : ?><form' ) && false !== strpos( $r26_dashboard, 'Content-moderation authorization is required to view reports.' ), 'R26 only File00-authorized editors can see report moderation controls and sensitive text.' );
fifth_assert( false !== strpos( $r26_dashboard, "esc_html( \$report['status'] )" ), 'R26 reviewer must see whether a case is open or reviewing.' );

// R27: the File00 delegated report read gate must be enforced at every native entry point.
$r27_source = file_get_contents( $plugin . '/includes/class-gcu-future-intelligence.php' );
$r27_rest_start = strpos( $r27_source, 'public static function rest_reports(' );
$r27_rest_end = strpos( $r27_source, 'public static function rest_revalidate_claim(', $r27_rest_start );
$r27_native_start = strpos( $r27_source, 'public static function reports( $status' );
$r27_queue_start = strpos( $r27_source, 'public static function review_queue_reports(' );
$r27_mutate_start = strpos( $r27_source, 'public static function resolve_report_record(', $r27_queue_start );
$r27_rest = substr( $r27_source, $r27_rest_start, $r27_rest_end - $r27_rest_start );
$r27_native = substr( $r27_source, $r27_native_start, $r27_queue_start - $r27_native_start );
$r27_queue = substr( $r27_source, $r27_queue_start, $r27_mutate_start - $r27_queue_start );
fifth_assert( false !== strpos( $r27_source, 'private static function require_report_read()' ) && false !== strpos( $r27_source, "'future_intelligence_content'" ) && false !== strpos( $r27_source, 'GCU_Capabilities::require_capability( GCU_Capabilities::MANAGE_CONTENT' ), 'R27 report reader uses current File00 delegated content purpose, not a new unapproved permission.' );
foreach ( array( 'REST' => $r27_rest, 'native' => $r27_native, 'queue' => $r27_queue ) as $surface => $body ) {
    fifth_assert( false !== strpos( $body, 'self::require_report_read()' ) && false !== strpos( $body, 'is_wp_error( $read_permission )' ), 'R27 ' . $surface . ' report surface must fail closed before returning PII-bearing report rows.' );
}
fifth_assert( false !== strpos( $r27_queue, "status IN ('open','reviewing')" ), 'R27 must preserve R26 pending report queue status coverage.' );

// R28 (source + static SQL guard): terminal report statuses are not allowed to reenter the active workflow.
$r28_source = file_get_contents( $plugin . '/includes/class-gcu-future-intelligence.php' );
$r28_start = strpos( $r28_source, 'public static function resolve_report_record(' );
$r28_end = strpos( $r28_source, 'private static function upsert_record(', $r28_start );
$r28_mutation = substr( $r28_source, $r28_start, $r28_end - $r28_start );
$r28_dashboard_at = strpos( $r28_source, 'public static function admin_page()' );
$r28_dashboard_to = strpos( $r28_source, 'public static function can_manage_content()', $r28_dashboard_at );
$r28_dashboard = substr( $r28_source, $r28_dashboard_at, $r28_dashboard_to - $r28_dashboard_at );
fifth_assert( false !== strpos( $future_policy, 'function report_resolution_transition_allowed(' ) && false !== strpos( $r28_mutation, 'GCU_Future_Policy::report_resolution_transition_allowed(' ), 'R28 rejected final-status report transitions are checked before transactional mutation.' );
fifth_assert( false !== strpos( $r28_mutation, "AND status IN ('open','reviewing')" ), 'R28 database CAS rejects a simultaneous terminal transition, not just a stale pre-read.' );
fifth_assert( false !== strpos( $r28_mutation, 'gcu_future_report_transition_invalid' ), 'R28 invalid final-to-active moderation transition has a safe explicit conflict code.' );
fifth_assert( false !== strpos( $r28_dashboard, 'is_wp_error( $reports )' ) && false !== strpos( $r28_dashboard, '! is_array( $reports )' ), 'R28 dashboard handles revoked permission and failed DB return without a PHP count TypeError.' );
fifth_assert( false !== strpos( $r28_dashboard, 'if ( $reports_read_error ) { $reports = array(); }' ), 'R28 unexpected failure response is removed from the sensitive report render path.' );
fifth_assert( false !== strpos( $r28_dashboard, "'unavailable'" ) && false !== strpos( $r28_dashboard, 'Report review is temporarily unavailable.' ), 'R28 missing reporter rows are reported as unavailable rather than falsely empty or exposed.' );
fifth_assert( false !== strpos( $r28_dashboard, 'if ( $reports_read_error )' ), 'R28 diagnostic failure UX must be available before action controls.' );

// R29 — CV-262: every privileged Future REST callback independently checks File00.
$r29_source = file_get_contents( $plugin . '/includes/class-gcu-future-intelligence.php' );
$r29_grants = array(
    'rest_copy_preflight' => array('MANAGE_CONTENT','future_intelligence_content'),
    'rest_experiment_preflight' => array('MANAGE_EXPERIMENTS','future_intelligence_experiments'),
    'rest_quality' => array('VIEW_ANALYTICS','future_intelligence_analytics'),
    'rest_friction' => array('VIEW_ANALYTICS','future_intelligence_analytics'),
    'rest_scenarios' => array('SYSTEM_CHECK','future_intelligence_system'),
    'rest_faq_gaps' => array('MANAGE_CONTENT','future_intelligence_content'),
    'rest_consistency' => array('MANAGE_CONTENT','future_intelligence_content'),
    'rest_ai_copy' => array('MANAGE_CONTENT','future_intelligence_content'),
    'rest_records' => array('MANAGE_CONTENT','future_intelligence_content'),
    'rest_record_write' => array('MANAGE_CONTENT','future_intelligence_content'),
    'rest_revalidate_claim' => array('APPROVE_CLAIMS','future_intelligence_claims')
);
foreach ( $r29_grants as $callback => $contract ) {
    $anchor = 'public static function ' . $callback . '(';
    $start = strpos( $r29_source, $anchor );
    $end = false === $start ? false : strpos( $r29_source, "\n\tpublic static function ", $start + strlen( $anchor ) );
    $body = false !== $start ? substr( $r29_source, $start, false !== $end ? $end - $start : 1600 ) : '';
    $guard = 'GCU_Capabilities::require_capability( GCU_Capabilities::' . $contract[0] . ", null, '" . $contract[1] . "' )";
    fifth_assert( false !== strpos( $body, $guard ) && false !== strpos( $body, 'is_wp_error( $native_permission )' ),
        'R29 ' . $callback . ' must natively enforce exact current File00 role and purpose before any protected work.' );
}
$r29_private = strpos( $r29_source, 'private static function upsert_record(' );
$r29_public = strpos( $r29_source, 'public static function upsert_record(' );
fifth_assert( false !== $r29_private && false === $r29_public, 'R29 system-trusted record mutation is no longer a public PHP entry point.' );
$r29_records_at = strpos( $r29_source, 'public static function records( $type' );
$r29_records = substr( $r29_source, $r29_records_at, 560 );
fifth_assert( false !== strpos( $r29_records, 'if ( ! $public_only )' ) && false !== strpos( $r29_records, 'GCU_Capabilities::MANAGE_CONTENT' ),
    'R29 native nonpublic record list rechecks File00 while keeping the public-safe change log.' );
$r29_claim_at = strpos( $r29_source, 'public static function revalidate_claim(' );
$r29_claim = substr( $r29_source, $r29_claim_at, 500 );
fifth_assert( false !== strpos( $r29_claim, 'GCU_Capabilities::APPROVE_CLAIMS' )
    && false !== strpos( $r29_claim, "'future_intelligence_claims'" ),
    'R29 direct claim revalidation must require the File00 claim approver, not a generic content editor.' );

// R30 — source gates must reject unsupported explicit Future governance identity and malformed readiness JSON.
$r30_policy = file_get_contents( $plugin . '/includes/class-gcu-future-policy.php' );
$r30_code = file_get_contents( $plugin . '/includes/class-gcu-future-intelligence.php' );
$r30_write_from = strpos( $r30_code, 'public static function rest_record_write(' );
$r30_write_to = strpos( $r30_code, 'public static function rest_reports(', $r30_write_from );
$r30_write = substr( $r30_code, $r30_write_from, $r30_write_to - $r30_write_from );
$r30_readiness_from = strpos( $r30_code, 'public static function rest_readiness(' );
$r30_readiness_to = strpos( $r30_code, 'public static function rest_copy_preflight(', $r30_readiness_from );
$r30_readiness = substr( $r30_code, $r30_readiness_from, $r30_readiness_to - $r30_readiness_from );
fifth_assert( false !== strpos( $r30_policy, 'function validate_future_record_command_values' ) && false !== strpos( $r30_write, 'GCU_Future_Policy::validate_future_record_command_values( $data )' ), 'R30 REST record write validates explicit canonical identity/state before sanitizer/idempotent command.' );
fifth_assert( false !== strpos( $r30_write, 'gcu_future_record_values_invalid' ), 'R30 invalid governed record key/status/locale/region is rejected with controlled 400.' );
fifth_assert( false !== strpos( $r30_policy, 'function validate_readiness_check_inputs' ) && false !== strpos( $r30_readiness, 'GCU_Future_Policy::validate_readiness_check_inputs( $data )' ), 'R30 FUT-24 readiness answers are explicitly checked before producing score.' );
fifth_assert( false !== strpos( $r30_readiness, 'json_decode( (string) $request->get_body() ) instanceof stdClass' ) && false !== strpos( $r30_readiness, 'gcu_future_readiness_body_invalid' ), 'R30 non-object JSON body must not be mislabeled as successful doctor readiness assessment.' );
fifth_assert( false !== strpos( $r30_readiness, 'gcu_future_readiness_answers_invalid' ), 'R30 unsupported self-check values must fail as a controlled input error.' );

if ( $failures ) {
	fwrite( STDERR, "Fifth review regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
	exit( 1 );
}

echo "Fifth review regression tests: PASS\n";
