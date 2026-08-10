<?php

defined( 'ABSPATH' ) || exit;

/** Additional fail-closed authorization guards for Future CTI governance writes. */
final class GCU_Future_Guards {
	public static function bootstrap() {
		add_filter( 'rest_request_before_callbacks', array( __CLASS__, 'guard_record_publication' ), 8, 3 );
	}

	public static function guard_record_publication( $response, $handler, WP_REST_Request $request ) {
		if ( null !== $response || '/gcu/v1/future/records' !== $request->get_route() || WP_REST_Server::CREATABLE !== $request->get_method() ) {
			return $response;
		}
		$data = $request->get_json_params();
		$data = is_array( $data ) ? $data : array();
		$type = sanitize_key( isset( $data['record_type'] ) ? $data['record_type'] : '' );
		$status = sanitize_key( isset( $data['status'] ) ? $data['status'] : 'draft' );
		$public = ! empty( $data['is_public'] );
		$governed_public_types = array( 'jurisdiction_copy', 'terminology_lock', 'change_log' );
		if ( in_array( $type, $governed_public_types, true ) && ( 'active' === $status || $public ) ) {
			if ( ! GCU_Capabilities::can( GCU_Capabilities::APPROVE_CLAIMS, $type, 'future_public_governance_record' ) ) {
				return new WP_Error( 'gcu_future_founder_approval_required', __( 'Founder-level claim/copy approval is required before this Future Intelligence record can become active or public.', 'global-clinic-usp-integration' ), array( 'status' => 403 ) );
			}
		}
		if ( 'faq_gap' === $type && ( 'active' === $status || $public ) ) {
			return new WP_Error( 'gcu_future_faq_suggestion_cannot_publish', __( 'FAQ Gap Intelligence produces suggestions only. Publish an FAQ through the canonical File 14 content workflow after review.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
		}
		if ( 'ai_draft' === $type && ( 'active' === $status || $public ) ) {
			return new WP_Error( 'gcu_future_ai_draft_cannot_publish', __( 'AI Ethical Copy Assistant output is draft-only and can never auto-publish.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
		}
		if ( 'scenario_note' === $type && $public ) {
			return new WP_Error( 'gcu_future_scenario_note_private', __( 'Scenario notes are internal governance records and cannot be published.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
		}
		return $response;
	}
}
