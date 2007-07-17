<?php
define('LOG4PHP_DIR', dirname(__FILE__).'/../log4php');
define('LOG4PHP_CONFIGURATION', 'console.properties');

require_once LOG4PHP_DIR.'/LoggerManager.php';
$logger = LoggerManager::getRootLogger();
$logger->debug("Hello World!");
?>
