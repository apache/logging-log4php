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
 * Testclass for the MongoDB appender.
 * 
 * This class has been originally contributed from Vladimir Gorej 
 * (http://github.com/log4mongo/log4mongo-php).
 * 
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 * @since 2.1
 */
class LoggerAppenderMongoDBTest extends PHPUnit_Framework_TestCase {
		
	protected static $appender;
	protected static $event;
	
	public static function setUpBeforeClass() {
		self::$appender = new LoggerAppenderMongoDB('mongo_appender');
		self::$event    = new LoggerLoggingEvent("LoggerAppenderMongoDBTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
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
		try {
			self::$appender->setUserName(null);
			self::$appender->setPassword(null);
			self::$appender->activateOptions();	
		} catch (Exception $ex) {
			$this->fail('Activating appender options was not successful');
		}		
	}		
	
	public function testAppend() {
		self::$appender->append(self::$event);
	}
	
	public function testMongoDB() {
		self::$appender->activateOptions();		
		$mongo  = self::$appender->getConnection();
		$db     = $mongo->selectDB('log4php_mongodb');
		$db->drop('logs');		
		$collection = $db->selectCollection('logs');
				
		self::$appender->append(self::$event);

		$this->assertNotEquals(null, $collection->findOne(), 'Collection should return one record');
	} 

	public function testMongoDBException() {
		self::$appender->activateOptions();		
		$mongo	= self::$appender->getConnection();
		$db		= $mongo->selectDB('log4php_mongodb');
		$db->drop('logs');				
		$collection = $db->selectCollection('logs');
			
		$throwable = new Exception('exception1');
								
		self::$appender->append(new LoggerLoggingEvent("LoggerAppenderMongoDBTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage", microtime(true), $throwable));				 
		
		$this->assertNotEquals(null, $collection->findOne(), 'Collection should return one record');
	}		
		
	public function testMongoDBInnerException() {
		self::$appender->activateOptions();
		$mongo	= self::$appender->getConnection();
		$db		= $mongo->selectDB('log4php_mongodb');
		$db->drop('logs');				
		$collection = $db->selectCollection('logs');
				
		$throwable1 = new Exception('exception1');
		$throwable2 = new Exception('exception2', 0, $throwable1);
								
		self::$appender->append(new LoggerLoggingEvent("LoggerAppenderMongoDBTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage", microtime(true), $throwable2));				
		
		$this->assertNotEquals(null, $collection->findOne(), 'Collection should return one record');
	}
	
	public function testClose() {
		self::$appender->close();
	}
}
?>