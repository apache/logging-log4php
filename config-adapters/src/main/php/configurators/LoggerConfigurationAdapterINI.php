<?php

class LoggerConfigurationAdapterINI implements LoggerConfigurationAdapter {
	
	const ROOT_LOGGER_NAME = "root";
	
	const THRESHOLD_PREFIX = "log4php.threshold";
	const ROOT_LOGGER_PREFIX = "log4php.rootLogger";
	const LOGGER_PREFIX = "log4php.logger.";
	
// 	const CATEGORY_PREFIX = "log4php.category.";
// 	const FACTORY_PREFIX = "log4php.factory";
	const ADDITIVITY_PREFIX = "log4php.additivity.";
	const ROOT_CATEGORY_PREFIX = "log4php.rootCategory";
	const APPENDER_PREFIX = "log4php.appender.";
	const RENDERER_PREFIX = "log4php.renderer.";
	
	private $config = array();
	
	private $properties;
	
	/**
	 * Loads and parses the INI configuration file.
	 * 
	 * INI_SCANNER_RAW is used here because otherwise parse_ini_file() will 
	 * try to parse all values, with some strange results. For example, "true"
	 * will become "1", while "false" and "null" will become "" (empty string). 
	 * 
	 * @see http://php.net/manual/en/function.parse-ini-file.php
	 * 
	 * @param string $path Path to the config file.
	 * @throws LoggerException
	 */
	private function load($path) {
		if(!file_exists($path)) {
			throw new LoggerException("Config file not found on given path: [$path].");	
		}
		
		$properties = @parse_ini_file($path, true, INI_SCANNER_RAW);
		if ($properties === false) {
			$error = error_get_last();
			throw new LoggerException("Error parsing configuration file: {$error['message']}");
		}
		
		$this->properties = $properties;
	}
	
	/**
	* Converts the provided INI configuration file to a PHP array config.
	*
	* @param string $path Path to the config file.
	* @throws LoggerException If the file cannot be loaded or parsed.
	*/
	public function convert($path) {
		// Load the configuration
		$this->load($path);
		
		// Parse threshold
		if (isset($this->properties[self::THRESHOLD_PREFIX])) {
			$this->config['threshold'] = $this->properties[self::THRESHOLD_PREFIX]; 
		}
		
		// Parse root logger
		if (isset($this->properties[self::ROOT_LOGGER_PREFIX])) {
			$this->parseLogger($this->properties[self::ROOT_LOGGER_PREFIX], self::ROOT_LOGGER_NAME);
		}
		
		$appenders = array();
		
		foreach($this->properties as $key => $value) {
			// Parse loggers
			if ($this->beginsWith($key, self::LOGGER_PREFIX)) {
				$name = substr($key, strlen(self::LOGGER_PREFIX));
				$this->parseLogger($property, $name);
			}
			
			// Parse appenders
			else if ($this->beginsWith($key, self::APPENDER_PREFIX)) {
				$this->parseAppender($key, $value);
			}
		}
		
		return $this->config;
	}
	
	
	/**
	 * Parses a logger property.
	 * 
	 * Loggers are defined in the following manner:
	 * <pre>
	 * log4php.logger.<name> = [<level>], [<appender-ref>, <appender-ref>, ...] 
	 * </pre>
	 * 
	 * Where:
	 * 	- level        - level to assign to the logger (optional)
	 * 	- appender-ref - name of the appenders to attach to the logger (optional)
	 * 
	 * @param string $property
	 * @param string $loggerName
	 */
	private function parseLogger($property, $loggerName) {
		// Values are divided by commas
		$values = explode(',', $property);
		
		if (empty($property) || empty($values)) {
			return;
		}

		// The first value is the logger level 
		$level = array_shift($values);
		
		// The remaining values are appender references 
		$appenders = array();
		while($appender = array_shift($values)) {
			$appender = trim($appender);
			if (!empty($appender)) {
				$appenders[] = trim($appender);
			}
		}

		$config = array(
			'level' => trim($level),
			'appenders' => $appenders
		);
		
		if ($loggerName == self::ROOT_LOGGER_NAME) {
			$this->config['rootLogger'] = $config; 
		} else {
			$this->config['loggers'][$loggerName] = $config;
		}
	}
	
	
	/**
	 * Parses an configuration line pertaining to an appender.
	 * 
	 * Parses the following patterns:
	 * 
	 * Appender class:
	 * <pre>
	 * log4php.appender.<name> = <class>
	 * </pre>
	 * 
	 * Appender parameter:
	 * <pre>
	 * log4php.appender.<name>.<param> = <value>
	 * </pre>
	 * 
 	 * Appender layout:
	 * <pre>
	 * log4php.appender.<name>.layout = <layoutClass>
	 * </pre>
	 * 
	 * Layout parameter:
	 * <pre>
	 * log4php.appender.<name>.layout.<param> = <value>
	 * </pre> 
	 * 
	 * For example, a full appender config might look like:
	 * <pre>
	 * log4php.appender.myAppender = LoggerAppenderConsole
	 * log4php.appender.myAppender.target = STDOUT
	 * log4php.appender.default.layout = LoggerLayoutPattern
	 * log4php.appender.default.layout.conversionPattern = "%d %c: %m%n"
	 * </pre>
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	private function parseAppender($key, $value) {

		// Remove the appender prefix from key
		$subKey = substr($key, strlen(self::APPENDER_PREFIX));
		
		// Divide the string by dots
		$parts = explode('.', $subKey);
		$count = count($parts);
		
		// The first part is always the appender name
		$name = trim($parts[0]);
		
		// Only one part - this line defines the appender class 
		if ($count == 1) {
			$this->config['appenders'][$name]['class'] = $value;
			return;
		}
		
		// Two parts - either an appender property or layout class
		else if ($count == 2) {
			
			if ($parts[1] == 'layout') {
				$this->config['appenders'][$name]['layout']['class'] = $value;
				return;
			} else {
				$this->config['appenders'][$name][$parts[1]] = $value;
				return;
			}
		}
		
		// Three parts - this can only be a layout property
		else if ($count == 3) {
			if ($parts[1] == 'layout') {
				$this->config['appenders'][$name]['layout'][$parts[2]] = $value;
				return;
			}
		}
		
		trigger_error("log4php: Error in config file \"$key = $value\". Skipped this line.");
	}
	
	private function beginsWith($str, $sub) {
		return (strncmp($str, $sub, strlen($sub)) == 0);
	}
	
	
}

?>