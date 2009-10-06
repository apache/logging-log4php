<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';
Logger::configure(dirname(__FILE__).'/../resources/ndc.properties');
$logger = Logger::getRootLogger();

LoggerNDC::push('conn=1234');
$logger->debug("just received a new connection");
LoggerNDC::push('client=ab23');
$logger->debug("some more messages that can");
$logger->debug("now related to a client");
LoggerNDC::pop();
LoggerNDC::pop();
$logger->debug("back and waiting for new connections");
// END SNIPPET: doxia
