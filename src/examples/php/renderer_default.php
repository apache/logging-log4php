<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';
Logger::configure(dirname(__FILE__).'/../resources/renderer_default.properties');

class Person {
    public $firstName = 'John';
    public $lastName = 'Doe';

    public function __toString() {
        return $this->lastName . ', ' . $this->firstName;
    }
}

$person = new Person();

$logger = Logger::getRootLogger();
$logger->debug("Now comes the current MyClass object:");
$logger->debug($person);
// END SNIPPET: doxia
