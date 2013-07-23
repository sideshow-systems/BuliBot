<?php

/*
 * BuliBot Class
 */

/**
 * BuliBot is a bot who calculates football scores
 *
 * @author Florian Binder <fb@sideshow-systems.de>
 */
class BuliBot {

	/**
	 * Holds config data
	 *
	 * @var array
	 */
	private $config = array();

	/**
	 * Cache
	 *
	 * @var Zend_Cache_Core
	 */
	private $cache = null;

	/**
	 * Playday
	 *
	 * @var int
	 */
	private $playday = 0;

	/**
	 * Dryrun
	 *
	 * @var bool
	 */
	private $dryrun = false;

	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct($config) {
		$this->config = $config;

		// Setup cache
		$this->setupCache();
	}

	/**
	 * Setup cache
	 */
	private function setupCache() {
		$frontendOptionsCore = array(
			'automatic_serialization' => true,
			'lifetime' => 86400 // 1 day
		);
		$backendOptions = array('cache_dir' => 'cache');
		$this->cache = Zend_Cache::factory('Core', 'File', $frontendOptionsCore, $backendOptions);
	}

	/**
	 * Get config
	 *
	 * @return array
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * Get cache
	 *
	 * @return Zend_Cache_Core
	 */
	public function getCache() {
		return $this->cache;
	}

	/**
	 * Set playday
	 *
	 * @param int $pd
	 */
	public function setPlayday($pd) {
		$this->playday = $pd;
	}

	/**
	 * Get playday
	 *
	 * @return int
	 */
	public function getPlayday() {
		return $this->playday;
	}

	/**
	 * Set dryrun
	 *
	 * @param bool $dr
	 */
	public function setDryrun($dr) {
		$this->dryrun = $dr;
	}

	/**
	 * Get dryrun
	 *
	 * @return bool
	 */
	public function getDryrun() {
		return $this->dryrun;
	}

}

?>
