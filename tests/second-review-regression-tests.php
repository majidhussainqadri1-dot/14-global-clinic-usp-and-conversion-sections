<?php
$root = dirname( __DIR__ );
$plugin = $root . '/14-global-clinic-usp-integration';
$failures = array();
function sr_text( $path ) { return file_exists( $path ) ? file_get_contents( $path ) : ''; }
function sr_check( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }

$contracts = sr_text( $plugin . '/includes/class-gcu-contracts.php' );
$rest = sr_text( $plugin . '/includes/class-gcu-rest.php' );
$future = sr_text( $plugin . '/includes/class-gcu-future-intelligence.php' );
$install = sr_text( $plugin . '/includes/class-gcu-install.php' );
$review = sr_text( $plugin . '/includes/class-gcu-review80-hardening.php' );
$trace = sr_text( $root . '/docs/REQUIREMENTS-TRACEABILITY.md' );
$status = sr_text( $root . '/STATUS.md' );
$release = sr_text( $root . '/docs/RELEASE-EVIDENCE.md' );

sr_check( false !== strpos( $contracts, 'public function public_destination_health()' ), 'Public destination DTO missing.' );
sr_check( false !== strpos( $rest, 'public_destination_health()' ) && false !== strpos( $rest, "public function destinations(){if(!get_option('gcu_enabled',1))" ), 'Public destinations endpoint is not safe-DTO/safe-mode bounded.' );
sr_check( false === strpos( substr( $future, strpos( $future, 'public static function bootstrap()' ), strpos( $future, 'public static function tables()' ) - strpos( $future, 'public static function bootstrap()' ) ), 'ensure_schema' ), 'Future schema migration must not run from every bootstrap request.' );
sr_check( false !== strpos( $future, "acquire_db_lock( 'future-schema', 5 )" ) && false !== strpos( $future, 'public static function runtime_ready()' ), 'Future schema locking/runtime-ready boundary missing.' );
sr_check( false !== strpos( $install, 'private static function ensure_future_schema()' ) && false !== strpos( $install, 'self::ensure_future_schema();' ), 'Future schema is not integrated with activation/upgrade lifecycle.' );
sr_check( false !== strpos( $review, 'GCU_Future_Intelligence::runtime_ready()' ), 'Future REST safe-mode fail-close guard missing.' );
sr_check( false !== strpos( $future, "'sample_count' => GCU_Future_Policy::cohort_allowed( \$selected ) ? \$selected : null" ), 'Quality score leaks sub-threshold sample count.' );
sr_check( false !== strpos( $future, "'current_sample' => null" ) && false !== strpos( $future, "'baseline_sample' => null" ), 'Anomaly detector leaks sub-threshold cohort counts.' );
sr_check( false !== strpos( $review, 'get_headers()' ) && false !== strpos( $review, "empty( \$headers['Cache-Control'] )" ), 'REST hardening overwrites explicit endpoint cache policy.' );
sr_check( ! file_exists( $root . '/.github/workflows/file14-one-shot-release-gate.yml' ), 'Obsolete v1.4.0/PR-3 release automation remains.' );
sr_check( false !== strpos( $trace, 'Requirements Traceability — v1.4.1' ) && false !== strpos( $trace, 'File 14 v1.4.1 may only claim a status' ), 'Traceability current-version truth is stale.' );
sr_check( false !== strpos( $status, 'Repository Release State' ) && false === strpos( $status, 'Corrective Candidate — Merged' ), 'Status wording remains contradictory.' );
sr_check( false !== strpos( $release, 'Repository Release Evidence' ) && false !== strpos( $release, 'exact current review/main SHA' ), 'Release evidence remains frozen at pre-merge candidate wording.' );

if ( $failures ) {
    fwrite( STDERR, "Second-review regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
    exit( 1 );
}
echo "Second-review regression tests: PASS\n";
