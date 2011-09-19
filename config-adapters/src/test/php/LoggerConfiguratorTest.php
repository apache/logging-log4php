<?php
 

/**
 * 
 * @group configurators
 *
 */
 class LoggerConfiguratorTest extends PHPUnit_Framework_TestCase
 {
 	/** Reset configuration after each test. */
 	public function setUp() {
 		Logger::resetConfiguration();
 	}
 	/** Reset configuration after each test. */
 	public function tearDown() {
 		Logger::resetConfiguration();
 	}
 	
 	/** Check proper default setup. */
 	public function testDefaultConfig() {
 		// Uses default config file
 		Logger::configure();
 		
 		$actual = Logger::getCurrentLoggers();
 		$expected = array();
		$this->assertSame($expected, $actual);

 		$appenders = Logger::getRootLogger()->getAllAppenders();
 		$this->assertInternalType('array', $appenders);
 		$this->assertEquals(count($appenders), 1);
 		$this->assertSame('default', $appenders[0]->getName());
 		
 		$appender = $appenders[0];
 		$this->assertInstanceOf('LoggerAppenderEcho', $appender);
 		
 		$layout = $appender->getLayout();
 		$this->assertInstanceOf('LoggerLayoutSimple', $layout);
 		
 		$root = Logger::getRootLogger();
 		$appenders = $root->getAllAppenders();
 		$this->assertInternalType('array', $appenders);
 		$this->assertEquals(count($appenders), 1);
		
 		$actual = $root->getLevel();
 		$expected = LoggerLevel::getLevelInfo();
 		$this->assertSame($expected, $actual);
 	}
 	
 	/**
 	 * Test that an error is reported when config file is not found. 
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage log4php: Configuration failed. File not found
 	 */
 	public function testNonexistantFile() {
 		Logger::configure('hopefully/this/path/doesnt/exist/config.xml');
 		
 	}
 	
 	/** Test correct fallback to the default configuration. */
 	public function testNonexistantFileFallback() {
 		@Logger::configure('hopefully/this/path/doesnt/exist/config.xml');
 		$this->testDefaultConfig();
 	}
 	
 	public function testAppendersWithLayout() {
 		Logger::configure(array(
 			'rootLogger' => array(
 				'appenders' => array('app1', 'app2')
 			),
 			'appenders' => array(
 				'app1' => array(
 					'class' => 'LoggerAppenderEcho',
 					'layout' => array(
 						'class' => 'LoggerLayoutSimple'
 					)
 				),
		 		'app2' => array(
		 		 	'class' => 'LoggerAppenderEcho',
		 		 	'layout' => array(
		 		 		'class' => 'LoggerLayoutPattern',
		 		 		'conversionPattern' => 'message: %m%n'
		 			)
		 		),
 			) 
 		));
 		
 		ob_start();
 		Logger::getRootLogger()->info('info');
 		$actual = ob_get_contents();
 		ob_end_clean();
 		
 		$expected = "INFO - info" . PHP_EOL . "message: info" . PHP_EOL;
  		$this->assertSame($expected, $actual);
 	}
 }