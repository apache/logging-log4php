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
 *
 * @package log4php
 * @subpackage varia
 */

/**
 * This is a very simple filter based on string matching.
 * 
 * <p>The filter admits two options {@link $stringToMatch} and
 * {@link $acceptOnMatch}. If there is a match (using {@link PHP_MANUAL#strpos}
 * between the value of the {@link $stringToMatch} option and the message 
 * of the {@link LoggerLoggingEvent},
 * then the {@link decide()} method returns {@link LoggerFilter::ACCEPT} if
 * the <b>AcceptOnMatch</b> option value is true, if it is false then
 * {@link LoggerFilter::DENY} is returned. If there is no match, {@link LoggerFilter::NEUTRAL}
 * is returned.</p>
 *
 * @version $Revision$
 * @package log4php
 * @subpackage varia
 * @since 0.3
 */
class LoggerStringMatchFilter extends LoggerFilter {

	/**
	 * @var boolean
	 */
	var $acceptOnMatch = true;

	/**
	 * @var string
	 */
	var $stringToMatch = null;

	/**
	 * @return boolean
	 */
	function getAcceptOnMatch() {
		return $this->acceptOnMatch;
	}
	
	/**
	 * @param mixed $acceptOnMatch a boolean or a string ('true' or 'false')
	 */
	function setAcceptOnMatch($acceptOnMatch) {
		$this->acceptOnMatch = is_bool($acceptOnMatch) ? $acceptOnMatch : (bool)(strtolower($acceptOnMatch) == 'true');
	}
	
	/**
	 * @return string
	 */
	function getStringToMatch() {
		return $this->stringToMatch;
	}
	
	/**
	 * @param string $s the string to match
	 */
	function setStringToMatch($s) {
		$this->stringToMatch = $s;
	}

	/**
	 * @return integer a {@link LOGGER_FILTER_NEUTRAL} is there is no string match.
	 */
	function decide($event) {
		$msg = $event->getRenderedMessage();
		
		if($msg === null or $this->stringToMatch === null) {
			return LoggerFilter::NEUTRAL;
		}
		
		if(strpos($msg, $this->stringToMatch) !== false ) {
			return ($this->acceptOnMatch) ? LoggerFilter::ACCEPT : LoggerFilter::DENY;
		}
		return LoggerFilter::NEUTRAL;
	}
}
