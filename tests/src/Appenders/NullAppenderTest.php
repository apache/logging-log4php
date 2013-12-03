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

use Apache\Log4php\Appenders\NullAppender;
use Apache\Log4php\Level;
use Apache\Log4php\Logger;
use Apache\Log4php\LoggingEvent;

/**
 * @group appenders
 */
class NullAppenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The Null appender does nothing - nothing to assert.
     * Just here for the sake of completness and a good testing ratio :-)
     */
    public function testActivateOptions()
    {
        $event = new LoggingEvent("LoggerAppenderNullTest", new Logger("TEST"), Level::getLevelInfo(), "testmessage");

        $appender = new NullAppender("TEST");
        $appender->activateOptions();
        $appender->append($event);
        $appender->close();
    }

    public function testRequiresLayout()
    {
        $appender = new NullAppender();
        self::assertFalse($appender->requiresLayout());
    }
}
