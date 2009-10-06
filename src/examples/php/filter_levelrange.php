<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';
 
Logger::configure(dirname(__FILE__).'/../resources/filter_levelrange.xml');
$logger = Logger::getRootLogger();
$logger->debug("This is a debug message");
$logger->info("This is an info message");
$logger->warn("This is a warning");
$logger->error("This is an error");
$logger->fatal("This is a fatal error");
// END SNIPPET: doxia
