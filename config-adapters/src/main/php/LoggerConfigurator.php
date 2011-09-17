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
 * Configures log4php based on a provided configuration file or array.  
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
	
	/** Default configuration; used if no configuration file is provided. */
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
		// No input - use default configuration
		if (!isset($input)) {
			$config =  $this->defaultConfiguration;
		}
		
		// Array input - contains configuration within the array
		else if (is_array($input)) {
			$config = $input;
		}
		
		// String input - contains path to configuration file 
		else if (is_string($input)) {
			try {
				$config = $this->parseConfigFile($input);
			} catch (Exception $e) {
				$this->warn("Failed parsing configuration file: " . $e->getMessage());
				$config = $this->defaultConfiguration;
			}
		}
		
		// Anything else is an error
		else {
			$this->warn("Invalid configuration param given. Reverting to default configuration.");
			$config = $this->defaultConfiguration;
		}

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
	public function parseConfigFile($url)
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
			$threshold = LoggerLevel::toLevel($config['threshold']);
			if (isset($threshold)) {
				$hierarchy->setThreshold($threshold);
			} else {
				$this->warn("Invalid threshold {$config['threshold']} specified.");
			}
		}
		
		// Configure appenders and add them to the appender pool
		if (isset($config['appenders']) && is_array($config['appenders'])) {
			foreach($config['appenders'] as $name => $appenderConfig) {
				$this->configureAppender($hierarchy, $name, $appenderConfig);
			}
		}
		
		// Configure root logger 
		if (isset($config['rootLogger'])) {
			$this->configureRootLogger($hierarchy, $config['rootLogger']);
		}
		
		// Configure loggers
		if (isset($config['loggers']) && is_array($config['loggers'])) {
			foreach($config['loggers'] as $loggerName => $loggerConfig) {
				$this->configureOtherLogger($hierarchy, $loggerName, $loggerConfig);
			}
		}

		// Configure renderers
		if (isset($config['renderers']) && is_array($config['renderers'])) {
			foreach($config['renderers'] as $renderer) {
				$hierarchy->getRendererMap()->addRenderer($renderer['renderedClass'], $renderer['renderingClass']);
			}
		}
	}
	
	private function configureAppender(LoggerHierarchy $hierarchy, $name, $config) {
		$class = $config['class'];
		
		if (!class_exists($class)) {
			$this->warn("Class [$class] does not exist. Skipping appender [$name].");
			return;
		}
		
		$appender = new $class($name);
		if (!($appender instanceof LoggerAppender)) {
			$this->warn("[$class] is not a valid appender class. Skipping appender [$name].");
			return;
		}
		
		// Parse the threshold
		if (isset($config['threshold'])) {
			$threshold = LoggerLevel::toLevel($config['threshold']);
			if ($threshold instanceof LoggerLevel) {
				$appender->setThreshold($threshold);
			} else {
				$default = $appender->getThreshold();
				$this->warn("Invalid threshold value [{$config['threshold']}] specified for appender [$name]. Reverting to default value [$default].");
			}
		}
		
		// Parse the appender layout
		if ($appender->requiresLayout() && isset($config['layout'])) {
			$this->createAppenderLayout($appender, $config['layout']);
		}
		
		$appender->activateOptions();
		
		// Save appender in pool 
		LoggerAppenderPool::add($appender);
	}
	
	/**
	 * Parses layout config, creates the layout and links it to the appender.
	 * @param LoggerAppender $appender
	 * @param array $config Layout configuration options.
	 */
	private function createAppenderLayout(LoggerAppender $appender, $config) {
		$name = $appender->getName();
		$class = $config['class'];
		if (!class_exists($class)) {
			$this->warn("Nonexistant layout class [$class] specified for appender [$name]. Reverting to default layout.");
			return;
		}
		
		$layout = new $class;
		if (!($layout instanceof LoggerLayout)) {
			$this->warn("Invalid layout class [$class] sepcified for appender [$name]. Reverting to default layout.");
			return;
		}
		
		unset($config['class']);
		$this->setOptions($layout, $config);
		$layout->activateOptions();
		
		$appender->setLayout($layout);
	}
	
	private function configureRootLogger(LoggerHierarchy $hierarchy, $config) {
		$logger = $hierarchy->getRootLogger();
		$this->configureLogger($logger, $config);
	}
	
	private function configureOtherLogger(LoggerHierarchy $hierarchy, $name, $config) {
		// Get logger from hierarchy (this creates it if it doesn't already exist)
		$logger = $hierarchy->getLogger($name);
		$this->configureLogger($logger, $config);
	}
	
	private function configureLogger(Logger $logger, $config) {
		$name = $logger->getName();
		
		// Set logger level
		if (isset($config['level'])) {
			$level = LoggerLevel::toLevel($config['level']);
			if (isset($level)) {
				$logger->setLevel($level);
			} else {
				$this->warn("Invalid logger level [{$config['level']}] specified for logger [$name].");
			}
		}
		
		// Link appenders to logger
		if (isset($config['appenders'])) {
			foreach($config['appenders'] as $appenderName) {
				$appender = LoggerAppenderPool::get($appenderName);
				if (isset($appender)) {
					$logger->addAppender($appender);
				} else {
					$this->warn("Nonexistnant appender [$appenderName] linked to logger [$name].");
				}
			}
		}
		
		// Set logger additivity
		if (isset($config['additivity'])) {
			$additivity = LoggerOptionConverter::toBoolean($config['additivity'], null);
			if (is_bool($additivity)) {
				$logger->setAdditivity($additivity);
			} else {
				$this->warn("Invalid additivity value [{$config['additivity']}] specified for logger [$name].");
			}
		}
	}

	private function setOptions($object, $options) {
		foreach($options as $name => $value) {
			$setter = "set$name";
			if (method_exists($object, $setter)) {
				$object->$setter($value);
			} else {
				$class = get_class($object);
				$this->warn("Nonexistant option [$name] specified on [$class]. Skipping.");
			}
		}
	}
	
	/** Helper method to simplify error reporting. */
	private function warn($message) {
		trigger_error("log4php: $message", E_USER_WARNING);
	}
}