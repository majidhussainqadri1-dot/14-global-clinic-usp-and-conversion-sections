<?php

defined( 'ABSPATH' ) || exit;

final class GCU_Plugin {
	private static $instance;
	private $repository;
	private $contracts;
	private $privacy;
	private $frontend;
	private $observability;
	private $ran = false;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function run() {
		if ( $this->ran ) {
			return;
		}
		$this->ran = true;
		load_plugin_textdomain( 'global-clinic-usp-integration', false, dirname( GCU_BASENAME ) . '/languages' );
		$upgrade=GCU_Install::maybe_upgrade();if(is_wp_error($upgrade)){GCU_Observability::log('error','runtime_upgrade_pending',array('code'=>$upgrade->get_error_code()));}
		$this->contracts()->hooks();
		$this->privacy()->hooks();
		$this->frontend()->hooks();
		$this->observability()->hooks();
		( new GCU_REST() )->hooks();
		if ( is_admin() ) {
			( new GCU_Admin() )->hooks();
		}
	}

	public function repository() {
		if ( ! $this->repository ) {
			$this->repository = new GCU_Repository();
		}
		return $this->repository;
	}

	public function contracts() {
		if ( ! $this->contracts ) {
			$this->contracts = new GCU_Contracts();
		}
		return $this->contracts;
	}

	public function privacy() {
		if ( ! $this->privacy ) {
			$this->privacy = new GCU_Privacy();
		}
		return $this->privacy;
	}

	public function frontend() {
		if ( ! $this->frontend ) {
			$this->frontend = new GCU_Frontend();
		}
		return $this->frontend;
	}

	public function observability() {
		if ( ! $this->observability ) {
			$this->observability = new GCU_Observability();
		}
		return $this->observability;
	}

	private function __construct() {}
	private function __clone() {}
}
