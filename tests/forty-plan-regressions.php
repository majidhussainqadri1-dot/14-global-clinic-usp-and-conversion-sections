<?php
/**
 * R33: 40 independent source contract lenses from amended File14 + central
 * governing plan + owner modules. This is NOT 40 runtime E2E passes.
 * A locally contained but unimplemented safe mutated-placement restore is
 * reported as BLOCKED and never made to look production accepted.
 */
$root = dirname( __DIR__ ) . '/14-global-clinic-usp-integration/includes/';
function r33_source( $name ) {
    global $root;
    $path = $root . 'class-gcu-' . $name . '.php';
    return is_file( $path ) ? file_get_contents( $path ) : '';
}
$files = array();
foreach ( array( 'policy', 'future-policy', 'future-intelligence', 'repository', 'companion-adapters', 'frontend', 'install', 'privacy', 'owner-outcomes', 'experiment-baseline', 'fifth-review-hardening', 'capabilities', 'future-i18n' ) as $key ) {
    $files[ $key ] = r33_source( $key );
}
function r33_has( $file, $needle ) {
    global $files;
    return isset( $files[ $file ] ) && false !== strpos( $files[ $file ], $needle );
}
$checks = array(
    'Canonical owner scope' => r33_has( 'repository', 'global_clinic' ),
    'File00 restricted authority' => r33_has( 'companion-adapters', 'file00_assertions' ),
    'File01 versioned route registry' => r33_has( 'companion-adapters', 'sync_file01_registry' ),
    'File07 native doctor discovery' => r33_has( 'owner-outcomes', 'ddd_after_directory' ),
    'File08 committed booking hook' => r33_has( 'owner-outcomes', 'wca_appointment_requested_committed_v1' ),
    'File09 committed onboarding hook' => r33_has( 'owner-outcomes', 'gdo_application_submitted' ),
    'File19 notification producer' => r33_has( 'companion-adapters', 'register_file19_producer' ),
    'File20 shell contract' => r33_has( 'companion-adapters', 'file20_contract' ),
    'File24 assurance contract' => r33_has( 'companion-adapters', 'file24_manifest' ),
    'File25 visual components' => r33_has( 'companion-adapters', 'file25_contract' ),
    'Zero percent commission law' => r33_has( 'policy', 'platform_commission_percent' ),
    'Founder approval and audit' => r33_has( 'repository', 'governed_approval' ),
    'Canonical native privacy gate and subject erasure' => r33_has( 'owner-outcomes', 'GCU_Privacy::measurement_allowed()' ) && r33_has( 'owner-outcomes', 'event_subject_hash()' ) && r33_has( 'privacy', 'erase_data' ),
    'Atomic inbox/idempotency' => r33_has( 'repository', 'process_inbox' ) && r33_has( 'repository', 'run_idempotent_command' ),
    'Migration and rollback guards' => r33_has( 'install', 'verify_schema' ) && r33_has( 'install', 'rollback_snapshot' ),
    'Public degraded state' => r33_has( 'frontend', 'render_degraded' ),
    'FUT01 Explicit intent' => r33_has( 'future-policy', 'sanitize_explicit_intent' ),
    'FUT02 Context handoff' => r33_has( 'future-policy', 'sanitize_handoff_context' ),
    'FUT03 Trust claim evidence' => r33_has( 'future-intelligence', 'render_trust_evidence' ),
    'FUT04 Freshness sentinel' => r33_has( 'future-intelligence', 'claim_freshness_sentinel' ),
    'FUT05 Commission sentinel' => r33_has( 'future-intelligence', 'parity_status' ),
    'FUT06 Jurisdiction copy' => r33_has( 'future-intelligence', 'jurisdiction_disclosure' ),
    'FUT07 Semantic risk' => r33_has( 'future-policy', 'semantic_risk_scan' ),
    'FUT08 Dark pattern lint' => r33_has( 'future-policy', 'dark_pattern_scan' ),
    'FUT09 Failover matrix' => r33_has( 'future-intelligence', 'failover_matrix' ),
    'FUT10 Scenario laboratory' => r33_has( 'future-intelligence', 'scenario_lab' ),
    'FUT11 Evidence-gated quality score' => r33_has( 'future-intelligence', 'quality_score' ) && r33_has( 'future-policy', 'quality_evidence_status' ),
    'FUT12 Copy report lifecycle' => r33_has( 'future-intelligence', 'resolve_report' ),
    'FUT13 Consented friction aggregate' => r33_has( 'owner-outcomes', 'owner_stage_summary' ),
    'FUT14 Native opt-in volume anomaly' => r33_has( 'owner-outcomes', 'observed_anomaly' ) && r33_has( 'future-intelligence', 'GCU_Owner_Outcomes::observed_anomaly()' ),
    'FUT15 Cohort threshold 10' => r33_has( 'future-policy', 'MIN_COHORT = 10' ),
    'FUT16 Experiment preflight' => r33_has( 'future-policy', 'experiment_preflight' ),
    'FUT17 Containment, NOT changed-placement restoration' => r33_has( 'experiment-baseline', 'reconcile_after_stop' ) && r33_has( 'repository', 'gcu_experiment_baseline_restore_unverified' ),
    'FUT18 FAQ gap suggestions' => r33_has( 'future-intelligence', 'faq_gap_candidates' ),
    'FUT19 Consistency graph' => r33_has( 'future-intelligence', 'consistency_graph' ),
    'FUT20 Provenance/term lock' => r33_has( 'future-policy', 'terminology_lock' ) && r33_has( 'future-i18n', 'ur-PK' ),
    'FUT21 AI copy guard' => r33_has( 'future-intelligence', 'ai_copy_assist' ),
    'FUT22 Public trust log' => r33_has( 'future-intelligence', 'public_change_log' ),
    'FUT23 Safe choice guide' => r33_has( 'future-policy', 'patient_guide' ),
    'FUT24 Doctor readiness' => r33_has( 'future-policy', 'doctor_readiness_check' ),
);
if ( 40 !== count( $checks ) ) { fwrite( STDERR, "R33 review definition not forty.\n" ); exit( 2 ); }
$failed = array();
$n = 0;
foreach ( $checks as $name => $ok ) {
    $n++;
    $status = $ok ? ( 33 === $n ? 'CONTAINED/RELEASE-BLOCKED' : 'SOURCE PRESENT' ) : 'MISSING';
    echo sprintf( "Review %02d: %s — %s\n", $n, $status, $name );
    if ( ! $ok ) { $failed[] = $n; }
}
if ( $failed ) { fwrite( STDERR, 'Source regressions: ' . implode( ',', $failed ) . PHP_EOL ); exit( 1 ); }
echo "Forty source scope markers accounted for; 39 covered, FUT17 changed-placement restore BLOCKED. No staging/live acceptance claimed.\n";
