<?php

defined( 'ABSPATH' ) || exit;

final class GCU_Capabilities {
	const MANAGE_CONTENT     = 'gcu_manage_content';
	const APPROVE_CLAIMS     = 'gcu_approve_claims';
	const MANAGE_PLACEMENTS  = 'gcu_manage_placements';
	const MANAGE_EXPERIMENTS = 'gcu_manage_experiments';
	const VIEW_ANALYTICS     = 'gcu_view_analytics';
	const SYSTEM_CHECK       = 'gcu_system_check';

	public static function all() {
		return array(
			self::MANAGE_CONTENT,
			self::APPROVE_CLAIMS,
			self::MANAGE_PLACEMENTS,
			self::MANAGE_EXPERIMENTS,
			self::VIEW_ANALYTICS,
			self::SYSTEM_CHECK,
		);
	}

	public static function install() {
		$role = get_role( 'administrator' );
		if ( $role ) {
			foreach ( self::all() as $capability ) {
				$role->add_cap( $capability );
			}
		}
	}

	public static function remove() {
		$role = get_role( 'administrator' );
		if ( $role ) {
			foreach ( self::all() as $capability ) {
				$role->remove_cap( $capability );
			}
		}
	}

	public static function authorization_adapter_available() {
		return function_exists( 'has_filter' ) && false !== has_filter( 'gcu_authorize' );
	}

	public static function can( $capability, $object = null, $purpose = '' ) {
		$allowed = current_user_can( $capability );
		if ( ! $allowed ) { return false; }
		// File 00 owns versioned authorization truth. Native WordPress capabilities are necessary, never sufficient.
		if ( ! self::authorization_adapter_available() ) { return false; }
		return (bool) apply_filters( 'gcu_authorize', true, $capability, $object, sanitize_key( $purpose ) );
	}

	public static function require_capability( $capability, $object = null, $purpose = '' ) {
		if ( ! self::can( $capability, $object, $purpose ) ) {
			return new WP_Error( 'gcu_forbidden', __( 'You are not authorized to perform this action.', 'global-clinic-usp-integration' ), array( 'status' => 403 ) );
		}
		return true;
	}
}
