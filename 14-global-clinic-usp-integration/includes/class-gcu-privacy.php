<?php

defined( 'ABSPATH' ) || exit;

final class GCU_Privacy {
	public function hooks() {
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'exporters' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'erasers' ) );
		add_action( 'init', array( $this, 'capture_attribution' ), 5 );
	}

	public function exporters( $exporters ) {
		$exporters['gcu-conversion-attribution'] = array(
			'exporter_friendly_name' => __( 'Global Clinic conversion attribution', 'global-clinic-usp-integration' ),
			'callback'               => array( $this, 'export_data' ),
		);
		return $exporters;
	}

	public function erasers( $erasers ) {
		$erasers['gcu-conversion-attribution'] = array(
			'eraser_friendly_name' => __( 'Global Clinic conversion attribution', 'global-clinic-usp-integration' ),
			'callback'             => array( $this, 'erase_data' ),
		);
		return $erasers;
	}

	public function export_data( $email_address, $page = 1 ) {
		unset( $email_address, $page );
		$data = array();
		if ( GCU_Policy::analytics_consent() && ! empty( $_COOKIE['gcu_attribution'] ) ) {
			$decoded = $this->decode_attribution( wp_unslash( $_COOKIE['gcu_attribution'] ) );
			if ( $decoded ) {
				$data[] = array(
					'group_id'    => 'gcu-attribution',
					'group_label' => __( 'Global Clinic attribution', 'global-clinic-usp-integration' ),
					'item_id'     => 'current-browser-attribution',
					'data'        => array_map( static function ( $key, $value ) { return array( 'name' => $key, 'value' => $value ); }, array_keys( $decoded ), array_values( $decoded ) ),
				);
			}
		}
		return array( 'data' => $data, 'done' => true );
	}

	public function erase_data( $email_address, $page = 1 ) {
		unset( $email_address, $page );
		$this->expire_cookie( 'gcu_attribution' );
		return array(
			'items_removed'  => true,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	public function capture_attribution() {
		if ( ! GCU_Policy::analytics_consent() ) {
			return;
		}
		$input = array(
			'source'   => isset( $_GET['utm_source'] ) ? $_GET['utm_source'] : '',
			'medium'   => isset( $_GET['utm_medium'] ) ? $_GET['utm_medium'] : '',
			'campaign' => isset( $_GET['utm_campaign'] ) ? $_GET['utm_campaign'] : '',
			'ref'      => isset( $_GET['ref'] ) ? $_GET['ref'] : '',
		);
		$campaign = GCU_Policy::sanitize_campaign( $input );
		if ( ! array_filter( $campaign ) ) {
			return;
		}
		$now      = time();
		$existing = ! empty( $_COOKIE['gcu_attribution'] ) ? $this->decode_attribution( wp_unslash( $_COOKIE['gcu_attribution'] ) ) : array();
		$payload  = array(
			'first_source'   => isset( $existing['first_source'] ) ? $existing['first_source'] : $campaign['source'],
			'first_medium'   => isset( $existing['first_medium'] ) ? $existing['first_medium'] : $campaign['medium'],
			'first_campaign' => isset( $existing['first_campaign'] ) ? $existing['first_campaign'] : $campaign['campaign'],
			'first_ref'      => isset( $existing['first_ref'] ) ? $existing['first_ref'] : $campaign['ref'],
			'first_at'       => isset( $existing['first_at'] ) ? (int) $existing['first_at'] : $now,
			'last_source'    => $campaign['source'],
			'last_medium'    => $campaign['medium'],
			'last_campaign'  => $campaign['campaign'],
			'last_ref'       => $campaign['ref'],
			'last_at'        => $now,
			'expires_at'     => $now + GCU_Policy::ATTRIBUTION_TTL,
		);
		$value = $this->encode_attribution( $payload );
		setcookie( 'gcu_attribution', $value, array( 'expires' => $payload['expires_at'], 'path' => COOKIEPATH ? COOKIEPATH : '/', 'domain' => COOKIE_DOMAIN, 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax' ) );
		$_COOKIE['gcu_attribution'] = $value;
	}

	public function current_campaign() {
		if ( ! GCU_Policy::analytics_consent() || empty( $_COOKIE['gcu_attribution'] ) ) {
			return array();
		}
		$data = $this->decode_attribution( wp_unslash( $_COOKIE['gcu_attribution'] ) );
		if ( empty( $data['expires_at'] ) || (int) $data['expires_at'] < time() ) {
			$this->expire_cookie( 'gcu_attribution' );
			return array();
		}
		return array(
			'source'   => isset( $data['last_source'] ) ? $data['last_source'] : '',
			'medium'   => isset( $data['last_medium'] ) ? $data['last_medium'] : '',
			'campaign' => isset( $data['last_campaign'] ) ? $data['last_campaign'] : '',
			'ref'      => isset( $data['last_ref'] ) ? $data['last_ref'] : '',
		);
	}

	private function encode_attribution( array $payload ) {
		$json = wp_json_encode( $payload );
		$body = rtrim( strtr( base64_encode( $json ), '+/', '-_' ), '=' );
		$sig  = hash_hmac( 'sha256', $body, wp_salt( 'auth' ) );
		return $body . '.' . $sig;
	}

	private function decode_attribution( $value ) {
		$parts = explode( '.', (string) $value, 2 );
		if ( 2 !== count( $parts ) || ! hash_equals( hash_hmac( 'sha256', $parts[0], wp_salt( 'auth' ) ), $parts[1] ) ) {
			return array();
		}
		$body = strtr( $parts[0], '-_', '+/' );
		$body .= str_repeat( '=', ( 4 - strlen( $body ) % 4 ) % 4 );
		$data = json_decode( base64_decode( $body, true ), true );
		return is_array( $data ) ? $data : array();
	}

	private function expire_cookie( $name ) {
		setcookie( $name, '', array( 'expires' => time() - HOUR_IN_SECONDS, 'path' => COOKIEPATH ? COOKIEPATH : '/', 'domain' => COOKIE_DOMAIN, 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax' ) );
		unset( $_COOKIE[ $name ] );
	}
}
