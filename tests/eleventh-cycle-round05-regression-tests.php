<?php
$root = dirname( __DIR__ );
$main = file_get_contents( $root . '/14-global-clinic-usp-integration/global-clinic-usp-integration.php' );
$hard = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-eleventh-review-hardening.php' );
$failures = array();
function e11r05_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
e11r05_assert( false !== strpos( $main, 'class-gcu-eleventh-review-hardening.php' ), 'Eleventh-cycle postcondition hardening must load.' );
e11r05_assert( false !== strpos( $main, "register_activation_hook( GCU_FILE, array( 'GCU_Eleventh_Review_Hardening', 'activation_verify' )" ), 'Activation must verify migration/Future postconditions.' );
e11r05_assert( false !== strpos( $hard, 'gcu_snapshot_receipt_corrupt' ) && false !== strpos( $hard, "hash_equals( $stored_hash, hash( 'sha256', $encoded ) )" ), 'Persisted rollback snapshot must be verified from its full payload.' );
e11r05_assert( false !== strpos( $hard, 'gcu_migration_evidence_unverified' ) && false !== strpos( $hard, "get_option( GCU_Install::MIGRATION_LOG" ), 'Migration completion must have durable evidence readback.' );
e11r05_assert( false !== strpos( $hard, "'terminology_lock', 'protected_terms'" ) && false !== strpos( $hard, "'change_log', 'future_cti_v2_0'" ), 'Future schema readiness must include canonical governance defaults.' );
e11r05_assert( false !== strpos( $hard, "GCU_Future_Intelligence::upsert_record" ) && false !== strpos( $hard, 'gcu_future_default_readback_failed' ), 'Missing Future governance defaults must be repaired and read back before activation succeeds.' );
e11r05_assert( false !== strpos( $hard, 'gcu_future_cron_schedule_failed' ) && false !== strpos( $hard, 'gcu_future_daily_governance' ) && false !== strpos( $hard, 'gcu_future_hourly_intelligence' ), 'Future governance schedules must be fail-closed activation postconditions.' );
e11r05_assert( false !== strpos( $hard, "wp_schedule_event( $job[1], $job[2], $job[0], array(), true )" ), 'Future cron scheduling must request WP_Error evidence.' );
if ( $failures ) { fwrite( STDERR, "Eleventh-cycle Round 05 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Eleventh-cycle Round 05 regression tests: PASS\n";
