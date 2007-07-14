<?php
define('LOG4PHP_DIR', dirname(__FILE__).'/../log4php');
define('LOG4PHP_CONFIGURATOR_CLASS', LOG4PHP_DIR.'/xml/LoggerDOMConfigurator');
define('LOG4PHP_CONFIGURATION', 'levelrangefilter.xml');

require_once LOG4PHP_DIR.'/LoggerManager.php';
$logger = LoggerManager::getRootLogger();
$logger->debug("This is a debug message");
$logger->info("This is an info message");
$logger->warn("This is a warning");
$logger->error("This is an error");
$logger->fatal("This is a fatal error");
?>