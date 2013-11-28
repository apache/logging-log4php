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

use Apache\Log4php\Level;

/**
 * @group main
 */
class LevelTest extends \PHPUnit_Framework_TestCase {

	protected function doTestLevel($level, $code, $str, $syslog) {
		self::assertTrue($level instanceof Level);
		self::assertEquals($level->toInt(), $code);
		self::assertEquals($level->toString(), $str);
		self::assertEquals($level->getSyslogEquivalent(), $syslog);
	}

	public function testLevelOff() {
		$this->doTestLevel(Level::getLevelOff(), Level::OFF, 'OFF', LOG_ALERT);
		$this->doTestLevel(Level::toLevel(Level::OFF), Level::OFF, 'OFF', LOG_ALERT);
		$this->doTestLevel(Level::toLevel('OFF'), Level::OFF, 'OFF', LOG_ALERT);
    }

	public function testLevelFatal() {
		$this->doTestLevel(Level::getLevelFatal(), Level::FATAL, 'FATAL', LOG_ALERT);
		$this->doTestLevel(Level::toLevel(Level::FATAL), Level::FATAL, 'FATAL', LOG_ALERT);
		$this->doTestLevel(Level::toLevel('FATAL'), Level::FATAL, 'FATAL', LOG_ALERT);
    }

	public function testLevelError() {
		$this->doTestLevel(Level::getLevelError(), Level::ERROR, 'ERROR', LOG_ERR);
		$this->doTestLevel(Level::toLevel(Level::ERROR), Level::ERROR, 'ERROR', LOG_ERR);
		$this->doTestLevel(Level::toLevel('ERROR'), Level::ERROR, 'ERROR', LOG_ERR);
    }

	public function testLevelWarn() {
		$this->doTestLevel(Level::getLevelWarn(), Level::WARN, 'WARN', LOG_WARNING);
		$this->doTestLevel(Level::toLevel(Level::WARN), Level::WARN, 'WARN', LOG_WARNING);
		$this->doTestLevel(Level::toLevel('WARN'), Level::WARN, 'WARN', LOG_WARNING);
    }

	public function testLevelInfo() {
		$this->doTestLevel(Level::getLevelInfo(), Level::INFO, 'INFO', LOG_INFO);
		$this->doTestLevel(Level::toLevel(Level::INFO), Level::INFO, 'INFO', LOG_INFO);
		$this->doTestLevel(Level::toLevel('INFO'), Level::INFO, 'INFO', LOG_INFO);
    }

	public function testLevelDebug() {
		$this->doTestLevel(Level::getLevelDebug(), Level::DEBUG, 'DEBUG', LOG_DEBUG);
		$this->doTestLevel(Level::toLevel(Level::DEBUG), Level::DEBUG, 'DEBUG', LOG_DEBUG);
		$this->doTestLevel(Level::toLevel('DEBUG'), Level::DEBUG, 'DEBUG', LOG_DEBUG);
	}

    public function testLevelTrace() {
		$this->doTestLevel(Level::getLevelTrace(), Level::TRACE, 'TRACE', LOG_DEBUG);
		$this->doTestLevel(Level::toLevel(Level::TRACE), Level::TRACE, 'TRACE', LOG_DEBUG);
		$this->doTestLevel(Level::toLevel('TRACE'), Level::TRACE, 'TRACE', LOG_DEBUG);
    }

	public function testLevelAll() {
		$this->doTestLevel(Level::getLevelAll(), Level::ALL, 'ALL', LOG_DEBUG);
		$this->doTestLevel(Level::toLevel(Level::ALL), Level::ALL, 'ALL', LOG_DEBUG);
		$this->doTestLevel(Level::toLevel('ALL'), Level::ALL, 'ALL', LOG_DEBUG);
    }
}
