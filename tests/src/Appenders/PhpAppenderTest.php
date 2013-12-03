<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   tests
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link       http://logging.apache.org/log4php
 */

namespace Apache\Log4php\Tests\Appenders;

use Apache\Log4php\Appenders\PhpAppender;
use Apache\Log4php\Logger;

/**
 * @group appenders
 */
class PhpAppenderTest extends \PHPUnit_Framework_TestCase
{
    public static $expectedMessage;

    public static $expectedError;

    private $config = array(
        'rootLogger' => array(
            'appenders' => array('default'),
            'level' => 'trace'
        ),
        'appenders' => array(
            'default' => array(
                'class' => 'PhpAppender',
                'layout' => array(
                    'class' => 'SimpleLayout'
                ),
            )
        )
    );

    protected function setUp()
    {
        $that = $this; // hack for PHP 5.3
        set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($that) {
            $that::assertEquals($that::$expectedError, $errno);
            $that::assertEquals($that::$expectedMessage, $errstr);
        });
    }

    public function testRequiresLayout()
    {
        $appender = new PhpAppender();
        $this->assertTrue($appender->requiresLayout());
    }

    public function testPhp()
    {
        Logger::configure($this->config);
        $logger = Logger::getRootLogger();

        self::$expectedError = E_USER_ERROR;
        self::$expectedMessage = "FATAL - This is a test" . PHP_EOL;
        $logger->fatal("This is a test");

        self::$expectedError = E_USER_ERROR;
        self::$expectedMessage = "ERROR - This is a test" . PHP_EOL;
        $logger->error("This is a test");

        self::$expectedError = E_USER_WARNING;
        self::$expectedMessage = "WARN - This is a test" . PHP_EOL;
        $logger->warn("This is a test");

        self::$expectedError = E_USER_NOTICE;
        self::$expectedMessage = "INFO - This is a test" . PHP_EOL;
        $logger->info("This is a test");

        self::$expectedError = E_USER_NOTICE;
        self::$expectedMessage = "DEBUG - This is a test" . PHP_EOL;
        $logger->debug("This is a test");

        self::$expectedError = E_USER_NOTICE;
        self::$expectedMessage = "TRACE - This is a test" . PHP_EOL;
        $logger->trace("This is a test");
    }

    protected function tearDown()
    {
        restore_error_handler();
    }
}
