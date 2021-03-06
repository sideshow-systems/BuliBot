<?php

// Require bulibot and config
require_once 'src/BuliBot.php';

/**
 * Test BuliBot - UnitTest
 *
 * @author Florian Binder <fb@sideshow-systems.de>
 */
class BuliBotTest extends PHPUnit_Framework_TestCase {

	/**
	 * BuliBot instance
	 */
	protected $buliBot = null;

	/**
	 * Setup method
	 */
	protected function setUp() {
		$this->buliBot = new BuliBot($_SERVER['config']);
	}

	/**
	 * Test that the config member is not empty
	 */
	public function testConfigMemberIsNotEmpty() {
		$this->assertNotEmpty($this->buliBot->getConfig());
	}

	/**
	 * Test get zend cache object
	 */
	public function testGetCache() {
		$this->assertInstanceOf('Zend_Cache_Core', $this->buliBot->getCache());
	}

	/**
	 * Minor cache test (read and write)
	 */
	public function testCacheWriteAndRead() {
		$testData = array(1, 2, 3, 4, array('mep1' => 1, 'mep2' => 2));
		$cacheId = 'ut_testCacheWriteAndRead';

		// Write data to cache
		$cache = $this->buliBot->getCache();
		$cache->save($testData, $cacheId);

		// Check
		$cachedData = $cache->load($cacheId);
		$this->assertEquals($testData, $cachedData);

		// Delete
		$cache->remove($cacheId);
	}

	/**
	 * Setter and getter playday member
	 *
	 * @covers BuliBot::setPlayday
	 * @covers BuliBot::getPlayday
	 */
	public function testSetterAndGetterPlayday() {
		$playday = 3;

		$this->buliBot->setPlayday($playday);
		$this->assertEquals($playday, $this->buliBot->getPlayday());
	}

	/**
	 * Test url string replacer
	 */
	public function testUrlStringReplacer() {
		$urlKey1 = 'get_matches_for_playday';
		$valuesSet1 = array(
			'{T1}' => 2013,
			'{T2}' => 'bl1',
			'{T3}' => 3
		);
		$result1 = $this->buliBot->urlStringReplacer($urlKey1, $valuesSet1);
		$expected = 'http://openligadb-json.heroku.com/api/matchdata_by_group_league_saison?league_saison=2013&league_shortcut=bl1&group_order_id=3';
		$this->assertEquals($expected, $result1);
	}

	/**
	 * Setter and getter dryrun member
	 *
	 * @covers BuliBot::setDryrun
	 * @covers BuliBot::getDryrun
	 */
	public function testSetterAndGetterDryrun() {
		$dr = true;

		$this->buliBot->setDryrun($dr);
		$this->assertEquals($dr, $this->buliBot->getDryrun());
	}

	public function testJustAGetAndConvertTest() {
		$cacheId = 'test123';
		$cache = $this->buliBot->getCache();

		$cacheData = null;
		if (!$cache->load($cacheId)) {
			$client = new Zend_Http_Client('http://openligadb-json.heroku.com/api/matchdata_by_teams?team_id_1=100&team_id_2=134', array(
				'maxredirects' => 0,
				'timeout' => 30));

			$response = $client->request('GET');
			$cacheData = $response->getBody();
			$cache->save($cacheData, $cacheId);
		} else {
			$cacheData = $cache->load($cacheId);
		}

		$phpNative = Zend_Json::decode($cacheData);
//		Zend_Debug::dump($phpNative);
	}

}

?>
