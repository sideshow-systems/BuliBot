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
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct($config) {
		$this->config = $config;
	}

	/**
	 * Get config
	 *
	 * @return array
	 */
	public function getConfig() {
		return $this->config;
	}

}

?>
