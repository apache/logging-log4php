<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
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
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link       http://logging.apache.org/log4php
 */

namespace Apache\Log4php\Tests;

use Apache\Log4php\Appenders\EchoAppender;
use Apache\Log4php\Appenders\NullAppender;
use Apache\Log4php\Filters\DenyAllFilter;
use Apache\Log4php\Filters\LevelMatchFilter;
use Apache\Log4php\Layouts\SimpleLayout;
use Apache\Log4php\Level;
use Apache\Log4php\Logger;
use Apache\Log4php\LoggingEvent;

/**
 * @group appenders
 */
class AppenderTest extends \PHPUnit_Framework_TestCase
{
    public function testThreshold()
    {
        $appender = new EchoAppender("LoggerAppenderTest");

        $layout = new SimpleLayout();
        $appender->setLayout($layout);

        $warn = Level::getLevelWarn();
        $appender->setThreshold($warn);
        $appender->activateOptions();

        $event = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelFatal(), "testmessage");
        ob_start();
        $appender->doAppend($event);
        $v = ob_get_contents();
        ob_end_clean();
        $e = "FATAL - testmessage" . PHP_EOL;
        self::assertEquals($e, $v);

        $event = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelError(), "testmessage");
        ob_start();
        $appender->doAppend($event);
        $v = ob_get_contents();
        ob_end_clean();
        $e = "ERROR - testmessage" . PHP_EOL;
        self::assertEquals($e, $v);

        $event = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelWarn(), "testmessage");
        ob_start();
        $appender->doAppend($event);
        $v = ob_get_contents();
        ob_end_clean();
        $e = "WARN - testmessage" . PHP_EOL;
        self::assertEquals($e, $v);

        $event = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelInfo(), "testmessage");
        ob_start();
        $appender->doAppend($event);
        $v = ob_get_contents();
        ob_end_clean();
        $e = "";
        self::assertEquals($e, $v);

        $event = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelDebug(), "testmessage");
        ob_start();
        $appender->doAppend($event);
        $v = ob_get_contents();
        ob_end_clean();
        $e = "";
        self::assertEquals($e, $v);
    }

    public function testGetThreshold()
    {
        $appender = new EchoAppender("LoggerAppenderTest");

        $layout = new SimpleLayout();
        $appender->setLayout($layout);

        $warn = Level::getLevelWarn();
        $appender->setThreshold($warn);

        $a = $appender->getThreshold();
        self::assertEquals($warn, $a);
    }

    public function testSetStringThreshold()
    {
        $appender = new EchoAppender("LoggerAppenderTest");

        $layout = new SimpleLayout();
        $appender->setLayout($layout);

        $warn = Level::getLevelWarn();
        $appender->setThreshold('WARN');
        $a = $appender->getThreshold();
        self::assertEquals($warn, $a);

        $e = Level::getLevelFatal();
        $appender->setThreshold('FATAL');
        $a = $appender->getThreshold();
        self::assertEquals($e, $a);

        $e = Level::getLevelError();
        $appender->setThreshold('ERROR');
        $a = $appender->getThreshold();
        self::assertEquals($e, $a);

        $e = Level::getLevelDebug();
        $appender->setThreshold('DEBUG');
        $a = $appender->getThreshold();
        self::assertEquals($e, $a);

        $e = Level::getLevelInfo();
        $appender->setThreshold('INFO');
        $a = $appender->getThreshold();
        self::assertEquals($e, $a);
    }

     public function testSetFilter()
     {
        $appender = new EchoAppender("LoggerAppenderTest");

        $layout = new SimpleLayout();
        $appender->setLayout($layout);

        $filter  = new DenyAllFilter();
        $appender->addFilter($filter);

        $filter2  = new LevelMatchFilter();
        $appender->addFilter($filter2);

        $first = $appender->getFilter();
        self::assertEquals($first, $filter);

        $next = $first->getNext();
        self::assertEquals($next, $filter2);

        $appender->clearFilters();
        $nullfilter = $appender->getFilter();
        self::assertNull($nullfilter);
    }

    public function testInstanciateWithLayout()
    {
        $appender = new EchoAppender("LoggerAppenderTest");

        $expected = "Apache\\Log4php\\Layouts\\SimpleLayout";
        $actual = $appender->getLayout();
        $this->assertInstanceof($expected, $actual);
    }

    public function testOverwriteLayout()
    {
        $layout = new SimpleLayout();
        $appender = new EchoAppender("LoggerAppenderTest");
        $appender->setLayout($layout);

        $actual = $appender->getLayout();
        $this->assertEquals($layout, $actual);
    }

    public function testRequiresNoLayout()
    {
        $appender = new NullAppender("LoggerAppenderTest");

        $actual = $appender->getLayout();
        $this->assertNull($actual);
    }
}
