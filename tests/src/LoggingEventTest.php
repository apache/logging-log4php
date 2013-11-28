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
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link       http://logging.apache.org/log4php
 */

namespace Apache\Log4php\Tests;

use Apache\Log4php\Appenders\NullAppender;
use Apache\Log4php\Layouts\AbstractLayout;
use Apache\Log4php\Level;
use Apache\Log4php\Logger;
use Apache\Log4php\LoggingEvent;

class LoggingEventTestCaseAppender extends NullAppender {

	protected $requiresLayout = true;

	public function append(LoggingEvent $event) {
		$this->layout->format($event);
	}
}

class LoggingEventTestCaseLayout extends AbstractLayout {

	public function activateOptions() {
		return;
	}

	public function format(LoggingEvent $event) {
		LoggingEventTest::$locationInfo  = $event->getLocationInformation();
        LoggingEventTest::$throwableInfo = $event->getThrowableInformation();
	}
}

/**
 * @group main
 */
class LoggingEventTest extends \PHPUnit_Framework_TestCase {

	public static $locationInfo;
    public static $throwableInfo;

	public function testConstructWithLoggerName() {
		$l = Level::getLevelDebug();
		$e = new LoggingEvent('fqcn', 'TestLogger', $l, 'test');
		self::assertEquals($e->getLoggerName(), 'TestLogger');
	}

	public function testConstructWithTimestamp() {
		$l = Level::getLevelDebug();
		$timestamp = microtime(true);
		$e = new LoggingEvent('fqcn', 'TestLogger', $l, 'test', $timestamp);
		self::assertEquals($e->getTimeStamp(), $timestamp);
 	}

	public function testGetStartTime() {
		$time = LoggingEvent::getStartTime();
		self::assertInternalType('float', $time);
		$time2 = LoggingEvent::getStartTime();
		self::assertEquals($time, $time2);
	}

	public function testGetLocationInformation() {
		$hierarchy = Logger::getHierarchy();
		$root = $hierarchy->getRootLogger();

		$a = new LoggingEventTestCaseAppender('A1');
		$a->setLayout(new LoggingEventTestCaseLayout());
		$root->addAppender($a);

		$logger = $hierarchy->getLogger('test');

		$line = __LINE__; $logger->debug('test');
		$hierarchy->shutdown();

		$li = self::$locationInfo;

		self::assertEquals(get_class($this), $li->getClassName());
		self::assertEquals(__FILE__, $li->getFileName());
		self::assertEquals($line, $li->getLineNumber());
		self::assertEquals(__FUNCTION__, $li->getMethodName());
	}

	public function testGetThrowableInformation1() {
		$hierarchy = Logger::getHierarchy();
		$root = $hierarchy->getRootLogger();

		$a = new LoggingEventTestCaseAppender('A1');
		$a->setLayout( new LoggingEventTestCaseLayout() );
		$root->addAppender($a);

		$logger = $hierarchy->getLogger('test');
		$logger->debug('test');
		$hierarchy->shutdown();

		$ti = self::$throwableInfo;

		self::assertEquals($ti, null);
	}

	public function testGetThrowableInformation2() {
		$hierarchy = Logger::getHierarchy();
		$root = $hierarchy->getRootLogger();

		$a = new LoggingEventTestCaseAppender('A1');
		$a->setLayout( new LoggingEventTestCaseLayout() );
		$root->addAppender($a);

		$ex	= new \Exception('Message1');
		$logger = $hierarchy->getLogger('test');
		$logger->debug('test', $ex);
		$hierarchy->shutdown();

		$ti = self::$throwableInfo;

		self::assertInstanceOf("Apache\\Log4php\\ThrowableInformation", $ti);

		$result	= $ti->getStringRepresentation();
		self::assertInternalType('array', $result);
	}
}
