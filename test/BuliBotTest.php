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

}

?>
