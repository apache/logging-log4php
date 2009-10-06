<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';

Logger::configure(dirname(__FILE__).'/../resources/filter_denyall.xml');
$logger = Logger::getRootLogger();
$logger->info("Some text that will be discarded");
// END SNIPPET: doxia
