<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';

Logger::configure(dirname(__FILE__).'/../resources/appender_mail.properties');
$logger = Logger::getRootLogger();
$logger->fatal("Some critical message!");
$logger->fatal("Some more critical message!");
// END SNIPPET: doxia
