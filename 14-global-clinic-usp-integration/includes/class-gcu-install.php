<?php

defined( 'ABSPATH' ) || exit;

final class GCU_Install {
	const LOCK_OPTION     = 'gcu_install_lock';
	const VERSION_OPTION  = 'gcu_version';
	const SCHEMA_OPTION   = 'gcu_schema_version';
	const SNAPSHOT_OPTION = 'gcu_rollback_snapshot';
	const MIGRATION_LOG   = 'gcu_migration_log';

	public static function activate() {
		if ( ! self::acquire_lock() ) {
			wp_die( esc_html__( 'Global Clinic USP installation is already running. Please retry shortly.', 'global-clinic-usp-integration' ) );
		}

		try {
			self::capture_snapshot();
			self::schema();
			GCU_Capabilities::install();
			self::seed_governed_content();
			self::migrate_legacy();
			self::schedule();
			update_option( self::VERSION_OPTION, GCU_VERSION, false );
			update_option( self::SCHEMA_OPTION, GCU_SCHEMA_VERSION, false );
			update_option( 'gcu_enabled', 1, false );
			flush_rewrite_rules();
		} finally {
			self::release_lock();
		}
	}

	public static function deactivate() {
		wp_clear_scheduled_hook( 'gcu_daily_governance_check' );
		wp_clear_scheduled_hook( 'gcu_process_outbox' );
		flush_rewrite_rules();
	}

	public static function maybe_upgrade() {
		if ( GCU_VERSION === (string) get_option( self::VERSION_OPTION, '' ) && GCU_SCHEMA_VERSION === (int) get_option( self::SCHEMA_OPTION, 0 ) ) {
			return;
		}
		if ( ! self::acquire_lock() ) {
			return;
		}
		try {
			self::capture_snapshot();
			self::schema();
			GCU_Capabilities::install();
			self::seed_governed_content();
			self::migrate_legacy();
			self::schedule();
			update_option( self::VERSION_OPTION, GCU_VERSION, false );
			update_option( self::SCHEMA_OPTION, GCU_SCHEMA_VERSION, false );
		} finally {
			self::release_lock();
		}
	}

	public static function schema() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset = $wpdb->get_charset_collate();
		$tables  = self::tables();

		$sql = array();
		$sql[] = "CREATE TABLE {$tables['claims']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			public_id char(36) NOT NULL,
			claim_key varchar(191) NOT NULL,
			claim_text text NOT NULL,
			basis text NOT NULL,
			owner_name varchar(191) NOT NULL,
			source_uri text NULL,
			status varchar(32) NOT NULL DEFAULT 'active',
			effective_at datetime NOT NULL,
			review_due_at datetime NULL,
			expires_at datetime NULL,
			is_public tinyint(1) NOT NULL DEFAULT 0,
			row_version bigint(20) unsigned NOT NULL DEFAULT 1,
			created_by bigint(20) unsigned NOT NULL DEFAULT 0,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY public_id (public_id),
			UNIQUE KEY claim_key (claim_key),
			KEY status_review (status, review_due_at)
		) $charset;";

		$sql[] = "CREATE TABLE {$tables['blocks']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			public_id char(36) NOT NULL,
			block_key varchar(191) NOT NULL,
			audience varchar(32) NOT NULL DEFAULT 'all',
			block_type varchar(32) NOT NULL,
			slot_key varchar(191) NOT NULL,
			locale varchar(32) NOT NULL DEFAULT 'en-US',
			title text NOT NULL,
			body longtext NOT NULL,
			cta_label text NULL,
			cta_destination varchar(64) NULL,
			claim_keys longtext NULL,
			status varchar(32) NOT NULL DEFAULT 'draft',
			content_version bigint(20) unsigned NOT NULL DEFAULT 1,
			row_version bigint(20) unsigned NOT NULL DEFAULT 1,
			approved_by bigint(20) unsigned NOT NULL DEFAULT 0,
			approved_at datetime NULL,
			review_due_at datetime NULL,
			created_by bigint(20) unsigned NOT NULL DEFAULT 0,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY public_id (public_id),
			UNIQUE KEY block_locale_version (block_key, locale, content_version),
			KEY active_slot (status, slot_key, audience, locale)
		) $charset;";

		$sql[] = "CREATE TABLE {$tables['placements']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			public_id char(36) NOT NULL,
			placement_key varchar(191) NOT NULL,
			block_key varchar(191) NOT NULL,
			route_key varchar(191) NOT NULL,
			slot_key varchar(191) NOT NULL,
			audience varchar(32) NOT NULL DEFAULT 'all',
			priority int(11) NOT NULL DEFAULT 100,
			status varchar(32) NOT NULL DEFAULT 'planned',
			starts_at datetime NULL,
			ends_at datetime NULL,
			row_version bigint(20) unsigned NOT NULL DEFAULT 1,
			created_by bigint(20) unsigned NOT NULL DEFAULT 0,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY public_id (public_id),
			UNIQUE KEY placement_key (placement_key),
			KEY active_route_slot (status, route_key, slot_key, audience, priority)
		) $charset;";

		$sql[] = "CREATE TABLE {$tables['experiments']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			public_id char(36) NOT NULL,
			experiment_key varchar(191) NOT NULL,
			hypothesis text NOT NULL,
			variants longtext NOT NULL,
			audience varchar(32) NOT NULL DEFAULT 'all',
			guardrails longtext NOT NULL,
			success_metric varchar(191) NOT NULL,
			sample_policy text NOT NULL,
			privacy_policy text NOT NULL,
			status varchar(32) NOT NULL DEFAULT 'proposed',
			starts_at datetime NULL,
			ends_at datetime NULL,
			row_version bigint(20) unsigned NOT NULL DEFAULT 1,
			approved_by bigint(20) unsigned NOT NULL DEFAULT 0,
			created_by bigint(20) unsigned NOT NULL DEFAULT 0,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY public_id (public_id),
			UNIQUE KEY experiment_key (experiment_key),
			KEY status_window (status, starts_at, ends_at)
		) $charset;";

		$sql[] = "CREATE TABLE {$tables['events']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			event_id char(36) NOT NULL,
			event_type varchar(100) NOT NULL,
			event_version smallint(5) unsigned NOT NULL DEFAULT 1,
			funnel_stage varchar(64) NOT NULL,
			destination_key varchar(64) NULL,
			source_value varchar(100) NULL,
			medium_value varchar(100) NULL,
			campaign_value varchar(100) NULL,
			ref_value varchar(100) NULL,
			consent_state varchar(16) NOT NULL DEFAULT 'denied',
			occurred_at datetime NOT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY event_id (event_id),
			KEY funnel_time (funnel_stage, occurred_at),
			KEY campaign_time (campaign_value, occurred_at)
		) $charset;";

		$sql[] = "CREATE TABLE {$tables['audit']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			trace_id varchar(64) NOT NULL,
			actor_id bigint(20) unsigned NOT NULL DEFAULT 0,
			action_name varchar(100) NOT NULL,
			object_type varchar(64) NOT NULL,
			object_id varchar(191) NOT NULL,
			purpose varchar(100) NOT NULL,
			reason text NULL,
			before_hash char(64) NULL,
			after_hash char(64) NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY trace_id (trace_id),
			KEY object_lookup (object_type, object_id),
			KEY actor_time (actor_id, created_at)
		) $charset;";

		$sql[] = "CREATE TABLE {$tables['outbox']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			event_id char(36) NOT NULL,
			event_name varchar(100) NOT NULL,
			event_version smallint(5) unsigned NOT NULL DEFAULT 1,
			payload longtext NOT NULL,
			status varchar(24) NOT NULL DEFAULT 'pending',
			attempts smallint(5) unsigned NOT NULL DEFAULT 0,
			next_attempt_at datetime NULL,
			last_error_code varchar(100) NULL,
			created_at datetime NOT NULL,
			dispatched_at datetime NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY event_id (event_id),
			KEY dispatch_queue (status, next_attempt_at, id)
		) $charset;";

		$sql[] = "CREATE TABLE {$tables['inbox']} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			event_id char(36) NOT NULL,
			event_name varchar(100) NOT NULL,
			payload_hash char(64) NOT NULL,
			processed_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY event_id (event_id),
			KEY event_name (event_name)
		) $charset;";

		foreach ( $sql as $statement ) {
			dbDelta( $statement );
		}
	}

	public static function tables() {
		global $wpdb;
		return array(
			'claims'      => $wpdb->prefix . 'gcu_claims',
			'blocks'      => $wpdb->prefix . 'gcu_content_blocks',
			'placements'  => $wpdb->prefix . 'gcu_placements',
			'experiments' => $wpdb->prefix . 'gcu_experiments',
			'events'      => $wpdb->prefix . 'gcu_conversion_events',
			'audit'       => $wpdb->prefix . 'gcu_audit_log',
			'outbox'      => $wpdb->prefix . 'gcu_event_outbox',
			'inbox'       => $wpdb->prefix . 'gcu_event_inbox',
		);
	}

	public static function seed_governed_content() {
		global $wpdb;
		$tables = self::tables();
		$now    = current_time( 'mysql', true );

		foreach ( GCU_Policy::canonical_claims() as $key => $claim ) {
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tables['claims']} WHERE claim_key = %s", $key ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			if ( $exists ) {
				continue;
			}
			$wpdb->insert(
				$tables['claims'],
				array(
					'public_id'     => wp_generate_uuid4(),
					'claim_key'     => $key,
					'claim_text'    => $claim['text'],
					'basis'         => $claim['basis'],
					'owner_name'    => $claim['owner'],
					'source_uri'    => 'SSH-F14-PLAN-2026-v1.0',
					'status'        => 'active',
					'effective_at'  => gmdate( 'Y-m-d H:i:s', $claim['effective'] ),
					'review_due_at' => gmdate( 'Y-m-d H:i:s', $claim['review_due'] ),
					'is_public'     => empty( $claim['public'] ) ? 0 : 1,
					'created_by'    => get_current_user_id(),
					'created_at'    => $now,
					'updated_at'    => $now,
				),
				array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s' )
			);
		}

		foreach ( GCU_Policy::canonical_block_sets() as $locale => $blocks ) {
			foreach ( $blocks as $key => $block ) {
				$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tables['blocks']} WHERE block_key = %s AND locale = %s AND content_version = 1", $key, $locale ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				if ( $exists ) {
					continue;
				}
				$wpdb->insert(
					$tables['blocks'],
					array(
						'public_id'       => wp_generate_uuid4(),
						'block_key'       => $key,
						'audience'        => $block['audience'],
						'block_type'      => $block['type'],
						'slot_key'        => $block['slot'],
						'locale'          => $locale,
						'title'           => $block['title'],
						'body'            => $block['body'],
						'cta_label'       => $block['cta_label'],
						'cta_destination' => $block['destination'],
						'claim_keys'      => wp_json_encode( $block['claim_keys'] ),
						'status'          => 'active',
						'content_version' => 1,
						'approved_by'     => get_current_user_id(),
						'approved_at'     => $now,
						'review_due_at'   => gmdate( 'Y-m-d H:i:s', time() + ( GCU_Policy::COPY_REVIEW_DAYS * DAY_IN_SECONDS ) ),
						'created_by'      => get_current_user_id(),
						'created_at'      => $now,
						'updated_at'      => $now,
					),
					array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%s', '%s' )
				);
			}
		}

		$placements = array(
			'global_clinic_patient' => array( 'patient_hero', 'global_clinic', 'global_clinic_primary', 'patient', 10 ),
			'global_clinic_doctor'  => array( 'doctor_hero', 'global_clinic', 'global_clinic_primary', 'doctor', 20 ),
			'global_clinic_trust'   => array( 'trust', 'global_clinic', 'global_clinic_trust', 'all', 30 ),
			'global_clinic_steps'   => array( 'how_it_works', 'global_clinic', 'global_clinic_steps', 'all', 40 ),
			'global_clinic_faq_approval' => array( 'faq_approval', 'global_clinic', 'global_clinic_faq', 'all', 50 ),
			'global_clinic_faq_commission' => array( 'faq_commission', 'global_clinic', 'global_clinic_faq', 'all', 51 ),
			'global_clinic_faq_clinical' => array( 'faq_clinical', 'global_clinic', 'global_clinic_faq', 'all', 52 ),
			'global_clinic_faq_outcome' => array( 'faq_outcome', 'global_clinic', 'global_clinic_faq', 'all', 53 ),
		);
		foreach ( $placements as $key => $placement ) {
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tables['placements']} WHERE placement_key = %s", $key ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			if ( $exists ) {
				continue;
			}
			$wpdb->insert(
				$tables['placements'],
				array(
					'public_id'      => wp_generate_uuid4(),
					'placement_key'  => $key,
					'block_key'      => $placement[0],
					'route_key'      => $placement[1],
					'slot_key'       => $placement[2],
					'audience'       => $placement[3],
					'priority'       => $placement[4],
					'status'         => 'active',
					'created_by'     => get_current_user_id(),
					'created_at'     => $now,
					'updated_at'     => $now,
				),
				array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s' )
			);
		}
	}

	public static function migrate_legacy() {
		if ( get_option( 'gcu_legacy_migrated', false ) ) {
			return;
		}
		$legacy = array(
			'sgc_version'        => get_option( 'sgc_version', null ),
			'sgc_schema_version' => get_option( 'sgc_schema_version', null ),
			'sgc_placements'     => get_option( 'sgc_placements', null ),
			'sgc_page_map'       => get_option( 'sgc_page_map', null ),
		);
		$detected = array_filter( $legacy, static function ( $value ) { return null !== $value; } );
		update_option(
			self::MIGRATION_LOG,
			array(
				'detected'       => ! empty( $detected ),
				'legacy_snapshot'=> $detected,
				'action'         => 'read-only inventory; no legacy page, option or companion record deleted or overwritten',
				'migrated_at'    => time(),
			),
			false
		);
		update_option( 'gcu_legacy_migrated', 1, false );
	}

	public static function capture_snapshot( $force = false ) {
		if ( ! $force && get_option( self::SNAPSHOT_OPTION, false ) ) {
			return;
		}
		$names = array( self::VERSION_OPTION, self::SCHEMA_OPTION, 'gcu_enabled', 'gcu_settings', 'gcu_legacy_migrated', self::MIGRATION_LOG );
		$snapshot = array( 'captured_at' => time(), 'options' => array() );
		foreach ( $names as $name ) {
			$sentinel = '__gcu_missing__';
			$value = get_option( $name, $sentinel );
			$snapshot['options'][ $name ] = array( 'exists' => $sentinel !== $value, 'value' => $sentinel !== $value ? $value : null );
		}
		update_option( self::SNAPSHOT_OPTION, $snapshot, false );
	}

	public static function rollback_options() {
		$snapshot = get_option( self::SNAPSHOT_OPTION, array() );
		if ( empty( $snapshot['options'] ) || ! is_array( $snapshot['options'] ) ) {
			return new WP_Error( 'gcu_no_snapshot', __( 'No rollback snapshot is available.', 'global-clinic-usp-integration' ) );
		}
		foreach ( $snapshot['options'] as $name => $entry ) {
			if ( empty( $entry['exists'] ) ) {
				delete_option( $name );
			} else {
				update_option( $name, $entry['value'], false );
			}
		}
		return true;
	}

	public static function schedule() {
		if ( ! wp_next_scheduled( 'gcu_daily_governance_check' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'gcu_daily_governance_check' );
		}
		if ( ! wp_next_scheduled( 'gcu_process_outbox' ) ) {
			wp_schedule_event( time() + 300, 'hourly', 'gcu_process_outbox' );
		}
	}

	private static function acquire_lock() {
		$now = time();
		$lock = (int) get_option( self::LOCK_OPTION, 0 );
		if ( $lock && $lock > $now - 300 ) {
			return false;
		}
		return add_option( self::LOCK_OPTION, $now, '', false ) || update_option( self::LOCK_OPTION, $now, false );
	}

	private static function release_lock() {
		delete_option( self::LOCK_OPTION );
	}
}
