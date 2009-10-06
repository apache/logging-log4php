<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';
Logger::configure(dirname(__FILE__).'/../resources/renderer_map.properties');

class Person {
    public $firstName = 'John';
    public $lastName = 'Doe';
}

class PersonRenderer implements LoggerRendererObject {
    public function render($o) {
        return $o->lastName.', '.$o->firstName;
    }
}

$person = new Person();

$logger = Logger::getRootLogger();
$logger->debug("Now comes the current MyClass object:");
$logger->debug($person);
// END SNIPPET: doxia
