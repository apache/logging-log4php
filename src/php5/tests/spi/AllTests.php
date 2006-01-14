<?php
/**
 * Copyright 1999,2004 The Apache Software Foundation.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * @category   tests   
 * @package    log4php
 * @author     Marco Vassura
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

/** */
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'spi_AllTests::main');
}
if (!defined('LOG4PHP_DIR')) {
    define('LOG4PHP_DIR', '../log4php');
}

if (!defined('LOG4PHP_DEFAULT_INIT_OVERRIDE')) {
    define('LOG4PHP_DEFAULT_INIT_OVERRIDE', true);
}

require_once('PHPUnit2/Framework/TestSuite.php');
require_once('PHPUnit2/TextUI/TestRunner.php');

require_once('spi/LoggerLoggingEventTestCase.php');

class spi_AllTests {

    public static function main() {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite() {
        $suite = new PHPUnit2_Framework_TestSuite('spi');

        $suite->addTestSuite('LoggerLoggingEventTestCase');
        
        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'spi_AllTests::main') {
    spi_AllTests::main();
}
?>
