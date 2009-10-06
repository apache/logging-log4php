<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';

Logger::configure(dirname(__FILE__).'/../resources/filter_stringmatch.xml');
$logger = Logger::getRootLogger();
$logger->debug("Some text to match that will be rejected");
$logger->info("Some other text that will be accepted");
// END SNIPPET: doxia
