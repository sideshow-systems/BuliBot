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
	private $blKey = '';

	/**
	 * Season
	 *
	 * @var int
	 */
	private $season = 0;

	/**
	 * Matches
	 *
	 * @var array
	 */
	private $matches = array();

	/**
	 * Score calculator instance
	 *
	 * @var ScoreCalculator
	 */
	private $scoreCalculator = null;

	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct($config) {
		$this->config = $config;

		// Set some config values
		$this->blKey = $config['blkey'];
		$this->season = $config['current_season_id'];

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
	 * Set ScoreCalculator
	 *
	 * @param ScoreCalcualtor $obj
	 */
	public function setScoreCalculator(ScoreCalculator $obj) {
		$this->scoreCalculator = $obj;
	}

	/**
	 * Get ScoreCalculator
	 *
	 * @return ScoreCalculator
	 */
	public function getScoreCalculator() {
		return $this->scoreCalculator;
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
	 * @return array
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
//		Zend_Debug::dump($matches);
		$this->matches = $matches;

		return $this->matches;
	}

	/**
	 * Guess result of match by match id
	 *
	 * @param int $matchId
	 * @return array
	 */
	public function guessResultOfMatchByMatchId($matchId) {
		$result = array();

		// Get team1 and team2 of from matches
		$teamId1 = 0;
		$teamId2 = 0;
		foreach ($this->matches['matchdata'] as $match) {
			if ($match['match_id'] === $matchId) {
//				Zend_Debug::dump($match);
				// Reset statistic data
				$this->scoreCalculator->resetStatisticData();

				// Set team ids
				$teamId1 = $match['id_team1'];
				$teamId2 = $match['id_team2'];
				$this->scoreCalculator->setTeamId1($teamId1);
				$this->scoreCalculator->setTeamId2($teamId2);

				// Get all matches between team1 and team2
				$urlKey = 'compare_team1_with_team2_url';
				$values = array(
					'{T1}' => $teamId1,
					'{T2}' => $teamId2
				);
				$urlString = $this->urlStringReplacer($urlKey, $values);
				$data = $this->getOpenligaDataByUrl($urlString);
				$this->scoreCalculator->setMatchData($data);

				// Generate statistic data
				$result = $this->scoreCalculator->generateStatisticData();
//				Zend_Debug::dump($stats);
			}
		}

		return $result;
	}

	/**
	 * Submit data to botliga
	 *
	 * @param int $matchId
	 * @param array $data
	 */
	public function submitData($matchId, $data) {
		if (!$this->dryrun) {
//			Zend_Debug::dump($data);
			// Get goals
			$keys = array_keys($data);
			$goalsTeam1 = $data[$keys[0]]['guessgoals'];
			$goalsTeam2 = $data[$keys[1]]['guessgoals'];

			// Init client
			$url = 'http://botliga.de/api/guess';
			$client = new Zend_Http_Client($url);

			// Set params
			$client->setParameterPost(array(
				'match_id' => $matchId,
				'token' => $this->config['botliga_api_key'],
				'result' => $goalsTeam1 . ':' . $goalsTeam2
			));
			$response = $client->request('POST');
//			Zend_Debug::dump($response);
		}
	}

}

?>
