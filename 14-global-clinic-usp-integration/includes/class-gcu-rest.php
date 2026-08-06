<?php

defined( 'ABSPATH' ) || exit;

final class GCU_REST {
	const NAMESPACE = 'gcu/v1';

	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/blocks',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'blocks' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'slot'     => array( 'sanitize_callback' => 'sanitize_key' ),
					'audience' => array( 'sanitize_callback' => array( 'GCU_Policy', 'sanitize_audience' ) ),
					'locale'   => array( 'sanitize_callback' => array( 'GCU_Policy', 'sanitize_locale' ) ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/destinations',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'destinations' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/events',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'event' ),
				'permission_callback' => array( $this, 'verify_event_token' ),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/content',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_content' ),
				'permission_callback' => array( $this, 'can_manage_content' ),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/placements',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_placement' ),
				'permission_callback' => array( $this, 'can_manage_placements' ),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/experiments',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_experiment' ),
				'permission_callback' => array( $this, 'can_manage_experiments' ),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/claims/(?P<claim_key>[a-z0-9_\-]+)/withdraw',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'withdraw_claim' ),
				'permission_callback' => array( $this, 'can_approve_claims' ),
				'args'                => array(
					'expected_version' => array( 'required' => true, 'sanitize_callback' => 'absint' ),
					'reason'           => array( 'required' => true, 'sanitize_callback' => 'sanitize_textarea_field' ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/workflow/(?P<machine>copy|placement|experiment)/(?P<public_id>[a-f0-9\-]{36})',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'transition' ),
				'permission_callback' => array( $this, 'can_manage_workflow' ),
				'args'                => array(
					'expected_version' => array( 'required' => true, 'sanitize_callback' => 'absint' ),
					'target'           => array( 'required' => true, 'sanitize_callback' => 'sanitize_key' ),
					'reason'           => array( 'sanitize_callback' => 'sanitize_textarea_field' ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/analytics/funnel',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'funnel' ),
				'permission_callback' => array( $this, 'can_view_analytics' ),
				'args'                => array( 'days' => array( 'default' => 30, 'sanitize_callback' => 'absint' ) ),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/health',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'health' ),
				'permission_callback' => array( $this, 'can_system_check' ),
			)
		);
	}

	public function blocks( WP_REST_Request $request ) {
		$rows = GCU_Plugin::instance()->repository()->active_blocks(
			$request->get_param( 'slot' ),
			$request->get_param( 'audience' ) ?: 'all',
			$request->get_param( 'locale' ) ?: GCU_Plugin::instance()->frontend()->current_locale()
		);
		return rest_ensure_response( array( 'items' => $rows, 'count' => count( $rows ), 'version' => 1 ) );
	}

	public function destinations() {
		return rest_ensure_response( array( 'items' => GCU_Plugin::instance()->contracts()->all_destination_health(), 'version' => 1 ) );
	}

	public function event( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		if ( ! is_array( $data ) ) {
			$data = array();
		}
		$data['campaign'] = GCU_Plugin::instance()->privacy()->current_campaign();
		$result = GCU_Plugin::instance()->repository()->record_event( $data );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		return new WP_REST_Response( $result, 201 );
	}

	public function create_content( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$result = GCU_Plugin::instance()->repository()->create_content_draft( is_array( $data ) ? $data : array(), get_current_user_id() );
		return is_wp_error( $result ) ? $result : new WP_REST_Response( $result, 201 );
	}

	public function create_placement( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$result = GCU_Plugin::instance()->repository()->create_placement( is_array( $data ) ? $data : array() );
		return is_wp_error( $result ) ? $result : new WP_REST_Response( $result, 201 );
	}

	public function create_experiment( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$result = GCU_Plugin::instance()->repository()->create_experiment( is_array( $data ) ? $data : array() );
		return is_wp_error( $result ) ? $result : new WP_REST_Response( $result, 201 );
	}

	public function withdraw_claim( WP_REST_Request $request ) {
		$result = GCU_Plugin::instance()->repository()->withdraw_claim( $request['claim_key'], $request->get_param( 'expected_version' ), $request->get_param( 'reason' ) );
		return is_wp_error( $result ) ? $result : rest_ensure_response( $result );
	}

	public function transition( WP_REST_Request $request ) {
		$result = GCU_Plugin::instance()->repository()->transition(
			$request['machine'],
			$request['public_id'],
			$request->get_param( 'expected_version' ),
			$request->get_param( 'target' ),
			$request->get_param( 'reason' )
		);
		return is_wp_error( $result ) ? $result : rest_ensure_response( $result );
	}

	public function funnel( WP_REST_Request $request ) {
		$result = GCU_Plugin::instance()->repository()->funnel_summary( $request->get_param( 'days' ) );
		return is_wp_error( $result ) ? $result : rest_ensure_response( $result );
	}

	public function health() {
		return rest_ensure_response( GCU_Plugin::instance()->observability()->health_report() );
	}

	public function verify_event_token( WP_REST_Request $request ) {
		$identity = is_user_logged_in() ? 'u:' . get_current_user_id() : 'a:' . ( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown' );
		$bucket = 'gcu_rate_' . hash_hmac( 'sha256', $identity, wp_salt( 'auth' ) );
		$count = (int) get_transient( $bucket );
		if ( $count >= GCU_Policy::EVENT_RATE_LIMIT ) {
			return new WP_Error( 'gcu_rate_limited', __( 'Too many measurement requests. Please retry later.', 'global-clinic-usp-integration' ), array( 'status' => 429 ) );
		}
		set_transient( $bucket, $count + 1, MINUTE_IN_SECONDS );
		$token = sanitize_text_field( (string) $request->get_header( 'X-GCU-Event-Token' ) );
		if ( ! preg_match( '/^[a-f0-9\-]{36}\.[a-f0-9]{64}$/', $token ) ) {
			return new WP_Error( 'gcu_event_token_missing', __( 'A valid event token is required.', 'global-clinic-usp-integration' ), array( 'status' => 403 ) );
		}
		list( $id, $signature ) = explode( '.', $token, 2 );
		$expected = hash_hmac( 'sha256', $id, wp_salt( 'nonce' ) );
		if ( ! hash_equals( $expected, $signature ) ) {
			return new WP_Error( 'gcu_event_token_invalid', __( 'The event token is invalid.', 'global-clinic-usp-integration' ), array( 'status' => 403 ) );
		}
		$key = 'gcu_event_token_' . str_replace( '-', '', $id );
		if ( ! get_transient( $key ) ) {
			return new WP_Error( 'gcu_event_token_expired', __( 'The event token expired or was already used.', 'global-clinic-usp-integration' ), array( 'status' => 403 ) );
		}
		delete_transient( $key );
		return true;
	}

	public function can_manage_content() {
		return GCU_Capabilities::can( GCU_Capabilities::MANAGE_CONTENT, null, 'rest_content' );
	}

	public function can_manage_workflow( WP_REST_Request $request ) {
		$machine = sanitize_key( $request['machine'] );
		$cap = 'copy' === $machine ? GCU_Capabilities::MANAGE_CONTENT : ( 'placement' === $machine ? GCU_Capabilities::MANAGE_PLACEMENTS : GCU_Capabilities::MANAGE_EXPERIMENTS );
		return GCU_Capabilities::can( $cap, $request['public_id'], 'rest_transition' );
	}

	public function can_manage_placements() {
		return GCU_Capabilities::can( GCU_Capabilities::MANAGE_PLACEMENTS, null, 'rest_placements' );
	}

	public function can_manage_experiments() {
		return GCU_Capabilities::can( GCU_Capabilities::MANAGE_EXPERIMENTS, null, 'rest_experiments' );
	}

	public function can_approve_claims( WP_REST_Request $request ) {
		return GCU_Capabilities::can( GCU_Capabilities::APPROVE_CLAIMS, $request['claim_key'], 'rest_claim_withdrawal' );
	}

	public function can_view_analytics() {
		return GCU_Capabilities::can( GCU_Capabilities::VIEW_ANALYTICS, null, 'rest_analytics' );
	}

	public function can_system_check() {
		return GCU_Capabilities::can( GCU_Capabilities::SYSTEM_CHECK, null, 'rest_health' );
	}
}
