<?php

/**
 * @group configurators
 */
class LoggerConfigurationAdapterPHPTest extends PHPUnit_Framework_TestCase {
	
	private $expected1 = array(
		'rootLogger' => array(
			'level' => 'info',
			'appenders' => array('default')
		),
		'appenders' => array(
			'default' => array(
				'class' => 'LoggerAppenderEcho',
				'layout' => array(
					'class' => 'LoggerLayoutSimple'
				 )
			)
		)
	);
	
	public function testConfig() {
		$url = dirname(__FILE__) . '/config1.php';
		$adapter = new LoggerConfigurationAdapterPHP();
		$actual = $adapter->convert($url);
		
		$this->assertSame($this->expected1, $actual);
	}
	
	/**
	 * Test exception is thrown when file cannot be found.
 	 * @expectedException LoggerException
 	 * @expectedExceptionMessage File [you/will/never/find/me.conf] does not exist.
	 */
	public function testNonExistantFileWarning() {
		$adapter = new LoggerConfigurationAdapterPHP();
		$adapter->convert('you/will/never/find/me.conf');
	}
	
	/**
	 * Test exception is thrown when file is not valid.
	 * @expectedException LoggerException
	 * @expectedExceptionMessage Error parsing configuration: syntax error
	 */
	public function testInvalidFileWarning() {
		$url = dirname(__FILE__) . '/config2.php';
		$adapter = new LoggerConfigurationAdapterPHP();
		$adapter->convert($url);
	}
	
	/**
	 * Test exception is thrown when the configuration is empty.
	 * @expectedException LoggerException
	 * @expectedExceptionMessage Invalid configuration: empty configuration array.
	 */
	public function testEmptyConfigWarning() {
		$url = dirname(__FILE__) . '/config3.php';
		$adapter = new LoggerConfigurationAdapterPHP();
		$adapter->convert($url);
	}
	
	/**
	 * Test exception is thrown when the configuration does not contain an array.
	 * @expectedException LoggerException
	 * @expectedExceptionMessage Invalid configuration: not an array.
	 */
	public function testInvalidConfigWarning() {
		$url = dirname(__FILE__) . '/config4.php';
		$adapter = new LoggerConfigurationAdapterPHP();
		$adapter->convert($url);
	}


}

?>