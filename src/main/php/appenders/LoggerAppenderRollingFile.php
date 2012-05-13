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
 * LoggerAppenderRollingFile writes logging events to a specified file. The 
 * file is rolled over after a specified size has been reached.
 * 
 * This appender uses a layout.
 *
 * ## Configurable parameters: ##
 * 
 * - **file** - Path to the target file.
 * - **append** - If set to true, the appender will append to the file, 
 *     otherwise the file contents will be overwritten.
 * - **maxBackupIndex** - Maximum number of backup files to keep. Default is 1.
 * - **maxFileSize** - Maximum allowed file size (in bytes) before rolling 
 *     over. Suffixes "KB", "MB" and "GB" are allowed. 10KB = 10240 bytes, etc.
 *     Default is 10M.
 * - **compress** - If set to true, rolled-over files will be compressed. 
 *     Requires the zlib extension.
 *
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/rolling-file.html Appender documentation
 */
class LoggerAppenderRollingFile extends LoggerAppenderFile {

	/**
	 * Set the maximum size that the output file is allowed to reach
	 * before being rolled over to backup files.
	 *
	 * <p>In configuration files, the <var>MaxFileSize</var> option takes a
	 * long integer in the range 0 - 2^63. You can specify the value
	 * with the suffixes "KB", "MB" or "GB" so that the integer is
	 * interpreted being expressed respectively in kilobytes, megabytes
	 * or gigabytes. For example, the value "10KB" will be interpreted
	 * as 10240.</p>
	 * <p>The default maximum file size is 10MB.</p>
	 *
	 * <p>Note that MaxFileSize cannot exceed <b>2 GB</b>.</p>
	 *
	 * @var integer
	 */
	protected $maxFileSize = 10485760;
	
	/**
	 * Set the maximum number of backup files to keep around.
	 * 
	 * <p>The <var>MaxBackupIndex</var> option determines how many backup
	 * files are kept before the oldest is erased. This option takes
	 * a positive integer value. If set to zero, then there will be no
	 * backup files and the log file will be truncated when it reaches
	 * MaxFileSize.</p>
	 * <p>There is one backup file by default.</p>
	 *
	 * @var integer 
	 */
	protected $maxBackupIndex = 1;
	
	/**
	 * @var string the filename expanded
	 */
	private $expandedFileName = null;

	/**
	 * The <var>compress</var> parameter determindes the compression with zlib. 
	 * If set to true, the rollover files are compressed and saved with the .gz extension.
	 * @var boolean
	 */
	protected $compress = false;
	
	/**
	 * Returns the value of the MaxBackupIndex option.
	 * @return integer 
	 */
	private function getExpandedFileName() {
		return $this->expandedFileName;
	}

	/**
	 * Get the maximum size that the output file is allowed to reach
	 * before being rolled over to backup files.
	 * @return integer
	 */
	public function getMaximumFileSize() {
		return $this->maxFileSize;
	}

	/**
	 * Implements the usual roll over behaviour.
	 *
	 * <p>If MaxBackupIndex is positive, then files File.1, ..., File.MaxBackupIndex -1 are renamed to File.2, ..., File.MaxBackupIndex. 
	 * Moreover, File is renamed File.1 and closed. A new File is created to receive further log output.
	 * 
	 * <p>If MaxBackupIndex is equal to zero, then the File is truncated with no backup files created.
	 * 
	 * Rollover must be called while the file is locked so that it is safe for concurrent access. 
	 */
	private function rollOver() {
		// If maxBackups <= 0, then there is no file renaming to be done.
		if($this->maxBackupIndex > 0) {
			$fileName = $this->getExpandedFileName();

			// Delete the oldest file, to keep Windows happy.
			$file = $fileName . '.' . $this->maxBackupIndex;
			if(is_writable($file)) {
				unlink($file);
			}

			// Map {(maxBackupIndex - 1), ..., 2, 1} to {maxBackupIndex, ..., 3, 2}
			$this->renameArchievedLogs($fileName);
	
			if (true === $this->compress) {
				file_put_contents('compress.zlib://'.$fileName.'.1.gz', file_get_contents($fileName));
			} else {
				// Backup the active file
				copy($fileName, "$fileName.1");				
			}
		}
		
		// Truncate the active file
		ftruncate($this->fp, 0);
		rewind($this->fp);
	}
	
	private function renameArchievedLogs($fileName) {
		for($i = $this->maxBackupIndex - 1; $i >= 1; $i--) {
			
			$file = $fileName . "." . $i;
			if (true === $this->compress) {
				$file = $fileName . "." . $i .'.gz';
			}
			
			if(is_readable($file)) {
				$target = $fileName . '.' . ($i + 1);
				if (true === $this->compress) {
					$target = $fileName . '.' . ($i + 1) . '.gz';
				}				
				
				rename($file, $target);
			}
		}		
	}
	
	public function setFile($fileName) {
		$this->file = $fileName;
		// As LoggerAppenderFile does not create the directory, it has to exist.
		// realpath() fails if the argument does not exist so the filename is separated.
		$this->expandedFileName = realpath(dirname($fileName));
		if ($this->expandedFileName === false) throw new Exception("Directory of $fileName does not exist!");
		$this->expandedFileName .= DIRECTORY_SEPARATOR . basename($fileName);
	}


	/**
	 * Set the maximum number of backup files to keep around.
	 * 
	 * <p>The <b>MaxBackupIndex</b> option determines how many backup
	 * files are kept before the oldest is erased. This option takes
	 * a positive integer value. If set to zero, then there will be no
	 * backup files and the log file will be truncated when it reaches
	 * MaxFileSize.
	 *
	 * @param mixed $maxBackups
	 */
	public function setMaxBackupIndex($maxBackups) {
		$this->setPositiveInteger('maxBackupIndex', $maxBackups);
	}


	public function append(LoggerLoggingEvent $event) {
		if($this->fp and $this->layout !== null) {
			if(flock($this->fp, LOCK_EX)) {
				fwrite($this->fp, $this->layout->format($event));

				// Stats cache must be cleared, otherwise filesize() returns cached results
				clearstatcache();
				
				// Rollover if needed
				if (filesize($this->expandedFileName) > $this->maxFileSize) {
					$this->rollOver();
				}
				
				flock($this->fp, LOCK_UN);
			} else {
				$this->closed = true;
			}
		} 
	}
	
	public function activateOptions() {
		parent::activateOptions();
		
		if ($this->compress == true && !extension_loaded('zlib')) {
			$this->warn('The zlib extension is required for file-compression');
			$this->closed = true;
		}
	}
	
	/**
	 * Returns the 'maxBackupIndex' parameter.
	 * @return integer
	 */
	public function getMaxBackupIndex() {
		return $this->maxBackupIndex;
	}
	
	/**
	 * Set the maximum size that the output file is allowed to reach
	 * before being rolled over to backup files.
	 * <p>In configuration files, the <b>maxFileSize</b> option takes an
	 * long integer in the range 0 - 2^63. You can specify the value
	 * with the suffixes "KB", "MB" or "GB" so that the integer is
	 * interpreted being expressed respectively in kilobytes, megabytes
	 * or gigabytes. For example, the value "10KB" will be interpreted
	 * as 10240.
	 *
	 * @param mixed $value
	 * @return the actual file size set
	 */
	public function setMaxFileSize($value) {
		$this->setFileSize('maxFileSize', $value);
	}
	
	/**
	 * Set the maximum size that the output file is allowed to reach
	 * before being rolled over to backup files.
	 *
	 * @param mixed $maxFileSize
	 * @see setMaxFileSize()
	 * @deprecated
	 */
	public function setMaximumFileSize($maxFileSize) {
		return $this->setMaxFileSize($maxFileSize);
	}
	
	/**
	 * Returns the 'maxFileSize' parameter.
	 * @return integer
	 */
	public function getMaxFileSize() {
		return $this->maxFileSize;
	}
	
	public function setCompress($compress) {
		$this->setBoolean('compress', $compress);
	}
}
