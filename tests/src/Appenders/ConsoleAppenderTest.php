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

use Apache\Log4php\Logger;
use Apache\Log4php\Appenders\ConsoleAppender;

/**
 * @group appenders
 */
class ConsoleAppenderTest extends \PHPUnit_Framework_TestCase
{
    private $config = array(
        'rootLogger' => array(
            'appenders' => array('default'),
        ),
        'appenders' => array(
            'default' => array(
                'class' => 'ConsoleAppender',
                'layout' => array(
                    'class' => 'PatternLayout',
                    'params' => array(
                        // Intentionally blank so output doesn't clutter phpunit output
                        'conversionPattern' => ''
                    )
                ),
            )
        )
    );

    public function testRequiresLayout()
    {
        $appender = new ConsoleAppender();
        self::assertTrue($appender->requiresLayout());
    }

    public function testAppendDefault()
    {
        Logger::configure($this->config);
        $log = Logger::getRootLogger();

        $expected = ConsoleAppender::STDOUT;
        $actual = $log->getAppender('default')->getTarget();
        $this->assertSame($expected, $actual);

        $log->info("hello");
    }

    public function testAppendStdout()
    {
        $this->config['appenders']['default']['params']['target'] = 'stdout';

        Logger::configure($this->config);
        $log = Logger::getRootLogger();

        $expected = ConsoleAppender::STDOUT;
        $actual = $log->getAppender('default')->getTarget();
        $this->assertSame($expected, $actual);

        $log->info("hello");
    }

    public function testAppendStderr()
    {
        $this->config['appenders']['default']['params']['target'] = 'stderr';
        Logger::configure($this->config);
        $log = Logger::getRootLogger();
        $expected = ConsoleAppender::STDERR;

        $actual = $log->getAppender('default')->getTarget();
        $this->assertSame($expected, $actual);

        $log->info("hello");
    }
}
