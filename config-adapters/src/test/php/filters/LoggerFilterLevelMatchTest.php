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
 * @subpackage filters
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

/**
 * @group filters
 */
class LoggerFilterLevelMatchTest extends PHPUnit_Framework_TestCase {
		
	public function testDecideAcceptErrorLevel() {
		$filter = new LoggerFilterLevelMatch();
		$filter->setAcceptOnMatch(true);
		$filter->setLevelToMatch(LoggerLevel::getLevelError());
		
		$eventError = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		$eventDebug = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelDebug(), "testmessage");
		$eventWarn = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelWarn(), "testmessage");
		
		$result = $filter->decide($eventError);
		self::assertEquals($result, LoggerFilter::ACCEPT);
		
		$result = $filter->decide($eventDebug);
		self::assertEquals($result, LoggerFilter::NEUTRAL);
		
		$result = $filter->decide($eventWarn);
		self::assertEquals($result, LoggerFilter::NEUTRAL);
	}
	
	public function testDecideDenyErrorLevel() {
		$filter = new LoggerFilterLevelMatch();
		$filter->setAcceptOnMatch("false");
		$filter->setLevelToMatch(LoggerLevel::getLevelError());
		
		$eventError = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		$eventDebug = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelDebug(), "testmessage");
		$eventWarn = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelWarn(), "testmessage");
		
		$result = $filter->decide($eventError);
		self::assertEquals($result, LoggerFilter::DENY);
		
		$result = $filter->decide($eventDebug);
		self::assertEquals($result, LoggerFilter::NEUTRAL);
		
		$result = $filter->decide($eventWarn);
		self::assertEquals($result, LoggerFilter::NEUTRAL);
	}
	
	public function testDecideAcceptWarnLevel() {
		$filter = new LoggerFilterLevelMatch();
		$filter->setAcceptOnMatch("true");
		$filter->setLevelToMatch(LoggerLevel::getLevelWarn());
		
		$eventError = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		$eventDebug = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelDebug(), "testmessage");
		$eventWarn = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelWarn(), "testmessage");
		
		$result = $filter->decide($eventError);
		self::assertEquals($result, LoggerFilter::NEUTRAL);
		
		$result = $filter->decide($eventDebug);
		self::assertEquals($result, LoggerFilter::NEUTRAL);
		
		$result = $filter->decide($eventWarn);
		self::assertEquals($result, LoggerFilter::ACCEPT);
	}
	
	public function testDecideDenyWarnLevel() {
		$filter = new LoggerFilterLevelMatch();
		$filter->setAcceptOnMatch("false");
		$filter->setLevelToMatch(LoggerLevel::getLevelWarn());
		
		$eventError = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		$eventDebug = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelDebug(), "testmessage");
		$eventWarn = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelWarn(), "testmessage");
		
		$result = $filter->decide($eventError);
		self::assertEquals($result, LoggerFilter::NEUTRAL);
		
		$result = $filter->decide($eventDebug);
		self::assertEquals($result, LoggerFilter::NEUTRAL);
		
		$result = $filter->decide($eventWarn);
		self::assertEquals($result, LoggerFilter::DENY);
	}
	
	public function testDecideDenyDebugLevel() {
		$filter = new LoggerFilterLevelMatch();
		$filter->setAcceptOnMatch("false");
		$filter->setLevelToMatch(LoggerLevel::getLevelDebug());
		
		$eventError = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		$eventDebug = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelDebug(), "testmessage");
		$eventWarn = new LoggerLoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), LoggerLevel::getLevelWarn(), "testmessage");
		
		$result = $filter->decide($eventError);
		self::assertEquals($result, LoggerFilter::NEUTRAL);
		
		$result = $filter->decide($eventDebug);
		self::assertEquals($result, LoggerFilter::DENY);
		
		$result = $filter->decide($eventWarn);
		self::assertEquals($result, LoggerFilter::NEUTRAL);
	}
	
	public function testConfiguration() {
		$config = LoggerConfigurator::getDefaultConfiguration();
		
		// Add filters to accept debug and deny all others
		$config['appenders']['default']['filters'] = array(
			array(
				'class' => 'LoggerFilterLevelMatch',
				'params' => array(
					'levelToMatch' => 'error',
					'acceptOnMatch' => true
				)
			),
			array(
				'class' => 'LoggerFilterDenyAll',
			)
		);
		 
		Logger::configure($config);
		$logger = Logger::getRootLogger();
		 
		ob_start();
		$logger->trace('Test');
		$logger->debug('Test');
		$logger->info('Test');
		$logger->warn('Test');
		$logger->error('Test');
		$logger->fatal('Test');
		$actual = ob_get_clean();
		
		var_dump($actual);
		
		$this->assertEmpty($actual);
	}
	
	
	
}
