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
	 * League key
	 *
	 * @var string
	 */
	private $blKey = 'bl1';

	/**
	 * Season
	 *
	 * @var int
	 */
	private $season = 2013;

	/**
	 * Matches
	 *
	 * @var array
	 */
	private $matches = array();

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

	/**
	 * Replace values in url string
	 *
	 * @param string $urlKey
	 * @param array $values
	 * @return string
	 */
	public function urlStringReplacer($urlKey, $values) {
		// Get url string from config
		$urlString = $this->config['openliga'][$urlKey];

		// Define search and replace
		$search = array_keys($values);
		$replace = array_values($values);

		// Replace
		return str_replace($search, $replace, $urlString);
	}

	/**
	 * Get openliga data by url
	 *
	 * @param string $url
	 * @return array
	 */
	public function getOpenligaDataByUrl($url) {
		$cacheId = md5($url);
		$cacheData = null;
		if (!$this->cache->load($cacheId)) {
			$client = new Zend_Http_Client($url, array(
				'maxredirects' => 0,
				'timeout' => 30));

			$response = $client->request('GET');
			$cacheData = $response->getBody();
			$this->cache->save($cacheData, $cacheId);
		} else {
			$cacheData = $this->cache->load($cacheId);
		}

		return Zend_Json::decode($cacheData);
	}

	/**
	 * Get matches by playday
	 *
	 * @param int $pd
	 */
	public function getMatchesByPlayday($pd) {
		// Get string from config
		$urlKey = 'get_matches_for_playday';
		$values = array(
			'{T1}' => $this->season,
			'{T2}' => $this->blKey,
			'{T3}' => $pd
		);
		$urlString = $this->urlStringReplacer($urlKey, $values);
		$matches = $this->getOpenligaDataByUrl($urlString);
		Zend_Debug::dump($matches);
		$this->matches = $matches;
	}

}

?>
