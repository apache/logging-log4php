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
		if (extension_loaded('mongo') == false) {
			$this->markTestSkipped(
				'The Mongo extension is not available.'
			);
		}
	}
	
	public function test__construct() {
		$appender = new LoggerAppenderMongoDB('mongo_appender');
		$this->assertTrue($appender instanceof LoggerAppenderMongoDB);
	}
	
	public function testSetGetHost() {
		$expected = 'mongodb://localhost';
		self::$appender->setHost($expected);
		$result = self::$appender->getHost();
		$this->assertEquals($expected, $result, 'Host doesn\'t match expted value');
	}
	
	public function testSetGetHostMongoPrefix() {
		$expected = 'mongodb://localhost';
		self::$appender->setHost('localhost');
		$result = self::$appender->getHost();
		$this->assertEquals($expected, $result, 'Host doesn\'t match expted value');
	}
	
	public function testSetPort() {
		$expected = 27017;
		self::$appender->setPort($expected);
		$result = self::$appender->getPort();
		$this->assertEquals($expected, $result, 'Port doesn\'t match expted value');
	}

	public function testGetPort() {
		$expected = 27017;
		self::$appender->setPort($expected);
		$result = self::$appender->getPort();
		$this->assertEquals($expected, $result, 'Port doesn\'t match expted value');
	}
	
	public function testSetDatabaseName() {
		$expected = 'log4php_mongodb';
		self::$appender->setDatabaseName($expected);
		$result	= self::$appender->getDatabaseName();
		$this->assertEquals($expected, $result, 'Database name doesn\'t match expted value');
	}
	
	public function testGetDatabaseName() {
		$expected = 'log4php_mongodb';
		self::$appender->setDatabaseName($expected);
		$result	= self::$appender->getDatabaseName();
		$this->assertEquals($expected, $result, 'Database name doesn\'t match expted value');
	}		 
	
	public function testSetCollectionName() {
		$expected = 'logs';
		self::$appender->setCollectionName($expected);
		$result = self::$appender->getCollectionName();
		$this->assertEquals($expected, $result, 'Collection name doesn\'t match expted value');
	}
	
	public function testGetCollectionName() {
		$expected = 'logs';
		self::$appender->setCollectionName($expected);
		$result = self::$appender->getCollectionName();
		$this->assertEquals($expected, $result, 'Collection name doesn\'t match expted value');
	}	 
	
	public function testSetUserName() {
		$expected = 'char0n';
		self::$appender->setUserName($expected);
		$result = self::$appender->getUserName();
		$this->assertEquals($expected, $result, 'UserName doesn\'t match expted value');
	}
	
	public function testGetUserName() {
		$expected = 'char0n';
		self::$appender->setUserName($expected);
		$result	= self::$appender->getUserName();
		$this->assertEquals($expected, $result, 'UserName doesn\'t match expted value');
	}					 
	
	public function testSetPassword() {
		$expected = 'secret pass';
		self::$appender->setPassword($expected);
		$result	= self::$appender->getPassword();
		$this->assertEquals($expected, $result, 'Password doesn\'t match expted value');
	}
	
	public function testGetPassword() {
		$expected = 'secret pass';
		self::$appender->setPassword($expected);
		$result	= self::$appender->getPassword();
		$this->assertEquals($expected, $result, 'Password doesn\'t match expted value');
	} 
	
	public function testActivateOptionsNoCredentials() {
		self::$appender->setUserName(null);
		self::$appender->setPassword(null);
		self::$appender->activateOptions();
	}		
	
	public function testAppend() {
		self::$appender->append(self::$event);
	}
	
	public function testFormat() {
		$record = $this->logOne(self::$event);
		
		$this->assertEquals('ERROR', $record['level']);
		$this->assertEquals('testmessage', $record['message']);
		$this->assertEquals('test.Logger', $record['loggerName']);
		
		$this->assertEquals('NA', $record['fileName']);		
		$this->assertEquals('getLocationInformation', $record['method']);
		$this->assertEquals('NA', $record['lineNumber']);
		$this->assertEquals('LoggerLoggingEvent', $record['className']);
		
		$this->assertTrue(is_int($record['thread']));
		$this->assertTrue(is_int($record['lineNumber']) || $record['lineNumber'] == 'NA');
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
		
		$this->assertArrayHasKey('exception', $record);
		$this->assertEquals(1, $record['exception']['code']);
		$this->assertEquals('test exception', $record['exception']['message']);
		$this->assertContains('[internal function]: LoggerAppenderMongoDBTest', $record['exception']['stackTrace']);
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

		$this->assertArrayHasKey('exception', $record);
		$this->assertEquals(1, $record['exception']['code']);
		$this->assertEquals('test exception', $record['exception']['message']);
		$this->assertContains('[internal function]: LoggerAppenderMongoDBTest', $record['exception']['stackTrace']);
		
		$this->assertTrue(array_key_exists('innerException', $record['exception']));
		$this->assertEquals(2, $record['exception']['innerException']['code']);
		$this->assertEquals('test exception inner', $record['exception']['innerException']['message']);
	}
	
	public function testClose() {
		self::$appender->close();
	}
	
	/** Logs the event and returns the record from the database. */
	private function logOne($event)
	{
		self::$appender = new LoggerAppenderMongoDB();
		self::$appender->setHost('localhost');
		self::$appender->activateOptions();
		$mongo = self::$appender->getConnection();
		$collection = $mongo->log4php_mongodb->logs;
		
		$result = $collection->drop();
		self::assertSame((float) 1, $result['ok'], "Could not clear the collection before logging.");
		
		self::$appender->append($event);
		
		$record = $collection->findOne();
		self::assertNotNull($record, "Could not read the record from the database.");
		
		return $record;
	}
	
}
?>