<?php
require_once dirname(__FILE__).'/../phpunit.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'or_AllTests::main');
}

require_once 'LoggerDefaultRendererTest.php';
require_once 'LoggerRendererMapTest.php';

class or_AllTests {

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('log4php.or');

        $suite->addTestSuite('LoggerDefaultRendererTest');
		$suite->addTestSuite('LoggerRendererMapTest');
        
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'or_AllTests::main') {
    or_AllTests::main();
}
?>
