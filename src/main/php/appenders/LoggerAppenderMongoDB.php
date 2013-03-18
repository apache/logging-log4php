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
 * Appender for writing to MongoDB.
 * 
 * This class was originally contributed by Vladimir Gorej.
 * 
 * ## Configurable parameters: ##
 *
 * - **connectionString ** - Connection string, if used host and port properties are ignored. Allows defining multiple hosts.
 * - **host** - Server on which mongodb instance is located. 
 * - **port** - Port on which the instance is bound.
 * - **databaseName** - Name of the database to which to log.
 * - **collectionName** - Name of the target collection within the given database.
 * - **username** - Username used to connect to the database.
 * - **password** - Password used to connect to the database.
 * - **timeout** - DEPRECATED; For how long the driver should try to connect to the database (in milliseconds).
 * - **connectionTimeout** - How long a connection can take to be opened before timing out.
 * - **socketTimeout** - How long a send or receive on a socket can take before timing out.
 * - **capped** - Whether the collection should be a fixed size.
 * - **cappedMax** - If the collection is fixed size, the maximum number of elements to store in the collection.
 * - **cappedSize** - If the collection is fixed size, its size in bytes.
 * - **writeConcern** - Controls how many nodes must acknowledge the write instruction before the driver continues.
 * - **writeConcernJournaled** - The write will be acknowledged by primary and the journal flushed to disk.
 * - **writeConcernTimeout** -  Controls how many milliseconds the server waits for the write concern to be satisfied.
 * - **replicaSet** - The name of the replica set to connect to. Primary will be automatically determined.
 *
 * @package log4php
 * @subpackage appenders
 * @since 2.1
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/mongodb.html Appender documentation
 * @link http://github.com/log4mongo/log4mongo-php Vladimir Gorej's original submission.
 * @link http://www.mongodb.org/ MongoDB website.
 */
class LoggerAppenderMongoDB extends LoggerAppender {

	// ******************************************
	// ** Constants                            **
	// ******************************************

	/** Default prefix for the {@link $host}. */	
	const DEFAULT_MONGO_URL_PREFIX = 'mongodb://';

	/** Default value for {@link $host}, without a prefix. */
	const DEFAULT_MONGO_HOST = 'localhost';

	/** Default value for {@link $port} */
	const DEFAULT_MONGO_PORT = 27017;

	/** Default value for {@link $databaseName}. */
	const DEFAULT_DB_NAME = 'log4php_mongodb';

	/** Default value for {@link $collectionName}. */
	const DEFAULT_COLLECTION_NAME = 'logs';

	/** Default value for {@link $timeout}
	 * @deprecated
	 */
	const DEFAULT_TIMEOUT_VALUE = 3000;

	/** Default value for {@link $capped}. */
	const DEFAULT_CAPPED = false;

	/** Default value for {@link $cappedMax}. */
	const DEFAULT_CAPPED_MAX = 1000;

	/** Default value for {@link $cappedSize}. */
	const DEFAULT_CAPPED_SIZE = 1000000;

	/** Single/Primary server acknowledgement of write operation. {@link $writeConcern}. */
	const WC_ACKNOWLEDGED = 1;

	/** Default value for {@link $writeConcernTimeout}. */
	const WC_TIMEOUT = 3000;

	/**
	 * Default value for {@link $writeConcernJournaled}.
	 */
	const WC_JOURNALED = false;


	// ******************************************
	// ** Configurable parameters              **
	// ******************************************

	/**
	 * Support for multiple hosts and additional connection options.
	 * @var string
	 */
	protected $connectionString;

	/** Server on which mongodb instance is located.
	 * @var string
	 */
	protected $host;

	/** Port on which the instance is bound.
	 * @var int
	 */
	protected $port;

	/** Name of the database to which to log.
	 * @var string
	 */
	protected $databaseName;

	/** Name of the collection within the given database.
	 * @var string
	 */
	protected $collectionName;

	/** Username used to connect to the database.
	 * @var string
	 */
	protected $userName;

	/** Password used to connect to the database.
	 * @var string
	 */
	protected $password;

	/** Timeout value used when connecting to the database (in milliseconds).
	 * @var int
	 */
	protected $timeout;

	/** Whether the collection should be a fixed size.
	 * @var bool
	 */
	protected $capped;

	/** If the collection is fixed size, the maximum number of elements to store in the collection.
	 * @var int
	 */
	protected $cappedMax;

	/** If the collection is fixed size, its size in bytes.
	 * @var int
	 */
	protected $cappedSize;

	/**
	 * How long the driver blocks when writing
	 * @var string
	 */
	protected $writeConcern;

	/**
	 * The write will be acknowledged by primary and the journal flushed to disk.
	 * @var bool
	 */
	protected $writeConcernJournaled;

	/**
	 * Write concern timeout in milliseconds.
	 * @var int
	 */
	protected $writeConcernTimeout;

	/**
	 * Connection timeout in milliseconds.
	 * @var int
	 */
	protected $connectionTimeout;

	/**
	 * Socket timeout in milliseconds.
	 * @var int
	 */
	protected $socketTimeout;

	/**
	 * Name of the replicaSet.
	 * @var string
	 */
	protected $replicaSet;


	// ******************************************
	// ** Member variables                     **
	// ******************************************

	/** 
	 * Connection to the MongoDB instance.
	 * @var Mongo
	 */
	protected $connection;

	/** 
	 * The collection to which log is written. 
	 * @var MongoCollection
	 */
	protected $collection;

	/**
	 * Write options.
	 * @var array
	 */
	protected $writeOptions;

	public function __construct($name = '') {
		parent::__construct($name);
		$this->host = self::DEFAULT_MONGO_HOST;
		$this->port = self::DEFAULT_MONGO_PORT;
		$this->databaseName = self::DEFAULT_DB_NAME;
		$this->collectionName = self::DEFAULT_COLLECTION_NAME;
		$this->timeout = self::DEFAULT_TIMEOUT_VALUE;
		$this->requiresLayout = false;
		$this->capped = self::DEFAULT_CAPPED;
		$this->cappedMax = self::DEFAULT_CAPPED_MAX;
		$this->cappedSize = self::DEFAULT_CAPPED_SIZE;
		$this->writeConcern = self::WC_ACKNOWLEDGED;
		$this->writeConcernJournaled = self::WC_JOURNALED;
		$this->writeConcernTimeout = self::WC_TIMEOUT;
	}

	/**
	 * Setup db connection.
	 * Based on defined options, this method connects to the database and 
	 * creates a {@link $collection}. 
	 */
	public function activateOptions() {
		# Building connection options.
		$options = array(
			'w' => (is_numeric($this->writeConcern)) ? (int) $this->writeConcern : $this->writeConcern,
			'timeout' => $this->timeout,
			'wTimeout' => $this->writeConcernTimeout
		);
		if ($this->replicaSet !== null) {
			$options['replicaSet'] = $this->replicaSet;
		}
		if ($this->socketTimeout !== null) {
			$options['socketTimeoutMS'] = $this->socketTimeout;
		}
		# Backwards compatibility with timeout parameter.
		if ($this->connectionTimeout !== null) {
			$options['connectTimeoutMS'] = $options['timeout'] = $this->connectionTimeout;
		}

		# Building write options.
		$this->writeOptions = array(
			'w' => (is_numeric($this->writeConcern)) ? (int) $this->writeConcern : $this->writeConcern,
			'j' => $this->writeConcernJournaled
		);

		try {
			$clientClass = class_exists('MongoClient') ? 'MongoClient' : 'Mongo';
			# Connection string generation.
			if ($this->connectionString === null) {
				$connectionString = sprintf('%s%s:%d', self::DEFAULT_MONGO_URL_PREFIX,
							    preg_replace('/^'.preg_quote(self::DEFAULT_MONGO_URL_PREFIX, '/').'/', '', $this->host),
							    $this->port);
			} else {
				$connectionString = $this->connectionString;
			}
			$this->connection = new $clientClass($connectionString, $options);
			$db	= $this->connection->selectDB($this->databaseName);
			if ($this->userName !== null && $this->password !== null) {
				$authResult = $db->authenticate($this->userName, $this->password);
				if ($authResult['ok'] == floatval(0)) {
					throw new Exception($authResult['errmsg'], $authResult['ok']);
				}
			}
			if ($this->capped === true) {
				$this->collection = $db->createCollection($this->collectionName, $this->capped, $this->cappedSize,
														  $this->cappedMax);
			} else {
				$this->collection = $db->selectCollection($this->collectionName);
			}
		} catch (MongoConnectionException $ex) {
			$this->closed = true;
			$this->warn(sprintf('Failed to connect to mongo daemon: %s', $ex->getMessage()));
		} catch (InvalidArgumentException $ex) {
			$this->closed = true;
			$this->warn(sprintf('Error while selecting mongo database: %s', $ex->getMessage()));
		} catch (Exception $ex) {
			$this->closed = true;
			$this->warn('Invalid credentials for mongo database authentication');
		}
	}

	/**
	 * Appends a new event to the mongo database.
	 *
	 * @param LoggerLoggingEvent $event
	 */
	public function append(LoggerLoggingEvent $event) {
		try {
			if ($this->collection != null) {
				$this->collection->insert($this->format($event), $this->writeOptions);
			}
		} catch (MongoCursorException $ex) {
			$this->warn(sprintf('Error while writing to mongo collection: %s', $ex->getMessage()));
		}
	}

	/**
	 * Converts the logging event into an array which can be logged to mongodb.
	 * 
	 * @param LoggerLoggingEvent $event
	 * @return array The array representation of the logging event.
	 */
	protected function format(LoggerLoggingEvent $event) {
		$timestampSec = (int) $event->getTimestamp();
		$timestampUsec = (int) (($event->getTimestamp() - $timestampSec) * 1000000);

		$document = array(
			'timestamp' => new MongoDate($timestampSec, $timestampUsec),
			'level' => $event->getLevel()->toString(),
			'thread' => (int) $event->getThreadName(),
			'message' => $event->getMessage(),
			'loggerName' => $event->getLoggerName() 
		);	

		$locationInfo = $event->getLocationInformation();
		if ($locationInfo != null) {
			$document['fileName'] = $locationInfo->getFileName();
			$document['method'] = $locationInfo->getMethodName();
			$document['lineNumber'] = ($locationInfo->getLineNumber() == 'NA') ? 'NA' : (int) $locationInfo->getLineNumber();
			$document['className'] = $locationInfo->getClassName();
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
	 * Supports inner exceptions (PHP >= 5.3)
	 * 
	 * @param Exception $ex
	 * @return array
	 */
	protected function formatThrowable(Exception $ex) {
		$array = array(				
			'message' => $ex->getMessage(),
			'code' => $ex->getCode(),
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

	/**
	 * Sets the value of {@link $connectionString}.
	 * @param string $connectionString
	 */
	public function setConnectionString($connectionString) {
		$this->setString('connectionString', $connectionString);
	}

	/**
	 * Returns the value of {@link $connectionString}.
	 * @return string
	 */
	public function getConnectionString() {
		return $this->connectionString;
	}

	/**
	 * Sets the value of {@link $host} parameter.
	 * @param string $host
	 */
	public function setHost($host) {
		$this->setString('host', $host);
	}

	/** 
	 * Returns the value of {@link $host} parameter.
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}

	/** 
	 * Sets the value of {@link $port} parameter.
	 * @param int $port
	 */
	public function setPort($port) {
		$this->setPositiveInteger('port', $port);
	}

	/** 
	 * Returns the value of {@link $port} parameter.
	 * @return int
	 */
	public function getPort() {
		return $this->port;
	}

	/** 
	 * Sets the value of {@link $databaseName} parameter.
	 * @param string $databaseName
	 */
	public function setDatabaseName($databaseName) {
		$this->setString('databaseName', $databaseName);
	}

	/** 
	 * Returns the value of {@link $databaseName} parameter.
	 * @return string
	 */
	public function getDatabaseName() {
		return $this->databaseName;
	}

	/** 
	 * Sets the value of {@link $collectionName} parameter.
	 * @param string $collectionName
	 */
	public function setCollectionName($collectionName) {
		$this->setString('collectionName', $collectionName);
	}

	/** 
	 * Returns the value of {@link $collectionName} parameter.
	 * @return string
	 */
	public function getCollectionName() {
		return $this->collectionName;
	}

	/** 
	 * Sets the value of {@link $userName} parameter.
	 * @param string $userName
	 */
	public function setUserName($userName) {
		$this->setString('userName', $userName, true);
	}

	/** 
	 * Returns the value of {@link $userName} parameter.
	 * @return string
	 */
	public function getUserName() {
		return $this->userName;
	}

	/** 
	 * Sets the value of {@link $password} parameter.
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->setString('password', $password, true);
	}

	/** 
	 * Returns the value of {@link $password} parameter.
	 * @return string 
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Sets the value of {@link $timeout} parameter.
	 * @deprecated Use {@link $connectionTimeout} and {@link $socketTimeout} instead.
	 * @param int $timeout
	 */
	public function setTimeout($timeout) {
		$this->setPositiveInteger('timeout', $timeout);
	}

	/** 
	 * Returns the value of {@link $timeout} parameter.
	 * @deprecated Use {@link $connectionTimeout} and {@link $socketTimeout} instead.
	 * @return int
	 */
	public function getTimeout() {
		return $this->timeout;
	}

	/**
	 * Sets the value of {@link $capped} parameter.
	 * @param bool $capped
	 */
	public function setCapped($capped) {
		$this->setBoolean('capped', $capped);
	}

	/**
	 * Returns the value of {@link $capped} parameter.
	 * @return bool
	 */
	public function getCapped() {
		return $this->capped;
	}

	/**
	 * Sets the value of {@link $cappedMax} parameter.
	 * @param int $cappedMax
	 */
	public function setCappedMax($cappedMax) {
		$this->setPositiveInteger('cappedMax', $cappedMax);
	}

	/**
	 * Returns the value of {@link $cappedMax} parameter.
	 * @return int
	 */
	public function getCappedMax() {
		return $this->cappedMax;
	}

	/**
	 * Sets the value of {@link $cappedSize} parameter.
	 * @param int $cappedSize
	 */
	public function setCappedSize($cappedSize) {
		$this->setPositiveInteger('cappedSize', $cappedSize);
	}

	/**
	 * Returns the value of {@link $cappedSzie} parameter.
	 * @return int
	 */
	public function getCappedSize() {
		return $this->cappedSize;
	}

	/**
	 * Sets the value of {@link $writeConcern} parameter.
	 * @param string $writeConcern
	 */
	public function setWriteConcern($writeConcern) {
		$this->setString('writeConcern', $writeConcern);
	}

	/**
	 * Returns the value of {@link $writeConcern} parameter.
	 * @return string
	 */
	public function getWriteConcern() {
		return $this->writeConcern;
	}

	/**
	 * Sets the value of {@link $writeConcernJournaled} parameter.
	 * @param bool $writeConcernJournaled
	 */
	public function setWriteConcernJournaled($writeConcernJournaled) {
		$this->setBoolean('writeConcernJournaled', $writeConcernJournaled);
	}

	/**
	 * Returns the value of {@link $writeConcernJournaled} parameter.
	 * @return bool
	 */
	public function getWriteConcernJournaled() {
		return $this->writeConcernJournaled;
	}

	/**
	 * Sets the value of {@link $writeConcernTimeout} parameter.
	 * @param int $writeConcernTimeout
	 */
	public function setWriteConcernTimeout($writeConcernTimeout) {
		$this->setPositiveInteger('writeConcernTimeout', $writeConcernTimeout);
	}

	/**
	 * Returns the value of {@link $writeConcernTimeout} parameter.
	 * @return int
	 */
	public function getWriteConcernTimeout() {
		return $this->writeConcernTimeout;
	}

	/**
	 * Sets the value of {@link $connectionTimeout} parameter.
	 * @param int $connectionTimeout
	 */
	public function setConnectionTimeout($connectionTimeout) {
		$this->setPositiveInteger('connectionTimeout', $connectionTimeout);
	}

	/**
	 * Returns the value of {@link $connectionTimeout} parameter.
	 * @return int
	 */
	public function getConnectionTimeout() {
		return $this->connectionTimeout;
	}

	/**
	 * Sets the value of {@link $socketTimeout} parameter.
	 * @param int $socketTimeout
	 */
	public function setSocketTimeout($socketTimeout) {
		$this->setPositiveInteger('socketTimeout', $socketTimeout);
	}

	/**
	 * Returns the value of {@link $socketTimeout} parameter.
	 * @return int
	 */
	public function getSocketTimeout() {
		return $this->socketTimeout;
	}

	/**
	 * Sets the value of {@link $replicaSet} parameter.
	 * @param string $replicaSet
	 */
	public function setReplicaSet($replicaSet) {
		$this->setString('replicaSet', $replicaSet);
	}

	/**
	 * Returns the value of {@link $replicaSet} parameter.
	 * @return string
	 */
	public function getReplicaSet() {
		return $this->replicaSet;
	}

	/**
	 * Returns the mongodb connection.
	 * @return Mongo
	 */
	public function getConnection() {
		return $this->connection;
	}

	/** 
	 * Returns the active mongodb collection.
	 * @return MongoCollection
	 */
	public function getCollection() {
		return $this->collection;
	}
}