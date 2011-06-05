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
 * @package log4php
 */

if (function_exists('__autoload')) {
	trigger_error("log4php: It looks like your code is using an __autoload() function. log4php uses spl_autoload_register() which will bypass your __autoload() function and may break autoloading.", E_USER_WARNING);
}

spl_autoload_register(array('Logger', 'autoload'));

/**
 * This is the central class in the log4php package. All logging operations 
 * are done through this class.
 * 
 * The main logging methods are:
 * 	<ul>
 * 		<li>{@link trace()}</li>
 * 		<li>{@link debug()}</li>
 * 		<li>{@link info()}</li>
 * 		<li>{@link warn()}</li>
 * 		<li>{@link error()}</li>
 * 		<li>{@link fatal()}</li>
 * 	</ul>
 * 
 * @category   log4php
 * @package    log4php
 * @license	   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version	   SVN: $Id$
 * @link	   http://logging.apache.org/log4php
 */
class Logger {
	private static $_classes = array(
		'LoggerException' => '/LoggerException.php',
		'LoggerHierarchy' => '/LoggerHierarchy.php',
		'LoggerLayout' => '/LoggerLayout.php',
		'LoggerLevel' => '/LoggerLevel.php',
		'LoggerMDC' => '/LoggerMDC.php',
		'LoggerNDC' => '/LoggerNDC.php',
		'LoggerReflectionUtils' => '/LoggerReflectionUtils.php',
		'LoggerConfigurator' => '/LoggerConfigurator.php',
		'LoggerConfiguratorBasic' => '/configurators/LoggerConfiguratorBasic.php',
		'LoggerConfiguratorIni' => '/configurators/LoggerConfiguratorIni.php',
		'LoggerConfiguratorPhp' => '/configurators/LoggerConfiguratorPhp.php',
		'LoggerConfiguratorXml' => '/configurators/LoggerConfiguratorXml.php',
		'LoggerRoot' => '/LoggerRoot.php',
		'LoggerAppender' => '/LoggerAppender.php',
		'LoggerAppenderPool' => '/LoggerAppenderPool.php',
		'LoggerAppenderAdodb' => '/appenders/LoggerAppenderAdodb.php',
		'LoggerAppenderPDO' => '/appenders/LoggerAppenderPDO.php',
		'LoggerAppenderConsole' => '/appenders/LoggerAppenderConsole.php',
		'LoggerAppenderDailyFile' => '/appenders/LoggerAppenderDailyFile.php',
		'LoggerAppenderEcho' => '/appenders/LoggerAppenderEcho.php',
		'LoggerAppenderFile' => '/appenders/LoggerAppenderFile.php',
		'LoggerAppenderMail' => '/appenders/LoggerAppenderMail.php',
		'LoggerAppenderMailEvent' => '/appenders/LoggerAppenderMailEvent.php',
		'LoggerAppenderMongoDB' => '/appenders/LoggerAppenderMongoDB.php',
		'LoggerAppenderNull' => '/appenders/LoggerAppenderNull.php',
		'LoggerAppenderPhp' => '/appenders/LoggerAppenderPhp.php',
		'LoggerAppenderRollingFile' => '/appenders/LoggerAppenderRollingFile.php',
		'LoggerAppenderSocket' => '/appenders/LoggerAppenderSocket.php',
		'LoggerAppenderSyslog' => '/appenders/LoggerAppenderSyslog.php',
		'LoggerFormattingInfo' => '/helpers/LoggerFormattingInfo.php',
		'LoggerOptionConverter' => '/helpers/LoggerOptionConverter.php',
		'LoggerPatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerBasicPatternConverter' => '/helpers/LoggerBasicPatternConverter.php',
		'LoggerCategoryPatternConverter' => '/helpers/LoggerCategoryPatternConverter.php',
		'LoggerClassNamePatternConverter' => '/helpers/LoggerClassNamePatternConverter.php',
		'LoggerDatePatternConverter' => '/helpers/LoggerDatePatternConverter.php',
		'LoggerLiteralPatternConverter' => '/helpers/LoggerLiteralPatternConverter.php',
		'LoggerLocationPatternConverter' => '/helpers/LoggerLocationPatternConverter.php',
		'LoggerLoggingEventBsonifier' => '/helpers/LoggerLoggingEventBsonifier.php',
		'LoggerMDCPatternConverter' => '/helpers/LoggerMDCPatternConverter.php',
		'LoggerNamedPatternConverter' => '/helpers/LoggerNamedPatternConverter.php',
		'LoggerBasicPatternConverter' => '/helpers/LoggerBasicPatternConverter.php',
		'LoggerLiteralPatternConverter' => '/helpers/LoggerLiteralPatternConverter.php',
		'LoggerDatePatternConverter' => '/helpers/LoggerDatePatternConverter.php',
		'LoggerMDCPatternConverter' => '/helpers/LoggerMDCPatternConverter.php',
		'LoggerLocationPatternConverter' => '/helpers/LoggerLocationPatternConverter.php',
		'LoggerNamedPatternConverter' => '/helpers/LoggerNamedPatternConverter.php',
		'LoggerClassNamePatternConverter' => '/helpers/LoggerClassNamePatternConverter.php',
		'LoggerCategoryPatternConverter' => '/helpers/LoggerCategoryPatternConverter.php',
		'LoggerPatternParser' => '/helpers/LoggerPatternParser.php',
		'LoggerLayoutBson' => '/layouts/LoggerLayoutBson.php',
		'LoggerLayoutHtml' => '/layouts/LoggerLayoutHtml.php',
		'LoggerLayoutSimple' => '/layouts/LoggerLayoutSimple.php',
		'LoggerLayoutTTCC' => '/layouts/LoggerLayoutTTCC.php',
		'LoggerLayoutPattern' => '/layouts/LoggerLayoutPattern.php',
		'LoggerLayoutXml' => '/layouts/LoggerLayoutXml.php',
		'LoggerRendererDefault' => '/renderers/LoggerRendererDefault.php',
		'LoggerRendererObject' => '/renderers/LoggerRendererObject.php',
		'LoggerRendererMap' => '/renderers/LoggerRendererMap.php',
		'LoggerRendererException' => '/renderers/LoggerRendererException.php',
		'LoggerLocationInfo' => '/LoggerLocationInfo.php',
		'LoggerThrowableInformation' => '/LoggerThrowableInformation.php',
		'LoggerLoggingEvent' => '/LoggerLoggingEvent.php',
		'LoggerFilter' => '/LoggerFilter.php',
		'LoggerFilterDenyAll' => '/filters/LoggerFilterDenyAll.php',
		'LoggerFilterLevelMatch' => '/filters/LoggerFilterLevelMatch.php',
		'LoggerFilterLevelRange' => '/filters/LoggerFilterLevelRange.php',
		'LoggerFilterStringMatch' => '/filters/LoggerFilterStringMatch.php'
	);

	/**
	 * Class autoloader. This method is provided to be invoked within an 
	 * __autoload() magic method.
	 * @param string $className The name of the class to load.
	 */
	public static function autoload($className) {
		if(isset(self::$_classes[$className])) {
			include dirname(__FILE__) . self::$_classes[$className];
		}
	}

	/**
	 * Logger additivity. If set to true then child loggers will inherit
	 * the appenders of their ancestors by default.
	 * @var boolean
	 */
	private $additive = true;
	
	/** The Logger's fully qualified class name. */
	private $fqcn = 'Logger';

	/** The assigned Logger level. */
	private $level;
	
	/** The name of this Logger instance. */
	private $name;
	
	/** The parent logger. Set to null if this is the root logger. */
	private $parent;
	
	/**
	 * A collection of appenders associated with this logger.
	 * @see LoggerAppender
	 */
	private $appenders = array();

	/** The logger hierarchy used by log4php. */
	private static $hierarchy;
	
	/** 
	 * Name of the configurator class used to configure log4php. 
	 * Populated by {@link configure()} and used in {@link initialize()}.
	 */
	private static $configurationClass = 'LoggerConfiguratorBasic';
	
	/** 
	 * Path to the configuration file which may be used by the configurator.
	 * Populated by {@link configure()} and used in {@link initialize()}. 
	 */
	private static $configurationFile;
	
	/** Inidicates if log4php has been initialized */
	private static $initialized = false;
	
	/**
	 * Constructor.
	 * @param string $name Name of the logger.	  
	 */
	public function __construct($name) {
		$this->name = $name;
	}
	
	/**
	 * Returns the logger name.
	 * @return string
	 */
	public function getName() {
		return $this->name;
	} 

	/**
	 * Returns the parent Logger. Can be null if this is the root logger.
	 * @return Logger
	 */
	public function getParent() {
		return $this->parent;
	}
	
	/**
	 * Returns the hierarchy used by this Logger.
	 * Caution: do not use this hierarchy unless you have called initialize().
	 * To get Loggers, use the Logger::getLogger and Logger::getRootLogger methods
	 * instead of operating on on the hierarchy directly.
	 * 
	 * @deprecated - will be moved to private
	 * @return LoggerHierarchy
	 */
	public static function getHierarchy() {
		if(!isset(self::$hierarchy)) {
			self::$hierarchy = new LoggerHierarchy(new LoggerRoot());
		}
		return self::$hierarchy;
	}
	
	/* Logging methods */
	/**
	 * Log a message object with the TRACE level.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function trace($message, $caller = null) {
		$this->log(LoggerLevel::getLevelTrace(), $message, $caller);
	} 		
	
	/**
	 * Log a message object with the DEBUG level.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function debug($message, $caller = null) {
		$this->log(LoggerLevel::getLevelDebug(), $message, $caller);
	} 


	/**
	 * Log a message object with the INFO Level.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function info($message, $caller = null) {
		$this->log(LoggerLevel::getLevelInfo(), $message, $caller);
	}

	/**
	 * Log a message with the WARN level.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function warn($message, $caller = null) {
		$this->log(LoggerLevel::getLevelWarn(), $message, $caller);
	}
	
	/**
	 * Log a message object with the ERROR level.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function error($message, $caller = null) {
		$this->log(LoggerLevel::getLevelError(), $message, $caller);
	}
	
	/**
	 * Log a message object with the FATAL level.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function fatal($message, $caller = null) {
		$this->log(LoggerLevel::getLevelFatal(), $message, $caller);
	}
	
	/**
	 * This method creates a new logging event and logs the event without 
	 * further checks.
	 *
	 * It should not be called directly. Use {@link trace()}, {@link debug()},
	 * {@link info()}, {@link warn()}, {@link error()} and {@link fatal()} 
	 * wrappers.
	 *
	 * @param string $fqcn Fully qualified class name of the Logger
	 * @param mixed $caller caller object or caller string id
	 * @param LoggerLevel $level log level	   
	 * @param mixed $message message to log
	 */
	public function forcedLog($fqcn, $caller, $level, $message) {
		$throwable = ($caller !== null && $caller instanceof Exception) ? $caller : null;
		
		$this->callAppenders(new LoggerLoggingEvent($fqcn, $this, $level, $message, null, $throwable));
	} 
	
	
	/**
	 * Check whether this Logger is enabled for the DEBUG Level.
	 * @return boolean
	 */
	public function isDebugEnabled() {
		return $this->isEnabledFor(LoggerLevel::getLevelDebug());
	}		

	/**
	 * Check whether this Logger is enabled for a given Level passed as parameter.
	 *
	 * @param LoggerLevel level
	 * @return boolean
	 */
	public function isEnabledFor($level) {
		return (bool)($level->isGreaterOrEqual($this->getEffectiveLevel()));
	} 

	/**
	 * Check whether this Logger is enabled for the INFO Level.
	 * @return boolean
	 */
	public function isInfoEnabled() {
		return $this->isEnabledFor(LoggerLevel::getLevelInfo());
	} 

	/**
	 * Log a message using the provided logging level.
	 *
	 * @param LoggerLevel $priority The logging level.
	 * @param mixed $message Message to log.
	 * @param mixed $caller caller object or caller string id
	 */
	public function log($priority, $message, $caller = null) {
		if($this->isEnabledFor($priority)) {
			$this->forcedLog($this->fqcn, $caller, $priority, $message);
		}
	}
	
	/**
	 * If assertion parameter is false, then logs the message as an error.
	 *
	 * @param bool $assertion
	 * @param string $msg message to log
	 */
	public function assertLog($assertion = true, $msg = '') {
		if($assertion == false) {
			$this->error($msg);
		}
	}
	
	/* Factory methods */ 
	
	/**
	 * Get a Logger by name (Delegate to {@link Logger})
	 * 
	 * @param string $name logger name
	 * @param LoggerFactory $factory a {@link LoggerFactory} instance or null
	 * @return Logger
	 * @static 
	 */
	public static function getLogger($name) {
		if(!self::isInitialized()) {
			self::initialize();
		}
		return self::getHierarchy()->getLogger($name);
	}
	
	/**
	 * Returns all currently defined loggers
	 * @return all currently defined logger instances
	 */
	public static function getAllLoggers() {
		return self::getHierarchy()->getCurrentLoggers();
	}
	
	/**
	 * Get the Root Logger (Delegate to {@link Logger})
	 * @return LoggerRoot
	 * @static 
	 */	   
	public static function getRootLogger() {
		if(!self::isInitialized()) {
			self::initialize();
		}
		return self::getHierarchy()->getRootLogger();	  
	}
	
	/* Configuration methods */
	
	/**
	 * Add a new appender to the Logger.
	 *
	 * @param LoggerAppender $appender The appender to add.
	 */
	public function addAppender($appender) {
		$appenderName = $appender->getName();
		$this->appenders[$appenderName] = $appender;
	}
	
	/**
	 * Remove all previously added appenders from the Logger.
	 */
	public function removeAllAppenders() {
		$appenderNames = array_keys($this->appenders);
		$enumAppenders = count($appenderNames);
		for($i = 0; $i < $enumAppenders; $i++) {
			$this->removeAppender($appenderNames[$i]); 
		}
	} 
			
	/**
	 * Remove the appender passed as parameter form the Logger.
	 *
	 * @param string|LoggerAppender $appender an appender name or a {@link LoggerAppender} instance.
	 */
	public function removeAppender($appender) {
		if($appender instanceof LoggerAppender) {
			$appender->close();
			unset($this->appenders[$appender->getName()]);
		} else if (is_string($appender) and isset($this->appenders[$appender])) {
			$this->appenders[$appender]->close();
			unset($this->appenders[$appender]);
		}
	} 
			
	/**
	 * Forwards the given logging event to all appenders associated with the 
	 * Logger.
	 *
	 * @param LoggerLoggingEvent $event 
	 */
	public function callAppenders($event) {
		if(count($this->appenders) > 0) {
			foreach(array_keys($this->appenders) as $appenderName) {
				$this->appenders[$appenderName]->doAppend($event);
			}
		}
		if($this->parent != null and $this->getAdditivity()) {
			$this->parent->callAppenders($event);
		}
	}
	
	/**
	 * Get the appenders contained in this logger as an array.
	 * @return array collection of appender names
	 */
	public function getAllAppenders() {
		return array_values($this->appenders);
	}
	
	/**
	 * Get an appender by name.
	 * @return LoggerAppender
	 */
	public function getAppender($name) {
		return $this->appenders[$name];
	}
	
	/**
	 * Get the additivity flag.
	 * @return boolean
	 */
	public function getAdditivity() {
		return $this->additive;
	}
 
	/**
	 * Starting from this Logger, search the Logger hierarchy for a non-null level and return it.
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
	 * Get the assigned Logger level.
	 * @return LoggerLevel The assigned level or null if none is assigned. 
	 */
	public function getLevel() {
		return $this->level;
	}
	
	/**
	 * Set the Logger level.
	 *
	 * @param LoggerLevel $level the level to set
	 */
	public function setLevel($level) {
		$this->level = $level;
	}
	
	/**
	 * Clears all Logger definitions from the logger hierarchy.
	 * 
	 * @static
	 * @return boolean 
	 */
	public static function clear() {
		return self::getHierarchy()->clear();	 
	}
	
	/**
	 * Destroy configurations for logger definitions
	 * 
	 * @static
	 * @return boolean 
	 */
	public static function resetConfiguration() {
		$result = self::getHierarchy()->resetConfiguration();
		self::$initialized = false;
		self::$configurationClass = 'LoggerConfiguratorBasic';
		self::$configurationFile = null;
		return $result;	 
	}

	/**
	 * Safely close all appenders.
	 * 
	 * @deprecated This is no longer necessary due the appenders shutdown via
	 * destructors.
	 * @static
	 */
	public static function shutdown() {
		return self::getHierarchy()->shutdown();	   
	}
	
	/**
	 * check if a given logger exists.
	 * 
	 * @param string $name logger name 
	 * @static
	 * @return boolean
	 */
	public static function exists($name) {
		return self::getHierarchy()->exists($name);
	}
	
	/**
	 * Returns an array this whole Logger instances.
	 * 
	 * @static
	 * @see Logger
	 * @return array
	 */
	public static function getCurrentLoggers() {
		return self::getHierarchy()->getCurrentLoggers();
	}
	
	/**
	 * Checks whether an appender is attached to this logger instance.
	 *
	 * @param LoggerAppender $appender
	 * @return boolean
	 */
	public function isAttached(LoggerAppender $appender) {
		return isset($this->appenders[$appender->getName()]);
	} 
		   
	/**
	 * Sets the additivity flag.
	 * @param boolean $additive
	 */
	public function setAdditivity($additive) {
		$this->additive = (bool)$additive;
	}

	/**
	 * Sets the parent logger.
	 * @param Logger $logger
	 */
	public function setParent(Logger $logger) {
		$this->parent = $logger;
	} 
	
	/**
	 * Configures log4php by defining a configuration file and/or class.
	 * 
	 * This method needs to be called before the first logging event has 
	 * occured. If this method is not called before then, the standard 
	 * configuration takes place (@see LoggerConfiguratorBasic).
	 * 
	 * If only the configuration file is given, the configurator class will
	 * be determined by the config file extension.  
	 * 
	 * If a custom configurator class is provided, the configuration file
	 * should either be null or contain the path to file used by the custom 
	 * configurator. Make sure the configurator class is already loaded, or
	 * that it can be included by PHP when necessary.
	 * 
	 * @param string $configurationFile path to the configuration file
	 * @param string $configurationClass name of the custom configurator class 
	 */
	public static function configure($configurationFile = null, $configurationClass = null ) {
		if($configurationClass === null && $configurationFile === null) {
			self::$configurationClass = 'LoggerConfiguratorBasic';
			return;
		}
									 	
		if($configurationClass !== null) {
			self::$configurationFile = $configurationFile;
			self::$configurationClass = $configurationClass;
			return;
		}
		
		if (strtolower(substr( $configurationFile, -4 )) == '.xml') {
			self::$configurationFile = $configurationFile;
			self::$configurationClass = 'LoggerConfiguratorXml';
		} else {
			self::$configurationFile = $configurationFile;
			self::$configurationClass = 'LoggerConfiguratorIni';
		}
	}
	
	/**
	 * Returns the current {@link Logger::$configurationClass configurator class}.
	 * @return string the configurator class name
	 */
	public static function getConfigurationClass() {
		return self::$configurationClass;
	}
	
	/**
	 * Returns the current {@link Logger::$configurationFile configuration file}.
	 * @return string the configuration file
	 */
	public static function getConfigurationFile() {
		return self::$configurationFile;
	}
	
	/**
	 * Returns true if the log4php framework has been initialized.
	 * @return boolean 
	 */
	private static function isInitialized() {
		return self::$initialized;
	}
	
	/**
	 * Initializes the log4php framework using the provided {@link 
	 * Logger::$configurationClass configuration class}  and {@link 
	 * Logger::$configurationFile configuration file}.
	 * @return boolean
	 */
	public static function initialize() {
		self::$initialized = true;
		$instance = LoggerReflectionUtils::createObject(self::$configurationClass);
		$result = $instance->configure(self::getHierarchy(), self::$configurationFile);
		return $result;
	}
}
