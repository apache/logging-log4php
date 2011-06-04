<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   tests
 * @package    log4php
 * @subpackage helpers
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

/**
 * MongoDB BSON-ifier test.
 * 
 * This class has been originally contributed from Vladimir Gorej 
 * (http://github.com/log4mongo/log4mongo-php).
 * 
 * @group appenders
 */
class LoggerLoggingEventBsonifierTest extends PHPUnit_Framework_TestCase {
	
	protected static $logger;
	protected static $bsonifier;
	
	public static function setUpBeforeClass() {
		self::$logger = Logger::getLogger('test.Logger');
		self::$bsonifier = new LoggerLoggingEventBsonifier();
	}
	
	public static function tearDownAfterClass() {
		self::$logger  = null;
		self::$bsonifier  = null;
	}
	
	protected function setUp() {
		if (extension_loaded('mongo') == false) {
			$this->markTestSkipped(
				'The Mongo extension is not available.'
			);
		}
	}
	
	public function testFormatSimple() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		
		$this->assertEquals('WARN', $bsonifiedEvent['level']);
		$this->assertEquals('test message', $bsonifiedEvent['message']);
		$this->assertEquals('test.Logger', $bsonifiedEvent['loggerName']);
	}
	
	public function testFormatLocationInfo() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		
		$this->assertEquals('NA', $bsonifiedEvent['fileName']);		
		$this->assertEquals('getLocationInformation', $bsonifiedEvent['method']);
		$this->assertEquals('NA', $bsonifiedEvent['lineNumber']);
		$this->assertEquals('LoggerLoggingEvent', $bsonifiedEvent['className']);
	}
	
	public function testFormatThrowableInfo() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new Exception('test exception', 1)
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		
		$this->assertArrayHasKey('exception', $bsonifiedEvent);
		$this->assertEquals(1, $bsonifiedEvent['exception']['code']);
		$this->assertEquals('test exception', $bsonifiedEvent['exception']['message']);
		$this->assertContains('[internal function]: LoggerLoggingEventBsonifierTest', $bsonifiedEvent['exception']['stackTrace']);
	}
	
	public function testFormatThrowableInfoWithInnerException() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new Exception('test exeption', 1, new Exception('test exception inner', 2))
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);

		$this->assertTrue(array_key_exists('exception', $bsonifiedEvent));
		$this->assertTrue(array_key_exists('innerException', $bsonifiedEvent['exception']));
		$this->assertEquals(2, $bsonifiedEvent['exception']['innerException']['code']);
		$this->assertEquals('test exception inner', $bsonifiedEvent['exception']['innerException']['message']);
		$this->assertContains('[internal function]: LoggerLoggingEventBsonifierTest', $bsonifiedEvent['exception']['stackTrace']);		
	}	
	
	
	public function testBsonifySimple() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		
		$this->assertEquals('WARN', $bsonifiedEvent['level']);
		$this->assertEquals('test message', $bsonifiedEvent['message']);
		$this->assertEquals('test.Logger', $bsonifiedEvent['loggerName']);
	}
	
	public function testBsonifyLocationInfo() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		
		$this->assertEquals('NA', $bsonifiedEvent['fileName']);		
		$this->assertEquals('getLocationInformation', $bsonifiedEvent['method']);
		$this->assertEquals('NA', $bsonifiedEvent['lineNumber']);
		$this->assertEquals('LoggerLoggingEvent', $bsonifiedEvent['className']);
	}
	
	public function testBsonifyThrowableInfo() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new Exception('test exception', 1)
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		
		$this->assertTrue(array_key_exists('exception', $bsonifiedEvent));
		$this->assertEquals(1, $bsonifiedEvent['exception']['code']);
		$this->assertEquals('test exception', $bsonifiedEvent['exception']['message']);
		$this->assertContains('[internal function]: LoggerLoggingEventBsonifierTest', $bsonifiedEvent['exception']['stackTrace']);
	}
	
	public function testBsonifyThrowableInfoWithInnerException() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new Exception('test exeption', 1, new Exception('test exception inner', 2))
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);

		$this->assertTrue(array_key_exists('exception', $bsonifiedEvent));
		$this->assertTrue(array_key_exists('innerException', $bsonifiedEvent['exception']));
		$this->assertEquals(2, $bsonifiedEvent['exception']['innerException']['code']);
		$this->assertEquals('test exception inner', $bsonifiedEvent['exception']['innerException']['message']);
		$this->assertContains('[internal function]: LoggerLoggingEventBsonifierTest', $bsonifiedEvent['exception']['stackTrace']);		
	}

	public function testIsThreadInteger() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		$this->assertTrue(is_int($bsonifiedEvent['thread']));
	}

	public function testIsLocationInfoLineNumberIntegerOrNA() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		$this->assertTrue(is_int($bsonifiedEvent['lineNumber']) || $bsonifiedEvent['lineNumber'] == 'NA');
	}

	public function testIsThrowableInfoExceptionCodeInteger() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new Exception('test exeption', 1, new Exception('test exception inner', 2))
		);
		$bsonifiedEvent = self::$bsonifier->bsonify($event);
		$this->assertTrue(is_int($bsonifiedEvent['exception']['code']));
	}
}
?>