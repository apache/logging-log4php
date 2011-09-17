<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *	   http://www.apache.org/licenses/LICENSE-2.0
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
 * Pool implmentation for LoggerAppender instances
 *
 * @version $Revision: 795727 $
 * @package log4php
 */
class LoggerAppenderPool {
	
	/** Holds appenders indexed by their name */
	public static $appenders =  array();

	/**
	 * Adds an appender to the pool. 
	 * The appender must be named for this operation. 
	 * @param LoggerAppender $appender
	 */
	public static function add(LoggerAppender $appender)
	{
		$name = $appender->getName();
		
		if(empty($name)) {
			trigger_error('Cannot add unnamed appender to pool.', E_USER_WARNING);
			return;
		}
		
		self::$appenders[$name] = $appender;
	}
	
	/** 
	 * Retrieves an appender from the pool by name. 
	 * @param string $name Name of the appender to retrieve.
	 * @return LoggerAppender The named appender or NULL if no such appender 
	 *  exists in the pool.
	 */
	public static function get($name) {
		return isset(self::$appenders[$name]) ? self::$appenders[$name] : null;
	}
	
	/**
	 * Checks whether an appender exists in the pool.
	 * @param string $name Name of the appender to look for.
	 * @return boolean TRUE if the appender with the given name exists.
	 */
	public static function exists($name) {
		return isset(self::$appenders[$name]);
	}

	/**
	 * Clears all appenders from the pool.
	 */
	public static function clear() {
		 self::$appenders =  array();
	}
}