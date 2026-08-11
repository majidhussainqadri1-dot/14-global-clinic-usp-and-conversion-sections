<?php

defined( 'ABSPATH' ) || exit;

/** Stable File 14 integrity and pseudonymization keys plus one-time legacy migration. */
final class GCU_Integrity {
	const AUDIT_KEY_OPTION = 'gcu_audit_hmac_key_v1';
	const PRIVACY_KEY_OPTION = 'gcu_privacy_hmac_key_v1';
	const MIGRATION_OPTION = 'gcu_integrity_key_migration_v1';

	public static function ensure_keys() {
		foreach ( array( self::AUDIT_KEY_OPTION, self::PRIVACY_KEY_OPTION ) as $option ) {
			$current = (string) get_option( $option, '' );
			if ( preg_match( '/^[a-f0-9]{64}$/', $current ) ) { continue; }
			try { $value = bin2hex( random_bytes( 32 ) ); }
			catch ( Exception $e ) { return new WP_Error( 'gcu_integrity_entropy_unavailable', __( 'File 14 could not establish a stable integrity key.', 'global-clinic-usp-integration' ) ); }
			if ( ! add_option( $option, $value, '', 'no' ) ) {
				$current = (string) get_option( $option, '' );
				if ( ! preg_match( '/^[a-f0-9]{64}$/', $current ) ) { return new WP_Error( 'gcu_integrity_key_store_failed', __( 'File 14 could not persist a stable integrity key.', 'global-clinic-usp-integration' ) ); }
			}
		}
		return true;
	}

	private static function key( $option ) {
		$key = (string) get_option( $option, '' );
		return preg_match( '/^[a-f0-9]{64}$/', $key ) ? $key : '';
	}
	public static function audit_key() { return self::key( self::AUDIT_KEY_OPTION ); }
	public static function privacy_key() { return self::key( self::PRIVACY_KEY_OPTION ); }
	public static function audit_hash( $message ) { $key=self::audit_key(); return $key ? hash_hmac( 'sha256', (string) $message, $key ) : ''; }
	public static function privacy_hash( $message ) { $key=self::privacy_key(); return $key ? hash_hmac( 'sha256', (string) $message, $key ) : ''; }
	public static function user_subject_hash( $seed ) { return self::privacy_hash( 'event-user|' . (string) $seed ); }
	public static function future_actor_hash( $user_id ) { return $user_id ? self::privacy_hash( 'future-report-user|' . absint( $user_id ) ) : ''; }

	private static function audit_message( array $r ) {
		return implode( '|', array(
			(string) $r['previous_hash'], (string) $r['trace_id'], (string) $r['actor_id'], (string) $r['action_name'],
			(string) $r['object_type'], (string) $r['object_id'], (string) $r['purpose'], (string) $r['reason'],
			(string) $r['before_hash'], (string) $r['after_hash'], (string) $r['created_at'],
		) );
	}
	public static function stable_audit_row_hash( array $r ) { return self::audit_hash( self::audit_message( $r ) ); }
	private static function legacy_audit_row_hash( array $r ) { return hash_hmac( 'sha256', self::audit_message( $r ), wp_salt( 'auth' ) ); }

	public static function migrate_legacy_hashes() {
		$keys = self::ensure_keys(); if ( is_wp_error( $keys ) ) { return $keys; }
		$state = get_option( self::MIGRATION_OPTION, array() );
		if ( is_array( $state ) && ! empty( $state['completed'] ) ) { return true; }
		$audit = self::migrate_audit_chain(); if ( is_wp_error( $audit ) ) { return $audit; }
		$privacy = self::migrate_privacy_hashes(); if ( is_wp_error( $privacy ) ) { return $privacy; }
		$record = array( 'completed'=>1, 'version'=>1, 'migrated_at'=>time() );
		update_option( self::MIGRATION_OPTION, $record, false );
		$stored = get_option( self::MIGRATION_OPTION, array() );
		if ( ! is_array( $stored ) || empty( $stored['completed'] ) || 1 !== (int) $stored['version'] ) {
			return new WP_Error( 'gcu_integrity_migration_state_failed', __( 'Integrity migration completed but its durable state could not be confirmed.', 'global-clinic-usp-integration' ) );
		}
		return true;
	}

	private static function migrate_audit_chain() {
		global $wpdb; $t=GCU_Install::tables();
		$exists=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($t['audit']))); if($exists!==$t['audit']){return true;}
		$count=(int)$wpdb->get_var("SELECT COUNT(*) FROM {$t['audit']}"); if(!$count){return true;}
		$lock=GCU_Hardening::acquire_db_lock('audit-chain',5); if(!$lock){return new WP_Error('gcu_integrity_audit_lock_busy',__('Audit migration is busy.','global-clinic-usp-integration'));}
		try{
			if(false===$wpdb->query('START TRANSACTION')){return new WP_Error('gcu_integrity_audit_transaction_failed',__('Audit migration transaction could not start.','global-clinic-usp-integration'));}
			$legacy_prev=str_repeat('0',64); $stable_prev=str_repeat('0',64); $offset=0;
			while($offset<$count){
				$rows=$wpdb->get_results($wpdb->prepare("SELECT * FROM {$t['audit']} ORDER BY id ASC LIMIT 500 OFFSET %d",$offset),ARRAY_A);
				if(!is_array($rows)){ $wpdb->query('ROLLBACK'); return new WP_Error('gcu_integrity_audit_read_failed',__('Audit migration could not read the chain.','global-clinic-usp-integration')); }
				foreach($rows as$row){
					$old_hash=(string)$row['row_hash'];
					if(!hash_equals($legacy_prev,(string)$row['previous_hash'])||!hash_equals($old_hash,self::legacy_audit_row_hash($row))){$wpdb->query('ROLLBACK');return new WP_Error('gcu_integrity_legacy_audit_invalid',__('The existing audit chain could not be verified before key migration.','global-clinic-usp-integration'));}
					$legacy_prev=$old_hash; $row['previous_hash']=$stable_prev; $new_hash=self::stable_audit_row_hash($row);
					$changed=$new_hash?$wpdb->query($wpdb->prepare("UPDATE {$t['audit']} SET previous_hash=%s,row_hash=%s WHERE id=%d AND row_hash=%s",$stable_prev,$new_hash,(int)$row['id'],$old_hash)):false;
					if(1!==$changed){$wpdb->query('ROLLBACK');return new WP_Error('gcu_integrity_audit_rehash_failed',__('Audit key migration could not be completed safely.','global-clinic-usp-integration'));}
					$stable_prev=$new_hash;
				}
				$offset+=count($rows); if(!$rows){break;}
			}
			if(false===$wpdb->query('COMMIT')){$wpdb->query('ROLLBACK');return new WP_Error('gcu_integrity_audit_commit_failed',__('Audit key migration could not commit.','global-clinic-usp-integration'));}
			return true;
		}finally{GCU_Hardening::release_db_lock($lock);}
	}

	private static function migrate_privacy_hashes() {
		global $wpdb; $t=GCU_Install::tables();
		$exists=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($t['events']))); if($exists!==$t['events']){return true;}
		if(false===$wpdb->query('START TRANSACTION')){return new WP_Error('gcu_integrity_privacy_transaction_failed',__('Privacy hash migration transaction could not start.','global-clinic-usp-integration'));}
		try{
			$users=$wpdb->get_results($wpdb->prepare("SELECT user_id,meta_value FROM {$wpdb->usermeta} WHERE meta_key=%s",GCU_Privacy::USER_SUBJECT_META),ARRAY_A);
			if(!is_array($users)){throw new RuntimeException('user-read');}
			foreach($users as$u){
				$seed=(string)$u['meta_value'];if(!preg_match('/^[a-f0-9]{64}$/',$seed)){continue;}
				$old=hash_hmac('sha256',$seed,wp_salt('secure_auth'));$new=self::user_subject_hash($seed);if(!$new){throw new RuntimeException('privacy-key');}
				$changed=$wpdb->query($wpdb->prepare("UPDATE {$t['events']} SET subject_hash=%s WHERE subject_hash=%s",$new,$old));if(false===$changed){throw new RuntimeException('event-update');}
				if(class_exists('GCU_Future_Intelligence')){
					$ft=GCU_Future_Intelligence::tables();$fexists=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($ft['reports'])));
					if($fexists===$ft['reports']){$old_actor=hash_hmac('sha256','u:'.(int)$u['user_id'],wp_salt('auth'));$new_actor=self::future_actor_hash((int)$u['user_id']);$changed=$wpdb->query($wpdb->prepare("UPDATE {$ft['reports']} SET actor_hash=%s WHERE actor_hash=%s",$new_actor,$old_actor));if(false===$changed){throw new RuntimeException('report-update');}}
				}
			}
			if(false===$wpdb->query('COMMIT')){throw new RuntimeException('commit');}return true;
		}catch(Throwable$e){$wpdb->query('ROLLBACK');return new WP_Error('gcu_integrity_privacy_migration_failed',__('Privacy hash migration could not be completed safely.','global-clinic-usp-integration'));}
	}
}
