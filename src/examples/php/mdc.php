<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';

Logger::configure(dirname(__FILE__).'/../resources/mdc.properties');
LoggerMDC::put('username', 'knut');
$logger = Logger::getRootLogger();
$logger->debug("Testing MDC");
// END SNIPPET: doxia
