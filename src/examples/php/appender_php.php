<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';
Logger::configure(dirname(__FILE__).'/../resources/appender_php.properties');
$logger = Logger::getRootLogger();
$logger->debug("Hello PHP!");
// END SNIPPET: doxia
