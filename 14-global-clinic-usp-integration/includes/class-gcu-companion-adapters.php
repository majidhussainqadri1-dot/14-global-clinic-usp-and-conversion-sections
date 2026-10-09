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
	const REVIEW_BASELINE = '2026-10-09-review20-r4-contract-truth-v1';

	const FILE00_MIN_VERSION = '1.2.44';
	const FILE00_MIN_CONTRACT = '1.2.3';
	const FILE00_MAX_CONTRACT_EXCLUSIVE = '2.0.0';
	const FILE01_MIN_VERSION = '2.0.1';
	const FILE01_MIN_CONTRACT = '2.0.0';
	const FILE01_MAX_CONTRACT_EXCLUSIVE = '3.0.0';
	const FILE07_MIN_VERSION = '1.2.1';
	const FILE07_MIN_CONTRACT = '1.2.1';
	const FILE07_MAX_CONTRACT_EXCLUSIVE = '2.0.0';
	const FILE08_MIN_VERSION = '1.2.15';
	const FILE08_MIN_API = '1.0.0';
	const FILE08_MAX_API_EXCLUSIVE = '2.0.0';
	const FILE09_MIN_VERSION = '1.3.0';
	const FILE09_MIN_CONTRACT = '1.1.0';
	const FILE09_MAX_CONTRACT_EXCLUSIVE = '2.0.0';
	const FILE19_MIN_VERSION = '3.0.5';
	const FILE20_MIN_VERSION = '1.4.17';
	const FILE20_MIN_CONTRACT = '1.0.0';
	const FILE20_MAX_CONTRACT_EXCLUSIVE = '2.0.0';
	const FILE24_MIN_VERSION = '0.99.0';
	const FILE25_MIN_VERSION = '0.15.0';
	const FILE25_MIN_CONTRACT = '1.9.0';
	const FILE25_MAX_CONTRACT_EXCLUSIVE = '2.0.0';
	const FILE25_MIN_COMPONENT_CONTRACT = '1.2.0';
	const FILE25_MAX_COMPONENT_CONTRACT_EXCLUSIVE = '2.0.0';

	private static function version_at_least( $version, $minimum ) {
		$version = trim( (string) $version );
		return '' !== $version && version_compare( $version, $minimum, '>=' );
	}

	private static function version_in_range( $version, $minimum, $maximum_exclusive ) {
		$version = trim( (string) $version );
		return self::version_at_least( $version, $minimum )
			&& ( '' === $maximum_exclusive || version_compare( $version, $maximum_exclusive, '<' ) );
	}

	/**
	 * Execute a companion-owner read/action boundary without allowing an
	 * exception in another module to take down File 14. Failures are logged
	 * with bounded non-PII context and callers must choose a fail-closed fallback.
	 */
	private static function owner_call( $owner, $surface, $callback, $fallback = null ) {
		try {
			return call_user_func( $callback );
		} catch ( Throwable $exception ) {
			if ( class_exists( 'GCU_Observability' ) ) {
				GCU_Observability::log(
					'warning',
					'companion_contract_exception',
					array(
						'owner'           => sanitize_key( (string) $owner ),
						'surface'         => sanitize_key( (string) $surface ),
						'exception_class' => sanitize_key( get_class( $exception ) ),
					)
				);
			}
			return $fallback;
		}
	}

	public static function hooks() {
		add_filter( 'smc_restricted_capabilities', array( __CLASS__, 'file00_restricted_capabilities' ), 30, 1 );
		if ( is_admin() ) {
			add_action( 'admin_init', array( __CLASS__, 'sync_file01_registry' ), 80 );
		}
		add_filter( 'sabri_shell_context_navigation_fallback_url', array( __CLASS__, 'context_fallback_url' ), 30, 2 );
		add_filter( 'sabri_shell_route_result_allowed', array( __CLASS__, 'file20_route_result_allowed' ), 30, 5 );
		add_filter( 'sabri_shell_system_check_sections', array( __CLASS__, 'file20_system_check_sections' ), 30, 1 );
		add_filter( 'spcrc/module_manifests', array( __CLASS__, 'file24_manifest' ), 30, 1 );
		add_action( 'init', array( __CLASS__, 'register_file19_producer' ), 40 );
		add_action( 'gcu_operational_alert_v1', array( __CLASS__, 'notify_file19' ), 30, 1 );
	}

	public static function file00_available() {
		return defined( 'SMC_VERSION' )
			&& defined( 'SMC_CONTRACT_VERSION' )
			&& self::version_at_least( SMC_VERSION, self::FILE00_MIN_VERSION )
			&& self::version_in_range( SMC_CONTRACT_VERSION, self::FILE00_MIN_CONTRACT, self::FILE00_MAX_CONTRACT_EXCLUSIVE )
			&& class_exists( 'SMC_Contracts' )
			&& is_callable( array( 'SMC_Contracts', 'assertions' ) );
	}

	public static function file00_assertions( $user_id ) {
		$user_id = absint( $user_id );
		if ( ! $user_id || ! self::file00_available() ) {
			return new WP_Error( 'gcu_file00_unavailable', __( 'The canonical File 00 authorization provider is unavailable.', 'global-clinic-usp-integration' ) );
		}

		$assertions = self::owner_call(
			'file00',
			'authorization_assertions',
			static function() use ( $user_id ) {
				$claim = SMC_Contracts::assertions( $user_id );
				if ( is_array( $claim ) && function_exists( 'apply_filters' ) ) {
					// Consume File 00's current assertion hardening (including action-time age containment).
					$claim = apply_filters( 'smc_assertions_v1', $claim, $user_id );
				}
				return $claim;
			},
			null
		);

		if ( ! is_array( $assertions ) || absint( isset( $assertions['user_id'] ) ? $assertions['user_id'] : 0 ) !== $user_id ) {
			return new WP_Error( 'gcu_file00_claim_invalid', __( 'The canonical File 00 authorization claim is invalid.', 'global-clinic-usp-integration' ) );
		}

		$version = isset( $assertions['contract_version'] ) ? (string) $assertions['contract_version'] : '';
		if (
			! preg_match( '/^\d+\.\d+(?:\.\d+)?$/', $version )
			|| ! self::version_in_range( $version, self::FILE00_MIN_CONTRACT, self::FILE00_MAX_CONTRACT_EXCLUSIVE )
		) {
			return new WP_Error( 'gcu_file00_contract_invalid', __( 'The canonical File 00 authorization contract version is invalid or unsupported.', 'global-clinic-usp-integration' ) );
		}

		return $assertions;
	}

	public static function file00_restricted_capabilities( $capabilities ) {
		$capabilities = is_array( $capabilities ) ? $capabilities : array();
		return array_values( array_unique( array_merge( $capabilities, GCU_Capabilities::all() ) ) );
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
		$blocked_statuses = array( 'rejected', 'suspended', 'expired', 'appeal_review', 'erasure_pending', 'invalid_application', 'effects_reconciliation' );
		$status = sanitize_key( isset( $claim['status'] ) ? (string) $claim['status'] : '' );
		if (
			'' === $purpose ||
			empty( $claim['eligible'] ) ||
			! empty( $claim['suspended'] ) ||
			in_array( $status, $blocked_statuses, true )
		) {
			return false;
		}

		$allowed = true;
		if ( false !== has_filter( 'gcu_authorize' ) ) {
			// Compatibility filters may restrict an owner-approved action, never elevate it.
			$allowed = (bool) apply_filters( 'gcu_authorize', true, $capability, $object, $purpose );
		}
		return $allowed;
	}

	public static function file07_available() {
		return defined( 'DDD_VERSION' )
			&& defined( 'DDD_CONTRACT_VERSION' )
			&& self::version_at_least( DDD_VERSION, self::FILE07_MIN_VERSION )
			&& self::version_in_range( DDD_CONTRACT_VERSION, self::FILE07_MIN_CONTRACT, self::FILE07_MAX_CONTRACT_EXCLUSIVE )
			&& class_exists( 'DDD_Contracts' )
			&& is_callable( array( 'DDD_Contracts', 'dependency_health' ) )
			&& class_exists( 'DDD_Observability' )
			&& is_callable( array( 'DDD_Observability', 'system_check' ) );
	}

	public static function file08_available() {
		return defined( 'WCA_VERSION' )
			&& self::version_at_least( WCA_VERSION, self::FILE08_MIN_VERSION )
			&& class_exists( 'WCA_Contracts' )
			&& is_callable( array( 'WCA_Contracts', 'contract_manifest' ) )
			&& class_exists( 'WCA_Observability' )
			&& is_callable( array( 'WCA_Observability', 'health' ) );
	}

	public static function file09_available() {
		return defined( 'GDO_VERSION' )
			&& self::version_at_least( GDO_VERSION, self::FILE09_MIN_VERSION )
			&& function_exists( 'gdo_file14_onboarding_destination' );
	}

	public static function destination_probe( $key ) {
		$key = sanitize_key( (string) $key );
		switch ( $key ) {
			case 'doctor_directory':
				if ( self::file07_available() ) {
					$health = self::owner_call( 'file07', 'dependency_health', array( 'DDD_Contracts', 'dependency_health' ), null );
					$system = self::owner_call( 'file07', 'system_check', array( 'DDD_Observability', 'system_check' ), null );
					$overall = is_array( $system ) && isset( $system['overall'] ) ? sanitize_key( (string) $system['overall'] ) : 'fail';
					$ready = is_array( $health )
						&& ! empty( $health['ready'] )
						&& in_array( $overall, array( 'pass', 'degraded' ), true );
					$reason = $ready ? ( 'degraded' === $overall ? 'owner_runtime_degraded_readable' : 'owner_runtime_ready' ) : 'owner_runtime_unhealthy';
					return self::probe_result( $key, 'File 07', $ready, home_url( '/doctors/' ), $reason, DDD_CONTRACT_VERSION );
				}
				return self::probe_result( $key, 'File 07', false, '', 'owner_contract_unavailable_or_incompatible', '' );

			case 'clinic':
				if ( self::file08_available() ) {
					$manifest = self::owner_call( 'file08', 'contract_manifest', array( 'WCA_Contracts', 'contract_manifest' ), null );
					$runtime_health = self::owner_call( 'file08', 'runtime_health', array( 'WCA_Observability', 'health' ), null );
					$routes = is_array( $manifest ) && isset( $manifest['routes'] ) && is_array( $manifest['routes'] ) ? $manifest['routes'] : array();
					$runtime_version = is_array( $manifest ) && isset( $manifest['runtime_version'] ) ? (string) $manifest['runtime_version'] : '';
					$api_version = is_array( $manifest ) && isset( $manifest['api_version'] ) ? (string) $manifest['api_version'] : '';
					$appointments = isset( $routes['appointments'] ) && is_array( $routes['appointments'] ) ? $routes['appointments'] : array();
					$ready = is_array( $manifest )
						&& is_array( $runtime_health )
						&& ! empty( $runtime_health['ok'] )
						&& self::version_at_least( $runtime_version, self::FILE08_MIN_VERSION )
						&& self::version_in_range( $api_version, self::FILE08_MIN_API, self::FILE08_MAX_API_EXCLUSIVE )
						&& '/appointments' === ( isset( $appointments['pattern'] ) ? (string) $appointments['pattern'] : '' )
						&& 0 === (int) ( isset( $manifest['commission_percent'] ) ? $manifest['commission_percent'] : -1 )
						&& empty( $manifest['donation_visibility_link'] );
					return self::probe_result( $key, 'File 08', $ready, home_url( '/appointments/' ), $ready ? 'owner_runtime_ready' : 'owner_runtime_degraded_or_policy_mismatch', $api_version );
				}
				return self::probe_result( $key, 'File 08', false, '', 'owner_contract_unavailable_or_incompatible', '' );

			case 'doctor_onboarding':
				if ( self::file09_available() ) {
					$destination = self::owner_call( 'file09', 'onboarding_destination', 'gdo_file14_onboarding_destination', null );
					$version = is_array( $destination ) && ! empty( $destination['contract_version'] ) ? (string) $destination['contract_version'] : '';
					$contract_ok = self::version_in_range( $version, self::FILE09_MIN_CONTRACT, self::FILE09_MAX_CONTRACT_EXCLUSIVE );
					$owner_ok = is_array( $destination )
						&& 'file09' === sanitize_key( isset( $destination['owner'] ) ? (string) $destination['owner'] : '' )
						&& 'file14' === sanitize_key( isset( $destination['consumer'] ) ? (string) $destination['consumer'] : '' );
					$read_only = is_array( $destination )
						&& array_key_exists( 'writes_data', $destination ) && false === (bool) $destination['writes_data']
						&& array_key_exists( 'automatic_enrollment', $destination ) && false === (bool) $destination['automatic_enrollment']
						&& array_key_exists( 'automatic_verification', $destination ) && false === (bool) $destination['automatic_verification'];
					$ready = $contract_ok && $owner_ok && $read_only && ! empty( $destination['available'] );
					$url = is_array( $destination ) && ! empty( $destination['canonical_url'] ) ? (string) $destination['canonical_url'] : '';
					$reason = is_array( $destination ) && ! empty( $destination['reason_code'] ) ? sanitize_key( (string) $destination['reason_code'] ) : ( $ready ? 'owner_runtime_ready' : 'owner_runtime_degraded_or_contract_mismatch' );
					return self::probe_result( $key, 'File 09', $ready, $url, $reason, $version );
				}
				return self::probe_result( $key, 'File 09', false, '', 'owner_contract_unavailable_or_incompatible', '' );
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

	public static function file01_available() {
		return defined( 'SPF_VERSION' )
			&& defined( 'SPF_CONTRACT_VERSION' )
			&& self::version_at_least( SPF_VERSION, self::FILE01_MIN_VERSION )
			&& self::version_in_range( SPF_CONTRACT_VERSION, self::FILE01_MIN_CONTRACT, self::FILE01_MAX_CONTRACT_EXCLUSIVE )
			&& class_exists( 'SPF_Registry' )
			&& is_callable( array( 'SPF_Registry', 'get_module' ) )
			&& is_callable( array( 'SPF_Registry', 'list_routes' ) );
	}

	public static function file01_route_registry_state() {
		$expected = array(
			'global-clinic'             => '/global-clinic/',
			'find-a-global-doctor'      => '/find-a-global-doctor/',
			'start-your-global-clinic'  => '/start-your-global-clinic/',
			'clinic-how-it-works'       => '/clinic/how-it-works/',
		);
		$state = array(
			'available'              => false,
			'registered'             => false,
			'module_state'           => 'unavailable',
			'contracts_ready'        => false,
			'ready'                  => false,
			'missing'                => array_values( $expected ),
			'conflicts'              => array(),
			'missing_contracts'      => array(),
			'incompatible_contracts' => array(),
		);
		if ( ! self::file01_available() ) {
			return $state;
		}
		$module = self::owner_call( 'file01', 'get_module', static function() { return SPF_Registry::get_module( 'file-14' ); }, null );
		$routes = self::owner_call( 'file01', 'list_routes', array( 'SPF_Registry', 'list_routes' ), new WP_Error( 'gcu_file01_routes_exception' ) );
		$contracts = self::owner_call(
			'file01',
			'list_contracts_for_readiness',
			static function() { return SPF_Registry::list_contracts( array( 'owner_module' => 'file-14', 'limit' => 100 ) ); },
			new WP_Error( 'gcu_file01_contracts_exception' )
		);
		if ( is_wp_error( $routes ) || ! is_array( $routes ) || is_wp_error( $contracts ) || ! is_array( $contracts ) ) {
			$state['available'] = true;
			return $state;
		}
		$state['available'] = true;
		$state['module_state'] = is_array( $module ) && isset( $module['state'] ) ? sanitize_key( (string) $module['state'] ) : 'unavailable';
		$state['registered'] = is_array( $module )
			&& isset( $module['module_key'] )
			&& 'file-14' === sanitize_key( (string) $module['module_key'] )
			&& in_array( $state['module_state'], array( 'registered', 'compatible', 'active' ), true );

		$seen = array();
		foreach ( $routes as $route ) {
			if ( ! is_array( $route ) ) {
				continue;
			}
			$owner = sanitize_key( isset( $route['owner_module'] ) ? (string) $route['owner_module'] : '' );
			$path = isset( $route['route_path'] ) ? (string) $route['route_path'] : '';
			$path = '/' . trim( (string) wp_parse_url( $path, PHP_URL_PATH ), '/' ) . '/';
			if ( '//' === $path ) {
				$path = '/';
			}
			$status = sanitize_key( isset( $route['status'] ) ? (string) $route['status'] : '' );
			foreach ( $expected as $key => $expected_path ) {
				if ( untrailingslashit( $path ) !== untrailingslashit( $expected_path ) ) {
					continue;
				}
				if ( 'file-14' !== $owner ) {
					$state['conflicts'][] = $expected_path . ':' . ( $owner ? $owner : 'unknown-owner' );
					continue;
				}
				if ( in_array( $status, array( 'registered', 'active', 'redirect' ), true ) ) {
					$seen[ $key ] = true;
				}
			}
		}
		$state['missing'] = array();
		foreach ( $expected as $key => $expected_path ) {
			if ( empty( $seen[ $key ] ) ) {
				$state['missing'][] = $expected_path;
			}
		}
		$state['conflicts'] = array_values( array_unique( $state['conflicts'] ) );

		$by_contract = array();
		foreach ( $contracts as $contract ) {
			if ( is_array( $contract ) && isset( $contract['contract_key'], $contract['contract_version'] ) ) {
				$by_contract[ (string) $contract['contract_key'] . '@' . (string) $contract['contract_version'] ] = $contract;
			}
		}
		foreach ( self::file01_registry_contracts() as $wanted ) {
			$key = $wanted['contract_key'] . '@' . $wanted['contract_version'];
			if ( ! isset( $by_contract[ $key ] ) ) {
				$state['missing_contracts'][] = $key;
				continue;
			}
			if ( ! self::file01_contract_current( $by_contract[ $key ], $wanted ) ) {
				$state['incompatible_contracts'][] = $key;
			}
		}
		$state['missing_contracts'] = array_values( array_unique( $state['missing_contracts'] ) );
		$state['incompatible_contracts'] = array_values( array_unique( $state['incompatible_contracts'] ) );
		$state['contracts_ready'] = empty( $state['missing_contracts'] ) && empty( $state['incompatible_contracts'] );
		$state['ready'] = $state['registered']
			&& $state['contracts_ready']
			&& empty( $state['missing'] )
			&& empty( $state['conflicts'] );
		return $state;
	}

	public static function placement_contract_ready( $route, $slot ) {
		$route = sanitize_key( (string) $route );
		$slot = sanitize_key( (string) $slot );
		if (
			'global_clinic' !== $route ||
			! in_array( $slot, array( 'global_clinic_primary', 'global_clinic_trust', 'global_clinic_steps', 'global_clinic_faq' ), true ) ||
			! self::file20_available()
		) {
			return false;
		}
		$registry = self::file01_route_registry_state();
		if ( empty( $registry['ready'] ) ) {
			return false;
		}
		return true;
	}

	public static function file01_manifest() {
		$dependency = static function( $module, $minimum, $purpose, $fail_mode ) {
			return array(
				'module_key'      => $module,
				'minimum_version' => $minimum,
				'maximum_version' => '',
				'purpose'         => $purpose,
				'fail_mode'       => $fail_mode,
			);
		};
		return array(
			'module_key'              => 'file-14',
			'owner_file'              => '14',
			'owner_name'              => 'Global Clinic USP and Conversion Integration',
			'slug'                    => 'global-clinic-usp-integration',
			'namespace_prefix'        => 'GCU_',
			'software_version'        => GCU_VERSION,
			'contract_version'        => '1.0.0',
			'state'                   => 'registered',
			'required'                => array(
				$dependency( 'file-00', self::FILE00_MIN_VERSION, 'Canonical identity, eligibility and action-time authorization assertions.', 'Protected mutations fail closed.' ),
				$dependency( 'file-01', self::FILE01_MIN_VERSION, 'Canonical module, route and contract registry plus platform integration backbone.', 'File 14 route/contract readiness remains degraded and semantic placement activation fails closed.' ),
				$dependency( 'file-07', self::FILE07_MIN_VERSION, 'Canonical doctor discovery destination.', 'Doctor-discovery CTA is unavailable.' ),
				$dependency( 'file-08', self::FILE08_MIN_VERSION, 'Canonical clinic and appointment destination health.', 'Clinic/appointment journey is unavailable.' ),
				$dependency( 'file-09', self::FILE09_MIN_VERSION, 'Canonical doctor onboarding and verification destination.', 'Doctor onboarding CTA is unavailable.' ),
				$dependency( 'file-20', self::FILE20_MIN_VERSION, 'Single global shell, navigation and route presentation.', 'File 14 semantic placements fail closed.' ),
				$dependency( 'file-25', self::FILE25_MIN_VERSION, 'Canonical public visual components and accessibility presentation.', 'Accessible scoped File 14 fallback components are used.' ),
			),
			'optional'                => array(
				$dependency( 'file-19', self::FILE19_MIN_VERSION, 'Unified notification delivery for explicitly addressed operational notices.', 'File 14 continues without notification delivery; no recipient is guessed.' ),
				$dependency( 'file-24', self::FILE24_MIN_VERSION, 'Cross-cutting security, privacy, compliance and resilience assurance.', 'Assurance remains unassessed and release readiness is degraded.' ),
			),
			'capabilities'            => GCU_Capabilities::all(),
			'commands'                => array( 'PublishClinicUSPContent.v1', 'ActivateClinicUSPPlacement.v1', 'StartClinicUSPExperiment.v1', 'WithdrawClinicUSPClaim.v1' ),
			'queries'                 => array( 'GetClinicUSPBlocks.v1', 'GetClinicUSPDestinationHealth.v1', 'GetClinicUSPFunnelSummary.v1', 'GetClinicUSPHealth.v1' ),
			'events'                  => array( 'ClinicUSPContentPublished.v1', 'ClinicUSPCTASelected.v1', 'ClinicUSPClaimWithdrawn.v1', 'DoctorDirectoryAvailable.v1', 'ClinicBookingAvailable.v1', 'DoctorOnboardingAvailable.v1', 'BusinessPolicyChanged.v1' ),
			'routes'                  => array( '/global-clinic/', '/find-a-global-doctor/', '/start-your-global-clinic/', '/clinic/how-it-works/' ),
			'data_classes'            => array( 'public_usp_content', 'public_claim', 'placement', 'experiment', 'privacy_minimized_conversion', 'audit' ),
			'canonical_entities'      => array( 'clinic_usp_content', 'clinic_usp_claim', 'clinic_usp_placement', 'clinic_usp_experiment', 'clinic_usp_conversion_event' ),
			'writes'                  => array(),
			'global_shell_owner'      => false,
			'application_shell_owner' => false,
			'health'                  => array( 'contract' => 'gcu.health.v1', 'callback' => 'GCU_Companion_Adapters::file01_health' ),
		);
	}

	public static function file01_routes() {
		return array(
			array( 'route_key' => 'file14-global-clinic', 'route_path' => '/global-clinic/', 'owner_module' => 'file-14', 'layout_context' => 'minimal', 'status' => 'active', 'destination' => home_url( '/global-clinic/' ), 'redirects' => array() ),
			array( 'route_key' => 'file14-find-global-doctor', 'route_path' => '/find-a-global-doctor/', 'owner_module' => 'file-14', 'layout_context' => 'minimal', 'status' => 'active', 'destination' => home_url( '/find-a-global-doctor/' ), 'redirects' => array() ),
			array( 'route_key' => 'file14-start-global-clinic', 'route_path' => '/start-your-global-clinic/', 'owner_module' => 'file-14', 'layout_context' => 'minimal', 'status' => 'active', 'destination' => home_url( '/start-your-global-clinic/' ), 'redirects' => array() ),
			array( 'route_key' => 'file14-clinic-how-it-works', 'route_path' => '/clinic/how-it-works/', 'owner_module' => 'file-14', 'layout_context' => 'minimal', 'status' => 'active', 'destination' => home_url( '/clinic/how-it-works/' ), 'redirects' => array() ),
		);
	}

	public static function file01_registry_contracts() {
		return array(
			array(
				'contract_key'     => 'gcu.file14.api',
				'contract_version' => '1.0.0',
				'owner_module'     => 'file-14',
				'status'           => 'current',
				'schema'           => array(
					'rest_namespace' => 'gcu/v1',
					'commands'       => array( 'PublishClinicUSPContent.v1', 'ActivateClinicUSPPlacement.v1', 'StartClinicUSPExperiment.v1', 'WithdrawClinicUSPClaim.v1' ),
					'queries'        => array( 'GetClinicUSPBlocks.v1', 'GetClinicUSPDestinationHealth.v1', 'GetClinicUSPFunnelSummary.v1', 'GetClinicUSPHealth.v1' ),
					'authorization'  => 'File 00 assertions plus native File 14 capabilities, object state and purpose checks.',
					'privacy'        => 'Explicit DTOs and privacy-minimized conversion measurement; no health/application/profile detail.',
				),
				'consumers'        => array(),
			),
			array(
				'contract_key'     => 'gcu.file14.events',
				'contract_version' => '1.0.0',
				'owner_module'     => 'file-14',
				'status'           => 'current',
				'schema'           => array(
					'publishes' => array( 'ClinicUSPContentPublished.v1', 'ClinicUSPCTASelected.v1', 'ClinicUSPClaimWithdrawn.v1' ),
					'consumes'  => array( 'DoctorDirectoryAvailable.v1', 'ClinicBookingAvailable.v1', 'DoctorOnboardingAvailable.v1', 'BusinessPolicyChanged.v1' ),
					'semantics' => 'Past-tense facts with idempotent inbox/outbox processing; events never replace owner command authorization.',
				),
				'consumers'        => array(),
			),
		);
	}

	private static function file01_manifest_current( $existing, $wanted ) {
		if ( ! is_array( $existing ) ) {
			return false;
		}
		foreach ( array( 'owner_file', 'owner_name', 'slug', 'namespace_prefix', 'software_version', 'contract_version', 'state', 'required', 'optional', 'capabilities', 'commands', 'queries', 'events', 'routes', 'data_classes', 'canonical_entities', 'writes', 'global_shell_owner', 'application_shell_owner', 'health' ) as $field ) {
			if ( wp_json_encode( $existing[ $field ] ?? null ) !== wp_json_encode( $wanted[ $field ] ?? null ) ) {
				return false;
			}
		}
		return true;
	}

	private static function file01_route_current( $current, $wanted ) {
		return is_array( $current )
			&& ( $current['route_path'] ?? '' ) === $wanted['route_path']
			&& ( $current['owner_module'] ?? '' ) === $wanted['owner_module']
			&& ( $current['layout_context'] ?? '' ) === $wanted['layout_context']
			&& ( $current['status'] ?? '' ) === $wanted['status']
			&& ( $current['destination'] ?? '' ) === $wanted['destination'];
	}

	private static function file01_contract_current( $current, $wanted ) {
		return is_array( $current )
			&& ( $current['owner_module'] ?? '' ) === $wanted['owner_module']
			&& ( $current['status'] ?? '' ) === $wanted['status']
			&& wp_json_encode( $current['schema'] ?? array() ) === wp_json_encode( $wanted['schema'] )
			&& wp_json_encode( $current['consumers'] ?? array() ) === wp_json_encode( $wanted['consumers'] );
	}

	public static function sync_file01_registry() {
		if (
			! self::file01_available() ||
			! is_user_logged_in() ||
			! current_user_can( GCU_Capabilities::MANAGE_CONTENT ) ||
			! current_user_can( 'manage_sabri_foundation' ) ||
			! is_callable( array( 'SPF_Registry', 'register_manifest' ) ) ||
			! is_callable( array( 'SPF_Registry', 'map_route' ) ) ||
			! is_callable( array( 'SPF_Registry', 'list_contracts' ) ) ||
			! is_callable( array( 'SPF_Registry', 'register_contract' ) )
		) {
			return;
		}

		$status = array(
			'version'      => GCU_VERSION,
			'attempted_at' => current_time( 'mysql', true ),
			'manifest'     => false,
			'routes'       => array(),
			'contracts'    => array(),
		);

		$manifest = self::file01_manifest();
		$existing = self::owner_call( 'file01', 'get_module_for_sync', static function() { return SPF_Registry::get_module( 'file-14' ); }, null );
		// Registration never promotes or demotes File 01 maturity implicitly.
		if ( is_array( $existing ) && ! empty( $existing['state'] ) ) {
			$manifest['state'] = sanitize_key( (string) $existing['state'] );
		}
		$context = array( 'purpose' => 'file14_cross_file_registry_sync' );
		if ( is_array( $existing ) && isset( $existing['record_version'] ) ) {
			$context['expected_version'] = (int) $existing['record_version'];
		}
		$current = self::file01_manifest_current( $existing, $manifest );
		$result = $current ? array( 'unchanged' => true ) : self::owner_call(
			'file01',
			'register_manifest',
			static function() use ( $manifest, $context ) { return SPF_Registry::register_manifest( $manifest, $context ); },
			new WP_Error( 'gcu_file01_manifest_exception' )
		);
		if ( is_wp_error( $result ) ) {
			$status['error'] = $result->get_error_code();
			update_option( 'gcu_file01_registry_sync', $status, false );
			return;
		}
		$status['manifest'] = true;

		$routes = self::owner_call( 'file01', 'list_routes_for_sync', array( 'SPF_Registry', 'list_routes' ), new WP_Error( 'gcu_file01_routes_exception' ) );
		if ( is_wp_error( $routes ) ) {
			$status['error'] = $routes->get_error_code();
			update_option( 'gcu_file01_registry_sync', $status, false );
			return;
		}
		$by_route = array();
		foreach ( (array) $routes as $route ) {
			if ( is_array( $route ) && ! empty( $route['route_key'] ) ) {
				$by_route[ (string) $route['route_key'] ] = $route;
			}
		}
		foreach ( self::file01_routes() as $route ) {
			$current_route = $by_route[ $route['route_key'] ] ?? null;
			$route_context = array( 'purpose' => 'file14_cross_file_route_sync' );
			if ( is_array( $current_route ) && isset( $current_route['record_version'] ) ) {
				$route_context['expected_version'] = (int) $current_route['record_version'];
			}
			$route_current = self::file01_route_current( $current_route, $route );
			$mapped = $route_current ? array( 'unchanged' => true ) : self::owner_call(
				'file01',
				'map_route',
				static function() use ( $route, $route_context ) { return SPF_Registry::map_route( $route, $route_context ); },
				new WP_Error( 'gcu_file01_route_exception' )
			);
			$status['routes'][ $route['route_key'] ] = is_wp_error( $mapped ) ? $mapped->get_error_code() : ( $route_current ? 'unchanged' : 'ok' );
		}

		$existing_contracts = self::owner_call(
			'file01',
			'list_contracts',
			static function() { return SPF_Registry::list_contracts( array( 'owner_module' => 'file-14', 'limit' => 100 ) ); },
			new WP_Error( 'gcu_file01_contracts_exception' )
		);
		if ( is_wp_error( $existing_contracts ) ) {
			$status['error'] = $existing_contracts->get_error_code();
			update_option( 'gcu_file01_registry_sync', $status, false );
			return;
		}
		$by_contract = array();
		foreach ( (array) $existing_contracts as $contract ) {
			if ( is_array( $contract ) ) {
				$by_contract[ (string) $contract['contract_key'] . '@' . (string) $contract['contract_version'] ] = $contract;
			}
		}
		foreach ( self::file01_registry_contracts() as $contract ) {
			$key = $contract['contract_key'] . '@' . $contract['contract_version'];
			$current_contract = $by_contract[ $key ] ?? null;
			$contract_context = array( 'purpose' => 'file14_cross_file_contract_sync' );
			if ( is_array( $current_contract ) && isset( $current_contract['record_version'] ) ) {
				$contract_context['expected_version'] = (int) $current_contract['record_version'];
			}
			$contract_current = self::file01_contract_current( $current_contract, $contract );
			$registered = $contract_current ? array( 'unchanged' => true ) : self::owner_call(
				'file01',
				'register_contract',
				static function() use ( $contract, $contract_context ) { return SPF_Registry::register_contract( $contract, $contract_context ); },
				new WP_Error( 'gcu_file01_contract_exception' )
			);
			$status['contracts'][ $key ] = is_wp_error( $registered ) ? $registered->get_error_code() : ( $contract_current ? 'unchanged' : 'ok' );
		}

		update_option( 'gcu_file01_registry_sync', $status, false );
	}

	public static function file20_contract() {
		if (
			! defined( 'SABRI_SHELL_VERSION' )
			|| ! self::version_at_least( SABRI_SHELL_VERSION, self::FILE20_MIN_VERSION )
			|| ! class_exists( 'Sabri\\UnifiedShell\\Plugin' )
			|| ! class_exists( 'Sabri\\UnifiedShell\\CentralPlanContract' )
			|| ! is_callable( array( 'Sabri\\UnifiedShell\\CentralPlanContract', 'canonical_contracts' ) )
		) {
			return array();
		}
		$registry = self::owner_call(
			'file20',
			'central_plan_contract_registry',
			array( 'Sabri\\UnifiedShell\\CentralPlanContract', 'canonical_contracts' ),
			null
		);
		$row = is_array( $registry ) && isset( $registry['14'] ) && is_array( $registry['14'] ) ? $registry['14'] : array();
		$version = isset( $row['contract_version'] ) ? (string) $row['contract_version'] : '';
		if (
			empty( $row )
			|| ! self::version_in_range( $version, self::FILE20_MIN_CONTRACT, self::FILE20_MAX_CONTRACT_EXCLUSIVE )
			|| 'approved-clinic-cta' !== sanitize_key( isset( $row['native_scope'] ) ? (string) $row['native_scope'] : '' )
			|| 'slots-only' !== sanitize_key( isset( $row['file20_boundary'] ) ? (string) $row['file20_boundary'] : '' )
			|| 'cta-hidden' !== sanitize_key( isset( $row['failure_behavior'] ) ? (string) $row['failure_behavior'] : '' )
			|| 'owner-aware-bounded' !== sanitize_key( isset( $row['cache_policy'] ) ? (string) $row['cache_policy'] : '' )
		) {
			return array();
		}
		return $row;
	}

	public static function file20_available() {
		return ! empty( self::file20_contract() );
	}

	public static function file25_contract() {
		if (
			! defined( 'SABRI_PUBLIC_EXPERIENCE_VERSION' )
			|| ! self::version_at_least( SABRI_PUBLIC_EXPERIENCE_VERSION, self::FILE25_MIN_VERSION )
			|| ! function_exists( 'sabri_visual_experience_contract' )
			|| ! function_exists( 'sabri_visual_experience_render_state' )
		) {
			return array();
		}
		$contract = self::owner_call( 'file25', 'visual_contract', 'sabri_visual_experience_contract', null );
		if ( ! is_array( $contract ) ) {
			return array();
		}
		$version = isset( $contract['contract_version'] ) ? (string) $contract['contract_version'] : '';
		$runtime = isset( $contract['runtime_version'] ) ? (string) $contract['runtime_version'] : '';
		$components = isset( $contract['components'] ) && is_array( $contract['components'] ) ? $contract['components'] : array();
		$component_version = isset( $components['contract_version'] ) ? (string) $components['contract_version'] : '';
		if (
			! self::version_in_range( $version, self::FILE25_MIN_CONTRACT, self::FILE25_MAX_CONTRACT_EXCLUSIVE )
			|| ! self::version_at_least( $runtime, self::FILE25_MIN_VERSION )
			|| ! self::version_in_range( $component_version, self::FILE25_MIN_COMPONENT_CONTRACT, self::FILE25_MAX_COMPONENT_CONTRACT_EXCLUSIVE )
			|| 'file-25' !== sanitize_key( isset( $contract['visual_system_owner'] ) ? (string) $contract['visual_system_owner'] : '' )
			|| 'file-20' !== sanitize_key( isset( $contract['global_shell_owner'] ) ? (string) $contract['global_shell_owner'] : '' )
		) {
			return array();
		}
		return $contract;
	}

	public static function file25_available() {
		return ! empty( self::file25_contract() );
	}

	public static function file24_available() {
		return defined( 'SPCRC_VERSION' )
			&& self::version_at_least( SPCRC_VERSION, self::FILE24_MIN_VERSION )
			&& function_exists( 'did_action' )
			&& did_action( 'spcrc/booted' ) > 0
			&& class_exists( 'Sabri\\Platform\\Security\\Plugin' )
			&& false !== has_filter( 'spcrc/governed_artifact_registry' );
	}

	public static function file19_available() {
		return defined( 'SUN_VERSION' )
			&& self::version_at_least( SUN_VERSION, self::FILE19_MIN_VERSION )
			&& function_exists( 'sun_ingest_domain_event' )
			&& function_exists( 'sun_register_notification_producer' );
	}

	public static function file01_health() {
		$runtime = GCU_Install::ready_for_runtime();
		$registry = self::file01_route_registry_state();
		return array(
			'available'                => ! is_wp_error( $runtime ),
			'route_registry_ready'     => ! empty( $registry['ready'] ),
			'module_state'             => isset( $registry['module_state'] ) ? $registry['module_state'] : 'unavailable',
			'contracts_ready'          => ! empty( $registry['contracts_ready'] ),
			'missing_routes'           => isset( $registry['missing'] ) ? array_values( $registry['missing'] ) : array(),
			'route_conflicts'          => isset( $registry['conflicts'] ) ? array_values( $registry['conflicts'] ) : array(),
			'missing_contracts'        => isset( $registry['missing_contracts'] ) ? array_values( $registry['missing_contracts'] ) : array(),
			'incompatible_contracts'   => isset( $registry['incompatible_contracts'] ) ? array_values( $registry['incompatible_contracts'] ) : array(),
			'version'                  => GCU_VERSION,
			'contract'                 => 'gcu.health.v1',
		);
	}

	public static function register_file19_producer() {
		if ( ! self::file19_available() ) {
			return false;
		}
		return (bool) self::owner_call(
			'file19',
			'register_producer',
			static function() {
				return sun_register_notification_producer(
					'file14-global-clinic-usp',
					array(
						'owner'               => 'File 14',
						'event_types'         => array( 'ClinicUSP.OperationalAlert' ),
						'schema_versions'     => array( '1.0' ),
						'allowed_data_fields' => array( 'summary', 'status', 'count', 'actions', 'why', 'group_key' ),
					)
				);
			},
			false
		);
	}

	public static function notify_file19( $alert ) {
		if ( ! self::file19_available() || ! is_array( $alert ) ) {
			return false;
		}
		self::register_file19_producer();
		$recipients = apply_filters( 'gcu_operational_notification_recipients', array(), $alert );
		$recipients = is_array( $recipients ) ? array_values( array_unique( array_filter( array_map( 'absint', $recipients ) ) ) ) : array();
		if ( ! $recipients ) {
			// File 19 requires explicit canonical recipients. File 14 never guesses operators.
			return false;
		}
		$severity = sanitize_key( isset( $alert['severity'] ) ? (string) $alert['severity'] : 'warning' );
		$report = isset( $alert['report'] ) && is_array( $alert['report'] ) ? $alert['report'] : array();
		$problems = 0;
		foreach ( array( 'missing_tables', 'non_innodb_tables', 'localization_missing' ) as $key ) {
			if ( ! empty( $report[ $key ] ) && is_array( $report[ $key ] ) ) {
				$problems += count( $report[ $key ] );
			}
		}
		if ( isset( $report['stale_claims'] ) ) {
			$problems += max( 0, (int) $report['stale_claims'] );
		}
		$event_id = 'gcu-alert-' . substr( hash( 'sha256', wp_json_encode( array( GCU_VERSION, $severity, $problems, gmdate( 'Y-m-d-H' ) ) ) ), 0, 32 );
		$event = array(
			'producer'       => 'file14-global-clinic-usp',
			'owner'          => 'File 14',
			'event_id'       => $event_id,
			'event_type'     => 'ClinicUSP.OperationalAlert',
			'schema_version' => '1.0',
			'occurred_at'    => gmdate( 'c' ),
			'recipients'     => $recipients,
			'category'       => 'system',
			'priority'       => in_array( $severity, array( 'critical', 'error' ), true ) ? 'critical' : 'high',
			'sensitivity'    => 'restricted',
			'deep_link'      => admin_url( 'admin.php?page=gcu-settings' ),
			'deep_context'   => 'file14-health',
			'trace_id'       => GCU_Policy::trace_id(),
			'idempotency_key'=> $event_id,
			'source_version' => GCU_VERSION,
			'data'           => array(
				'summary'   => __( 'File 14 detected an operational condition that requires review.', 'global-clinic-usp-integration' ),
				'status'    => $severity,
				'count'     => $problems,
				'actions'   => 'open_file14_health',
				'why'       => 'governance_health_check',
				'group_key' => 'file14-operational-health',
			),
		);
		$result = self::owner_call(
			'file19',
			'ingest_domain_event',
			static function() use ( $event ) { return sun_ingest_domain_event( $event ); },
			new WP_Error( 'gcu_file19_ingest_exception' )
		);
		return ! is_wp_error( $result );
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
		if ( self::file25_available() ) {
			$state = self::owner_call(
				'file25',
				'render_state',
				static function() use ( $type, $title, $message ) {
					return sabri_visual_experience_render_state(
						array(
							'type'    => sanitize_key( $type ),
							'title'   => sanitize_text_field( $title ),
							'message' => sanitize_text_field( $message ),
						)
					);
				},
				''
			);
			return is_string( $state ) ? $state : '';
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
			'posture'                => 'unassessed',
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
			'last_security_test'     => '',
			'verification_level'     => 'not-applicable',
			'contract_version'       => '1.0.0',
			'canonical_data_owner'   => 'File 14 USP copy placements experiments and conversion measurement',
			'canonical_action_owner' => 'File 14 governed content placement experiment and measurement actions',
			'evidence_source'        => 'file14-review20-r4-contract-truth-20261009',
			'degraded_behavior'      => 'Protected actions fail closed and unavailable companion destinations remain unavailable without permissive fallback.',
			'release_gate'           => 'Repository QA is necessary only; staging restore accessibility Founder acceptance deployment and live verification remain separate.',
		);
		return $manifests;
	}

	public static function dependency_health() {
		$file01 = self::file01_route_registry_state();
		return array(
			'file00_authorization'   => self::file00_available(),
			'file01_route_registry'  => ! empty( $file01['ready'] ),
			'file07_directory'       => ! empty( self::destination_probe( 'doctor_directory' )['available'] ),
			'file08_clinic'          => ! empty( self::destination_probe( 'clinic' )['available'] ),
			'file09_onboarding'      => ! empty( self::destination_probe( 'doctor_onboarding' )['available'] ),
			'file19_notifications'   => self::file19_available(),
			'file20_shell'           => self::file20_available(),
			'file24_assurance'       => self::file24_available(),
			'file25_visual'          => self::file25_available(),
		);
	}
}
