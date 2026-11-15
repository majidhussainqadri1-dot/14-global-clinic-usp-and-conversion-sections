<?php
/**
 * F14-FUT-20 + F14-FR-013: unapproved English cannot be disguised as
 * Urdu/Arabic content; approved English must still be available in English.
 */
define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['gcu_v123_locale'] = 'ur_PK';
function apply_filters( $hook, $value ) { return $value; }
function determine_locale() { return $GLOBALS['gcu_v123_locale']; }
function esc_attr( $v ) { return htmlspecialchars( (string) $v, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $v ) { return htmlspecialchars( (string) $v, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $v ) { return $v; }
function home_url( $path = '/' ) { return 'https://local.example.test' . $path; }
function wpautop( $v ) { return '<p>' . $v . '</p>'; }
function wp_kses_post( $v ) { return $v; }

class GCU_V123_Repository {
    public $calls = array();
    public function active_blocks( $slot, $aud, $locale ) {
        $this->calls[] = array( $slot, $aud, $locale );
        // Deliberately simulate one-locale approval, not mixed-language approval.
        if ( 'en-US' !== $locale ) { return array(); }
        return array( array(
            'title' => 'ENGLISH_APPROVED_ONLY_FOR_EN',
            'body' => 'English-only publication is an English-locale source.',
            'content_version' => 1, 'block_type' => 'card', 'audience' => 'all',
            'cta_destination' => '', 'cta_label' => '', 'claim_keys' => array()
        ) );
    }
    public function public_claims( $keys ) { return array(); }
}
class GCU_Plugin {
    public $repo;
    private static $instance;
    public function __construct() { $this->repo = new GCU_V123_Repository(); }
    public static function instance() {
        if ( ! self::$instance ) { self::$instance = new self(); }
        return self::$instance;
    }
    public function repository() { return $this->repo; }
}
require_once dirname( __DIR__ ) . '/14-global-clinic-usp-integration/includes/class-gcu-i18n.php';
require_once dirname( __DIR__ ) . '/14-global-clinic-usp-integration/includes/class-gcu-frontend.php';
function gcu_v123_assert( $ok, $message ) {
    if ( ! $ok ) { fwrite( STDERR, "FAIL " . $message . "\n" ); exit( 1 ); }
}
$frontend = new GCU_Frontend();
$faq = new ReflectionMethod( 'GCU_Frontend', 'render_faqs' );
$faq->setAccessible( true ); // Required on PHP 7.4.
$cases = array(
    'ur_PK' => array( 'ur-PK', 'مواد زیرِ جائزہ ہے' ),
    'ar_SA' => array( 'ar-SA', 'المحتوى قيد المراجعة' ),
);
foreach ( $cases as $wp_locale => $expect ) {
    $GLOBALS['gcu_v123_locale'] = $wp_locale;
    $before = count( GCU_Plugin::instance()->repo->calls );
    $body = $frontend->render_blocks( 'global_clinic_primary', 'all' );
    $faq_html = $faq->invoke( $frontend, 'gcu-sample', $expect[0] );
    gcu_v123_assert( false !== strpos( $body, $expect[1] ), 'Localized review state: ' . $expect[0] );
    gcu_v123_assert( false === strpos( $body, 'ENGLISH_APPROVED_ONLY_FOR_EN' ),
        'English must not leak into ' . $expect[0] );
    gcu_v123_assert( '' === $faq_html, 'No untranslated FAQ in ' . $expect[0] );
    $calls = array_slice( GCU_Plugin::instance()->repo->calls, $before );
    gcu_v123_assert( 2 === count( $calls ), 'Exactly two locale reads: ' . $expect[0] );
    foreach ( $calls as $call ) {
        gcu_v123_assert( $expect[0] === $call[2], 'No hidden English fallback query in ' . $expect[0] );
    }
}
// Positive test: a truly English page still publishes the canonical English copy/FAQ.
$GLOBALS['gcu_v123_locale'] = 'en_US';
$start = count( GCU_Plugin::instance()->repo->calls );
$english = $frontend->render_blocks( 'global_clinic_primary', 'all' );
$english_faq = $faq->invoke( $frontend, 'gcu-sample', 'en-US' );
gcu_v123_assert( false !== strpos( $english, 'ENGLISH_APPROVED_ONLY_FOR_EN' ),
    'Approved English page remains available.' );
gcu_v123_assert( false !== strpos( $english_faq, 'ENGLISH_APPROVED_ONLY_FOR_EN' ),
    'Approved English FAQ remains available.' );
$calls = array_slice( GCU_Plugin::instance()->repo->calls, $start );
gcu_v123_assert( 2 === count( $calls ), 'Two approved English reads.' );
foreach ( $calls as $call ) {
    gcu_v123_assert( 'en-US' === $call[2], 'English read is exactly locale-scoped.' );
}
echo "File14 V12.3 strict governed content locale: Urdu, Arabic and English PASS\n";
