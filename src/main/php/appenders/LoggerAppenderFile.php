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
 * LoggerAppenderFile appends log events to a file.
 *
 * This appender uses a layout.
 * 
 * ## Configurable parameters: ##
 * 
 * - **file** - Path to the target file. Relative paths are resolved based on 
 *     the working directory.
 * - **append** - If set to true, the appender will append to the file, 
 *     otherwise the file contents will be overwritten.
 *
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/file.html Appender documentation
 */
class LoggerAppenderFile extends LoggerAppender {

	/**
	 * @var boolean if {@link $file} exists, appends events.
	 */
	protected $append = true;
	
	/**
	 * @var string the file name used to append events
	 */
	protected $file;

	/**
	 * @var mixed file resource
	 */
	protected $fp = false;
	
	public function activateOptions() {
		$fileName = $this->getFile();

		if (empty($fileName)) {
			$this->warn("Required parameter 'fileName' not set. Closing appender.");
			$this->closed = true;
			return;
		}
		
		if(!is_file($fileName)) {
			$dir = dirname($fileName);
			if(!is_dir($dir)) {
				mkdir($dir, 0777, true);
			}
		}

		$this->fp = fopen($fileName, ($this->getAppend()? 'a':'w'));
		if($this->fp) {
			if(flock($this->fp, LOCK_EX)) {
				if($this->getAppend()) {
					fseek($this->fp, 0, SEEK_END);
				}
				fwrite($this->fp, $this->layout->getHeader());
				flock($this->fp, LOCK_UN);
				$this->closed = false;
			} else {
				// TODO: should we take some action in this case?
				$this->closed = true;
			}		 
		} else {
			$this->closed = true;
		}
	}
	
	public function close() {
		if($this->closed != true) {
			if($this->fp and $this->layout !== null) {
				if(flock($this->fp, LOCK_EX)) {
					fwrite($this->fp, $this->layout->getFooter());
					flock($this->fp, LOCK_UN);
				}
				fclose($this->fp);
			}
			$this->closed = true;
		}
	}

	public function append(LoggerLoggingEvent $event) {
		if($this->fp and $this->layout !== null) {
			if(flock($this->fp, LOCK_EX)) {
				fwrite($this->fp, $this->layout->format($event));
				flock($this->fp, LOCK_UN);
			} else {
				$this->closed = true;
			}
		} 
	}
	
	/**
	 * Sets the 'file' parameter.
	 * @param string $file
	 */
	public function setFile($file) {
		$this->setString('file', $file);
	}
	
	/**
	 * Returns the 'file' parameter.
	 * @return string
	 */
	public function getFile() {
		return $this->file;
	}
	
	/**
	 * Returns the 'append' parameter.
	 * @return boolean
	 */
	public function getAppend() {
		return $this->append;
	}

	/**
	 * Sets the 'append' parameter.
	 * @param boolean $append
	 */
	public function setAppend($append) {
		$this->setBoolean('append', $append);
	}

	/**
	 * Sets the 'file' parmeter. Left for legacy reasons.
	 * @param string $fileName
	 * @deprecated Use setFile() instead.
	 */
	public function setFileName($fileName) {
		$this->setFile($fileName);
	}
	
	/**
	 * Returns the 'file' parmeter. Left for legacy reasons.
	 * @return string
	 * @deprecated Use getFile() instead.
	 */
	public function getFileName() {
		return $this->getFile();
	}
}
