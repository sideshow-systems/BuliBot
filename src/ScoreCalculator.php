<?php

/**
 * Calculate the score of a match between two teams
 *
 * @author Florian Binder <florian.binder@mp-muenchen.de>
 */
class ScoreCalculator extends BuliBot {

	const KEYS_VICTORIES = 'victories';
	const KEYS_DEFEATS = 'defeats';
	const KEYS_TIED = 'tied';
	const KEYS_POINTS = 'points';

	/**
	 * Team id of team 1
	 *
	 * @var int
	 */
	private $teamId1 = 0;

	/**
	 * Team id of team 2
	 *
	 * @var int
	 */
	private $teamId2 = 0;

	/**
	 * Holds the matchdata between team1 and team2
	 *
	 * @var array
	 */
	private $matchdata = array();

	/**
	 * Store statistics data
	 *
	 * @var array
	 */
	private $statData = array();

	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct($config) {
		parent::__construct($config);
	}

	/**
	 * Set team id of team 1
	 *
	 * @param int $id
	 */
	public function setTeamId1($id) {
		$this->teamId1 = $id;
	}

	/**
	 * Get team id of team 1
	 *
	 * @return int
	 */
	public function getTeamId1() {
		return $this->teamId1;
	}

	/**
	 * Set team id of team 2
	 *
	 * @param int $id
	 */
	public function setTeamId2($id) {
		$this->teamId2 = $id;
	}

	/**
	 * Get team id of team 2
	 *
	 * @return int
	 */
	public function getTeamId2() {
		return $this->teamId2;
	}

	/**
	 * Set matchdata
	 *
	 * @param array $data
	 */
	public function setMatchData($data) {
		$this->matchdata = $data;
	}

	/**
	 * Get matchdata
	 *
	 * @return array
	 */
	public function getMatchData() {
		return $this->matchdata;
	}

	/**
	 *
	 * @param int $teamId
	 * @param string $key
	 * @param mixed $value
	 */
	public function setStatisticData($teamId, $key, $value) {
		$this->statData[$teamId][$key] = $value;
	}

	/**
	 * Get data by team id and key
	 *
	 * @param int $teamId
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function getStatisticDataByTeamIdAndKey($teamId, $key) {
		return $this->statData[$teamId][$key];
	}

	/**
	 * Get statistic data
	 *
	 * @return array
	 */
	public function getStatisticData() {
		return $this->statData;
	}

	/**
	 * Generate statistic data
	 */
	public function generateStatisticData() {
		if (empty($this->matchdata)) {
			throw new Exception('No matchdata is set!', 3000);
		}

//		Zend_Debug::dump($this->matchdata);
		$matchdata = $this->matchdata['matchdata'];
		if (!empty($matchdata)) {

			// Now we're dealing with a single match
			foreach ($matchdata as $match) {
				if ($match['match_is_finished']) {
					Zend_Debug::dump($match);
					// Get data for team1
					// Count points
				}
			}
		}
	}

}

?>
