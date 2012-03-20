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
 * @example   use Monolog/Logger;				//Alternate to firephp lib
 * @example   use Monolog/Handler/FirePHPHandler;
 */

/**
 * Connect Apache Log4php to Insight, the next version of FirePHP
 * Read the link at sourcemint, to <b>define</b> 4 INSIGHT_* constants in your php
 * config / bootstrap
 *
 * @category  Include
 * @package   LoggerAppenderFirephp
 * @author    Bruce Ingalls <Bruce.Ingalls-at-gmail-dot-com>
 * @copyright 2012 Apache Software Foundation
 * @license   Apache License, Version 2.0
 * @link      http://sourcemint.com/github.com/firephp/firephp/1:1.0.0b1rc6/-docs/Configuration/Constants
 * @example   define('INSIGHT_IPS', '*');		//If using firephp lib
 * @example   define('INSIGHT_AUTHKEYS', '*');  //weak security (ok for dev only)
 * @example   define('INSIGHT_PATHS', dirname(__FILE__));
 * @example   define('INSIGHT_SERVER_PATH', '/index.php');
 */
class LoggerAppenderFirephp extends LoggerAppender {
	protected $console;
	protected $medium;

	/**
	 * Default constructor
	 *
	 * @param string $name Default ''
	 */
	public function __construct($name = '') {
		$this->requiresLayout = false;
		
		parent::__construct($name);
	}

	/**
	 * Enable
	 *
	 * @return void
	 */
	public function activateOptions() {
		$this->closed = false;
	}

	/**
	 * Write event object to Log. Defaults to INFO level
	 *
	 * @param LoggerLoggingEvent $event Includes level & message
	 *
	 * @return void
	 */
	public function append(LoggerLoggingEvent $event) {
		$console = $this->getConsole();
		if (null === $console) {
			return;		//Silently fail, if FirePHP is unavailable
		}

		$msg = '';
		if ($this->layout !== null) {
			$msg = trim($this->layout->format($event));
		} else {
			$msg = '[' . $event->getLevel()->toString() . '] - ' . $event->getRenderedMessage();
		}

		switch (strtolower($event->getLevel()->toString())) {
		case 'debug':
			$console->trace($msg);	//includes backtrace
			break;
		case 'warn':
			$console->debug($msg);
			break;
		case 'error':
			$console->warn($msg);
			break;
		case 'fatal':
			$console->error($msg);
			break;
		default:
			$console->info($msg);
		}
	}

	/**
	 * Disable
	 *
	 * @return void
	 */
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
	 *
	 * @return void
	 */
	public function setMedium($medium='page') {
		$this->setString('medium', $medium);
	}

}
