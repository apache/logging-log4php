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
 * Logs messages as HTTP headers using the FirePHP Insight API.
 * 
 * This appender requires the FirePHP server library version 1.0 or later.
 * 
 * Configurable parameters of this appender are:
 * - medium - (string) The target to which messages will be sent. Possible options are 
 *            'page' (default), 'request', 'package' and 'controller'. For more details,
 *            see FirePHP documentation.
 * 
 * This class was originally contributed by Bruce Ingalls (Bruce.Ingalls-at-gmail-dot-com).
 * 
 * An example php file:
 * 
 * {@example ../../examples/php/appender_firephp.php 19}
 *
 * An example configuration file:
 * 
 * {@example ../../examples/resources/appender_firephp.xml 18}
 * 
 * @link https://github.com/firephp/firephp FirePHP 1.0 homepage.
 * @link http://sourcemint.com/github.com/firephp/firephp/1:1.0.0b1rc6/-docs/Welcome 
 *       FirePHP documentation.
 * @link http://sourcemint.com/github.com/firephp/firephp/1:1.0.0b1rc6/-docs/Configuration/Constants
 *       FirePHP constants documentation.
 * 
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @since 2.3
 */
class LoggerAppenderFirephp extends LoggerAppender {
	
	/**
	 * Instance of the Insight console class.
	 * @var Insight_Plugin_Console
	 */
	protected $console;
	
	/**
	 * The target for log messages. Possible values are: 'page' (default), 
	 * 'request', 'package' and 'contoller'.
	 */
	protected $medium;

	public function activateOptions() {
		$this->console = $this->getConsole();
		if (null === $this->console) {
			$this->warn('FirePHP is not installed correctly.');
		}		
		
		$this->closed = true;
	}

	public function append(LoggerLoggingEvent $event) {
		$msg = $this->getLayout()->format($event);
		
		switch ($this->getLogLevel($event)) {
		case 'debug':
			$this->console->trace($msg);	//includes backtrace
			break;
		case 'warn':
			$this->console->debug($msg);
			break;
		case 'error':
			$this->console->warn($msg);
			break;
		case 'fatal':
			$this->console->error($msg);
			break;
		default:
			$this->console->info($msg);
		}
	}
	
	private function getLogLevel(LoggerLoggingEvent $event) {
		return strtolower($event->getLevel()->toString());
	}

	public function close() {
		$this->closed = true;
	}

	public function __destruct() {
		$this->close();
	}

	/**
	 * Returns the FirePHP Insight console.
	 * @return Insight_Plugin_Console
	 */
	private function getConsole() {
		if (isset($this->console)) {
			return $this->console;
		}
		
		if (method_exists('FirePHP', 'to')) {
			$inspector = FirePHP::to($this->getMedium());
		
			return $inspector->console();
		}
		
		return null;
	}

	/** Returns the medium. */
	public function getMedium() {
		return $this->medium;
	}

	/** Sets the medium. */
	public function setMedium($medium = 'page') {
		$this->setString('medium', $medium);
	}
}