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

use Apache\Log4php\Filters\AbstractFilter;
use Apache\Log4php\Level;
use Apache\Log4php\Logger;
use Apache\Log4php\LoggingEvent;

/** A set of helper functions for running tests. */
class TestHelper {

	/**
	 * Returns a test logging event with level set to TRACE.
	 * @return LoggingEvent
	 */
	public static function getTraceEvent($message = 'test', $logger = "test") {
		return new LoggingEvent(__CLASS__, new Logger($logger), Level::getLevelTrace(), $message);
	}

	/**
	 * Returns a test logging event with level set to DEBUG.
	 * @return LoggingEvent
	 */
	public static function getDebugEvent($message = 'test', $logger = "test") {
		return new LoggingEvent(__CLASS__, new Logger($logger), Level::getLevelDebug(), $message);
	}

	/**
	 * Returns a test logging event with level set to INFO.
	 * @return LoggingEvent
	 */
	public static function getInfoEvent($message = 'test', $logger = "test") {
		return new LoggingEvent(__CLASS__, new Logger($logger), Level::getLevelInfo(), $message);
	}

	/**
	 * Returns a test logging event with level set to WARN.
	 * @return LoggingEvent
	 */
	public static function getWarnEvent($message = 'test', $logger = "test") {
		return new LoggingEvent(__CLASS__, new Logger($logger), Level::getLevelWarn(), $message);
	}

	/**
	 * Returns a test logging event with level set to ERROR.
	 * @return LoggingEvent
	 */
	public static function getErrorEvent($message = 'test', $logger = "test") {
		return new LoggingEvent(__CLASS__, new Logger($logger), Level::getLevelError(), $message);
	}

	/**
	 * Returns a test logging event with level set to FATAL.
	 * @return LoggingEvent
	 */
	public static function getFatalEvent($message = 'test', $logger = "test") {
		return new LoggingEvent(__CLASS__, new Logger($logger), Level::getLevelFatal(), $message);
	}

	/**
	 * Returns an array of logging events, one for each level, sorted ascending
	 * by severitiy.
	 */
	public static function getAllEvents($message = 'test') {
		return array(
			self::getTraceEvent($message),
			self::getDebugEvent($message),
			self::getInfoEvent($message),
			self::getWarnEvent($message),
			self::getErrorEvent($message),
			self::getFatalEvent($message),
		);
	}

	/** Returns an array of all existing levels, sorted ascending by severity. */
	public static function getAllLevels() {
		return array(
			Level::getLevelTrace(),
			Level::getLevelDebug(),
			Level::getLevelInfo(),
			Level::getLevelWarn(),
			Level::getLevelError(),
			Level::getLevelFatal(),
		);
	}

	/** Returns a string representation of a filter decision. */
	public static function decisionToString($decision) {
		switch($decision) {
			case AbstractFilter::ACCEPT: return 'ACCEPT';
			case AbstractFilter::NEUTRAL: return 'NEUTRAL';
			case AbstractFilter::DENY: return 'DENY';
		}
	}

	/** Returns a simple configuration with one echo appender tied to root logger. */
	public static function getEchoConfig() {
		return array(
	        'threshold' => 'ALL',
	        'rootLogger' => array(
	            'level' => 'trace',
	            'appenders' => array('default'),
			),
	        'appenders' => array(
	            'default' => array(
	                'class' => 'EchoAppender',
	                'layout' => array(
	                    'class' => 'SimpleLayout',
					),
				),
			),
		);
	}

	/** Returns a simple configuration with one echo appender using the pattern layout. */
	public static function getEchoPatternConfig($pattern) {
		return array(
			'threshold' => 'ALL',
			'rootLogger' => array(
				'level' => 'trace',
				'appenders' => array('default'),
			),
			'appenders' => array(
				'default' => array(
					'class' => 'EchoAppender',
					'layout' => array(
						'class' => 'PatternLayout',
						'params' => array(
							'conversionPattern' => $pattern
						)
					),
				),
			),
		);
	}
}

?>