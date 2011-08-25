<?php

interface LoggerConfigurationAdapter
{
	/** Converts the configuration file to PHP format usable by the configurator. */
	public function convert($input); 

}

?>