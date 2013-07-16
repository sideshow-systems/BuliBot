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
		$this->buliBot = new BuliBot();
	}

	public function testEmpty() {
		$stack = array();
		$this->assertEmpty($stack);

		Zend_Debug::dump($this->buliBot);

		return $stack;
	}

}

?>
