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
 * @subpackage appenders
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

/**
 * Testclass for the MongoDB appender.
 * 
 * This class has been originally contributed from Vladimir Gorej 
 * (http://github.com/log4mongo/log4mongo-php).
 * 
 * @group appenders
 */
class LoggerAppenderMongoDBTest extends PHPUnit_Framework_TestCase {
		
	protected static $appender;
	protected static $event;
	
	public static function setUpBeforeClass() {
		self::$appender = new LoggerAppenderMongoDB('mongo_appender');
		self::$event = new LoggerLoggingEvent("LoggerAppenderMongoDBTest", new Logger("test.Logger"), LoggerLevel::getLevelError(), "testmessage");
	}
	
	public static function tearDownAfterClass() {
		self::$appender->close();
		self::$appender = null;
		self::$event = null;
	}
	
	protected function setUp() {
		if (!extension_loaded('mongo')) {
			$this->markTestSkipped(
				'The Mongo extension is not available.'
			);
		}
	}
	
	public function testHost() {
		$expected = 'mongodb://localhost';
		self::$appender->setHost($expected);
		$result = self::$appender->getHost();
		self::assertEquals($expected, $result);
	}
	
	public function testPort() {
		$expected = 27017;
		self::$appender->setPort($expected);
		$result = self::$appender->getPort();
		self::assertEquals($expected, $result);
	}

	public function testDatabaseName() {
		$expected = 'log4php_mongodb';
		self::$appender->setDatabaseName($expected);
		$result	= self::$appender->getDatabaseName();
		self::assertEquals($expected, $result);
	}
	
	public function testCollectionName() {
		$expected = 'logs';
		self::$appender->setCollectionName($expected);
		$result = self::$appender->getCollectionName();
		self::assertEquals($expected, $result);
	}
	
	public function testUserName() {
		$expected = 'char0n';
		self::$appender->setUserName($expected);
		$result = self::$appender->getUserName();
		self::assertEquals($expected, $result);
	}
	
	public function testPassword() {
		$expected = 'secret pass';
		self::$appender->setPassword($expected);
		$result	= self::$appender->getPassword();
		self::assertEquals($expected, $result);
	}
	
	public function testActivateOptionsNoCredentials() {
		self::$appender->setUserName(null);
		self::$appender->setPassword(null);
		self::$appender->activateOptions();
	}		
	
	public function testFormat() {
		$event = LoggerTestHelper::getErrorEvent("testmessage");
		$record = $this->logOne($event);
		
		self::assertEquals('ERROR', $record['level']);
		self::assertEquals('testmessage', $record['message']);
		self::assertEquals('test', $record['loggerName']);
		
		self::assertEquals('NA', $record['fileName']);		
		self::assertEquals('getLocationInformation', $record['method']);
		self::assertEquals('NA', $record['lineNumber']);
		self::assertEquals('LoggerLoggingEvent', $record['className']);
		
		self::assertTrue(is_int($record['thread']));
		self::assertSame(getmypid(), $record['thread']);
		self::assertTrue(is_int($record['lineNumber']) || $record['lineNumber'] == 'NA');
	}
	
	public function testFormatThrowableInfo() {
		$event = new LoggerLoggingEvent(
			'testFqcn',
			new Logger('test.Logger'),
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new Exception('test exception', 1)
		);
		
		$record = $this->logOne($event);
		
		self::assertArrayHasKey('exception', $record);
		self::assertEquals(1, $record['exception']['code']);
		self::assertEquals('test exception', $record['exception']['message']);
		self::assertContains('[internal function]: LoggerAppenderMongoDBTest', $record['exception']['stackTrace']);
	}
	
	public function testFormatThrowableInfoWithInnerException() {
		
		// Skip test if PHP version is lower than 5.3.0 (no inner exception support)
		if (version_compare(PHP_VERSION, '5.3.0') < 0) {
			$this->markTestSkipped();
		}
		
		$event = new LoggerLoggingEvent(
			'testFqcn',
			new Logger('test.Logger'),
			LoggerLevel::getLevelWarn(),
			'test message',
			microtime(true),
			new Exception('test exception', 1, new Exception('test exception inner', 2))
		);
		
		$record = $this->logOne($event);

		self::assertArrayHasKey('exception', $record);
		self::assertEquals(1, $record['exception']['code']);
		self::assertEquals('test exception', $record['exception']['message']);
		self::assertContains('[internal function]: LoggerAppenderMongoDBTest', $record['exception']['stackTrace']);
		
		self::assertTrue(array_key_exists('innerException', $record['exception']));
		self::assertEquals(2, $record['exception']['innerException']['code']);
		self::assertEquals('test exception inner', $record['exception']['innerException']['message']);
	}
	
	public function testClose() {
		self::$appender->close();
	}
	
	/** Logs the event and returns the record from the database. */
	private function logOne($event)
	{
		$appender = new LoggerAppenderMongoDB();
		$appender->setHost('localhost');
		$appender->activateOptions();
		
		$mongo = $appender->getConnection();
		$collection = $mongo->log4php_mongodb->logs;
		
		$result = $collection->drop();
		self::assertSame((float) 1, $result['ok'], "Could not clear the collection before logging.");
		
		$appender->append($event);
		
		$record = $collection->findOne();
		self::assertNotNull($record, "Could not read the record from the database.");
		
		$appender->close();
		
		return $record;
	}
	
}
?>