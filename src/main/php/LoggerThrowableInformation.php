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
 * The internal representation of throwables.
 *
 * @package log4php
 * @since 2.1
 */
class LoggerThrowableInformation {
	
	/** @var Exception Throwable to log */
	private $throwable;
	
	/** @var array Array of throwable messages */
	private $throwableArray;
	
	/** @var Logger reference */
	private $logger;
	
	/**
	 * Create a new instance
	 * 
	 * @param $throwable - a throwable either as array or as a exception
	 */
	public function __construct($throwable)  {
		if(is_array($throwable)) {
		    $this->throwableArray = $throwable;
		} else if($throwable instanceof Exception) {
		    $this->throwable = $throwable;
		} else {
		    throw new InvalidArgumentException();
		}
	}
	
	/**
	 * @desc Returns string representation of throwable
	 * 
	 * @return array 
	 */
	public function getStringRepresentation() {
		if (!is_array($this->throwableArray) && $this->throwable !== null) {
			$this->throwableArray = array();
			$ex	= $this->throwable;
			$this->throwableArray[] = $ex->getMessage();
			while (method_exists($ex, 'getPrevious')) {
				$ex	= $ex->getPrevious();
				if ($ex !== null && $ex instanceof Exception) {
					$this->throwableArray[] = $ex->getMessage();
				}
			}
		}
		
		return $this->throwableArray;
	}
}
?>