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
 */

/**
 * TTCC layout format consists of:
 * - **t**ime,
 * - **t**hread, 
 * - **c**ategory and
 * - nested diagnostic **c**ontext information
 * 
 * Each of the four fields can be individually enabled or disabled. The time 
 * format depends on the **dateFormat** used. If no dateFormat is specified 
 * it defaults to '%c'. See php {@link PHP_MANUAL#date} function for details.
 *
 * ## Configurable parameters: ##
 * 
 * - **$threadPrinting** (boolean) enable/disable pid reporting.
 * - **$categoryPrefixing** (boolean) enable/disable logger category reporting.
 * - **$contextPrinting** (boolean) enable/disable NDC reporting.
 * - **$microSecondsPrinting** (boolean) enable/disable micro seconds reporting 
 *   in timestamp.
 * - **$dateFormat** (string) sets the date format.
 *
 * @package log4php
 * @subpackage layouts
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/layouts/ttcc.html Layout documentation
 * @deprecated LoggerLayout TTCC is deprecated and will be removed in a future release. Please use 
 *   LoggerLayoutPattern instead. 
 */
class LoggerLayoutTTCC extends LoggerLayout {
	 
	/** Whether to output the process ID. */
	protected $threadPrinting = true;
	
	/** Whether to output the category name. */
	protected $categoryPrefixing = true;
	
	/** Whether to output the nested diagnostic context. */
	protected $contextPrinting   = true;
	
	/** Whether to include microseconds in the timestmap. */
	protected $microSecondsPrinting = true;
	
	/** The date format */
	protected $dateFormat = '%c';

	public function __construct($dateFormat = null) {
		$this->warn("LoggerLayout TTCC is deprecated and will be removed in a future release. Please use LoggerLayoutPattern instead.");
		if (isset($dateFormat)) {
			$this->dateFormat = $dateFormat;
		}
	}

	/**
	 * Sets the 'threadPrinting' parameter.
	 * @param boolean $threadPrinting
	 */
	public function setThreadPrinting($threadPrinting) {
		$this->setBoolean('threadPrinting', $threadPrinting);
	}

	/**
	 * Returns the value of the 'threadPrinting' parameter.
	 * @return boolean
	 */
	public function getThreadPrinting() {
		return $this->threadPrinting;
	}

	/**
	 * Sets the 'categoryPrefixing' parameter.
	 * @param boolean $categoryPrefixing
	 */
	public function setCategoryPrefixing($categoryPrefixing) {
		$this->setBoolean('categoryPrefixing', $categoryPrefixing);
	}

	/**
	 * Returns the value of the 'categoryPrefixing' parameter.
	 * @return boolean
	 */
	public function getCategoryPrefixing() {
		return $this->categoryPrefixing;
	}

	/**
	 * Sets the 'contextPrinting' parameter.
	 * @param boolean $contextPrinting
	 */
	public function setContextPrinting($contextPrinting) {
		$this->setBoolean('contextPrinting', $contextPrinting);
	}

	/**
	 * Returns the value of the 'contextPrinting' parameter.
	 * @return boolean
	 */
	public function getContextPrinting() {
		return $this->contextPrinting;
	}
	
	/**
	 * Sets the 'microSecondsPrinting' parameter.
	 * @param boolean $microSecondsPrinting
	 */
	public function setMicroSecondsPrinting($microSecondsPrinting) {
		$this->setBoolean('microSecondsPrinting', $microSecondsPrinting);
	}

	/**
	 * Returns the value of the 'microSecondsPrinting' parameter.
	 * @return boolean
	 */
	public function getMicroSecondsPrinting() {
		return $this->microSecondsPrinting;
	}
	
	/**
	 * Sets the 'dateFormat' parameter.
	 * @param string $dateFormat
	 */
	public function setDateFormat($dateFormat) {
		$this->setString('dateFormat', $dateFormat);
	}
	
	/**
	 * Returns the value of the 'dateFormat' parameter.
	 * @return string
	 */
	public function getDateFormat() {
		return $this->dateFormat;
	}

	public function format(LoggerLoggingEvent $event) {
		$timeStamp = (float)$event->getTimeStamp();
		$format = strftime($this->dateFormat, (int)$timeStamp);
		
		if ($this->microSecondsPrinting) {
			$usecs = floor(($timeStamp - (int)$timeStamp) * 1000);
			$format .= sprintf(',%03d', $usecs);
		}
			
		$format .= ' ';
		
		if ($this->threadPrinting) {
			$format .= '['.getmypid().'] ';
		}
		
		$level = $event->getLevel();
		$format .= $level.' ';
		
		if($this->categoryPrefixing) {
			$format .= $event->getLoggerName().' ';
		}
	   
		if($this->contextPrinting) {
			$ndc = $event->getNDC();
			if($ndc != null) {
				$format .= $ndc.' ';
			}
		}
		
		$format .= '- '.$event->getRenderedMessage();
		$format .= PHP_EOL;
		
		return $format;
	}
}
