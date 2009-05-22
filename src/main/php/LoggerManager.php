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
 * LOG4PHP_DIR points to the log4php root directory.
 *
 * If not defined it will be set automatically when the first package classfile 
 * is included
 * 
 * @var string 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__));

spl_autoload_register(array('LoggerManager', 'autoload'));

/**
 * Use the LoggerManager to get Logger instances.
 *
 * @version $Revision$
 * @package log4php
 * @see Logger
 * @todo create a configurator selector	 
 */
class LoggerManager {
	/** Private to prevent instantiation */
	private function __construct() {
	}

	private static $_classes = array(
		'Logger' => '/Logger.php',
		'LoggerHierarchy' => '/LoggerHierarchy.php',
		'LoggerDefaultCategoryFactory' => '/LoggerDefaultCategoryFactory.php',
		'LoggerHierarchy' => '/LoggerHierarchy.php',
		'LoggerLayout' => '/LoggerLayout.php',
		'LoggerLevel' => '/LoggerLevel.php',
		'LoggerMDC' => '/LoggerMDC.php',
		'LoggerNDC' => '/LoggerNDC.php',
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
		'LoggerPropertySetter' => '/config/LoggerPropertySetter.php',
		'LoggerFormattingInfo' => '/helpers/LoggerFormattingInfo.php',
		'LoggerOptionConverter' => '/helpers/LoggerOptionConverter.php',
		'LoggerPatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerBasicPatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerLiteralPatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerDatePatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerMDCPatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerLocationPatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerNamedPatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerClassNamePatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerCategoryPatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerPatternParser' => '/helpers/LoggerPatternParser.php',
		'LoggerLayoutHtml' => '/layouts/LoggerLayoutHtml.php',
		'LoggerLayoutSimple' => '/layouts/LoggerLayoutSimple.php',
		'LoggerLayoutTTCC' => '/layouts/LoggerLayoutTTCC.php',
		'LoggerPatternLayout' => '/layouts/LoggerPatternLayout.php',
		'LoggerLayoutXml' => '/layouts/LoggerLayoutXml.php',
		'LoggerRendererDefault' => '/renderers/LoggerRendererDefault.php',
		'LoggerRendererObject' => '/renderers/LoggerRendererObject.php',
		'LoggerRendererMap' => '/renderers/LoggerRendererMap.php',
		'LoggerFactory' => '/LoggerFactory.php',
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
	 * check if a given logger exists.
	 * 
	 * @param string $name logger name 
	 * @static
	 * @return boolean
	 */
	public static function exists($name) {
		return self::getLoggerRepository()->exists($name);
	}

	/**
	 * Returns an array this whole Logger instances.
	 * 
	 * @static
	 * @see Logger
	 * @return array
	 */
	public static function getCurrentLoggers() {
		return self::getLoggerRepository()->getCurrentLoggers();
	}
	
	/**
	 * Returns the root logger.
	 * 
	 * @static
	 * @return object
	 * @see LoggerRoot
	 */
	public static function getRootLogger() {
		return self::getLoggerRepository()->getRootLogger();
	}
	
	/**
	 * Returns the specified Logger.
	 * 
	 * @param string $name logger name
	 * @param LoggerFactory $factory a {@link LoggerFactory} instance or null
	 * @static
	 * @return Logger
	 */
	public static function getLogger($name, $factory = null)  {
		return self::getLoggerRepository()->getLogger($name, $factory);
	}
	
	/**
	 * Returns the LoggerHierarchy.
	 * 
	 * @static
	 * @return LoggerHierarchy
	 */
	public static function getLoggerRepository() {
		return LoggerHierarchy::singleton();	
	}
	

	/**
	 * Destroy loggers object tree.
	 * 
	 * @static
	 * @return boolean 
	 */
	public static function resetConfiguration() {
		return self::getLoggerRepository()->resetConfiguration();	 
	}
	
	/**
	 * Does nothing.
	 * @static
	 * 
	 * TODO: remove this method?
	 */
	public static function setRepositorySelector($selector, $guard) {
		return;
	}
	
	/**
	 * Safely close all appenders.
	 * @static
	 */
	public static function shutdown() {
		return self::getLoggerRepository()->shutdown();	   
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
	if (!LoggerManagerDefaultInit()) {
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
function LoggerManagerDefaultInit() {
	$configuratorClass = basename(LOG4PHP_CONFIGURATOR_CLASS);	
	return call_user_func(array($configuratorClass, 'configure'), LOG4PHP_CONFIGURATION);
}

