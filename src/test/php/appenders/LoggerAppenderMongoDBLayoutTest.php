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
 * Test with an external layout.
 * 
 * This class has been originally contributed from Vladimir Gorej 
 * (http://github.com/log4mongo/log4mongo-php).
 * 
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 * @since 2.1
 */
class LoggerAppenderMongoDBLayoutTest extends PHPUnit_Framework_TestCase {

	protected static $appender;
	protected static $layout;
	protected static $event;
	
	public static function setUpBeforeClass() {
		self::$appender         = new LoggerAppenderMongoDB('mongo_appender');
		self::$layout           = new LoggerLayoutBson();
		self::$appender->setLayout(self::$layout);
		self::$event            = new LoggerLoggingEvent("LoggerAppenderMongoDBLayoutTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
	}
	
	public static function tearDownAfterClass() {
		self::$appender->close();
		self::$appender = null;
		self::$layout = null;
		self::$event = null;
	}	
	
	public function testMongoDB() {		
		self::$appender->activateOptions();
		$mongo  = self::$appender->getConnection();
		$db     = $mongo->selectDB('log4php_mongodb');
		$db->drop('logs');		
		$collection = $db->selectCollection('logs');
				
		self::$appender->append(self::$event);		
	
		$this->assertNotEquals($collection->findOne(), null, 'Collection should return one record');
	}
    	
}
?>