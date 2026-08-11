<?php

$root = dirname( __DIR__ );
$plugin = $root . '/14-global-clinic-usp-integration';
$failures = array();
function ninth_text( $path ) { return file_exists( $path ) ? file_get_contents( $path ) : ''; }
function ninth_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }

$main = ninth_text( $plugin . '/global-clinic-usp-integration.php' );
$repo = ninth_text( $plugin . '/includes/class-gcu-repository.php' );
$rest = ninth_text( $plugin . '/includes/class-gcu-rest.php' );
$contracts = ninth_text( $plugin . '/includes/class-gcu-contracts.php' );
$future = ninth_text( $plugin . '/includes/class-gcu-future-intelligence.php' );
$fifth = ninth_text( $plugin . '/includes/class-gcu-fifth-review-hardening.php' );
$front = ninth_text( $plugin . '/includes/class-gcu-frontend.php' );
$status = ninth_text( $root . '/STATUS.md' );
$release = ninth_text( $root . '/docs/RELEASE-EVIDENCE.md' );

ninth_assert( false !== strpos( $main, 'Version: 1.4.7' ) && false !== strpos( $main, "GCU_VERSION', '1.4.7" ), 'Ninth-cycle candidate must be v1.4.7.' );
ninth_assert( false !== strpos( $main, "GCU_SCHEMA_VERSION', 10005" ) && false !== strpos( $main, "GCU_FUTURE_SCHEMA_VERSION', 1" ), 'Ninth cycle must not invent a schema change.' );

ninth_assert( false !== strpos( $repo, 'public function validate_event_token' ) && false !== strpos( $repo, 'public function consume_event_token' ), 'Event-token validation/consumption split is missing.' );
ninth_assert( false !== strpos( $rest, 'validate_event_token' ) && false !== strpos( $rest, "record_event(\$d,sanitize_text_field((string)\$r->get_header('X-GCU-Event-Token'))" ), 'REST must validate without consuming, then pass the token into the event mutation.' );
$record_start = strpos( $repo, 'public function record_event' );
$record_end = strpos( $repo, 'public function funnel_summary', $record_start );
$record = false !== $record_start && false !== $record_end ? substr( $repo, $record_start, $record_end - $record_start ) : '';
ninth_assert( false !== strpos( $record, "consume_event_token(\$token,'measurement')" ) && false !== strpos( $record, 'begin_owned_transaction()' ), 'One-time event token must be consumed inside the event transaction.' );
ninth_assert( false !== strpos( $record, 'INSERT INTO' ) && false === strpos( $record, 'INSERT IGNORE INTO' ), 'Conversion-event persistence must not use broad INSERT IGNORE semantics.' );
ninth_assert( false !== strpos( $record, 'gcu_event_destination_required' ), 'Repository-level destination-bound funnel invariant is missing.' );
ninth_assert( false !== strpos( $record, 'gcu_event_subject_unavailable' ), 'Unstable/empty measurement subject must fail closed.' );

ninth_assert( false !== strpos( $contracts, 'gcu_owner_event_order_ambiguous' ) && false !== strpos( $contracts, 'owner_event_order_ambiguous' ), 'Equal-time distinct owner events must fail closed as ambiguous.' );
$claim_start = strpos( $repo, 'private function insert_claim_history' );
$claim_end = false !== $claim_start ? strpos( $repo, 'public function', $claim_start + 10 ) : false;
$claim_history = false !== $claim_start ? substr( $repo, $claim_start, false !== $claim_end ? $claim_end - $claim_start : 5000 ) : '';
ninth_assert( false !== strpos( $claim_history, 'existing_history' ) && false === strpos( $claim_history, 'INSERT IGNORE' ), 'Claim history must use strict persistence with exact duplicate equivalence verification.' );

foreach ( array( 'gcu_content_readback_failed', 'gcu_placement_readback_failed', 'gcu_experiment_readback_failed' ) as $marker ) {
    ninth_assert( false !== strpos( $repo, $marker ), 'Mandatory mutation read-back guard missing: ' . $marker );
}

foreach ( array( 'gcu_future_quality_query_failed', 'gcu_future_quality_claim_query_failed', 'gcu_future_quality_report_query_failed', 'gcu_future_friction_query_failed' ) as $marker ) {
    ninth_assert( false !== strpos( $future, $marker ), 'Future analytics DB fail-close marker missing: ' . $marker );
}
ninth_assert( false !== strpos( $future, "'status' => 'query_failed'" ) && false !== strpos( $future, "'severity' => 'high'" ), 'Anomaly DB failures must be high-severity fail-safe state.' );
ninth_assert( false !== strpos( $fifth, 'future_early_stop_report_query_failed' ) && false !== strpos( $fifth, '$report_query_failed' ), 'Experiment early-stop complaint-query DB failure must be a safety breach.' );

ninth_assert( false !== strpos( $front, 'private, no-store, max-age=0, must-revalidate' ), 'Logged-in File 14 rendering must be private/no-store.' );
ninth_assert( false !== strpos( $front, 'Vary: Accept-Language, Cookie' ), 'Guest governed rendering must vary shared-cache handling by language/cookie.' );
ninth_assert( substr_count( $front, "\$content_locale='en-US'" ) >= 2 && false !== strpos( $front, 'GCU_I18n::language($content_locale)' ) && false !== strpos( $front, 'GCU_I18n::direction($content_locale)' ), 'en-US fallback blocks/FAQs must carry correct language and direction semantics.' );

ninth_assert( ! file_exists( $root . '/scripts/apply-file14-ninth-review-corrections.py' ), 'Temporary ninth-review corrective applicator remains.' );
ninth_assert( ! file_exists( $root . '/.github/workflows/file14-ninth-review-apply.yml' ), 'Temporary ninth-review corrective workflow remains.' );
ninth_assert( false !== strpos( $status, 'Ninth Ten-Round Repository Candidate' ) && false !== strpos( $release, 'Ninth Ten-Round Repository Candidate' ), 'Current status/release evidence must identify the ninth corrective cycle.' );
ninth_assert( false !== strpos( $status, 'Exact deployed code is unverified' ) && false !== strpos( $release, 'Exact deployed code is unverified' ), 'Live-First truth boundary must remain explicit.' );

if ( $failures ) {
    fwrite( STDERR, "Ninth review regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" );
    exit( 1 );
}
echo "Ninth review regression tests: PASS\n";
