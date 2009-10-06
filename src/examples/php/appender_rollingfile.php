<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';
Logger::configure(dirname(__FILE__).'/../resources/appender_rollingfile.properties');

$logger = Logger::getRootLogger();
$logger->debug("Hello World!");
// END SNIPPET: doxia
