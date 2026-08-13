<?php
/** File 14 eleventh fresh cycle — Round 14 retention regression gate. */
$root = dirname( __DIR__ );
$path = $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php';
$src  = file_get_contents( $path );

if ( false === $src ) {
	fwrite( STDERR, "Unable to read Future Intelligence source.\n" );
	exit( 1 );
}

$required = array(
	"SELECT id,record_type,record_key,locale,region FROM {\$t['records']}",
	"GCU_Privacy::legal_hold_applies('future_record',\$identity)",
	"sanitize_key((string)\$row['record_type'])",
	"sanitize_key((string)\$row['record_key'])",
	"GCU_Policy::sanitize_locale((string)\$row['locale'])",
	"GCU_Future_Intelligence::sanitize_region((string)\$row['region'])",
);

foreach ( $required as $marker ) {
	if ( false === strpos( $src, $marker ) ) {
		fwrite( STDERR, "Round 14 retention marker missing: {$marker}\n" );
		exit( 1 );
	}
}

$forbidden = "SELECT id,public_id FROM {\$t['records']}";
if ( false !== strpos( $src, $forbidden ) ) {
	fwrite( STDERR, "Round 14 regression: Future records cleanup still queries nonexistent public_id.\n" );
	exit( 1 );
}

if ( false !== preg_match( '/CREATE TABLE \{\$t\[\x27records\x27\]\}.*?public_id/s', $src ) && preg_match( '/CREATE TABLE \{\$t\[\x27records\x27\]\}.*?public_id/s', $src ) ) {
	fwrite( STDERR, "Unexpected public_id drift detected in Future records schema; review identity contract.\n" );
	exit( 1 );
}

echo "Eleventh-cycle Round 14 retention regression gate: PASS\n";
