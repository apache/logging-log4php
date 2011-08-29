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
 * Configures log4php based on a provided configuration file.  
 */
class LoggerConfigurator
{
	/** XML configuration file format. */
	const FORMAT_XML = 'xml';
	
	/** PHP configuration file format. */
	const FORMAT_PHP = 'php';
	
	/** INI (properties) configuration file format. */
	const FORMAT_INI = 'ini';

	/** Defines which adapter should be used for parsing which format. */
	private $adapters = array(
		self::FORMAT_XML => 'LoggerConfigurationAdapterXML',
		self::FORMAT_INI => 'LoggerConfigurationAdapterINI',
		self::FORMAT_PHP => 'LoggerConfigurationAdapterPHP',
	);
	
	/** This configuration is used if no configuration file is provided. */
	private $defaultConfiguration = array(
        'threshold' => 'ALL',
        'rootLogger' => array(
            'level' => 'INFO',
            'appenders' => array('default'),
        ),
        'appenders' => array(
            'default' => array(
                'class' => 'LoggerAppenderEcho',
                'layout' => array(
                    'class' => 'LoggerLayoutSimple',
                ),
            ),
        ),
	);
	
	/**
	 * Starts logger configuration procedure.
	 * 
	 * If the config file cannot be loaded or parsed, reverts to the default 
	 * configuration contained in {@link $defaultConfiguration}.
	 * 
	 * @param LoggerHierarchy $hierarchy The hierarchy on which to perform 
	 * 		the configuration. 
	 * @param string|array $input Either path to the config file or the 
	 * 		configuration as an array. If not set, default configuration 
	 * 		will be used.
	 */
	public function configure(LoggerHierarchy $hierarchy, $input = null)
	{
		if (!isset($input)) {
			$config =  $this->defaultConfiguration;
		}
		
		else if (is_array($input)) {
			$config = $input;
		}
		
		else if (is_string($input)) {
			$config = $this->parseConfigFile($input);
		}
		
		else {
			throw new LoggerException("Invalid configuration param: " . var_export($input));
		}
		var_export($config);
		$this->doConfigure($hierarchy, $config);
	}
	
	/**
	 * Loads the configuration file from the given URL, determines which 
	 * adapter to use, converts the configuration to a PHP array and
	 * returns it.
	 * 
	 * @param string $url Path to the config file.
	 * @return The configuration from the config file, as a PHP array.
	 * @throws LoggerException If the configuration file cannot be loaded, or
	 * 		if the parsing fails.
	 */
	private function parseConfigFile($url)
	{
		$type = $this->getConfigType($url);
		$adapterClass = $this->adapters[$type];

		$adapter = new $adapterClass();
		return $adapter->convert($url);
	}
	
	/** Determines configuration file type based on the file extension. */
	private function getConfigType($url) {
		$info = pathinfo($url);
		$ext = strtolower($info['extension']);
		
		switch($ext) {
			case 'xml':
				return self::FORMAT_XML;
			
			case 'ini':
			case 'properties':
				return self::FORMAT_INI;
			
			case 'php':
				return self::FORMAT_PHP;
				
			default:
				throw new LoggerException("Unsupported configuration file extension: $ext");
		}
	}
	
	/**
	 * Constructs the logger hierarchy based on configuration.
	 * 
	 * @param LoggerHierarchy $hierarchy
	 * @param array $config
	 */
	private function doConfigure(LoggerHierarchy $hierarchy, $config) {
		
		if (isset($config['threshold'])) {
			$default = $hierarchy->getThreshold();
			$threshold = LoggerLevel::toLevel($config['threshold'], $default);
			$hierarchy->setThreshold($threshold);
		}
		
		if (isset($config['appenders']) && is_array($config['appenders'])) {
			foreach($config['appenders'] as $name => $appenderConfig) {
				$this->configureAppender($hierarchy, $name, $appenderConfig);
			}
		}
		
		if (isset($config['loggers']) && is_array($config['loggers'])) {
			foreach($config['loggers'] as $loggerConfig) {
				$this->configureLogger($hierarchy, $loggerConfig);
			}
		}
		
		if (isset($config['rootLogger'])) {
			$this->configureRootLogger($hierarchy, $config['rootLogger']);
		}
	}
	
	
	private function configureAppender(LoggerHierarchy $hierarchy, $name, $config) {
		$class = $config['class'];
		
		if (!class_exists($class)) {
			$this->warn("Invalid appender configuration [$name]: class [$class] does not exist.");
			return;
		}
		
		$appender = new $class();
		
		if (!($appender instanceof LoggerAppender)) {
			$this->warn("Invalid appender configuration [$name]: class [$class] is not a valid appender class.");
			return;
		}
		
		$appender->setName($name);
		
		if (isset($config['threshold'])) {
			$default = $appender->getThreshold();
			$threshold = LoggerOptionConverter::toLevel($config['threshold'], $default);
			$appender->setThreshold($threshold);
		}
		
		if ($appender->requiresLayout()) {
			$layout = $this->createAppenderLayout($name, $config);
			
			// If layout doesn't exist or is wrongly defined, revert to default layout
			if (!isset($layout)) {
				$layout = new LoggerLayoutSimple();
			}
			
			$appender->setLayout($layout);
		}
	}
	
	private function createAppenderLayout($name, $config) {
		if (isset($config['layout'])) {
			
			$class = $config['layout']['class'];
			
			if (!class_exists($class)) {
				$this->warn("Appender [$name]: layout [$class] does not exist. Reverting to LoggerLayoutSimple.");
				return;
			}
			
			$layout = new $class;
			
			if (!($layout instanceof LoggerLayout)) {
				$this->warn("Appender [$name]: class [$class] is not a valid layout class. Reverting to LoggerLayoutSimple.");
				return;
			}
			
			return $layout;
		}
	}
	
	private function configureLogger(LoggerHierarchy $hierarchy, $config) {
		
	}
	
	private function configureRootLogger(LoggerHierarchy $hierarchy, $config) {
		$root = $hierarchy->getRootLogger();
		
		if (isset($config['level'])) {
			$default = $root->getLevel();
			$level = LoggerOptionConverter::toLevel($config['level'], $default);
			$root->setLevel($level);
		}
		
		if (isset($config['appenders'])) {
			foreach($config['appenders'] as $appenderRef) {
				
			}
		}
	}
	
	private function warn($message)
	{
		trigger_error("log4php: $message", E_USER_WARNING);
	}
}