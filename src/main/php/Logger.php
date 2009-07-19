<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * 
 *		http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 */

/**
 * This is the central class in the log4j package. Most logging operations, 
 * except configuration, are done through this class. 
 *
 * In log4j this class replaces the Category class. There is no need to 
 * port deprecated classes; log4php Logger class doesn't extend Category.
 *
 * @category   log4php
 * @package log4php
 * @license	   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version	   SVN: $Id$
 * @link	   http://logging.apache.org/log4php
 */
 /*
  * TODO:
  * Localization: setResourceBundle($bundle) : not supported
  * Localization: getResourceBundle: not supported
  * Localization: getResourceBundleString($key): not supported
  * Localization: l7dlog($priority, $key, $params, $t) : not supported
  */
class Logger {
	/**
	 * Additivity is set to true by default, that is children inherit the 
	 * appenders of their ancestors by default.
	 * @var boolean
	 */
	private $additive = true;
	
	/** @var string fully qualified class name */
	private $fqcn = 'Logger';

	/** @var LoggerLevel The assigned level of this category. */
	private $level = null;
	
	/** @var string name of this category. */
	private $name = '';
	
	/** @var Logger The parent of this category. Null if this is the root logger*/
	private $parent = null;

	/** @var LoggerHierarchy the object repository */
	private $repository = null; 

	/**
	 * @var array collection of appenders
	 * @see LoggerAppender
	 */
	private $aai = array();

	/**
	 * Constructor.
	 * @param  string  $name  Category name	  
	 */
	public function __construct($name) {
		$this->name = $name;
	}
	
	/**
	 * Add a new Appender to the list of appenders of this Category instance.
	 *
	 * @param LoggerAppender $newAppender
	 */
	public function addAppender($newAppender) {
		$appenderName = $newAppender->getName();
		$this->aai[$appenderName] = $newAppender;
	} 
			
	/**
	 * If assertion parameter is false, then logs msg as an error statement.
	 *
	 * @param bool $assertion
	 * @param string $msg message to log
	 */
	public function assertLog($assertion = true, $msg = '') {
		if($assertion == false) {
			$this->error($msg);
		}
	} 

	/**
	 * Call the appenders in the hierarchy starting at this.
	 *
	 * @param LoggerLoggingEvent $event 
	 */
	public function callAppenders($event) {
		if(count($this->aai) > 0) {
			foreach(array_keys($this->aai) as $appenderName) {
				$this->aai[$appenderName]->doAppend($event);
			}
		}
		if($this->parent != null and $this->getAdditivity()) {
			$this->parent->callAppenders($event);
		}
	}
	
	/**
	 * Log a message object with the DEBUG level including the caller.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function debug($message, $caller = null) {
		$this->logLevel($message, LoggerLevel::getLevelDebug(), $caller);
	} 


	/**
	 * Log a message object with the INFO Level.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function info($message, $caller = null) {
		$this->logLevel($message, LoggerLevel::getLevelInfo(), $caller);
	}

	/**
	 * Log a message with the WARN level.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function warn($message, $caller = null) {
		$this->logLevel($message, LoggerLevel::getLevelWarn(), $caller);
	}
	
	/**
	 * Log a message object with the ERROR level including the caller.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function error($message, $caller = null) {
		$this->logLevel($message, LoggerLevel::getLevelError(), $caller);
	}
	
	/**
	 * Log a message object with the FATAL level including the caller.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function fatal($message, $caller = null) {
		$this->logLevel($message, LoggerLevel::getLevelFatal(), $caller);
	}
	
	private function logLevel($message, $level, $caller = null) {
	    if($this->repository->isDisabled($level)) {
			return;
		}
		if($level->isGreaterOrEqual($this->getEffectiveLevel())) {
			$this->forcedLog($this->fqcn, $caller, $level, $message);
		}
	}
	
	/**
	 * This method creates a new logging event and logs the event without further checks.
	 *
	 * It should not be called directly. Use {@link info()}, {@link debug()}, {@link warn()},
	 * {@link error()} and {@link fatal()} wrappers.
	 *
	 * @param string $fqcn Fully Qualified Class Name of the Logger
	 * @param mixed $caller caller object or caller string id
	 * @param LoggerLevel $level log level	   
	 * @param mixed $message message
	 * @see LoggerLoggingEvent			
	 */
	public function forcedLog($fqcn, $caller, $level, $message) {
		// $fqcn = is_object($caller) ? get_class($caller) : (string)$caller;
		$this->callAppenders(new LoggerLoggingEvent($fqcn, $this, $level, $message));
	} 

	/**
	 * Get the additivity flag for this Category instance.
	 * @return boolean
	 */
	public function getAdditivity() {
		return $this->additive;
	}
 
	/**
	 * Get the appenders contained in this category as an array.
	 * @return array collection of appenders
	 */
	public function getAllAppenders() {
		return array_values($this->aai);
	}
	
	/**
	 * Look for the appender named as name.
	 * @return LoggerAppender
	 */
	public function getAppender($name) {
		return $this->aai[$name];
	}
  
	/**
	 * Starting from this category, search the category hierarchy for a non-null level and return it.
	 * @see LoggerLevel
	 * @return LoggerLevel or null
	 */
	public function getEffectiveLevel() {
		for($c = $this; $c != null; $c = $c->parent) {
			if($c->getLevel() !== null) {
				return $c->getLevel();
			}
		}
		return null;
	}
  
	/**
	 * Returns the assigned Level, if any, for this Category.
	 * @return LoggerLevel or null 
	 */
	public function getLevel() {
		return $this->level;
	}
	
	/**
	 * Get a Logger by name (Delegate to {@link LoggerManager})
	 * 
	 * @param string $name logger name
	 * @param LoggerFactory $factory a {@link LoggerFactory} instance or null
	 * @return Logger
	 * @static 
	 */
	 // TODO: remove method? confusing design	   
	public function getLogger($name) {
		return LoggerManager::getLogger($name);
	}
	
	/**
	 * Return the the repository where this Category is attached.
	 * @return LoggerHierarchy
	 */
	public function getLoggerRepository() {
		return $this->repository;
	} 

	/**
	 * Return the category name.
	 * @return string
	 */
	public function getName() {
		return $this->name;
	} 

	/**
	 * Returns the parent of this category.
	 * @return Logger
	 */
	public function getParent() {
		return $this->parent;
	}	   
		  
	/**
	 * Return the root of the default category hierarchy.
	 * @return LoggerRoot
	 */
	 // TODO: remove method? confusing design
	public function getRoot() {
		return LoggerManager::getRootLogger();
	} 

	/**
	 * get the Root Logger (Delegate to {@link LoggerManager})
	 * @return LoggerRoot
	 * @static 
	 */	   
	 // TODO: remove method? confusing design
	public static function getRootLogger() {
		return LoggerManager::getRootLogger();	  
	}

	/**
	 * Is the appender passed as parameter attached to this category?
	 *
	 * @param LoggerAppender $appender
	 */
	public function isAttached($appender) {
		return isset($this->aai[$appender->getName()]);
	} 
		   
	/**
	 * Check whether this category is enabled for the DEBUG Level.
	 * @return boolean
	 */
	public function isDebugEnabled() {
		return $this->isEnabledFor(LoggerLevel::getLevelDebug());
	}		

	/**
	 * Check whether this category is enabled for a given Level passed as parameter.
	 *
	 * @param LoggerLevel level
	 * @return boolean
	 */
	public function isEnabledFor($level) {
		if($this->repository->isDisabled($level)) {
			return false;
		}
		return (bool)($level->isGreaterOrEqual($this->getEffectiveLevel()));
	} 

	/**
	 * Check whether this category is enabled for the info Level.
	 * @return boolean
	 * @see LoggerLevel
	 */
	public function isInfoEnabled() {
		return $this->isEnabledFor(LoggerLevel::getLevelInfo());
	} 

	/**
	 * This generic form is intended to be used by wrappers.
	 *
	 * @param LoggerLevel $priority a valid level
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function log($priority, $message, $caller = null) {
		if($this->isEnabledFor($priority)) {
			$this->forcedLog($this->fqcn, $caller, $priority, $message);
		}
	}

	/**
	 * Remove all previously added appenders from this Category instance.
	 */
	public function removeAllAppenders() {
		$appenderNames = array_keys($this->aai);
		$enumAppenders = count($appenderNames);
		for($i = 0; $i < $enumAppenders; $i++) {
			$this->removeAppender($appenderNames[$i]); 
		}
	} 
			
	/**
	 * Remove the appender passed as parameter form the list of appenders.
	 *
	 * @param mixed $appender can be an appender name or a {@link LoggerAppender} object
	 */
	public function removeAppender($appender) {
		if($appender instanceof LoggerAppender) {
			$appender->close();
			unset($this->aai[$appender->getName()]);
		} else if (is_string($appender) and isset($this->aai[$appender])) {
			$this->aai[$appender]->close();
			unset($this->aai[$appender]);
		}
	} 

	/**
	 * Set the additivity flag for this Category instance.
	 *
	 * @param boolean $additive
	 */
	public function setAdditivity($additive) {
		$this->additive = (bool)$additive;
	}

	/**
	 * Only the Hierarchy class can set the hierarchy of a category.
	 *
	 * @param LoggerHierarchy $repository
	 */
	public function setHierarchy($repository) {
		$this->repository = $repository;
	}

	/**
	 * Set the level of this Category.
	 *
	 * @param LoggerLevel $level a level string or a level constant 
	 */
	public function setLevel($level) {
		$this->level = $level;
	}
	
	/**
	 * Sets the parent logger of this logger
	 */
	public function setParent(Logger $logger) {
		$this->parent = $logger;
	} 
}
