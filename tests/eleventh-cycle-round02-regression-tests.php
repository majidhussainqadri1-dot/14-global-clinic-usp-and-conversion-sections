<?php
$root = dirname( __DIR__ );
$install = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-install.php' );
$failures = array();
function e11r02_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
e11r02_assert( false !== strpos( $install, 'install_lock_probe_failed' ), 'Stale install-lock ownership probe must expose DB failure.' );
e11r02_assert( false !== strpos( $install, "if(''!==(string)\$wpdb->last_error)" ), 'Stale install-lock DB probe must inspect last_error.' );
e11r02_assert( false !== strpos( $install, "return true;}if(null===\$used" ), 'Unknown stale-lock ownership must fail closed.' );
e11r02_assert( false !== strpos( $install, "wp_schedule_event(\$job[1],\$job[2],\$job[0],array(),true)" ), 'Required cron scheduling must request WP_Error evidence.' );
e11r02_assert( false !== strpos( $install, 'gcu_cron_schedule_failed' ), 'Required cron scheduling failures must block install/upgrade completion.' );
e11r02_assert( false !== strpos( $install, '$scheduled=self::schedule();if(is_wp_error($scheduled)){return$scheduled;}' ), 'Install/upgrade must propagate required cron scheduling failure.' );
if ( $failures ) { fwrite( STDERR, "Eleventh-cycle Round 02 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Eleventh-cycle Round 02 regression tests: PASS\n";
