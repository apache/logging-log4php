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
 * @version    $Revision$
 * @link       http://logging.apache.org/log4php
 */

/**
 * @group appenders
 */
class LoggerAppenderDailyFileTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		@unlink(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.' . date('Ymd'));
		@unlink(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.' . date('Y'));
	}
	
	public function testRequiresLayout() {
		$appender = new LoggerAppenderDailyFile(); 
		self::assertTrue($appender->requiresLayout());
	}
	
	public function testDefaultLayout() {
		$appender = new LoggerAppenderDailyFile();
		$actual = $appender->getLayout();
		self::assertInstanceOf('LoggerLayoutSimple', $actual);
	}
	
	public function testSimpleLogging() {
		$event = LoggerTestHelper::getWarnEvent("my message");

		$appender = new LoggerAppenderDailyFile(); 
		$appender->setFile(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.%s');
		$appender->activateOptions();
		$appender->append($event);
		$appender->close();

		$actual = file_get_contents(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.' . date("Ymd"));		
		$expected = "WARN - my message".PHP_EOL;
		self::assertEquals($expected, $actual);
	}
	 
	public function testChangedDateFormat() {
		$event = LoggerTestHelper::getWarnEvent("my message");
		
		$appender = new LoggerAppenderDailyFile(); 
		$appender->setDatePattern('Y');
		$appender->setFile(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.%s');
		$appender->activateOptions();
		$appender->append($event);
		$appender->close();

		$actual = file_get_contents(PHPUNIT_TEMP_DIR . '/TEST-daily.txt.' . date("Y"));		
		$expected = "WARN - my message".PHP_EOL;
		self::assertEquals($expected, $actual);
	} 
}
