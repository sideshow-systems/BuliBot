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

}

?>
