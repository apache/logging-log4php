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

/** A mock mailer class which always reports success. */
class LoggerMailerMockSuccess implements LoggerMailerInterface {

	/** Counts how many times send was called. */
	public $count = 0;

	public function send($to, $subject, $message, $headers = "") {
		$this->count++;
		return true;
	}
}

/** A mock mailer class which always reports failure. */
class LoggerMailerMockFailure implements LoggerMailerInterface {

	public function send($to, $subject, $message, $headers = "") {
		return false;
	}
}

/**
 * @group appenders
 */
class LoggerAppenderMailTest extends PHPUnit_Framework_TestCase {

	public function testRequiresLayout() {
		$appender = new LoggerAppenderMail();
		self::assertTrue($appender->requiresLayout());
	}

	/** For greater coverge! */
	public function testAccessors() {

		$buffer = 10;
		$from = 'log4php@localhost';
		$mailer = new LoggerMailerPHP();
		$subject = "Subject";
		$to = 'log4php@target';

		$appender = new LoggerAppenderMail("testAppender");
		$appender->setBufferSize($buffer);
		$appender->setFrom($from);
		$appender->setMailer($mailer);
		$appender->setSubject($subject);
		$appender->setTo($to);
		$appender->activateOptions();

		$this->assertSame($buffer, $appender->getBufferSize());
		$this->assertSame($from, $appender->getFrom());
		$this->assertSame($mailer, $appender->getMailer());
		$this->assertSame($subject, $appender->getSubject());
		$this->assertSame($to, $appender->getTo());
	}

	public function testDefaultMailer() {

		$appender = new LoggerAppenderMail("testAppender");
		$appender->setTo('log4php@gmail.com');
		$appender->setFrom('log4php@localhost');
		$appender->activateOptions();

		$this->assertInstanceOf('LoggerMailerInterface', $appender->getMailer());
	}

	public function testMail() {
		$mockMailer = new LoggerMailerMockSuccess();

		$this->assertSame(0, $mockMailer->count);

		$appender = new LoggerAppenderMail("testAppender");
		$appender->setTo('log4php@gmail.com');
		$appender->setFrom('log4php@localhost');
		$appender->setSubject("Testing text/plain " . date('Y-m-d H:i:s'));
		$appender->setMailer($mockMailer);
		$appender->activateOptions();

		$appender->append(LoggerTestHelper::getTraceEvent('tracing'));
		$appender->append(LoggerTestHelper::getDebugEvent('debugging'));
		$appender->append(LoggerTestHelper::getInfoEvent('informing'));
		$appender->append(LoggerTestHelper::getWarnEvent('warning'));
		$appender->append(LoggerTestHelper::getErrorEvent('erring'));
		$appender->append(LoggerTestHelper::getFatalEvent('fatality!'));

		$this->assertSame(0, $mockMailer->count);
		$appender->close();
		$this->assertSame(1, $mockMailer->count);
	}

	public function testMailBuffered() {
		$mockMailer = new LoggerMailerMockSuccess();

		$appender = new LoggerAppenderMail("testAppender");
		$appender->setTo('log4php@gmail.com');
		$appender->setFrom('log4php@localhost');
		$appender->setSubject("Testing text/plain " . date('Y-m-d H:i:s'));
		$appender->setBufferSize(4);
		$appender->setMailer($mockMailer);
		$appender->activateOptions();

		// Buffer should be cleared after 4 log messages

		$this->assertSame(0, $mockMailer->count);

		$appender->append(LoggerTestHelper::getTraceEvent('tracing'));
		$appender->append(LoggerTestHelper::getDebugEvent('debugging'));
		$appender->append(LoggerTestHelper::getInfoEvent('informing'));

		$this->assertSame(0, $mockMailer->count);

		$appender->append(LoggerTestHelper::getWarnEvent('warning'));
		$appender->append(LoggerTestHelper::getErrorEvent('erring'));

		$this->assertSame(1, $mockMailer->count);

		$appender->append(LoggerTestHelper::getFatalEvent('fatality!'));

		$this->assertSame(1, $mockMailer->count);

		// Close should send the remaining messages in buffer
		$appender->close();

		$this->assertSame(2, $mockMailer->count);
	}

	public function testMailHTML() {
		$mockMailer = new LoggerMailerMockSuccess();

		$appender = new LoggerAppenderMail("testAppender");
		$appender->setLayout(new LoggerLayoutHtml());
		$appender->setTo('log4php@gmail.com');
		$appender->setFrom('log4php@localhost');
		$appender->setSubject("Testing text/html " . date('Y-m-d H:i:s'));
		$appender->setMailer($mockMailer);
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
	 * @expectedExceptionMessage Failed sending email. Please check your php.ini settings. Closing appender.
	 */
	public function testMailerError() {
		$mockMailer = new LoggerMailerMockFailure();

		$appender = new LoggerAppenderMail("testAppender");
		$appender->setLayout(new LoggerLayoutHtml());
		$appender->setTo('log4php@gmail.com');
		$appender->setFrom('log4php@localhost');
		$appender->setSubject("Testing text/html " . date('Y-m-d H:i:s'));
		$appender->setMailer($mockMailer);
		$appender->activateOptions();

		$appender->append(LoggerTestHelper::getTraceEvent('tracing'));
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
