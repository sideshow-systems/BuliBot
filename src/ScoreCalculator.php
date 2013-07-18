<?php

/**
 * Calculate the score of a match between two teams
 *
 * @author Florian Binder <florian.binder@mp-muenchen.de>
 */
class ScoreCalculator extends BuliBot {

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

}

?>
