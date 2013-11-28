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

namespace Apache\Log4php\Tests\Appenders;

use Apache\Log4php\Appenders\SyslogAppender;
use Apache\Log4php\Layouts\SimpleLayout;
use Apache\Log4php\Level;
use Apache\Log4php\Logger;
use Apache\Log4php\LoggingEvent;

/**
 * Tests the syslog appender.
 *
 * Many of these tests rely on reflection features introduced in 5.3 and
 * will be skipped if run on a lower version.
 *
 * This test will only write a single entry to the syslog.
 *
 * @group appenders
 */
class SyslogAppenderTest extends \PHPUnit_Framework_TestCase {

	public function testSettersGetters() {

		// Setters should accept any value, without validation
		$expected = "Random string value";

		$appender = new SyslogAppender();
		$appender->setIdent($expected);
		$appender->setFacility($expected);
		$appender->setOverridePriority($expected);
		$appender->setPriority($expected);
		$appender->setOption($expected);

		$actuals = array(
			$appender->getIdent(),
			$appender->getFacility(),
			$appender->getOverridePriority(),
			$appender->getPriority(),
			$appender->getOption()
		);

		foreach($actuals as $actual) {
			$this->assertSame($expected, $actual);
		}
	}

	public function testRequiresLayout() {
		$appender = new SyslogAppender();
		$this->assertTrue($appender->requiresLayout());
	}

	public function testLogging() {
		$appender = new SyslogAppender("myname");
		$appender->setLayout(new SimpleLayout());
		$appender->activateOptions();

		$event = new LoggingEvent(__CLASS__, new Logger("TestLogger"), Level::getLevelError(), "testmessage");
		$appender->append($event);
	}

	/** Tests parsing of "option" parameter. */
	public function testOption() {
		$options = array(
			'CONS' => LOG_CONS,
			'NDELAY' => LOG_NDELAY,
			'ODELAY' => LOG_ODELAY,
			'PERROR' => LOG_PERROR,
			'PID' => LOG_PID,

			// test some combinations
			'CONS|NDELAY' => LOG_CONS | LOG_NDELAY,
			'PID|PERROR' => LOG_PID | LOG_PERROR,
			'CONS|PID|NDELAY' => LOG_CONS | LOG_PID | LOG_NDELAY
		);

		// Defaults
		$defaultStr = "PID|CONS";
		$default = LOG_PID | LOG_CONS;

		// This makes reading of a private property possible
		$property = new \ReflectionProperty('Apache\\Log4php\Appenders\\SyslogAppender', 'intOption');
		$property->setAccessible(true);

		// Check default value first
		$appender = new SyslogAppender();
		$appender->activateOptions();
		$actual = $property->getValue($appender);
		$this->assertSame($default, $actual, "Failed setting default option [$defaultStr]");

		foreach($options as $option => $expected) {
			$appender = new SyslogAppender();
			$appender->setOption($option);
			$appender->activateOptions();

			$actual = $property->getValue($appender);
			$this->assertSame($expected, $actual, "Failed setting option [$option].");
		}
	}

	/** Tests parsing of "priority" parameter. */
	public function testPriority() {
		$default = null;
		$defaultStr = 'null';

		$priorities = array(
			'EMERG' => LOG_EMERG,
			'ALERT' => LOG_ALERT,
			'CRIT' => LOG_CRIT,
			'ERR' => LOG_ERR,
			'WARNING' => LOG_WARNING,
			'NOTICE' => LOG_NOTICE,
			'INFO' => LOG_INFO,
			'DEBUG' => LOG_DEBUG
		);

		// This makes reading of a private property possible
		$property = new \ReflectionProperty('Apache\\Log4php\\Appenders\\SyslogAppender', 'intPriority');
		$property->setAccessible(true);

		// Check default value first
		$appender = new SyslogAppender();
		$appender->activateOptions();
		$actual = $property->getValue($appender);
		$this->assertSame($default, $actual, "Failed setting default priority [$defaultStr].");

		foreach($priorities as $priority => $expected) {
			$appender = new SyslogAppender();
			$appender->setPriority($priority);
			$appender->activateOptions();

			$actual = $property->getValue($appender);
			$this->assertSame($expected, $actual, "Failed setting priority [$priority].");
		}
	}

	/** Tests parsing of "facility" parameter. */
	public function testFacility() {
		// Default value is the same on all OSs
		$default = LOG_USER;
		$defaultStr = 'USER';

		// All possible facility strings (some of which might not exist depending on the OS)
		$strings = array(
			'KERN', 'USER', 'MAIL', 'DAEMON', 'AUTH',
			'SYSLOG', 'LPR', 'NEWS', 'UUCP', 'CRON', 'AUTHPRIV',
			'LOCAL0', 'LOCAL1', 'LOCAL2', 'LOCAL3', 'LOCAL4',
			'LOCAL5', 'LOCAL6', 'LOCAL7',
		);

		// Only test facilities which exist on this OS
		$facilities = array();
		foreach($strings as $string) {
			$const = "LOG_$string";
			if (defined($const)) {
				$facilities[$string] = constant($const);
			}
		}

		// This makes reading of a private property possible
		$property = new \ReflectionProperty('Apache\\Log4php\\Appenders\\SyslogAppender', 'intFacility');
		$property->setAccessible(true);

		// Check default value first
		$appender = new SyslogAppender();
		$appender->activateOptions();
		$actual = $property->getValue($appender);
		$this->assertSame($default, $default, "Failed setting default facility [$defaultStr].");

		foreach($facilities as $facility => $expected) {
			$appender = new SyslogAppender();
			$appender->setFacility($facility);
			$appender->activateOptions();

			$actual = $property->getValue($appender);
			$this->assertSame($expected, $actual, "Failed setting priority [$facility].");
		}
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInvalidOption() {
		$appender = new SyslogAppender();
		$appender->setOption('CONS|XYZ');
		$appender->activateOptions();
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInvalidPriority() {
		$appender = new SyslogAppender();
		$appender->setPriority('XYZ');
		$appender->activateOptions();
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testInvalidFacility() {
		$appender = new SyslogAppender();
		$appender->setFacility('XYZ');
		$appender->activateOptions();
	}


	public function testPriorityOverride() {
		$appender = new SyslogAppender();
		$appender->setPriority('EMERG');
		$appender->setOverridePriority(true);
		$appender->activateOptions();

		$levels = array(
			Level::getLevelTrace(),
			Level::getLevelDebug(),
			Level::getLevelInfo(),
			Level::getLevelWarn(),
			Level::getLevelError(),
			Level::getLevelFatal(),
		);

		$expected = LOG_EMERG;

		$method = new \ReflectionMethod('Apache\\Log4php\\Appenders\\SyslogAppender', 'getSyslogPriority');
		$method->setAccessible(true);

		foreach($levels as $level) {
			$actual = $method->invoke($appender, $level);
			$this->assertSame($expected, $actual);
		}
	}
}
