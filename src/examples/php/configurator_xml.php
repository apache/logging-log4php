<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';

Logger::configure(dirname(__FILE__).'/../resources/configurator_xml.xml');
$logger = Logger::getRootLogger();
$logger->info("Hello World!");
// END SNIPPET: doxia

