<?php
/**
 * F14-FUT-20, F14-FR-013: never silently serve approved English blocks
 * as though they were approved Urdu/Arabic translations.
 */
define( 'ABSPATH', __DIR__ . '/' );
function apply_filters( $hook, $value ) { return $value; }
function determine_locale() { return 'ur_PK'; }
function esc_attr( $v ) { return htmlspecialchars( (string) $v, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $v ) { return htmlspecialchars( (string) $v, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $v ) { return $v; }
function home_url( $path = '/' ) { return 'https://local.example.test' . $path; }
class GCU_V123_Repository {
    public $calls = array();
    public function active_blocks( $slot, $aud, $locale ) {
        $this->calls[] = array( $slot, $aud, $locale );
        // Simulate incomplete translation approval: only English is published.
        return 'en-US' === $locale ? array( array( 'title' => 'ENGLISH_ONLY_UNAPPROVED_FOR_URDU',
            'body' => 'This must not leak into the Urdu page',
            'content_version' => 1 ) ) : array();
    }
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
$body = $frontend->render_blocks( 'global_clinic_primary', 'all' );
gcu_v123_assert( false !== strpos( $body, 'مواد زیرِ جائزہ ہے' ), 'User must see honest Urdu unavailable state.' );
gcu_v123_assert( false === strpos( $body, 'ENGLISH_ONLY_UNAPPROVED_FOR_URDU' ), 'English-only block must not leak.' );
$faq = new ReflectionMethod( 'GCU_Frontend', 'render_faqs' );
$faq->setAccessible( true ); // Required for PHP 7.4 native reflection.
$faq_html = $faq->invoke( $frontend, 'gcu-sample', 'ur-PK' );
gcu_v123_assert( '' === $faq_html, 'English-only FAQ must not publish as Urdu FAQ.' );
$calls = GCU_Plugin::instance()->repo->calls;
gcu_v123_assert( 2 === count( $calls ), 'No additional English fallback owner read.' );
foreach ( $calls as $call ) {
    gcu_v123_assert( 'ur-PK' === $call[2], 'All governed reads remain locale-scoped.' );
}
echo "File14 V12.3 strict governed content locale: PASS\n";
