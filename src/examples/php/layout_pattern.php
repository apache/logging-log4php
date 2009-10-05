<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';

Logger::configure(dirname(__FILE__).'/../resources/layout_pattern.properties');
$logger = Logger::getRootLogger();
$logger->info("Hello World!");
$logger->debug("Second line");
// END SNIPPET: doxia
