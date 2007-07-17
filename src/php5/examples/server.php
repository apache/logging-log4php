<?php
define('LOG4PHP_DIR', dirname(__FILE__).'/../log4php');
define('LOG4PHP_CONFIGURATION', 'server.properties');
require_once LOG4PHP_DIR.'/LoggerManager.php';

require_once 'Net/Server.php';
require_once 'Net/Server/Handler.php';

class Net_Server_Handler_Log extends Net_Server_Handler {
  
        var $hierarchy;

        function onStart() {
                $this->hierarchy = LoggerManager::getLoggerRepository();
        }
  
        function onReceiveData($clientId = 0, $data = "") {
                $events = $this->getEvents($data);
                foreach($events as $event) {
                        $root = $this->hierarchy->getRootLogger();
                        if($event->getLoggerName() === 'root') {
                                $root->callAppenders($event);      
                        } else {
                                $loggers = $this->hierarchy->getCurrentLoggers();
                                foreach($loggers as $logger) {
                                        $root->callAppenders($event);
                                        $appenders = $logger->getAllAppenders();
                                        foreach($appenders as $appender) {
                                                $appender->doAppend($event);
                                        }
                                }
                        }
                }
        }
  
        function getEvents($data) {
                preg_match('/^(O:\d+)/', $data, $parts);
                $events = split($parts[1], $data);
                array_shift($events);
                $size = count($events);
                for($i=0; $i<$size; $i++) {
                        $events[$i] = unserialize($parts[1].$events[$i]);
                }
                return $events;
        }
}

$host = '127.0.0.1';
$port = 9090;
//$server =& Net_Server::create('fork', $host, $port);
$server =& Net_Server::create('sequential', $host, $port);
$handler =& new Net_Server_Handler_Log();
$server->setCallbackObject($handler);
$server->start();
?>
