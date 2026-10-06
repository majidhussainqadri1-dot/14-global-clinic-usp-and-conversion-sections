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
		add_filter( 'smc_restricted_capabilities', array( __CLASS__, 'file00_restricted_capabilities' ), 30, 1 );
		if ( is_admin() ) {
			add_action( 'admin_init', array( __CLASS__, 'sync_file01_registry' ), 80 );
		}
		add_filter( 'sabri_shell_context_navigation_fallback_url', array( __CLASS__, 'context_fallback_url' ), 30, 2 );
		add_filter( 'sabri_shell_route_result_allowed', array( __CLASS__, 'file20_route_result_allowed' ), 30, 5 );
		add_filter( 'sabri_shell_system_check_sections', array( __CLASS__, 'file20_system_check_sections' ), 30, 1 );
		add_filter( 'spcrc/module_manifests', array( __CLASS__, 'file24_manifest' ), 30, 1 );
	}

	public static function file00_available() {
		return class_exists( 'SMC_Contracts' ) && is_callable( array( 'SMC_Contracts', 'assertions' ) );
	}

	public static function file00_assertions( $user_id ) {
		$user_id = absint( $user_id );
		if ( ! $user_id || ! self::file00_available() ) {
			return new WP_Error( 'gcu_file00_unavailable', __( 'The canonical File 00 authorization provider is unavailable.', 'global-clinic-usp-integration' ) );
		}

		$assertions = SMC_Contracts::assertions( $user_id );
		if ( is_array( $assertions ) && function_exists( 'apply_filters' ) ) {
			// Consume File 00's current assertion hardening (including action-time age containment).
			$assertions = apply_filters( 'smc_assertions_v1', $assertions, $user_id );
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
				if ( function_exists( 'gdo_file14_onboarding_destination' ) ) {
					$destination = gdo_file14_onboarding_destination();
					$ready = is_array( $destination ) && ! empty( $destination['available'] ) && 'file09' === sanitize_key( isset( $destination['owner'] ) ? (string) $destination['owner'] : '' );
					$url = is_array( $destination ) && ! empty( $destination['canonical_url'] ) ? (string) $destination['canonical_url'] : '';
					$reason = is_array( $destination ) && ! empty( $destination['reason_code'] ) ? sanitize_key( (string) $destination['reason_code'] ) : ( $ready ? 'owner_runtime_ready' : 'owner_runtime_degraded' );
					$version = is_array( $destination ) && ! empty( $destination['contract_version'] ) ? (string) $destination['contract_version'] : '';
					return self::probe_result( $key, 'File 09', $ready, $url, $reason, $version );
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

	public static function file01_available() {
		return class_exists( 'SPF_Registry' )
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
			'available'  => false,
			'registered' => false,
			'ready'      => false,
			'missing'    => array_values( $expected ),
			'conflicts'  => array(),
		);
		if ( ! self::file01_available() ) {
			return $state;
		}
		$module = SPF_Registry::get_module( 'file-14' );
		$routes = SPF_Registry::list_routes();
		if ( is_wp_error( $routes ) || ! is_array( $routes ) ) {
			$state['available'] = true;
			return $state;
		}
		$state['available'] = true;
		$state['registered'] = is_array( $module )
			&& isset( $module['module_key'] )
			&& 'file-14' === sanitize_key( (string) $module['module_key'] );

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
		$state['ready'] = $state['registered'] && empty( $state['missing'] ) && empty( $state['conflicts'] );
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
			'state'                   => 'active',
			'required'                => array(
				$dependency( 'file-00', '1.2.44', 'Canonical identity, eligibility and action-time authorization assertions.', 'Protected mutations fail closed.' ),
				$dependency( 'file-07', '1.2.0', 'Canonical doctor discovery destination.', 'Doctor-discovery CTA is unavailable.' ),
				$dependency( 'file-08', '1.2.15', 'Canonical clinic and appointment destination health.', 'Clinic/appointment journey is unavailable.' ),
				$dependency( 'file-09', '1.3.0', 'Canonical doctor onboarding and verification destination.', 'Doctor onboarding CTA is unavailable.' ),
				$dependency( 'file-20', '1.4.17', 'Single global shell, navigation and route presentation.', 'File 14 semantic placements fail closed.' ),
				$dependency( 'file-25', '0.15.0', 'Canonical public visual components and accessibility presentation.', 'Accessible scoped File 14 fallback components are used.' ),
			),
			'optional'                => array(
				$dependency( 'file-19', '1.0.0', 'Unified notification delivery for future approved operational notices.', 'File 14 continues without notification delivery.' ),
				$dependency( 'file-24', '0.99.0', 'Cross-cutting security, privacy, compliance and resilience assurance.', 'Assurance remains unassessed and release readiness is degraded.' ),
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
			'health'                  => array( 'contract' => 'gcu.health.v1', 'callback' => 'GCU_Observability::health' ),
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
		$existing = SPF_Registry::get_module( 'file-14' );
		$context = array( 'purpose' => 'file14_cross_file_registry_sync' );
		if ( is_array( $existing ) && isset( $existing['record_version'] ) ) {
			$context['expected_version'] = (int) $existing['record_version'];
		}
		$current = self::file01_manifest_current( $existing, $manifest );
		$result = $current ? array( 'unchanged' => true ) : SPF_Registry::register_manifest( $manifest, $context );
		if ( is_wp_error( $result ) ) {
			$status['error'] = $result->get_error_code();
			update_option( 'gcu_file01_registry_sync', $status, false );
			return;
		}
		$status['manifest'] = true;

		$routes = SPF_Registry::list_routes();
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
			$mapped = $route_current ? array( 'unchanged' => true ) : SPF_Registry::map_route( $route, $route_context );
			$status['routes'][ $route['route_key'] ] = is_wp_error( $mapped ) ? $mapped->get_error_code() : ( $route_current ? 'unchanged' : 'ok' );
		}

		$existing_contracts = SPF_Registry::list_contracts( array( 'owner_module' => 'file-14', 'limit' => 100 ) );
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
			$registered = $contract_current ? array( 'unchanged' => true ) : SPF_Registry::register_contract( $contract, $contract_context );
			$status['contracts'][ $key ] = is_wp_error( $registered ) ? $registered->get_error_code() : ( $contract_current ? 'unchanged' : 'ok' );
		}

		update_option( 'gcu_file01_registry_sync', $status, false );
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

	public static function file19_available() {
		return function_exists( 'sun_ingest_domain_event' ) && function_exists( 'sun_register_notification_producer' );
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
			'evidence_source'        => 'file14-review20-cross-file-20261005',
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
