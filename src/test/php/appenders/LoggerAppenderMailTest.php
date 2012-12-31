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
 * @link       http://logging.apache.org/log4php
 */

/**
 * @group appenders
 */
class LoggerAppenderMailTest extends PHPUnit_Framework_TestCase {

	public function testRequiresLayout() {
		$appender = new LoggerAppenderMail();
		self::assertTrue($appender->requiresLayout());
	}

	public function testMail() {
		$appender = new LoggerAppenderMail("testAppender");
		$appender->setTo('log4php@gmail.com');
		$appender->setFrom('log4php@localhost');
		$appender->setSubject("Testing text/plain " . date('Y-m-d H:i:s'));
		$appender->activateOptions();

		$appender->append(LoggerTestHelper::getTraceEvent('tracing'));
		$appender->append(LoggerTestHelper::getDebugEvent('debugging'));
		$appender->append(LoggerTestHelper::getInfoEvent('informing'));
		$appender->append(LoggerTestHelper::getWarnEvent('warning'));
		$appender->append(LoggerTestHelper::getErrorEvent('erring'));
		$appender->append(LoggerTestHelper::getFatalEvent('fatality!'));
		$appender->close();
	}

	public function testMailHTML() {
		$appender = new LoggerAppenderMail("testAppender");
		$appender->setLayout(new LoggerLayoutHtml());
		$appender->setTo('log4php@gmail.com');
		$appender->setFrom('log4php@localhost');
		$appender->setSubject("Testing text/html " . date('Y-m-d H:i:s'));
		$appender->activateOptions();

		$appender->append(LoggerTestHelper::getTraceEvent('tracing'));
		$appender->append(LoggerTestHelper::getDebugEvent('debugging'));
		$appender->append(LoggerTestHelper::getInfoEvent('informing'));
		$appender->append(LoggerTestHelper::getWarnEvent('warning'));
		$appender->append(LoggerTestHelper::getErrorEvent('erring'));
		$appender->append(LoggerTestHelper::getFatalEvent('fatality!'));
		$appender->close();
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage Required parameter 'to' not set.
	 */
	public function testErrorMissingTo() {
		$appender = new LoggerAppenderMail("testAppender");
		$appender->setLayout(new LoggerLayoutHtml());
		$appender->setFrom('log4php@localhost');
		$appender->setSubject("Testing text/html " . date('Y-m-d H:i:s'));
		$appender->activateOptions();
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage Required parameter 'from' not set.
	 */
	public function testErrorMissingFrom() {
		$appender = new LoggerAppenderMail("testAppender");
		$appender->setLayout(new LoggerLayoutHtml());
		$appender->setTo('log4php@gmail.com');
		$appender->setSubject("Testing text/html " . date('Y-m-d H:i:s'));
		$appender->activateOptions();
	}
}
