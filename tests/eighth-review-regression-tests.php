<?php

$root = dirname( __DIR__ );
$plugin = $root . '/14-global-clinic-usp-integration';
$failures = array();
function eighth_text( $path ) { return file_exists( $path ) ? file_get_contents( $path ) : ''; }
function eighth_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }

$main = eighth_text( $plugin . '/global-clinic-usp-integration.php' );
$rest = eighth_text( $plugin . '/includes/class-gcu-rest.php' );
$hard = eighth_text( $plugin . '/includes/class-gcu-hardening.php' );
$privacy = eighth_text( $plugin . '/includes/class-gcu-privacy.php' );
$contracts = eighth_text( $plugin . '/includes/class-gcu-contracts.php' );
$status = eighth_text( $root . '/STATUS.md' );
$release = eighth_text( $root . '/docs/RELEASE-EVIDENCE.md' );
$quality_workflow = eighth_text( $root . '/.github/workflows/file14-quality.yml' );
$fresh_workflow = eighth_text( $root . '/.github/workflows/file14-fresh-reviews.yml' );
$baseline_workflow = eighth_text( $root . '/.github/workflows/baseline-import-and-integrity.yml' );

$version = '';
if ( preg_match( '/Version:\s*([0-9]+\.[0-9]+\.[0-9]+)/', $main, $match ) ) { $version = $match[1]; }
eighth_assert( '1.4.6' === $version && false !== strpos( $main, "GCU_VERSION', '1.4.6" ), 'Eighth-cycle software identity must be v1.4.6.' );
eighth_assert( false !== strpos( $main, "GCU_SCHEMA_VERSION', 10005" ) && false !== strpos( $main, "GCU_FUTURE_SCHEMA_VERSION', 1" ), 'Patch release must not invent a schema change.' );

eighth_assert( false !== strpos( $rest, "if(!empty(\$x['deduplicated']))" ) && substr_count( $rest, 'event_identity_guard($d)' ) >= 2, 'Concurrent duplicate conversion events must be identity-rechecked after deduplication.' );
eighth_assert( false !== strpos( $rest, 'gcu_event_destination_required' ) && false !== strpos( $rest, "'cta_selected','destination_loaded','application_started','booking_started'" ), 'Destination-bound funnel stages must require a canonical destination.' );
eighth_assert( false !== strpos( $rest, "\$wpdb->last_error=''" ) && false !== strpos( $rest, 'gcu_analytics_query_failed' ), 'Funnel query database failures must fail closed instead of looking like empty analytics.' );

eighth_assert( false === strpos( substr( $hard, strpos( $hard, 'public static function request_fingerprint' ), strpos( $hard, 'public static function command_key' ) - strpos( $hard, 'public static function request_fingerprint' ) ), 'sanitize_structured_value' ), 'Idempotency fingerprints must not truncate request semantics through the 500-character structured sanitizer.' );
eighth_assert( false !== strpos( $hard, '$nodes > 2000' ) && false !== strpos( $hard, 'strlen( $encoded ) > 1048576' ), 'Full request fingerprints must retain explicit abuse bounds.' );

eighth_assert( false !== strpos( $privacy, "acquire_db_lock('subject-user:'" ) && false !== strpos( $privacy, 'hash_equals($candidate,$s)' ), 'Logged-in measurement subject initialization must be serialized and read-back verified.' );
eighth_assert( false !== strpos( $privacy, 'release_db_lock($lock)' ), 'Measurement subject initialization lock must always be released.' );

eighth_assert( false !== strpos( $contracts, 'owner_event_time' ) && false !== strpos( $contracts, "'owner_occurred_at'" ) && false !== strpos( $contracts, "'received_at'" ), 'Owner readiness must distinguish owner occurrence time from receipt time.' );
eighth_assert( false !== strpos( $contracts, "\$owner_time<=(int)\$existing['owner_occurred_at']" ), 'Older/out-of-order owner readiness events must not overwrite newer state.' );
eighth_assert( false === strpos( $contracts, 'gcu_file20_slot_ready_v1' ) && false !== strpos( $contracts, 'sabri_shell_slot_ready_v1' ), 'Only the canonical File 20 slot-readiness contract may authorize placement readiness.' );
eighth_assert( false !== strpos( $contracts, "''!==\$owner_url" ) && false !== strpos( $contracts, '$url=$available?$owner_url:$fallback' ), 'Owner availability must require an owner-confirmed safe URL; fallback URL may not manufacture readiness.' );

$head_expression = '${{ github.event.pull_request.head.sha || github.sha }}';
eighth_assert( substr_count( $quality_workflow, 'ref: ' . $head_expression ) >= 2 && substr_count( $quality_workflow, 'Verify exact checkout SHA' ) >= 2, 'Quality/package workflow must checkout and verify the exact PR head instead of the synthetic pull-request merge ref.' );
eighth_assert( false !== strpos( $quality_workflow, 'name: file-14-package-' . $head_expression ), 'Package artifact identity must be keyed to the exact PR-head SHA.' );
eighth_assert( substr_count( $fresh_workflow, 'ref: ' . $head_expression ) >= 2 && substr_count( $fresh_workflow, 'Verify exact checkout SHA' ) >= 2, 'Both fresh-review jobs must execute the exact PR-head SHA.' );
eighth_assert( false !== strpos( $baseline_workflow, 'ref: ' . $head_expression ) && false !== strpos( $baseline_workflow, 'Verify exact checkout SHA' ), 'Baseline provenance PR job must execute the exact PR-head SHA.' );

eighth_assert( false !== strpos( $status, 'Eighth Ten-Round Repository Candidate' ) && false !== strpos( $release, 'Eighth Ten-Round Repository Candidate' ), 'Status and release evidence must identify the eighth corrective cycle.' );
eighth_assert( false !== strpos( $status, 'Exact deployed code is unverified' ) && false !== strpos( $release, 'Exact deployed code is unverified' ), 'Live-First truth boundary must remain explicit.' );

if ( $failures ) {
    fwrite( STDERR, "Eighth review regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
    exit( 1 );
}
echo "Eighth review regression tests: PASS\n";
