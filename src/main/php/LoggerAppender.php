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
 */

/**
 * Abstract class that defines output logs strategies.
 *
 * @version $Revision$
 * @package log4php
 */
abstract class LoggerAppender {
	
	/**
	 * @var boolean closed appender flag
	 */
	protected $closed = false;
	
	/**
	 * @var object unused
	 */
	protected $errorHandler;
		   
	/**
	 * The first filter in the filter chain
	 * @var LoggerFilter
	 */
	protected $headFilter = null;
			
	/**
	 * LoggerLayout for this appender. It can be null if appender has its own layout
	 * @var LoggerLayout
	 */
	protected $layout = null; 
		   
	/**
	 * @var string Appender name
	 */
	protected $name;
		   
	/**
	 * The last filter in the filter chain
	 * @var LoggerFilter
	 */
	protected $tailFilter = null; 
		   
	/**
	 * @var LoggerLevel There is no level threshold filtering by default.
	 */
	protected $threshold = null;
	
	/**
	 * @var boolean needs a layout formatting ?
	 */
	protected $requiresLayout = false;
	
	/**
	 * Constructor
	 *
	 * @param string $name appender name
	 */
	public function __construct($name) {
		$this->name = $name;
		$this->clearFilters();
	}

	/**
	 * Factory
	 *
	 * @param string $name appender name
	 * @param string $class create an instance of this appender class
	 * @return LoggerAppender
	 */
	public static function factory($name, $class) {
		$class = basename($class);
		if(!empty($class)) {
			return new $class($name);
		}
		return null;
	}

	/**
	 * Singleton
	 *
	 * @param string $name appender name
	 * @param string $class create or get a reference instance of this class
	 * @return LoggerAppender 
	 */
	public static function singleton($name, $class = '') {
		static $instances;
		
		if(!empty($name)) {
			if(!isset($instances[$name])) {
				if(!empty($class)) {
					$appender = self::factory($name, $class);
					if($appender !== null) { 
						$instances[$name] = $appender;
						return $instances[$name];
					}
				}
				return null;
			}
			return $instances[$name];				 
		}		 
		return null;		
	}

	/**
	 * Add a filter to the end of the filter list.
	 *
	 * @param LoggerFilter $newFilter add a new LoggerFilter
	 */
	public function addFilter($newFilter) {
		if($this->headFilter === null) {
			$this->headFilter = $newFilter;
			$this->tailFilter = $this->headFilter;
		} else {
			$this->tailFilter->next = $newFilter;
			$this->tailFilter = $this->tailFilter->next;
		}
	}
	
	/**
	 * Clear the list of filters by removing all the filters in it.
	 * @abstract
	 */
	public function clearFilters() {
		unset($this->headFilter);
		unset($this->tailFilter);
		$this->headFilter = null;
		$this->tailFilter = null;
	}

	/**
	 * Return the first filter in the filter chain for this Appender. 
	 * The return value may be <i>null</i> if no is filter is set.
	 * @return LoggerFilter
	 */
	public function getFilter() {
		return $this->headFilter;
	} 
	
	/** 
	 * Return the first filter in the filter chain for this Appender. 
	 * The return value may be <i>null</i> if no is filter is set.
	 * @return LoggerFilter
	 */
	public function getFirstFilter() {
		return $this->headFilter;
	}
	
	
	/**
	 * This method performs threshold checks and invokes filters before
	 * delegating actual logging to the subclasses specific <i>append()</i> method.
	 * @see LoggerAppender::doAppend()
	 * @param LoggerLoggingEvent $event
	 */
	public function doAppend($event) {
		if($this->closed) {
			return;
		}
		
		if(!$this->isAsSevereAsThreshold($event->getLevel())) {
			return;
		}

		$f = $this->getFirstFilter();
		while($f !== null) {
			switch ($f->decide($event)) {
				case LoggerFilter::DENY: return;
				case LoggerFilter::ACCEPT: return $this->append($event);
				case LoggerFilter::NEUTRAL: $f = $f->getNext();
			}
		}
		$this->append($event);	  
	}	 

	/**
	 * Do not use this method.
	 * @see LoggerAppender::setErrorHandler()
	 * @param object $errorHandler
	 */
	public function setErrorHandler($errorHandler) {
		if($errorHandler == null) {
			// We do not throw exception here since the cause is probably a
			// bad config file.
			//LoggerLog::warn("You have tried to set a null error-handler.");
		} else {
			$this->errorHandler = $errorHandler;
		}
	} 
	
	/**
	 * Do not use this method.
	 * @see LoggerAppender::getErrorHandler()
	 * @return object Returns the ErrorHandler for this appender.
	 */
	public function getErrorHandler() {
		return $this->errorHandler;
	} 

	/**
	 * Set the Layout for this appender.
	 * @see LoggerAppender::setLayout()
	 * @param LoggerLayout $layout
	 */
	public function setLayout($layout) {
		if($this->requiresLayout()) {
			$this->layout = $layout;
		}
	} 
	
	/**
	 * Returns this appender layout.
	 * @see LoggerAppender::getLayout()
	 * @return LoggerLayout
	 */
	public function getLayout() {
		return $this->layout;
	}
	
	/**
	 * Configurators call this method to determine if the appender
	 * requires a layout. 
	 *
	 * <p>If this method returns <i>true</i>, meaning that layout is required, 
	 * then the configurator will configure a layout using the configuration 
	 * information at its disposal.	 If this method returns <i>false</i>, 
	 * meaning that a layout is not required, then layout configuration will be
	 * skipped even if there is available layout configuration
	 * information at the disposal of the configurator.</p>
	 *
	 * <p>In the rather exceptional case, where the appender
	 * implementation admits a layout but can also work without it, then
	 * the appender should return <i>true</i>.</p>
	 * 
	 * @see LoggerAppender::requiresLayout()
	 * @return boolean
	 */
	public function requiresLayout() {
		return $this->requiresLayout;
	}
	
	/**
	 * Get the name of this appender.
	 * @see LoggerAppender::getName()
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
/**
	 * Set the name of this appender.
	 *
	 * The name is used by other components to identify this appender.
	 *
	 * 
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;	
	}
	
	/**
	 * Returns this appenders threshold level. 
	 * See the {@link setThreshold()} method for the meaning of this option.
	 * @return LoggerLevel
	 */
	public function getThreshold() { 
		return $this->threshold;
	}
	
	/**
	 * Set the threshold level of this appender.
	 *
	 * @param mixed $threshold can be a {@link LoggerLevel} object or a string.
	 * @see LoggerOptionConverter::toLevel()
	 */
	public function setThreshold($threshold) {
		if(is_string($threshold)) {
		   $this->threshold = LoggerOptionConverter::toLevel($threshold, null);
		} else if($threshold instanceof LoggerLevel) {
		   $this->threshold = $threshold;
		}
	}
	
	/**
	 * Check whether the message level is below the appender's threshold. 
	 *
	 *
	 * If there is no threshold set, then the return value is always <i>true</i>.
	 * @param LoggerLevel $priority
	 * @return boolean true if priority is greater or equal than threshold	
	 */
	public function isAsSevereAsThreshold($priority) {
		if($this->threshold === null) {
			return true;
		}
		return $priority->isGreaterOrEqual($this->getThreshold());
	}

	/**
	 * Derived appenders should override this method if option structure
	 * requires it.
	 */
	abstract public function activateOptions();	   
	
	/**
	 * Subclasses of {@link LoggerAppender} should implement 
	 * this method to perform actual logging.
	 *
	 * @param LoggerLoggingEvent $event
	 * @see doAppend()
	 * @abstract
	 */
	abstract protected function append($event); 

	/**
	 * Release any resources allocated.
	 * Subclasses of {@link LoggerAppender} should implement 
	 * this method to perform proper closing procedures.
	 * @abstract
	 */
	abstract public function close();

	/**
	 * Finalize this appender by calling the derived class' <i>close()</i> method.
	 */
	public function finalize()  {
		// An appender might be closed then garbage collected. There is no
		// point in closing twice.
		if($this->closed) {
			return;
		}
		$this->close();
	}
		
	/**
	 * Perform actions before object serialization.
	 *
	 * Call {@link finalize()} to properly close the appender.
	 */
	public function __sleep() {
		$this->finalize();
		return array_keys(get_object_vars($this)); 
	}

	public function __destruct() {
		$this->finalize();
	}

/**
	 * Perform actions after object de-serialization.
	 *
	 * Call {@link activateOptions()} to properly setup the appender.
	 */
	public function __wakeup() {
		$this->activateOptions();
	}
}
