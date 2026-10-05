<?php

defined( 'ABSPATH' ) || exit;

/**
 * Runtime adapters for the current canonical companion-file contracts.
 *
 * File 14 remains the owner of USP copy, placement metadata, experiments and
 * privacy-minimized conversion measurement. These adapters only consume
 * owner-native read/health contracts and never write companion domain truth.
 */
final class GCU_Companion_Adapters {
	const REVIEW_BASELINE = '2026-10-05-review20-cross-file-v1';

	public static function hooks() {
		add_filter( 'sabri_shell_context_navigation_fallback_url', array( __CLASS__, 'context_fallback_url' ), 30, 2 );
		add_filter( 'sabri_shell_route_result_allowed', array( __CLASS__, 'file20_route_result_allowed' ), 30, 5 );
		add_filter( 'sabri_shell_system_check_sections', array( __CLASS__, 'file20_system_check_sections' ), 30, 1 );
		add_filter( 'spcrc/module_manifests', array( __CLASS__, 'file24_manifest' ), 30, 1 );
	}

	public static function file00_available() {
		return function_exists( 'smc_membership_assertions' )
			|| ( class_exists( 'SMC_Contracts' ) && is_callable( array( 'SMC_Contracts', 'assertions' ) ) );
	}

	public static function file00_assertions( $user_id ) {
		$user_id = absint( $user_id );
		if ( ! $user_id || ! self::file00_available() ) {
			return new WP_Error( 'gcu_file00_unavailable', __( 'The canonical File 00 authorization provider is unavailable.', 'global-clinic-usp-integration' ) );
		}

		if ( function_exists( 'smc_membership_assertions' ) ) {
			$assertions = smc_membership_assertions( $user_id );
		} else {
			$assertions = SMC_Contracts::assertions( $user_id );
		}

		if ( ! is_array( $assertions ) || absint( isset( $assertions['user_id'] ) ? $assertions['user_id'] : 0 ) !== $user_id ) {
			return new WP_Error( 'gcu_file00_claim_invalid', __( 'The canonical File 00 authorization claim is invalid.', 'global-clinic-usp-integration' ) );
		}

		$version = isset( $assertions['contract_version'] ) ? (string) $assertions['contract_version'] : '';
		if ( '' === $version || ! preg_match( '/^\d+\.\d+(?:\.\d+)?$/', $version ) ) {
			return new WP_Error( 'gcu_file00_contract_invalid', __( 'The canonical File 00 authorization contract version is invalid.', 'global-clinic-usp-integration' ) );
		}

		return $assertions;
	}

	/**
	 * File 00 is the institutional authorization source. WordPress capabilities
	 * remain necessary in GCU_Capabilities; this method adds the action-time
	 * membership/trust restriction and can only be narrowed by a legacy bridge.
	 */
	public static function authorize( $capability, $object = null, $purpose = '' ) {
		$user_id = get_current_user_id();
		$claim = self::file00_assertions( $user_id );
		if ( is_wp_error( $claim ) ) {
			return false;
		}

		$purpose = sanitize_key( (string) $purpose );
		if ( '' === $purpose || empty( $claim['eligible'] ) || ! empty( $claim['suspended'] ) ) {
			return false;
		}

		$allowed = true;
		if ( false !== has_filter( 'gcu_authorize' ) ) {
			// Compatibility filters may restrict an owner-approved action, never elevate it.
			$allowed = (bool) apply_filters( 'gcu_authorize', true, $capability, $object, $purpose );
		}
		return $allowed;
	}

	public static function destination_probe( $key ) {
		$key = sanitize_key( (string) $key );
		switch ( $key ) {
			case 'doctor_directory':
				if ( class_exists( 'DDD_Contracts' ) && is_callable( array( 'DDD_Contracts', 'dependency_health' ) ) ) {
					$health = DDD_Contracts::dependency_health();
					$ready = is_array( $health ) && ! empty( $health['ready'] );
					return self::probe_result( $key, 'File 07', $ready, home_url( '/doctors/' ), $ready ? 'owner_runtime_ready' : 'owner_runtime_degraded', defined( 'DDD_CONTRACT_VERSION' ) ? DDD_CONTRACT_VERSION : 'runtime' );
				}
				return self::probe_result( $key, 'File 07', false, home_url( '/doctors/' ), 'owner_contract_unavailable', '' );

			case 'clinic':
				if ( class_exists( 'WCA_Contracts' ) && is_callable( array( 'WCA_Contracts', 'contract_manifest' ) ) ) {
					$manifest = WCA_Contracts::contract_manifest();
					$routes = is_array( $manifest ) && isset( $manifest['routes'] ) && is_array( $manifest['routes'] ) ? $manifest['routes'] : array();
					$ready = is_array( $manifest ) && ! empty( $manifest['runtime_version'] ) && isset( $routes['appointments'] );
					$version = is_array( $manifest ) && ! empty( $manifest['api_version'] ) ? (string) $manifest['api_version'] : 'runtime';
					return self::probe_result( $key, 'File 08', $ready, home_url( '/appointments/' ), $ready ? 'owner_runtime_ready' : 'owner_runtime_degraded', $version );
				}
				return self::probe_result( $key, 'File 08', false, '', 'owner_contract_unavailable', '' );

			case 'doctor_onboarding':
				if ( class_exists( 'GDO_Operations' ) && is_callable( array( 'GDO_Operations', 'health' ) ) && class_exists( 'GDO_Plugin' ) && is_callable( array( 'GDO_Plugin', 'application_url' ) ) ) {
					$health = GDO_Operations::health();
					$ready = is_array( $health ) && isset( $health['status'] ) && 'degraded' !== sanitize_key( (string) $health['status'] );
					$url = GDO_Plugin::application_url();
					$version = defined( 'GDO_INTEGRATION_CONTRACT_VERSION' ) ? GDO_INTEGRATION_CONTRACT_VERSION : ( defined( 'GDO_VERSION' ) ? GDO_VERSION : 'runtime' );
					return self::probe_result( $key, 'File 09', $ready, $url, $ready ? 'owner_runtime_ready' : 'owner_runtime_degraded', $version );
				}
				return self::probe_result( $key, 'File 09', false, '', 'owner_contract_unavailable', '' );
		}

		return null;
	}

	private static function probe_result( $key, $owner, $available, $url, $reason, $contract ) {
		$url = GCU_Hardening::strict_same_origin_url( $url );
		$available = (bool) $available && '' !== $url;
		return array(
			'key'         => sanitize_key( $key ),
			'owner'       => sanitize_text_field( $owner ),
			'available'   => $available,
			'url'         => $available ? $url : '',
			'reason'      => $available ? sanitize_key( $reason ) : sanitize_key( $reason ),
			'contract'    => GCU_Hardening::bounded_text( sanitize_text_field( (string) $contract ), 64 ),
			'verified_at' => time(),
			'source'      => 'owner_runtime_probe',
		);
	}

	public static function file20_available() {
		return defined( 'SABRI_SHELL_VERSION' ) && class_exists( 'Sabri\\UnifiedShell\\Plugin' );
	}

	public static function file25_available() {
		return class_exists( 'Sabri\\PublicExperience\\Components' );
	}

	public static function file24_available() {
		return defined( 'SPCRC_VERSION' ) || class_exists( 'Sabri\\Platform\\Security\\Registry\\ModuleRegistry' );
	}

	public static function file20_route_result_allowed( $allowed, $key, $url, $source, $destination ) {
		if ( ! $allowed ) {
			return false;
		}
		if ( 'clinic' !== sanitize_key( (string) $key ) ) {
			return true;
		}
		$safe = GCU_Hardening::strict_same_origin_url( $url );
		if ( '' === $safe ) {
			return false;
		}
		$path = wp_parse_url( $safe, PHP_URL_PATH );
		$path = is_string( $path ) ? '/' . trim( $path, '/' ) : '';
		if ( '/global-clinic' === $path ) {
			return ! is_wp_error( GCU_Install::ready_for_runtime() );
		}
		// File 20 may resolve its existing Worldwide Clinic destination to File 08.
		// File 14 never claims or manufactures a second global navigation owner.
		return true;
	}

	public static function file20_system_check_sections( $sections ) {
		$sections = is_array( $sections ) ? $sections : array();
		$ready = ! is_wp_error( GCU_Install::ready_for_runtime() );
		$sections['file14-global-clinic-usp'] = array(
			'label'              => 'File 14 — Global Clinic USP',
			'status'             => $ready ? 'pass' : 'warn',
			'severity'           => $ready ? 'info' : 'high',
			'version'            => GCU_VERSION,
			'plan_version'       => GCU_PLAN_VERSION,
			'dependency_health'  => self::dependency_health(),
			'destination_health' => GCU_Plugin::instance()->contracts()->public_destination_health(),
			'owner'              => 'File 14',
			'shell_owner'        => 'File 20',
		);
		return $sections;
	}

	public static function context_fallback_url( $default, $home_url ) {
		$path = GCU_Hardening::normalized_request_path();
		$owned = array( '/global-clinic/', '/find-a-global-doctor/', '/start-your-global-clinic/', '/clinic/how-it-works/' );
		if ( in_array( $path, $owned, true ) ) {
			return GCU_Hardening::strict_same_origin_url( home_url( '/global-clinic/' ) );
		}
		return $default;
	}

	public static function visual_state( $type, $title, $message ) {
		if ( self::file25_available() && is_callable( array( 'Sabri\\PublicExperience\\Components', 'render_state' ) ) ) {
			return Sabri\PublicExperience\Components::render_state(
				array(
					'type'    => sanitize_key( $type ),
					'title'   => sanitize_text_field( $title ),
					'message' => sanitize_text_field( $message ),
				)
			);
		}
		return '';
	}

	public static function file24_manifest( $manifests ) {
		$manifests = is_array( $manifests ) ? $manifests : array();
		$tables = class_exists( 'GCU_Install' ) ? array_keys( GCU_Install::tables() ) : array();
		$manifests[] = array(
			'module_key'             => 'file-14-global-clinic-usp',
			'name'                   => 'Global Clinic USP and Conversion Integration',
			'version'                => GCU_VERSION,
			'owner'                  => 'File 14',
			'posture'                => 'foundation',
			'data_classes'           => array( 'public-content', 'public-claim', 'placement', 'experiment', 'consented-conversion-measurement', 'audit' ),
			'public_routes'          => array( '/global-clinic/', '/clinic/how-it-works/', '/find-a-global-doctor/', '/start-your-global-clinic/' ),
			'private_routes'         => array( '/wp-admin/admin.php', '/wp-json/gcu/v1/content', '/wp-json/gcu/v1/placements', '/wp-json/gcu/v1/experiments', '/wp-json/gcu/v1/analytics/funnel', '/wp-json/gcu/v1/health' ),
			'tables'                 => $tables,
			'files'                  => array( 'global-clinic-usp-integration.php' ),
			'capabilities'           => GCU_Capabilities::all(),
			'external_vendors'       => array(),
			'secret_classes'         => array(),
			'privacy_operations'     => array( 'export', 'erase', 'retention' ),
			'exporters'              => array( 'gcu-conversion-attribution' ),
			'erasers'                => array( 'gcu-conversion-attribution' ),
			'emergency_callbacks'    => array( 'gcu_daily_governance_check', 'gcu_operational_alert_v1' ),
			'last_security_test'     => '2026-10-05T10:53:00Z',
			'verification_level'     => 'not-applicable',
			'contract_version'       => '1.0.0',
			'canonical_data_owner'   => 'File 14 USP copy placements experiments and conversion measurement',
			'canonical_action_owner' => 'File 14 governed content placement experiment and measurement actions',
			'evidence_source'        => 'file14-review20-cross-file-20261005',
			'degraded_behavior'      => 'Protected actions fail closed and unavailable companion destinations remain unavailable without permissive fallback.',
			'release_gate'           => 'Repository QA is necessary only; staging restore accessibility Founder acceptance deployment and live verification remain separate.',
		);
		return $manifests;
	}

	public static function dependency_health() {
		return array(
			'file00_authorization' => self::file00_available(),
			'file07_directory'     => ! empty( self::destination_probe( 'doctor_directory' )['available'] ),
			'file08_clinic'        => ! empty( self::destination_probe( 'clinic' )['available'] ),
			'file09_onboarding'    => ! empty( self::destination_probe( 'doctor_onboarding' )['available'] ),
			'file20_shell'         => self::file20_available(),
			'file24_assurance'     => self::file24_available(),
			'file25_visual'        => self::file25_available(),
		);
	}
}
