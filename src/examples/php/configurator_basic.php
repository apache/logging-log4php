<?php
// START SNIPPET: doxia
define('LOG4PHP_CONFIGURATOR_CLASS', 'LoggerConfiguratorBasic'); 
require_once dirname(__FILE__).'/../../main/php/Logger.php';

$logger = Logger::getRootLogger();
$logger->info("Hello World!");
// END SNIPPET: doxia
