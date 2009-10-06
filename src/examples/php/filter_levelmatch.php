<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';

Logger::configure(dirname(__FILE__).'/../resources/filter_levelmatch.xml');
$logger = Logger::getRootLogger();
$logger->debug("Matching and will be rejected");
$logger->info("Not matching and will be accepted");
// END SNIPPET: doxia
