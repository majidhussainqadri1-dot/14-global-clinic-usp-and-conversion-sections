<?php

defined( 'ABSPATH' ) || exit;

final class SGC_Activator {
	public static function activate() {
		$map = (array) get_option( 'sgc_page_map', array() );

		$map['portal'] = self::managed_page(
			! empty( $map['portal'] ) ? absint( $map['portal'] ) : 0,
			'Doctor Portal',
			'doctor-portal',
			'[sgc_doctor_portal]'
		);

		$map['mission'] = self::managed_page(
			! empty( $map['mission'] ) ? absint( $map['mission'] ) : 0,
			'Our Mission',
			'our-mission',
			'[sgc_our_mission]'
		);

		update_option( 'sgc_page_map', $map, false );
		update_option( 'sgc_placements', wp_parse_args( (array) get_option( 'sgc_placements', array() ), SGC_Helpers::defaults() ), false );
		update_option( 'sgc_version', SGC_VERSION, false );
		set_transient( 'sgc_activation_notice', '1', 120 );
		flush_rewrite_rules();
	}

	private static function managed_page( $id, $title, $slug, $shortcode ) {
		$page = $id ? get_post( $id ) : get_page_by_path( $slug, OBJECT, 'page' );

		if ( $page instanceof WP_Post ) {
			$managed = get_post_meta( $page->ID, '_sgc_managed_page', true );
			if ( $managed || '' === trim( $page->post_content ) || false !== strpos( $page->post_content, '[sgc_' ) ) {
				wp_update_post(
					array(
						'ID'           => $page->ID,
						'post_content' => $shortcode,
						'post_status'  => 'publish',
					)
				);
				update_post_meta( $page->ID, '_sgc_managed_page', '1' );
			}
			return $page->ID;
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

		if ( is_wp_error( $new_id ) ) {
			return 0;
		}

		update_post_meta( $new_id, '_sgc_managed_page', '1' );
		return absint( $new_id );
	}
}
