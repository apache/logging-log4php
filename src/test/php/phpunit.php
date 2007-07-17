<?php

if (!defined('LOG4PHP_DIR')) {
    define('LOG4PHP_DIR', dirname(__FILE__).'/../../main/php');
}

if (!defined('LOG4PHP_DEFAULT_INIT_OVERRIDE')) {
    define('LOG4PHP_DEFAULT_INIT_OVERRIDE', true);
}

set_include_path(LOG4PHP_DIR.':'.get_include_path());

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';


?>
