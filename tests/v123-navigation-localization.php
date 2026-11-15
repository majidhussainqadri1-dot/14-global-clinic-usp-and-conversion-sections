<?php
/**
 * Direct PHP regression: F14-FR-013 / F14-NFR-010.
 * Urdu and Arabic share RTL direction, but MUST NOT share the same UI labels.
 * Tests File14's no-File20 fallback navigation without a live WordPress runtime.
 */
define( 'ABSPATH', __DIR__ . '/' );
function apply_filters( $hook, $value ) { return $value; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value ) { return $value; }
function home_url( $path = '/' ) { return 'https://local.example.test' . $path; }
require_once dirname( __DIR__ ) . '/14-global-clinic-usp-integration/includes/class-gcu-i18n.php';
require_once dirname( __DIR__ ) . '/14-global-clinic-usp-integration/includes/class-gcu-frontend.php';

function gcu_nav_assert( $ok, $message ) {
    if ( ! $ok ) { fwrite( STDERR, "FAIL: " . $message . "\n" ); exit( 1 ); }
}
gcu_nav_assert( GCU_I18n::is_complete(), 'Every locale must keep all canonical copy keys.' );
$frontend = new GCU_Frontend();
$method = new ReflectionMethod( 'GCU_Frontend', 'navigation_controls' );
$method->setAccessible( true ); // Required for PHP 7.4 native reflection.
$cases = array(
    'en-US' => array( 'Page navigation', 'Back', 'Home', 'ltr' ),
    'ur-PK' => array( 'صفحہ کی رہنمائی', 'واپس', 'ہوم', 'rtl' ),
    'ar-SA' => array( 'تنقل الصفحة', 'رجوع', 'الرئيسية', 'rtl' ),
);
foreach ( $cases as $locale => $case ) {
    $html = $method->invoke( $frontend, 'global_clinic', $locale );
    gcu_nav_assert( GCU_I18n::direction( $locale ) === $case[3], 'Direction ' . $locale );
    gcu_nav_assert( false !== strpos( $html, 'aria-label="' . $case[0] . '"' ), 'Accessible nav ' . $locale );
    gcu_nav_assert( false !== strpos( $html, '>' . $case[1] . '</a>' ), 'Back translation ' . $locale );
    gcu_nav_assert( false !== strpos( $html, '>' . $case[2] . '</a>' ), 'Home translation ' . $locale );
    gcu_nav_assert( 1 === substr_count( $html, 'class="gcu-context-nav__back"' ), 'Exactly one Back anchor' );
    gcu_nav_assert( 1 === substr_count( $html, 'class="gcu-context-nav__home"' ), 'Exactly one Home anchor' );
    gcu_nav_assert( false !== strpos( $html, 'https://local.example.test/' ), 'Canonical safe Home URL' );
}
// The root and degraded states cannot safely Back-link to their own failing routes.
foreach ( array( 'global_clinic', 'degraded' ) as $context ) {
    $root_nav = $method->invoke( $frontend, $context, 'ur-PK' );
    gcu_nav_assert( false !== strpos( $root_nav, 'class="gcu-context-nav__back" href="https://local.example.test/"' ),
        'Root/degraded Back must not be a self-link: ' . $context );
}
$child_nav = $method->invoke( $frontend, 'how_it_works', 'ur-PK' );
gcu_nav_assert( false !== strpos( $child_nav, 'class="gcu-context-nav__back" href="https://local.example.test/global-clinic/"' ),
    'How-it-works Back should return to canonical Global Clinic.' );
gcu_nav_assert( false !== strpos( $child_nav, 'class="gcu-context-nav__home" href="https://local.example.test/"' ),
    'Child Home should return to canonical site root.' );
$ur = $method->invoke( $frontend, 'global_clinic', 'ur-PK' );
gcu_nav_assert( false === strpos( $ur, 'الرجوع / الرئيسية' ), 'Urdu shell must not contain Arabic hardcoding.' );
gcu_nav_assert( false === strpos( $ur, '>رجوع</a>' ), 'Urdu label must not be Arabic.' );
echo "File14 V12.3 fallback navigation i18n: 3/3 PASS\n";
