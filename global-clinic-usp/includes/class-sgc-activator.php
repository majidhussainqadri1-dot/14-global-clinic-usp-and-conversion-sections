<?php

defined( 'ABSPATH' ) || exit;

final class SGC_Activator {
	const SNAPSHOT_OPTION = 'sgc_activation_snapshot';
	const ERRORS_OPTION   = 'sgc_setup_errors';

	public static function activate() {
		self::capture_snapshot();
		self::ensure_options();
		$result = self::ensure_pages();
		update_option( 'sgc_version', SGC_VERSION, false );
		update_option( 'sgc_schema_version', SGC_SCHEMA_VERSION, false );
		set_transient( 'sgc_activation_notice', '1', 120 );
		if ( ! empty( $result['created'] ) ) {
			flush_rewrite_rules();
		}
	}

	public static function maybe_upgrade() {
		$version = (string) get_option( 'sgc_version', '' );
		$schema  = absint( get_option( 'sgc_schema_version', 0 ) );
		if ( SGC_VERSION === $version && SGC_SCHEMA_VERSION === $schema ) {
			return;
		}

		self::capture_snapshot();
		self::ensure_options();
		self::ensure_pages();
		update_option( 'sgc_version', SGC_VERSION, false );
		update_option( 'sgc_schema_version', SGC_SCHEMA_VERSION, false );
	}

	public static function repair() {
		self::capture_snapshot( true );
		self::ensure_options();
		$result = self::ensure_pages();
		update_option( 'sgc_version', SGC_VERSION, false );
		update_option( 'sgc_schema_version', SGC_SCHEMA_VERSION, false );
		if ( ! empty( $result['created'] ) ) {
			flush_rewrite_rules();
		}
		return $result;
	}

	public static function rollback() {
		$snapshot = get_option( self::SNAPSHOT_OPTION, array() );
		if ( ! is_array( $snapshot ) || empty( $snapshot['captured_at'] ) ) {
			return false;
		}

		self::restore_option( 'sgc_page_map', $snapshot );
		self::restore_option( 'sgc_placements', $snapshot );
		self::restore_option( 'sgc_version', $snapshot );
		self::restore_option( 'sgc_schema_version', $snapshot );
		return true;
	}

	public static function health_report() {
		$map    = (array) get_option( 'sgc_page_map', array() );
		$errors = (array) get_option( self::ERRORS_OPTION, array() );
		return array(
			'portal' => self::page_health( ! empty( $map['portal'] ) ? absint( $map['portal'] ) : 0, 'sgc_doctor_portal' ),
			'mission' => self::page_health( ! empty( $map['mission'] ) ? absint( $map['mission'] ) : 0, 'sgc_our_mission' ),
			'errors' => $errors,
			'snapshot' => (bool) get_option( self::SNAPSHOT_OPTION, false ),
		);
	}

	private static function ensure_options() {
		update_option(
			'sgc_placements',
			wp_parse_args( (array) get_option( 'sgc_placements', array() ), SGC_Helpers::defaults() ),
			false
		);
	}

	private static function ensure_pages() {
		$map     = (array) get_option( 'sgc_page_map', array() );
		$created = array();
		$errors  = array();

		$pages = array(
			'portal' => array( 'Doctor Portal', 'doctor-portal', '[sgc_doctor_portal]' ),
			'mission' => array( 'Our Mission', 'our-mission', '[sgc_our_mission]' ),
		);

		foreach ( $pages as $key => $definition ) {
			$result = self::managed_page(
				! empty( $map[ $key ] ) ? absint( $map[ $key ] ) : 0,
				$definition[0],
				$definition[1],
				$definition[2]
			);
			$map[ $key ] = $result['id'];
			if ( $result['created'] ) {
				$created[] = $result['id'];
			}
			if ( $result['error'] ) {
				$errors[] = $result['error'];
			}
		}

		update_option( 'sgc_page_map', $map, false );
		update_option( self::ERRORS_OPTION, $errors, false );
		return array( 'map' => $map, 'created' => $created, 'errors' => $errors );
	}

	private static function managed_page( $id, $title, $slug, $shortcode ) {
		$id   = absint( $id );
		$page = $id ? get_post( $id ) : null;

		if ( $page instanceof WP_Post && 'page' === $page->post_type ) {
			if ( has_shortcode( (string) $page->post_content, trim( $shortcode, '[]' ) ) ) {
				return array( 'id' => $page->ID, 'created' => false, 'error' => null );
			}
			return array(
				'id'      => 0,
				'created' => false,
				'error'   => self::error( 'mapped_page_content_conflict', $slug, $page->ID ),
			);
		}

		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page instanceof WP_Post ) {
			if ( has_shortcode( (string) $page->post_content, trim( $shortcode, '[]' ) ) ) {
				return array( 'id' => $page->ID, 'created' => false, 'error' => null );
			}
			return array(
				'id'      => 0,
				'created' => false,
				'error'   => self::error( 'slug_content_conflict', $slug, $page->ID ),
			);
		}

		$new_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => $shortcode,
				'post_status'  => 'publish',
				'post_type'    => 'page',
			),
			true
		);

		if ( is_wp_error( $new_id ) || ! $new_id ) {
			return array(
				'id'      => 0,
				'created' => false,
				'error'   => self::error( 'page_creation_failed', $slug, 0, is_wp_error( $new_id ) ? $new_id->get_error_message() : '' ),
			);
		}

		update_post_meta( $new_id, '_sgc_managed_page', '1' );
		update_post_meta( $new_id, '_sgc_managed_shortcode', $shortcode );
		update_post_meta( $new_id, '_sgc_managed_version', SGC_VERSION );
		return array( 'id' => absint( $new_id ), 'created' => true, 'error' => null );
	}

	private static function capture_snapshot( $force = false ) {
		if ( ! $force && get_option( self::SNAPSHOT_OPTION, false ) ) {
			return;
		}

		$snapshot = array(
			'captured_at'       => time(),
			'sgc_page_map'       => self::option_snapshot( 'sgc_page_map' ),
			'sgc_placements'     => self::option_snapshot( 'sgc_placements' ),
			'sgc_version'        => self::option_snapshot( 'sgc_version' ),
			'sgc_schema_version' => self::option_snapshot( 'sgc_schema_version' ),
		);
		update_option( self::SNAPSHOT_OPTION, $snapshot, false );
	}

	private static function option_snapshot( $name ) {
		$sentinel = '__sgc_missing_option__';
		$value    = get_option( $name, $sentinel );
		return array( 'exists' => $sentinel !== $value, 'value' => $sentinel !== $value ? $value : null );
	}

	private static function restore_option( $name, array $snapshot ) {
		if ( empty( $snapshot[ $name ]['exists'] ) ) {
			delete_option( $name );
			return;
		}
		update_option( $name, $snapshot[ $name ]['value'], false );
	}

	private static function page_health( $id, $shortcode ) {
		$page = $id ? get_post( $id ) : null;
		if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
			return array( 'status' => 'missing', 'id' => $id, 'url' => '' );
		}
		if ( ! has_shortcode( (string) $page->post_content, $shortcode ) ) {
			return array( 'status' => 'conflict', 'id' => $id, 'url' => '' );
		}
		$url = SGC_Helpers::validated_page_url( $id, array( $shortcode ) );
		return array( 'status' => $url ? 'pass' : 'not_public', 'id' => $id, 'url' => $url );
	}

	private static function error( $code, $slug, $page_id = 0, $detail = '' ) {
		return array(
			'code'      => sanitize_key( $code ),
			'slug'      => sanitize_title( $slug ),
			'page_id'   => absint( $page_id ),
			'detail'    => sanitize_text_field( $detail ),
			'timestamp' => time(),
		);
	}
}
