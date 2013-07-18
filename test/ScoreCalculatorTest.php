<?php

// Require bulibot and config
require_once 'src/ScoreCalculator.php';

/**
 * Test ScoreCalculator - UnitTest
 *
 * @author Florian Binder <fb@sideshow-systems.de>
 */
class ScoreCalculatorTest extends PHPUnit_Framework_TestCase {

	/**
	 * BuliBot instance
	 */
	protected $scoreCalculator = null;

	/**
	 * Setup method
	 */
	protected function setUp() {
		$this->scoreCalculator = new ScoreCalculator($_SERVER['config']);
	}

	/**
	 * Test setter and getter of team id methods
	 *
	 * @covers ScoreCalculator::setTeamId1
	 * @covers ScoreCalculator::setTeamId2
	 * @covers ScoreCalculator::getTeamId1
	 * @covers ScoreCalculator::getTeamId2
	 */
	public function testSetterAndGetterOfTeamIds() {
		$teamId1 = 111;
		$teamId2 = 222;

		$this->scoreCalculator->setTeamId1($teamId1);
		$this->scoreCalculator->setTeamId2($teamId2);
		$this->assertEquals($teamId1, $this->scoreCalculator->getTeamId1());
		$this->assertEquals($teamId2, $this->scoreCalculator->getTeamId2());
	}

	/**
	 * Get matchdata between FCB and BVB
	 *
	 * @return array
	 */
	protected function getMatchdataFCBvsBVB() {
		$matchdataFile = dirname(__FILE__) . '/_data/matchdata_allgames_fcb_bvb.json';
		$matchdataContents = file_get_contents($matchdataFile);
		return Zend_Json::decode($matchdataContents);
	}

	/**
	 * Test setter and getter of matchdata method
	 *
	 * @covers ScoreCalculator::setMatchdata
	 * @covers ScoreCalculator::getMatchdata
	 */
	public function testSetterAndGetterOfMatchdata() {
		$matchdata = $this->getMatchdataFCBvsBVB();
		$this->scoreCalculator->setMatchData($matchdata);
		$this->assertEquals($matchdata, $this->scoreCalculator->getMatchData());
	}

	/**
	 * Test get stat data
	 *
	 * @covers ScoreCalculator::setStatisticData
	 * @covers ScoreCalculator::getStatisticData
	 */
	public function testGetStatData() {
		$statData = 300;
		$expected = array(
			1 => array(
				ScoreCalculator::KEYS_VICTORIES => $statData
			),
			2 => array()
		);

		$this->scoreCalculator->setStatisticData(1, ScoreCalculator::KEYS_VICTORIES, $statData);
		$this->assertEquals($expected, $this->scoreCalculator->getStatisticData());
	}

}

?>
