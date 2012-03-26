<?php

/**
 * LoggerAppenderFirephp
 *
 * Licensed to the Apache Software Foundation (ASF) under one or more contributor
 * license agreements. See the NOTICE file distributed with this work for
 * additional information regarding copyright ownership. The ASF licenses this
 * file to you under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *      http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software distributed
 * under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
 * CONDITIONS OF ANY KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations under the License.
 *
 * PHP version 5
 *
 * @category  Include
 * @package   LoggerAppenderFirephp
 * @author    Bruce Ingalls <Bruce.Ingalls-at-gmail-dot-com>
 * @copyright 2012 Apache Software Foundation
 * @license   Apache License, Version 2.0
 * @version   SVN: $Id:$
 * @link      http://sourcemint.com/github.com/firephp/firephp/1:1.0.0b1rc6/-docs/Configuration/Constants
 * @link      https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/FirePHPHandler.php
 * @see       LoggerAutoloader.php Update class list
 * @since     Feb 22, 2012
 * @internal  CodeSniffs as PEAR style, adapted to Apache. Phpmd clean.
 * @example   require_once 'FirePHP/Init.php';	//Must be declared before log4php
 */

/**
 * Connect Apache Log4php to Insight, the next version of FirePHP
 * Read the link at sourcemint, to <b>define</b> 4 INSIGHT_* constants in your php
 * 
 * LoggerAppenderFirephp logs events by creating a PHP user-level message using the Browser-Extension FirePHP
 * 
 * An example php file:
 * 
 * {@example ../../examples/php/appender_firephp.php 19}
 *
 * An example configuration file:
 * 
 * {@example ../../examples/resources/appender_firephp.xml 18}
 * 
 * @package   LoggerAppenderFirephp
 * @author    Bruce Ingalls <Bruce.Ingalls-at-gmail-dot-com>
 * @copyright 2012 Apache Software Foundation
 * @license   Apache License, Version 2.0
 * @link      http://sourcemint.com/github.com/firephp/firephp/1:1.0.0b1rc6/-docs/Configuration/Constants
 */
class LoggerAppenderFirephp extends LoggerAppender {
	
	/**
	 * 
	 * @var FirePHP instance of FirePHP-Console Client
	 */
	protected $console;
	
	/**
	 * 
	 * where to write the log-message
	 * @var string possible values are: page, request, package, contoller, site, page
	 */
	protected $medium;

	/**
	 * Enable
	 *
	 * @return void
	 */
	public function activateOptions() {
		$this->console = $this->getConsole();
		if (null === $this->console) {
			$this->warn('FirePHP is not installed correctly.');
		}		
		
		$this->closed = true;
	}

	/**
	 * Write event object to Log. Defaults to INFO level
	 *
	 * @param LoggerLoggingEvent this->consoleIncludes level & message
	 *
	 * @return void
	 */
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
	 * Get firephp display object
	 *
	 * @return object null, if FirePHP is unavailable
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

	/**
	 * Get medium, typically 'page'
	 *
	 * @return string
	 */
	public function getMedium() {
		return $this->medium;
	}

	/**
	 * Sets medium. Defaults to 'page' for firebug console
	 *
	 * @param string $medium
	 */
	public function setMedium($medium = 'page') {
		$this->setString('medium', $medium);
	}
}