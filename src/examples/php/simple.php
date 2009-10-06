<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';

class Log4phpTest {
    private $_logger;
    
    public function __construct() {
        $this->_logger = Logger::getLogger('Log4phpTest');
        $this->_logger->debug('Hello!');
    }
}

function Log4phpTestFunction() {
    $logger = Logger::getLogger('Log4phpTestFunction');
    $logger->debug('Hello again!');    
}

$test = new Log4phpTest();
Log4phpTestFunction();
// END SNIPPET: doxia
