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
 *
 * @package log4php
 */
 
/**
 * Appender for writing to MongoDB.
 * 
 * This class was originally contributed by Vladimir Gorej.
 * 
 * @link http://github.com/log4mongo/log4mongo-php Vladimir Gorej's original submission.
 * @link http://www.mongodb.org/ MongoDB website.
 * 
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 * @since 2.1
 */
class LoggerAppenderMongoDB extends LoggerAppender {
		
	const DEFAULT_MONGO_URL_PREFIX = 'mongodb://';
	const DEFAULT_MONGO_HOST = 'localhost';
	const DEFAULT_MONGO_PORT = 27017;
	const DEFAULT_DB_NAME = 'log4php_mongodb';
	const DEFAULT_COLLECTION_NAME = 'logs';
	
	protected $hostname;
	protected $port;
	protected $dbName;
	protected $collectionName;
	
	protected $connection;
	protected $collection;
		
	protected $userName;
	protected $password;
	
	protected $canAppend = false;
	
	protected $requiresLayout = false;
		
	public function __construct($name = '') {
		parent::__construct($name);
		$this->hostname         = self::DEFAULT_MONGO_URL_PREFIX.self::DEFAULT_MONGO_HOST;
		$this->port             = self::DEFAULT_MONGO_PORT;
		$this->dbName           = self::DEFAULT_DB_NAME;
		$this->collectionName   = self::DEFAULT_COLLECTION_NAME;
	}
	
	/**
	 * Setup db connection.
	 * Based on defined options, this method connects to db defined in {@link $dbNmae}
	 * and creates a {@link $collection} 
	 * @return boolean true if all ok.
	 * @throws an Exception if the attempt to connect to the requested database fails.
	 */
	public function activateOptions() {
		try {
			$this->connection = new Mongo(sprintf('%s:%d', $this->hostname, $this->port));
			$db	= $this->connection->selectDB($this->dbName);
			if ($this->userName !== null && $this->password !== null) {
				$authResult = $db->authenticate($this->userName, $this->password);
				if ($authResult['ok'] == floatval(0)) {
					throw new Exception($authResult['errmsg'], $authResult['ok']);
				}
			}
			
			$this->collection = $db->selectCollection($this->collectionName);												 
		} catch (Exception $ex) {
			$this->canAppend = false;
			throw new LoggerException($ex);
		} 
			
		$this->canAppend = true;
		return true;
	}		 
	
	/**
	 * Appends a new event to the mongo database.
	 * 
	 * @throws LoggerException	If the pattern conversion or the INSERT statement fails.
	 */
	public function append(LoggerLoggingEvent $event) {
		if ($this->canAppend == true && $this->collection != null) {
			$document = $this->format($event);
			$this->collection->insert($document);			
		}
	}
	
	/**
	 * Converts the logging event into an array which can be logged to mongodb.
	 * 
	 * @param LoggerLoggingEvent $event
	 * @return array
	 */
	protected function format(LoggerLoggingEvent $event) {
		$timestampSec  = (int) $event->getTimestamp();
		$timestampUsec = (int) (($event->getTimestamp() - $timestampSec) * 1000000);

		$document = array(
			'timestamp'  => new MongoDate($timestampSec, $timestampUsec),
			'level'      => $event->getLevel()->toString(),
			'thread'     => (int) $event->getThreadName(),
			'message'    => $event->getMessage(),
			'loggerName' => $event->getLoggerName() 
		);	

		$locationInfo = $event->getLocationInformation();
		if ($locationInfo != null) {
			$document['fileName']   = $locationInfo->getFileName();
			$document['method']     = $locationInfo->getMethodName();
			$document['lineNumber'] = ($locationInfo->getLineNumber() == 'NA') ? 'NA' : (int) $locationInfo->getLineNumber();
			$document['className']  = $locationInfo->getClassName();
		}	

		$throwableInfo = $event->getThrowableInformation();
		if ($throwableInfo != null) {
			$document['exception'] = $this->formatThrowable($throwableInfo->getThrowable());
		}
		
		return $document;
	}
	
	/**
	 * Converts an Exception into an array which can be logged to mongodb.
	 * 
	 * Supports innner exceptions (PHP >= 5.3)
	 * 
	 * @param Exception $ex
	 * @return array
	 */
	protected function formatThrowable(Exception $ex) {
		$array = array(				
			'message'    => $ex->getMessage(),
			'code'       => $ex->getCode(),
			'stackTrace' => $ex->getTraceAsString(),
		);
                        
		if (method_exists($ex, 'getPrevious') && $ex->getPrevious() !== null) {
			$array['innerException'] = $this->formatThrowable($ex->getPrevious());
		}
			
		return $array;
	}
		
	/**
	 * Closes the connection to the logging database
	 */
	public function close() {
		if($this->closed != true) {
			$this->collection = null;
			if ($this->connection !== null) {
				$this->connection->close();
				$this->connection = null;	
			}					
			$this->closed = true;
		}
	}
		
	public function __destruct() {
		$this->close();
	}
	
	public function setHost($hostname) {
		if (!preg_match('/^mongodb\:\/\//', $hostname)) {
			$hostname = self::DEFAULT_MONGO_URL_PREFIX.$hostname;
		}			
		$this->hostname = $hostname;				
	}
		
	public function getHost() {
		return $this->hostname;
	}
		
	public function setPort($port) {
		$this->port = $port;
	}
		
	public function getPort() {
		return $this->port;
	}
		
	public function setDatabaseName($dbName) {
		$this->dbName = $dbName;
	}
		
	public function getDatabaseName() {
		return $this->dbName;
	}
		
	public function setCollectionName($collectionName) {
		$this->collectionName = $collectionName;
	}
		
	public function getCollectionName() {
		return $this->collectionName;
	}
		
	public function setUserName($userName) {
		$this->userName = $userName;
	}
		
	public function getUserName() {
		return $this->userName;
	}
		
	public function setPassword($password) {
		$this->password = $password;
	}
		
	public function getPassword() {
		return $this->password;
	}
	
	public function getConnection() {
		return $this->connection;
	}
	
	public function getCollection() {
		return $this->collection;
	}
}
?>