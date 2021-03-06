<?php

/**
 * Calculate the score of a match between two teams
 *
 * @author Florian Binder <florian.binder@mp-muenchen.de>
 */
class ScoreCalculator extends BuliBot {
	// Statistic keys

	const KEYS_VICTORIES = 'victories';
	const KEYS_DEFEATS = 'defeats';
	const KEYS_TIES = 'tied';
	const KEYS_POINTS = 'points';
	const KEYS_GAMES = 'gamescnt';
	const KEYS_GOALES = 'goales';
	const KEYS_GUESSGOALS = 'guessgoals';
	const KEYS_TEAMNAME = 'teamname';
	const KEYS_AVERAGEGOALS = 'averagegoals';
	const KEYS_CPOINTS = 'cpoints';

	// Calculation keys
	const CKEYS_GOAL = 0.2;
	const CKEYS_VICTORIES = 0.3;
	const CKEYS_TIES = 0.1;
	const CKEYS_DEFEATS = -0.1;

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
	 * Reset statistic data
	 */
	public function resetStatisticData() {
		$this->statData = array();
	}

	/**
	 * Generate statistic data
	 *
	 * @return array statistic data
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
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_TEAMNAME, '');
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_TEAMNAME, '');
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_AVERAGEGOALS, 0);
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_AVERAGEGOALS, 0);
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_CPOINTS, 0);
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_CPOINTS, 0);
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_GUESSGOALS, 0);
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_GUESSGOALS, 0);

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

					// Cpoints
					$cPointsTeamId1 = $this->getStatisticDataByTeamIdAndKey($this->teamId1, ScoreCalculator::KEYS_CPOINTS);
					$cPointsTeamId2 = $this->getStatisticDataByTeamIdAndKey($this->teamId2, ScoreCalculator::KEYS_CPOINTS);

					// Set team name
					$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_TEAMNAME, $match['name_team' . $teamId1Key]);
					$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_TEAMNAME, $match['name_team' . $teamId2Key]);

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

						// Set cpoints
						$cPointsTeamId1 += ScoreCalculator::CKEYS_VICTORIES;
						$cPointsTeamId2 += ScoreCalculator::CKEYS_DEFEATS;
					} else if ($matchPointsTeam1 < $matchPointsTeam2) {
						$cntVictoriesTeam2 = $this->getStatisticDataByTeamIdAndKey($this->teamId2, ScoreCalculator::KEYS_VICTORIES) + 1;
						$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_VICTORIES, $cntVictoriesTeam2);
						$cntDefeatsTeam1 = $this->getStatisticDataByTeamIdAndKey($this->teamId1, ScoreCalculator::KEYS_DEFEATS) + 1;
						$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_DEFEATS, $cntDefeatsTeam1);

						// Set cpoints
						$cPointsTeamId1 += ScoreCalculator::CKEYS_DEFEATS;
						$cPointsTeamId2 += ScoreCalculator::CKEYS_VICTORIES;
					} else if ($matchPointsTeam1 == $matchPointsTeam2) {
						$cntTiesTeam1 = $this->getStatisticDataByTeamIdAndKey($this->teamId1, ScoreCalculator::KEYS_TIES) + 1;
						$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_TIES, $cntTiesTeam1);
						$cntTiesTeam2 = $this->getStatisticDataByTeamIdAndKey($this->teamId2, ScoreCalculator::KEYS_TIES) + 1;
						$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_TIES, $cntTiesTeam2);

						// Set cpoints
						$cPointsTeamId1 += ScoreCalculator::CKEYS_TIES;
						$cPointsTeamId2 += ScoreCalculator::CKEYS_TIES;
					}

					// Count goales
					if (!empty($match['match_results']['match_result'][0])) {
						$result = $match['match_results']['match_result'][0];

						$matchGoalsTeam1 = $result['points_team' . $teamId1Key];
						$cntGoalsTeam1 = $this->getStatisticDataByTeamIdAndKey($this->teamId1, ScoreCalculator::KEYS_GOALES) + $matchGoalsTeam1;
						$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_GOALES, $cntGoalsTeam1);

						// Do not track cpoints for goals!
						// Set cpoints
						//$cPointsTeamId1 += $matchGoalsTeam1 * ScoreCalculator::CKEYS_GOAL;

						$matchGoalsTeam2 = $result['points_team' . $teamId2Key];
						$cntGoalsTeam2 = $this->getStatisticDataByTeamIdAndKey($this->teamId2, ScoreCalculator::KEYS_GOALES) + $matchGoalsTeam2;
						$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_GOALES, $cntGoalsTeam2);

						// Set cpoints
						//$cPointsTeamId2 += $matchGoalsTeam2 * ScoreCalculator::CKEYS_GOAL;
					}
				}
			}
		}

		// Get goals and calculate average goals
		// This is not working because of shitty data!
		$averageGoalsTeam1 = 0;
		$goalsCntTeamId1 = $this->getStatisticDataByTeamIdAndKey($this->teamId1, ScoreCalculator::KEYS_GOALES);
		if ($goalsCntTeamId1 !== 0) {
			$averageGoalsTeam1 = round($goalsCntTeamId1 / $gamesCnt);
		}
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_AVERAGEGOALS, $averageGoalsTeam1);

		$averageGoalsTeam2 = 0;
		$goalsCntTeamId2 = $this->getStatisticDataByTeamIdAndKey($this->teamId2, ScoreCalculator::KEYS_GOALES);
		if ($goalsCntTeamId2 !== 0) {
			$averageGoalsTeam2 = round($goalsCntTeamId2 / $gamesCnt);
		}
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_AVERAGEGOALS, $averageGoalsTeam2);

		// Set cpoints to statistic data
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_CPOINTS, $cPointsTeamId1);
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_CPOINTS, $cPointsTeamId2);

		// TODO: We have to adjust these values!
		$teamId1MinAndMax = array(5, 10);
		$teamId2MinAndMax = array(5, 10);

		// Try to guess result
		$cPointsTeamId1 = ($cPointsTeamId1 < 0) ? ($cPointsTeamId1 * -1) : $cPointsTeamId1;
		$cPointsTeamId2 = ($cPointsTeamId2 < 0) ? ($cPointsTeamId2 * -1) : $cPointsTeamId2;
		$guessGoalsTeamId1 = round($cPointsTeamId1 * mt_rand($teamId1MinAndMax[0], $teamId1MinAndMax[1]));
		$guessGoalsTeamId2 = round($cPointsTeamId2 * mt_rand($teamId2MinAndMax[0], $teamId2MinAndMax[1]));
		$this->setStatisticData($this->teamId1, ScoreCalculator::KEYS_GUESSGOALS, $guessGoalsTeamId1);
		$this->setStatisticData($this->teamId2, ScoreCalculator::KEYS_GUESSGOALS, $guessGoalsTeamId2);

//		Zend_Debug::dump($this->getStatisticData());
		return $this->getStatisticData();
	}

}

?>
