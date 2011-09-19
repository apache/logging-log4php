<?php

/**
 * @group configurators
 */
class LoggerConfigurationAdapterINITest extends PHPUnit_Framework_TestCase {
	
	/** Expected output of parsing config1.ini. */
	private $expected1 = array(
		'threshold' => 'debug',
		'rootLogger' => array(
			'level' => 'DEBUG',
			'appenders' => array('default'),
		),
		'appenders' => array(
			'default' => array(
				'class' => 'LoggerAppenderEcho',
				'layout' => array(
					'class' => 'LoggerLayoutTTCC',
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
			'foo' => array(
				'level' => 'warn',
				'appenders' => array('default'),
			),
			'foo.bar' => array(
				'level' => 'debug',
				'appenders' => array('file'),
				'additivity' => 'true',
			),
			'foo.bar.baz' => array(
				'level' => 'trace',
				'appenders' => array('default', 'file'),
				'additivity' => 'false',
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
	);	
	
	public function testConfig() {
		$url = dirname(__FILE__) . '/config1.ini';
		$adapter = new LoggerConfigurationAdapterINI();
		$actual = $adapter->convert($url);
	
		$this->assertSame($this->expected1, $actual);
	}
	
	/**
	 * Test exception is thrown when file cannot be found.
 	 * @expectedException LoggerException
 	 * @expectedExceptionMessage File [you/will/never/find/me.ini] does not exist.
	 */
	public function testNonExistantFileException() {
		$adapter = new LoggerConfigurationAdapterINI();
		$adapter->convert('you/will/never/find/me.ini');
	}
	
	/**
	 * Test exception is thrown when file is not a valid ini file.
	 * @expectedException LoggerException
	 * @expectedExceptionMessage Error parsing configuration file: syntax error, unexpected $end
	 */
	public function testInvalidFileException() {
		$url =  dirname(__FILE__) . '/config2.ini';
		$adapter = new LoggerConfigurationAdapterINI();
		$adapter->convert($url);
	}

	/**
	* Test a warning is triggered when configurator doesn't understand a line.
	* @expectedException PHPUnit_Framework_Error
	* @expectedExceptionMessage log4php: Don't know how to parse the following line: "log4php.appender.default.layout.param.bla = LoggerLayoutTTCC". Skipping.
	*/
	public function testInvalidLineWarning1() {
		$url =  dirname(__FILE__) . '/config3.ini';
		$adapter = new LoggerConfigurationAdapterINI();
		$adapter->convert($url);
	}
	
	/**
	* Test a warning is triggered when configurator doesn't understand a line.
	* @ expectedException PHPUnit_Framework_Error
	* @ expectedExceptionMessage log4php: Don't know how to parse the following line: "log4php.appender.default.layout.param.bla = LoggerLayoutTTCC". Skipping.
	*/
	public function testInvalidLineWarning2() {
		$url =  dirname(__FILE__) . '/config4.ini';
		$adapter = new LoggerConfigurationAdapterINI();
		$adapter->convert($url);
	}
}

?>