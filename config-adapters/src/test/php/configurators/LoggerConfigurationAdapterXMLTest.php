<?php

/**
 * @group configurators
 */
class LoggerConfigurationAdapterXMLTest extends PHPUnit_Framework_TestCase {
	
	/** Expected output of parsing config1.xml.*/
	private $expected1 = array(
		'appenders' => array(
			'default' => array(
				'class' => 'LoggerAppenderEcho',
				'layout' => array(
					'class' => 'LoggerLayoutTTCC',
				),
				'filters' => array(
					array(
						'class' => 'LoggerFilterLevelRange',
						'params' => array(
							'levelMin' => 'ERROR',
							'levelMax' => 'FATAL',
							'acceptOnMatch' => 'false',
						),
					),
					array(
						'class' => 'LoggerFilterDenyAll',
					),
				),
			),
			'file' => array(
				'class' => 'LoggerAppenderDailyFile',
				'layout' => array(
					'class' => 'LoggerLayoutPattern',
					'params' => array(
						'conversionPattern' => '%d{ISO8601} [%p] %c: %m (at %F line %L)%n',
					),
				),
				'params' => array(
					'datePattern' => 'Ymd',
					'file' => 'target/examples/daily_%s.log',
				),
				'threshold' => 'warn'
			),
		),
		'loggers' => array(
			'foo.bar.baz' => array(
				'level' => 'trace',
				'additivity' => 'false',
				'appenders' => array('default'),
			),
			'foo.bar' => array(
				'level' => 'debug',
				'additivity' => 'true',
				'appenders' => array('file'),
			),
			'foo' => array(
				'level' => 'warn',
				'appenders' => array('default', 'file'),
			),
		),
		'renderers' => array(
			array(
				'renderedClass' => 'Fruit',
				'renderingClass' => 'FruitRenderer',
			),
			array(
				'renderedClass' => 'Beer',
				'renderingClass' => 'BeerRenderer',
			),
		),
		'threshold' => 'debug',
		'rootLogger' => array(
			'level' => 'DEBUG',
			'appenders' => array('default'),
		),
	);
	
	public function setUp() {
		Logger::resetConfiguration();
	}
	
	public function tearDown() {
		Logger::resetConfiguration();
	}
	
	public function testConversion() {
		$url =  dirname(__FILE__) . '/config1.xml';
		$adapter = new LoggerConfigurationAdapterXML();
		$actual = $adapter->convert($url);
		$this->assertEquals($this->expected1, $actual);
	}
	
	/**
	 * Test exception is thrown when file cannot be found.
 	 * @expectedException LoggerException
 	 * @expectedExceptionMessage File [you/will/never/find/me.conf] does not exist.
	 */
	public function testNonExistantFile() {
		$adapter = new LoggerConfigurationAdapterXML();
		$adapter->convert('you/will/never/find/me.conf');
	}
	
	/**
	 * Test exception is thrown when file contains invalid XML.
	 * @ expectedException LoggerException
	 * @ expectedExceptionMessage Cannot load config file
	 */
	public function testInvalidXMLFile() {
		// TODO: fix error reporting for XML files
		//$url =  dirname(__FILE__) . '/config4.xml';
		//$adapter = new LoggerConfigurationAdapterXML();
		//$adapter->convert($url);
	}
	
	/**
	 * Test that a warning is triggered when two loggers with the same name 
	 * are defined.
 	 * @expectedException PHPUnit_Framework_Error
 	 * @expectedExceptionMessage log4php: Duplicate logger definition [foo]. Overwriting
	 */
	public function testDuplicateLoggerWarning() {
		$url =  dirname(__FILE__) . '/config3.xml';
		$adapter = new LoggerConfigurationAdapterXML();
		$adapter->convert($url);
	}
	
	
	/**
	 * Test that when two loggers with the same name are defined, the second 
	 * one will overwrite the first.
	 */
	public function testDuplicateLoggerConfig() {
		$url =  dirname(__FILE__) . '/config3.xml';
		$adapter = new LoggerConfigurationAdapterXML();
		
		// Supress the warning so that test can continue 
		$config = @$adapter->convert($url);

		// Second definition of foo has level set to warn (the first to info)
		$this->assertEquals('warn', $config['loggers']['foo']['level']);		
	}
}

?>