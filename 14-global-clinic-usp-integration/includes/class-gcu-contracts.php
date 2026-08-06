<?php

defined( 'ABSPATH' ) || exit;

final class GCU_Contracts {
	const CONTRACT_VERSION = 1;

	public function hooks() {
		add_filter( 'sabri_shell_content_slots', array( $this, 'register_slots' ) );
		add_filter( 'sabri_shell_navigation_destinations', array( $this, 'register_destinations' ) );
		add_filter( 'sabri_shell_system_check_report', array( $this, 'system_check_rows' ) );
		add_action( 'DoctorDirectoryAvailable.v1', array( $this, 'consume_availability' ), 10, 1 );
		add_action( 'ClinicBookingAvailable.v1', array( $this, 'consume_availability' ), 10, 1 );
		add_action( 'DoctorOnboardingAvailable.v1', array( $this, 'consume_availability' ), 10, 1 );
		add_action( 'BusinessPolicyChanged.v1', array( $this, 'consume_policy_change' ), 10, 1 );
	}

	public function destination_registry() {
		$registry = array(
			'doctor_directory' => array(
				'owner'            => 'File 07',
				'contract_version' => 1,
				'fallback_path'    => '/doctors/',
			),
			'clinic' => array(
				'owner'            => 'File 08',
				'contract_version' => 1,
				'fallback_path'    => '/worldwide-clinic/',
			),
			'doctor_onboarding' => array(
				'owner'            => 'File 09',
				'contract_version' => 1,
				'fallback_path'    => '/doctor/apply/',
			),
			'how_it_works' => array(
				'owner'            => 'File 14',
				'contract_version' => 1,
				'fallback_path'    => '/clinic/how-it-works/',
			),
		);
		$registry = apply_filters( 'gcu_destination_contract_v1', $registry );
		return is_array( $registry ) ? $registry : array();
	}

	public function destination( $key ) {
		$key      = sanitize_key( $key );
		$registry = $this->destination_registry();
		if ( empty( $registry[ $key ] ) || ! is_array( $registry[ $key ] ) ) {
			return array( 'key' => $key, 'available' => false, 'url' => '', 'reason' => 'unknown_destination', 'owner' => '' );
		}
		$contract = $registry[ $key ];
		$url      = isset( $contract['url'] ) ? GCU_Policy::same_origin_url( $contract['url'] ) : '';
		if ( ! $url && ! empty( $contract['fallback_path'] ) ) {
			$candidate = home_url( '/' . ltrim( $contract['fallback_path'], '/' ) );
			$path      = trim( wp_parse_url( $candidate, PHP_URL_PATH ), '/' );
			$page      = $path ? get_page_by_path( $path, OBJECT, 'page' ) : null;
			if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
				$url = get_permalink( $page );
			} elseif ( 'how_it_works' === $key ) {
				$url = $candidate;
			}
		}
		$available = ! empty( $contract['available'] ) || (bool) $url;
		if ( isset( $contract['contract_version'] ) && (int) $contract['contract_version'] < 1 ) {
			$available = false;
			$url       = '';
		}
		return array(
			'key'              => $key,
			'available'        => $available,
			'url'              => $available ? GCU_Policy::same_origin_url( $url ) : '',
			'reason'           => $available ? 'available' : 'owner_destination_unavailable',
			'owner'            => isset( $contract['owner'] ) ? sanitize_text_field( $contract['owner'] ) : '',
			'contract_version' => isset( $contract['contract_version'] ) ? (int) $contract['contract_version'] : 0,
		);
	}

	public function all_destination_health() {
		$out = array();
		foreach ( array_keys( $this->destination_registry() ) as $key ) {
			$out[ $key ] = $this->destination( $key );
		}
		return $out;
	}

	public function register_slots( $slots ) {
		if ( ! is_array( $slots ) ) {
			$slots = array();
		}
		$slots['file14.global_clinic_primary'] = array(
			'owner'       => 'File 14',
			'version'     => 1,
			'route'       => 'global_clinic',
			'priority'    => 40,
			'render'      => array( GCU_Plugin::instance()->frontend(), 'render_primary_slot' ),
			'cache_scope' => 'locale/audience/content-version',
		);
		$slots['file14.global_clinic_trust'] = array(
			'owner'       => 'File 14',
			'version'     => 1,
			'route'       => 'global_clinic',
			'priority'    => 50,
			'render'      => array( GCU_Plugin::instance()->frontend(), 'render_trust_slot' ),
			'cache_scope' => 'locale/content-version',
		);
		return $slots;
	}

	public function register_destinations( $destinations ) {
		if ( ! is_array( $destinations ) ) {
			$destinations = array();
		}
		$destinations['global_clinic'] = array(
			'label'      => __( 'Global Clinic', 'global-clinic-usp-integration' ),
			'group'      => 'clinic',
			'url'        => home_url( '/global-clinic/' ),
			'owner'      => 'File 14',
			'version'    => 1,
			'order'      => 60,
			'icon'       => 'globe-heart',
		);
		return $destinations;
	}

	public function system_check_rows( $rows ) {
		if ( ! is_array( $rows ) ) {
			$rows = array();
		}
		foreach ( $this->all_destination_health() as $key => $health ) {
			$rows[] = array(
				'label'  => sprintf( __( 'File 14 destination: %s', 'global-clinic-usp-integration' ), $key ),
				'value'  => $health['reason'],
				'status' => $health['available'] ? 'pass' : 'warn',
			);
		}
		return $rows;
	}

	public function consume_availability( $payload ) {
		$event = current_filter();
		$allowed = array( 'DoctorDirectoryAvailable.v1', 'ClinicBookingAvailable.v1', 'DoctorOnboardingAvailable.v1' );
		if ( ! in_array( $event, $allowed, true ) || ! is_array( $payload ) ) {
			return;
		}
		if ( ! GCU_Plugin::instance()->repository()->accept_inbound_event( $event, $payload ) ) {
			return;
		}
		update_option(
			'gcu_dependency_' . sanitize_key( $event ),
			array(
				'event_id'    => isset( $payload['event_id'] ) ? sanitize_text_field( $payload['event_id'] ) : '',
				'available'   => ! empty( $payload['available'] ),
				'received_at' => time(),
			),
			false
		);
	}

	public function consume_policy_change( $payload ) {
		if ( ! is_array( $payload ) || ! GCU_Plugin::instance()->repository()->accept_inbound_event( 'BusinessPolicyChanged.v1', $payload ) ) {
			return;
		}
		update_option(
			'gcu_policy_revalidation_required',
			array(
				'event_id'    => is_array( $payload ) && isset( $payload['event_id'] ) ? sanitize_text_field( $payload['event_id'] ) : '',
				'received_at' => time(),
			),
			false
		);
	}
}
