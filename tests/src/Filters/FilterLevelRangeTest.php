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

namespace Apache\Log4php\Tests;

use Apache\Log4php\Filters\AbstractFilter;
use Apache\Log4php\Filters\LevelRangeFilter;
use Apache\Log4php\Level;
use Apache\Log4php\Logger;
use Apache\Log4php\LoggingEvent;

/**
 * @group filters
 */
class FilterLevelRangeTest extends \PHPUnit_Framework_TestCase
{
    public function testDecide()
    {
        $filter = new LevelRangeFilter();
        $filter->setAcceptOnMatch("true");
        $filter->setLevelMin(Level::getLevelWarn());
        $filter->setLevelMax(Level::getLevelError());

        $eventError = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelError(), "testmessage");
        $eventDebug = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelDebug(), "testmessage");
        $eventWarn = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelWarn(), "testmessage");

        $result = $filter->decide($eventError);
        self::assertEquals($result, AbstractFilter::ACCEPT);

        $result = $filter->decide($eventDebug);
        self::assertEquals($result, AbstractFilter::DENY);

        $result = $filter->decide($eventWarn);
        self::assertEquals($result, AbstractFilter::ACCEPT);
    }

    public function testDecideAcceptFalse()
    {
        $filter = new LevelRangeFilter();
        $filter->setAcceptOnMatch("false");
        $filter->setLevelMin(Level::getLevelWarn());
        $filter->setLevelMax(Level::getLevelError());

        $eventError = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelError(), "testmessage");
        $eventDebug = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelDebug(), "testmessage");
        $eventWarn = new LoggingEvent("LoggerAppenderEchoTest", new Logger("TEST"), Level::getLevelWarn(), "testmessage");

        $result = $filter->decide($eventError);
        self::assertEquals($result, AbstractFilter::NEUTRAL);

        $result = $filter->decide($eventDebug);
        self::assertEquals($result, AbstractFilter::DENY);

        $result = $filter->decide($eventWarn);
        self::assertEquals($result, AbstractFilter::NEUTRAL);
    }
 }
