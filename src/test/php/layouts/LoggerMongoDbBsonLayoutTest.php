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
 * @package log4php
 */

/**
 * Layout test.
 * 
 * This class has been originally contributed from Vladimir Gorej 
 * (http://github.com/log4mongo/log4mongo-php).
 * 
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 * @since 2.1
 */
class LoggerMongoDbBsonLayoutTest extends PHPUnit_Framework_TestCase {
	
	protected static $logger;
	protected static $layout;
	
	public static function setUpBeforeClass() {
		self::$logger    = Logger::getLogger('test.Logger');
		self::$layout    = new LoggerLayoutBson();
	}	
	
	public static function tearDownAfterClass() {
		self::$logger  = null;
		self::$layout  = null;
	}
	
	public function testActivateOptions() {
		$result = self::$layout->activateOptions();
		$this->assertTrue($result);
	}
	
	public function testgetContentType() {
		$result = self::$layout->getContentType();
		$this->assertEquals('application/bson', $result);
	}
	
	public function testFormatSimple() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$layout->format($event);
		
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
		$bsonifiedEvent = self::$layout->format($event);
		
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
		$bsonifiedEvent = self::$layout->format($event);
		
		$this->assertTrue(array_key_exists('exception', $bsonifiedEvent));
		$this->assertEquals(1, $bsonifiedEvent['exception']['code']);
		$this->assertEquals('test exception', $bsonifiedEvent['exception']['message']);
		$this->assertContains('[internal function]: LoggerMongoDbBsonLayoutTest', $bsonifiedEvent['exception']['stackTrace']);
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
		$bsonifiedEvent = self::$layout->format($event);

		$this->assertTrue(array_key_exists('exception', $bsonifiedEvent));
		$this->assertTrue(array_key_exists('innerException', $bsonifiedEvent['exception']));
		$this->assertEquals(2, $bsonifiedEvent['exception']['innerException']['code']);
		$this->assertEquals('test exception inner', $bsonifiedEvent['exception']['innerException']['message']);
		$this->assertContains('[internal function]: LoggerMongoDbBsonLayoutTest', $bsonifiedEvent['exception']['stackTrace']);		
	}	
	
	
	public function testBsonifySimple() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			self::$logger,
			LoggerLevel::getLevelWarn(),
			'test message'
		);
		$bsonifiedEvent = self::$layout->bsonify($event);
		
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
		$bsonifiedEvent = self::$layout->bsonify($event);
		
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
		$bsonifiedEvent = self::$layout->bsonify($event);
		
		$this->assertTrue(array_key_exists('exception', $bsonifiedEvent));
		$this->assertEquals(1, $bsonifiedEvent['exception']['code']);
		$this->assertEquals('test exception', $bsonifiedEvent['exception']['message']);
		$this->assertContains('[internal function]: LoggerMongoDbBsonLayoutTest', $bsonifiedEvent['exception']['stackTrace']);
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
		$bsonifiedEvent = self::$layout->bsonify($event);

		$this->assertTrue(array_key_exists('exception', $bsonifiedEvent));
		$this->assertTrue(array_key_exists('innerException', $bsonifiedEvent['exception']));
		$this->assertEquals(2, $bsonifiedEvent['exception']['innerException']['code']);
		$this->assertEquals('test exception inner', $bsonifiedEvent['exception']['innerException']['message']);
		$this->assertContains('[internal function]: LoggerMongoDbBsonLayoutTest', $bsonifiedEvent['exception']['stackTrace']);		
	}

	public function testIsThreadInteger() {
        $event = new LoggerLoggingEvent(
                'testFqcn',
                self::$logger,
                LoggerLevel::getLevelWarn(),
                'test message'
        );
        $bsonifiedEvent = self::$layout->bsonify($event);
		$this->assertTrue(is_int($bsonifiedEvent['thread']));
	}

    public function testIsLocationInfoLineNumberIntegerOrNA() {
        $event = new LoggerLoggingEvent(
                'testFqcn',
                self::$logger,
                LoggerLevel::getLevelWarn(),
                'test message'
        );
        $bsonifiedEvent = self::$layout->bsonify($event);
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
        $bsonifiedEvent = self::$layout->bsonify($event);
        $this->assertTrue(is_int($bsonifiedEvent['exception']['code']));
    }
}
?>