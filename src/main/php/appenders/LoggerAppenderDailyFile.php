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
 */

/**
 * An Appender that automatically creates a new logfile each day.
 *
 * The file is rolled over once a day. That means, for each day a new file 
 * is created. A formatted version of the date pattern is used as to create 
 * the file name using the {@link PHP_MANUAL#sprintf} function.
 *
 * This appender uses a layout.
 * 
 * ##Configurable parameters:##
 * 
 * - **datePattern** - Format for the date in the file path, follows formatting
 *     rules used by the PHP date() function. Default value: "Ymd".
 * - **file** - Path to the target file. Should contain a %s which gets 
 *     substituted by the date.
 * - **append** - If set to true, the appender will append to the file, 
 *     otherwise the file contents will be overwritten. Defaults to true.
 * 
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/daily-file.html Appender documentation
 */
class LoggerAppenderDailyFile extends LoggerAppenderFile {

	/**
	 * The 'datePattern' parameter.
	 * Determines how date will be formatted in file name.
	 * @var string
	 */
	protected $datePattern = "Ymd";

	/** Additional validation for the date pattern. */
	public function activateOptions() {
		parent::activateOptions();
	
		if (empty($this->datePattern)) {
			$this->warn("Required parameter 'datePattern' not set. Closing appender.");
			$this->closed = true;
			return;
		}
	}
	
	/**
	 * Determines target file. Replaces %s in file path with a date. 
	 */
	protected function getTargetFile() {
		$date = date($this->datePattern);
		return str_replace('%s', $date, $this->file);
	}
	
	/**
	 * Sets the 'datePattern' parameter.
	 * @param string $datePattern
	 */
	public function setDatePattern($datePattern) {
		$this->setString('datePattern', $datePattern);
	}
	
	/**
	 * Returns the 'datePattern' parameter.
	 * @return string
	 */
	public function getDatePattern() {
		return $this->datePattern;
	}
}
