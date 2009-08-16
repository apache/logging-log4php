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
 * LOG4PHP_DIR points to the log4php root directory.
 *
 * If not defined it will be set automatically when the first package classfile 
 * is included
 * 
 * @var string 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__));

spl_autoload_register(array('Logger', 'autoload'));

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
		'LoggerAppenderAdodb' => '/appenders/LoggerAppenderAdodb.php',
		'LoggerAppenderPDO' => '/appenders/LoggerAppenderPDO.php',
		'LoggerAppenderConsole' => '/appenders/LoggerAppenderConsole.php',
		'LoggerAppenderDailyFile' => '/appenders/LoggerAppenderDailyFile.php',
		'LoggerAppenderEcho' => '/appenders/LoggerAppenderEcho.php',
		'LoggerAppenderFile' => '/appenders/LoggerAppenderFile.php',
		'LoggerAppenderMail' => '/appenders/LoggerAppenderMail.php',
		'LoggerAppenderMailEvent' => '/appenders/LoggerAppenderMailEvent.php',
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
		'LoggerLayoutHtml' => '/layouts/LoggerLayoutHtml.php',
		'LoggerLayoutSimple' => '/layouts/LoggerLayoutSimple.php',
		'LoggerLayoutTTCC' => '/layouts/LoggerLayoutTTCC.php',
		'LoggerLayoutPattern' => '/layouts/LoggerLayoutPattern.php',
		'LoggerLayoutXml' => '/layouts/LoggerLayoutXml.php',
		'LoggerRendererDefault' => '/renderers/LoggerRendererDefault.php',
		'LoggerRendererObject' => '/renderers/LoggerRendererObject.php',
		'LoggerRendererMap' => '/renderers/LoggerRendererMap.php',
		'LoggerLocationInfo' => '/LoggerLocationInfo.php',
		'LoggerLoggingEvent' => '/LoggerLoggingEvent.php',
		'LoggerFilter' => '/LoggerFilter.php',
		'LoggerFilterDenyAll' => '/filters/LoggerFilterDenyAll.php',
		'LoggerFilterLevelMatch' => '/filters/LoggerFilterLevelMatch.php',
		'LoggerFilterLevelRange' => '/filters/LoggerFilterLevelRange.php',
		'LoggerFilterStringMatch' => '/filters/LoggerFilterStringMatch.php',
	);

	/**
	 * Class autoloader
	 * This method is provided to be invoked within an __autoload() magic method.
	 * @param string class name
	 */
	public static function autoload($className) {
		if(isset(self::$_classes[$className])) {
			include LOG4PHP_DIR.self::$_classes[$className];
		}
	}

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
	
	private static $hierarchy;
	
	public static function getHierarchy() {
		if(!isset(self::$hierarchy)) {
			self::$hierarchy = new LoggerHierarchy(new LoggerRoot());
		}
		return self::$hierarchy;
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
	 * Get a Logger by name (Delegate to {@link Logger})
	 * 
	 * @param string $name logger name
	 * @param LoggerFactory $factory a {@link LoggerFactory} instance or null
	 * @return Logger
	 * @static 
	 */
	public static function getLogger($name) {
		return self::getHierarchy()->getLogger($name);
	}
	
	/**
	 * Clears all logger definitions
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
		return self::getHierarchy()->resetConfiguration();	 
	}

	/**
	 * Safely close all appenders.
	 * @static
	 */
	public static function shutdown() {
		return self::getHierarchy()->shutdown();	   
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
	 * get the Root Logger (Delegate to {@link Logger})
	 * @return LoggerRoot
	 * @static 
	 */	   
	public static function getRootLogger() {
		return self::getHierarchy()->getRootLogger();	  
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
	 * Returns the LoggerHierarchy.
	 * 
	 * @static
	 * @return LoggerHierarchy
	 * @deprecated
	 */
	public static function getLoggerRepository() {
		return self::getHierarchy();	
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


// ---------------------------------------------------------------------------
// ---------------------------------------------------------------------------
// ---------------------------------------------------------------------------

if (!defined('LOG4PHP_DEFAULT_INIT_OVERRIDE')) {
	if (isset($_ENV['log4php.defaultInitOverride'])) {
		/**
		 * @ignore
		 */
		define('LOG4PHP_DEFAULT_INIT_OVERRIDE', 
			LoggerOptionConverter::toBoolean($_ENV['log4php.defaultInitOverride'], false)
		);
	} elseif (isset($GLOBALS['log4php.defaultInitOverride'])) {
		/**
		 * @ignore
		 */
		define('LOG4PHP_DEFAULT_INIT_OVERRIDE', 
			LoggerOptionConverter::toBoolean($GLOBALS['log4php.defaultInitOverride'], false)
		);
	} else {
		/**
		 * Controls init execution
		 *
		 * With this constant users can skip the default init procedure that is
		 * called when this file is included.
		 *
		 * <p>If it is not user defined, log4php tries to autoconfigure using (in order):</p>
		 *
		 * - the <code>$_ENV['log4php.defaultInitOverride']</code> variable.
		 * - the <code>$GLOBALS['log4php.defaultInitOverride']</code> global variable.
		 * - defaults to <i>false</i>
		 *
		 * @var boolean
		 */
		define('LOG4PHP_DEFAULT_INIT_OVERRIDE', false);
	}
}

if (!defined('LOG4PHP_CONFIGURATION')) {
	if (isset($_ENV['log4php.configuration'])) {
		/**
		 * @ignore
		 */
		define('LOG4PHP_CONFIGURATION', trim($_ENV['log4php.configuration']));
	} else {
		/**
		 * Configuration file.
		 *
		 * <p>This constant tells configurator classes where the configuration
		 * file is located.</p>
		 * <p>If not set by user, log4php tries to set it automatically using 
		 * (in order):</p>
		 *
		 * - the <code>$_ENV['log4php.configuration']</code> enviroment variable.
		 * - defaults to 'log4php.properties'.
		 *
		 * @var string
		 */
		define('LOG4PHP_CONFIGURATION', 'log4php.properties');
	}
}

if (!defined('LOG4PHP_CONFIGURATOR_CLASS')) {
	if ( strtolower(substr( LOG4PHP_CONFIGURATION, -4 )) == '.xml') { 
		/**
		 * @ignore
		 */
		define('LOG4PHP_CONFIGURATOR_CLASS', 'LoggerConfiguratorXml');
	} else {
		/**
		 * Holds the configurator class name.
		 *
		 * <p>This constant is set with the fullname (path included but non the 
		 * .php extension) of the configurator class that init procedure will use.</p>
		 * <p>If not set by user, log4php tries to set it automatically.</p>
		 * <p>If {@link LOG4PHP_CONFIGURATION} has '.xml' extension set the 
		 * constants to '{@link LOG4PHP_DIR}/xml/{@link LoggerConfiguratorXml}'.</p>
		 * <p>Otherwise set the constants to 
		 * '{@link LOG4PHP_DIR}/{@link LoggerConfiguratorIni}'.</p>
		 *
		 * <p><b>Security Note</b>: classfile pointed by this constant will be brutally
		 * included with a:
		 * <code>@include_once(LOG4PHP_CONFIGURATOR_CLASS . ".php");</code></p>
		 *
		 * @var string
		 */
		define('LOG4PHP_CONFIGURATOR_CLASS', 'LoggerConfiguratorIni');
	}
}

if (!LOG4PHP_DEFAULT_INIT_OVERRIDE) {
	if (!LoggerDefaultInit()) {
//		LoggerLog::warn("LOG4PHP main() Default Init failed.");
	}
}

/**
 * Default init procedure.
 *
 * <p>This procedure tries to configure the {@link LoggerHierarchy} using the
 * configurator class defined via {@link LOG4PHP_CONFIGURATOR_CLASS} that tries
 * to load the configurator file defined in {@link LOG4PHP_CONFIGURATION}.
 * If something goes wrong a warn is raised.</p>
 * <p>Users can skip this procedure using {@link LOG4PHP_DEFAULT_INIT_OVERRIDE}
 * constant.</p> 
 *
 * @return boolean
 */
function LoggerDefaultInit() {
	$configuratorClass = basename(LOG4PHP_CONFIGURATOR_CLASS);	
	return call_user_func(array($configuratorClass, 'configure'), LOG4PHP_CONFIGURATION);
}
