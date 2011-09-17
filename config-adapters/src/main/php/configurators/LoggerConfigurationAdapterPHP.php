<?php

/**
 * Loads configuration from an PHP file and parses it to a PHP array.
 */
class LoggerConfigurationAdapterPHP implements LoggerConfigurationAdapter
{
	public function convert($url)
	{
		if (!file_exists($url)) {
			throw new LoggerException("Invalid configuration file: does not exist.");
		}
		
		$data = @include($url);
		
		if ($config === false) {
			$error = error_get_last();
			throw new LoggerException("Error loading PHP configuration file: " . $error['message']);
		}
		
		if (empty($config)) {
			throw new LoggerException("Invalid PHP configuration file: does not return any data.");
		}
		
		if (!is_array($config)) {
			throw new LoggerException("Invalid PHP configuration file: does not return an array.");
		}
		
		return $config;
	}
}

?>