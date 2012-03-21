<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   tests
 * @package    log4php
 * @subpackage appenders
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 * @internal   Phpmd clean.
 */

require_once('FirePHPCore/FirePHP.class.php');

/**
 * @group firephp
 */
class LoggerAppenderFirephpTest extends PHPUnit_Framework_TestCase {

	private $config = array(
		'rootLogger' => array(
			'appenders' => array('default'),
		),
		'appenders' => array(
			'default' => array(
				'class' => 'LoggerAppenderFirephp',
				'layout' => array(
					'class' => 'LoggerLayoutPattern',
				),
				'params' => array('medium' => 'page')
			)
		)
	);

	public function testRequiresLayout() {
		$appender = new LoggerAppenderFirephp();
		self::assertFalse($appender->requiresLayout());
	}

	public function testSetMedium() {
		$appender = new LoggerAppenderFirephp();
		$appender->setMedium('page');
		self::assertSame('page', $appender->getMedium());
	}

	private function createEvent($message, $level) {
		$eventMock = $this->getMock('LoggerLoggingEvent', array(), array(), '', false);
		$eventMock->expects($this->any())
			  	  ->method('getRenderedMessage')
			  	  ->will($this->returnValue($message));
		
		$levelMock = $this->getMock('LoggerLevel', array(), array(), '', false);
		$levelMock->expects($this->any())
			  	  ->method('toString')
			  	  ->will($this->returnValue($level));
		
		$eventMock->expects($this->any())
			  	  ->method('getLevel')
			  	  ->will($this->returnValue($levelMock));
		
		return $eventMock;
	}
	
	public function testAppend_HandleDebug() {
		$console = new FirePHPSpy();
		
		$appender = new TestableLoggerAppenderFirePhp();
		$appender->setConsole($console);
		
		$expectedMessage = 'trace message';
		$expectedLevel = 'debug';
		
		$appender->append($this->createEvent($expectedMessage, $expectedLevel));
		
		$this->assertLog($console, $expectedMessage, 'debug', 'trace');
	}
	
	public function testAppend_HandleWarn() {
		$console = new FirePHPSpy();
	
		$appender = new TestableLoggerAppenderFirePhp();
		$appender->setConsole($console);
	
		$expectedMessage = 'debug message';
		$expectedLevel = 'warn';
	
		$appender->append($this->createEvent($expectedMessage, $expectedLevel));
		
		$this->assertLog($console, $expectedMessage, 'warn', 'debug');
	}
	
	public function testAppend_HandleError() {
		$console = new FirePHPSpy();
	
		$appender = new TestableLoggerAppenderFirePhp();
		$appender->setConsole($console);
	
		$expectedMessage = 'error message';
		$expectedLevel = 'error';
	
		$appender->append($this->createEvent($expectedMessage, $expectedLevel));
		
		$this->assertLog($console, $expectedMessage, 'error', 'warn');
	}	
	
	public function testAppend_HandleFatal() {
		$console = new FirePHPSpy();
	
		$appender = new TestableLoggerAppenderFirePhp();
		$appender->setConsole($console);
	
		$expectedMessage = 'fatal message';
		$expectedLevel = 'fatal';
	
		$appender->append($this->createEvent($expectedMessage, $expectedLevel));

		$this->assertLog($console, $expectedMessage, 'fatal', 'error');
	}
	
	public function testAppend_HandleDefault() {
		$console = new FirePHPSpy();
	
		$appender = new TestableLoggerAppenderFirePhp();
		$appender->setConsole($console);
		
		$expectedMessage = 'info message';
		$expectedLevel = 'info';
	
		$appender->append($this->createEvent($expectedMessage, $expectedLevel));
	
		$this->assertLog($console, $expectedMessage, 'info', 'info');
	}
	
	public function assertLog($console, $expectedMessage, $logLevel, $calledMethod) {
		$this->assertEquals('['.$logLevel.']'.' - '.$expectedMessage, $console->getMessage());
		$this->assertEquals(1, $console->getCalls(), 'wasn\'t called once');
		$this->assertEquals($calledMethod, $console->getCalledMethod());
	}
}

class TestableLoggerAppenderFirePhp extends LoggerAppenderFirephp {
	public function setConsole($console) {
		$this->console = $console;
	}
}

class FirePHPSpy {
	private $calls = 0;
	private $message = '';
	private $calledMethod = '';
	
	public function getCalls() {
		return $this->calls;
	}
	
	public function getMessage() {
		return $this->message;
	}
	
	public function trace($message) {
		$this->calls++;
		$this->calledMethod = 'trace';
		$this->message = $message;
	}
	
	public function debug($message) {
		$this->calls++;
		$this->calledMethod = 'debug';
		$this->message = $message;		
	}
	
	public function warn($message) {
		$this->calls++;
		$this->calledMethod = 'warn';
		$this->message = $message;		
	}
	
	public function error($message) {
		$this->calls++;
		$this->calledMethod = 'error';
		$this->message = $message;
	}
	
	public function info($message) {
		$this->calls++;
		$this->calledMethod = 'info';
		$this->message = $message;
	}
	
	public function getCalledMethod() {
		return $this->calledMethod;
	}
}
