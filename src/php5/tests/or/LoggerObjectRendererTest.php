<?php

require_once dirname(__FILE__).'/../phpunit.php';

require_once LOG4PHP_DIR.'/or/LoggerObjectRenderer.php';

class LoggerObjectRendererTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
	}
	
	protected function tearDown() {
	}
	
	public function testFactory() {
		$renderer = LoggerObjectRenderer::factory('LoggerDefaultRenderer');
		self::assertType('LoggerDefaultRenderer', $renderer);
	}

}
?>