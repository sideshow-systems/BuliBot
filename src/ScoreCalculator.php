<?php

/**
 * Calculate the score of a match between two teams
 *
 * @author Florian Binder <florian.binder@mp-muenchen.de>
 */
class ScoreCalculator extends BuliBot {

	const KEYS_VICTORIES = 'victories';
	const KEYS_DEFEATS = 'defeats';
	const KEYS_TIES = 'tied';
	const KEYS_POINTS = 'points';
	const KEYS_GAMES = 'gamescnt';
	const KEYS_GOALES = 'goales';

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

		// Initialize some values
		$gamesCnt = 0;
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_POINTS, 0);
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_POINTS, 0);
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_VICTORIES, 0);
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_VICTORIES, 0);
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_DEFEATS, 0);
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_DEFEATS, 0);
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_TIES, 0);
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_TIES, 0);
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_GAMES, 0);
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_GAMES, 0);
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_GOALES, 0);
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_GOALES, 0);

//		Zend_Debug::dump($this->matchdata);
		$matchdata = $this->matchdata['matchdata'];
		if (!empty($matchdata)) {

			// Now we're dealing with a single match
			foreach ($matchdata as $match) {
				if ($match['match_is_finished']) {
//					Zend_Debug::dump($match);
					// Map team ids and generate keys
					if ($match['id_team1'] == $this->teamId1) {
						$teamId1Key = 1;
						$teamId2Key = 2;
					} else {
						$teamId1Key = 2;
						$teamId2Key = 1;
					}

					// Increase games count
					$gamesCnt++;
					$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_GAMES, $gamesCnt);
					$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_GAMES, $gamesCnt);

					// Count points
					$matchPointsTeam1 = $match['points_team' . $teamId1Key];
					$cntPointsTeam1 = $this->getStatisticDataByTeamIdAndKey($this->teamId1, ScoreCalculator::KEYS_POINTS) + $matchPointsTeam1;
					$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_POINTS, $cntPointsTeam1);

					$matchPointsTeam2 = $match['points_team' . $teamId2Key];
					$cntPointsTeam2 = $this->getStatisticDataByTeamIdAndKey($this->teamId2, ScoreCalculator::KEYS_POINTS) + $matchPointsTeam2;
					$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_POINTS, $cntPointsTeam2);

					// Count victories, defeats and ties
					if ($matchPointsTeam1 > $matchPointsTeam2) {
						$cntVictoriesTeam1 = $this->getStatisticDataByTeamIdAndKey($this->teamId1, ScoreCalculator::KEYS_VICTORIES) + 1;
						$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_VICTORIES, $cntVictoriesTeam1);
						$cntDefeatsTeam2 = $this->getStatisticDataByTeamIdAndKey($this->teamId2, ScoreCalculator::KEYS_DEFEATS) + 1;
						$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_DEFEATS, $cntDefeatsTeam2);
					} else if ($matchPointsTeam1 < $matchPointsTeam2) {
						$cntVictoriesTeam2 = $this->getStatisticDataByTeamIdAndKey($this->teamId2, ScoreCalculator::KEYS_VICTORIES) + 1;
						$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_VICTORIES, $cntVictoriesTeam2);
						$cntDefeatsTeam1 = $this->getStatisticDataByTeamIdAndKey($this->teamId1, ScoreCalculator::KEYS_DEFEATS) + 1;
						$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_DEFEATS, $cntDefeatsTeam1);
					} else if ($matchPointsTeam1 == $matchPointsTeam2) {
						$cntTiesTeam1 = $this->getStatisticDataByTeamIdAndKey($this->teamId1, ScoreCalculator::KEYS_TIES) + 1;
						$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_TIES, $cntTiesTeam1);
						$cntTiesTeam2 = $this->getStatisticDataByTeamIdAndKey($this->teamId2, ScoreCalculator::KEYS_TIES) + 1;
						$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_TIES, $cntTiesTeam2);
					}

					// Count goales
					if (!empty($match['match_results']['match_result'][0])) {
						$result = $match['match_results']['match_result'][0];

						$matchGoalsTeam1 = $result['points_team' . $teamId1Key];
						$cntGoalsTeam1 = $this->getStatisticDataByTeamIdAndKey($this->teamId1, ScoreCalculator::KEYS_GOALES) + $matchGoalsTeam1;
						$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_GOALES, $cntGoalsTeam1);

						$matchGoalsTeam2 = $result['points_team' . $teamId2Key];
						$cntGoalsTeam2 = $this->getStatisticDataByTeamIdAndKey($this->teamId2, ScoreCalculator::KEYS_GOALES) + $matchGoalsTeam2;
						$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_GOALES, $cntGoalsTeam2);
					}
				}
			}
		}

//		Zend_Debug::dump($this->getStatisticData());
	}

}

?>
