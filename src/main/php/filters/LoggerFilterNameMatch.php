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
 * This is a very simple filter based on string matching.
 *
 * <p>The filter admits four options: {@link $stringToMatch}, {@link $caseSensitive},
 * {@link $exactMatch}, and
 * {@link $acceptOnMatch}. If the value of the {@link $stringToMatch} option is included
 * in name of the {@link LoggerLoggingEvent}, then the {@link decide()} method returns
 * {@link LoggerFilter::ACCEPT} if the <b>AcceptOnMatch</b> option value is true and
 * {@link LoggerFilter::DENY} if it is false. If there is no match, {@link LoggerFilter::NEUTRAL}
 * is returned. Matching is case-sensitive by default. Setting {@link $caseSensitive}
 * to false makes matching case insensitive. An exact match can be required by setting
 * {@link $exactMatch} to true.</p>
 *
 * <p>
 * An example for this filter:
 *
 * {@example ../../examples/php/filter_namematch.php 19}
 *
 * <p>
 * The corresponding XML file:
 *
 * {@example ../../examples/resources/filter_namematch.xml 18}
 *
 * @package log4php
 * @subpackage filters
 * @since 2.3.1
 */
class LoggerFilterNameMatch extends LoggerFilter {

	/**
	 * @var boolean
	 */
	protected $acceptOnMatch = true;

	/**
	 * @var boolean
	 */
	protected $caseSensitive = true;

	/**
	 * @var boolean
	 */
	protected $exactMatch = false;

	/**
	 * @var string
	 */
	protected $stringToMatch;

	/**
	 * @param mixed $acceptOnMatch a boolean or a string ('true' or 'false')
	 */
	public function setAcceptOnMatch($acceptOnMatch) {
		$this->setBoolean('acceptOnMatch', $acceptOnMatch);
	}

	/**
	 * @param mixed $caseSensitive a boolean or a string ('true' or 'false')
	 */
	public function setCaseSensitive($caseSensitive) {
		$this->setBoolean('caseSensitive', $caseSensitive);
	}

	/**
	 * @param mixed $caseSensitive a boolean or a string ('true' or 'false')
	 */
	public function setExactMatch($exactMatch) {
		$this->setBoolean('exactMatch', $exactMatch);
	}

	/**
	 * @param string $s the string to match
	 */
	public function setStringToMatch($string) {
		$this->setString('stringToMatch', $string);
	}

	/**
	 * @return integer a {@link LOGGER_FILTER_NEUTRAL} is there is no string match.
	 */
	public function decide(LoggerLoggingEvent $event) {
		$msg = $event->getLoggerName();

		if($msg === null or $this->stringToMatch === null) {
			return LoggerFilter::NEUTRAL;
		}

		if($this->caseSensitive) {
			return $this->testString((function_exists('mb_strpos') ? 'mb_strpos' : 'strpos'), $msg);
		} else {
			return $this->testString((function_exists('mb_stripos') ? 'mb_stripos' : 'stripos'), $msg);
		}
	}


	protected function testString($method, $msg) {
		if($method($msg, $this->stringToMatch) !== false) {
			if($this->exactMatch) {
				$lenFunc = function_exists('mb_strlen') ? 'mb_strlen' : 'strlen';
				if($lenFunc($this->stringToMatch) === $lenFunc($msg)) {
					// We were looking for an exact match, and we found one.
					return ($this->acceptOnMatch) ? LoggerFilter::ACCEPT : LoggerFilter::DENY;
				}
			} else { // No exact match required.
				return ($this->acceptOnMatch) ? LoggerFilter::ACCEPT : LoggerFilter::DENY;
			}
		}
		return LoggerFilter::NEUTRAL;
	}
}
