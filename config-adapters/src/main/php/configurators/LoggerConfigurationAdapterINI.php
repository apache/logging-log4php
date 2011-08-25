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
	 * Parses an appender.
	 * 
	 * Appenders are defined:
	 * <pre>
	 * log4php.appender.<appender> = <class>
	 * log4php.appender.<appender>.<param> = <value>
	 * </pre>
	 * 
 	 * Appender layout is defined:
	 * <pre>
	 * log4php.appender.<appender>.layout = <layoutClass>
	 * log4php.appender.<appender>.layout.<param> = <value>
	 * </pre> 
	 * 
	 * Legend:
	 * 		- appender - name of the appender
	 * 		- class - the appender class to use
	 * 		- param - name of a configurable parameter
	 * 		- value - value of the configurable parameter
	 * 
	 * For example, a full appender config might look like:
	 * <pre>
	 * log4php.appender.myAppender = LoggerAppenderConsole
	 * log4php.appender.myAppender.target = STDOUT
	 * log4php.appender.myAppender.layout = LoggerLayoutSimple
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
		
		// No dots in key - this line defines the appender class 
		if ($count == 1) {
			$name = trim($parts[0]);
			$this->config['appenders'][$name]['class'] = $value;
		}
		
		// Dot(s) in key - this line defines an appender property.
		else {
			
			// Layouts may have their own properties
			if ($parts[0] == 'layout') {
				
				
			}
			
			
			
		}
		
		// No dot in key - this is the appender class 
		if (!strpos($appenderKey, '.')) {
			
		} 
			
		// Dot in key - this is an appender property.
		else {
			
		}
		
		
		
		
		// Remove the appender prefix from key to get name
		$appenderName = substr($key, strlen(self::APPENDER_PREFIX));
		
		if (strpos($appenderName, '.')) {
			return;
		}
		
		$appender = array(
			'class' => $value
		);
		
		// Iterate over params and search for params linked to this appender
		foreach($this->properties as $pKey => $pValue) {
			
			// Detect layout
			if($pKey == $appenderName . '.layout') {
				$layout = $this->parseLayout($pKey, $pValue);
			} 
			
			// Detect other parameters 
			else if ($this->beginsWith($pKey, $key) && $pKey != $key) {
				$paramName = substr($pKey, strlen($key) + 1);
				$appender[$paramName] = $pValue;
			}
		}
		
		$this->config['appenders'][$appenderName] = $appender; 

	
	}
	
	private function beginsWith($str, $sub) {
		return (strncmp($str, $sub, strlen($sub)) == 0);
	}
	
	
}

?>