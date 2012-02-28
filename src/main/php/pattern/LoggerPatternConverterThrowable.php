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
 * Returns the throwable information linked to the logging event, if any.
 * 
 * Option: the maximum stack trace lines to return (returns all if not set)
 * 
 * @package log4php
 * @subpackage pattern
 */
class LoggerPatternConverterThrowable extends LoggerPatternConverter {
	
	private $depth;
	
	public function activateOptions() {
		if (isset($this->option) && is_numeric($op) && $op >= 0) {
			$this->depth = (integer) $this->option;
		}
	}
	
	public function convert(LoggerLoggingEvent $event) {
		
		$info = $event->getThrowableInformation();
		if ($info === null) {
			return '';
		}
		
		$ex = $info->getThrowable();
		
		// Format exception to string
		$strEx = get_class($ex) . ': "' . $ex->getMessage() . '"' . PHP_EOL;
		$strEx .= 'at '. $ex->getFile() . ':' . $ex->getLine();
		
		// Add trace if required
		if ($this->depth === null || $this->depth > 0) {
			$trace = $ex->getTrace();
			foreach($trace as $key => $item) {
				if (isset($this->depth) && $key > $this->depth) {
					break;
				}
				$strEx .= PHP_EOL . "#$key " . 
					"{$item['file']}:{$item['line']} " .
					"in {$item['class']}{$item['type']}{$item['function']}()"; 
			}
		}
		
		return $strEx;
	}
}
 