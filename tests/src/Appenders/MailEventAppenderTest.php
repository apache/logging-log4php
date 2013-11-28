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

namespace Apache\Log4php\Tests\Appenders;

use Apache\Log4php\Appenders\MailEventAppender;
use Apache\Log4php\Layouts\SimpleLayout;
use Apache\Log4php\Level;
use Apache\Log4php\Logger;
use Apache\Log4php\LoggingEvent;

/**
 * @group appenders
 */
class MailEventAppenderTest extends \PHPUnit_Framework_TestCase
{
    public function testRequiresLayout()
    {
        $appender = new MailEventAppender();
        self::assertTrue($appender->requiresLayout());
    }

    public function testMail()
    {
        $appender = new MailEventAppender("myname");

        $layout = new SimpleLayout();
        $appender->setLayout($layout);
        $appender->setDry(true);
        $appender->setTo('test@example.com');
        $appender->setFrom('Testsender');

        $appender->activateOptions();
        $event = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelError(), "testmessage");

        ob_start();
        $appender->append($event);
        $v = ob_get_contents();
        ob_end_clean();

        $e = "DRY MODE OF MAIL APP.: Send mail to: test@example.com with additional headers 'From: Testsender' and content: ERROR - testmessage".PHP_EOL;
        self::assertEquals($e, $v);
        $appender->close();
    }

    /**
     * Check an error is reported if 'to' is not set.
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Required parameter 'to' not set.
     */
    public function testEmptyTo()
    {
        $appender = new MailEventAppender("myname");
        $appender->setLayout(new SimpleLayout());
        $appender->setFrom('info@example.com');
        $appender->activateOptions();
    }

    /**
     * Check an error is reported if 'from' is not set.
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Required parameter 'from' not set.
     */
    public function testEmptyFrom()
    {
        $appender = new MailEventAppender("myname");
        $appender->setLayout(new SimpleLayout());
        $appender->setTo('info@example.com');
        $appender->activateOptions();
    }
}
