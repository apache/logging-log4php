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

use Apache\Log4php\Filters\AbstractFilter;
use Apache\Log4php\Filters\LevelMatchFilter;
use Apache\Log4php\Logger;

/**
 * @group filters
 */
class FilterLevelMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests all possible combinations of event level and filter levelToMatch
     * option, with acceptOnMatch set to TRUE.
     */
    public function testDecideAccept()
    {
        $filter = new LevelMatchFilter();
        $filter->setAcceptOnMatch("true");

        $levels = TestHelper::getAllLevels();
        $events = TestHelper::getAllEvents();

        foreach ($levels as $level) {
            $filter->setLevelToMatch($level);

            foreach ($events as $event) {
                // Expecting given level to be accepted, neutral for others
                $expected = ($event->getLevel() == $level) ? AbstractFilter::ACCEPT : AbstractFilter::NEUTRAL;
                $actual = $filter->decide($event);

                // Get string represenations for logging
                $sExpected = TestHelper::decisionToString($expected);
                $sActual = TestHelper::decisionToString($actual);

                $this->assertSame($expected, $actual, "Failed asserting filter decision for event level <$level>. Expected <$sExpected>, found <$sActual>.");
            }
        }
    }

    /**
     * Tests all possible combinations of event level and filter levelToMatch
     * option, with acceptOnMatch set to TRUE.
     */
    public function testDecideDeny()
    {
        $filter = new LevelMatchFilter();
        $filter->setAcceptOnMatch("false");

        $levels = TestHelper::getAllLevels();
        $events = TestHelper::getAllEvents();

        foreach ($levels as $level) {
            $filter->setLevelToMatch($level);

            foreach ($events as $event) {
                // Expecting given level to be denied, neutral for others
                $expected = ($event->getLevel() == $level) ? AbstractFilter::DENY : AbstractFilter::NEUTRAL;
                $actual = $filter->decide($event);

                // Get string represenations for logging
                $sExpected = TestHelper::decisionToString($expected);
                $sActual = TestHelper::decisionToString($actual);

                $this->assertSame($expected, $actual, "Failed asserting filter decision for event level <$level>. Expected <$sExpected>, found <$sActual>.");
            }
        }
    }

    /** Test that filter always decides NEUTRAL when levelToMatch is not set. */
    public function testDecideNull()
    {
        $filter = new LevelMatchFilter();
        $events = TestHelper::getAllEvents();

        foreach ($events as $event) {
            $expected = AbstractFilter::NEUTRAL;
            $actual = $filter->decide($event);

            // Get string represenations for logging
            $sExpected = TestHelper::decisionToString($expected);
            $sActual = TestHelper::decisionToString($actual);
            $level = $event->getLevel();

            $this->assertSame($expected, $actual, "Failed asserting filter decision for event level <$level>. Expected <$sExpected>, found <$sActual>.");
        }
    }

    /** Test logger configuration. */
    public function testAcceptConfig()
    {
        $config = TestHelper::getEchoConfig();

        // Add filters to default appender
        $config['appenders']['default']['filters'] = array(

            // Accepts only INFO events
            array(
                'class' => 'LevelMatchFilter',
                'params' => array(
                    'levelToMatch' => 'info',
                    'acceptOnMatch' => true
                )
            ),

            // Denies all events not accepted by first filter
            array(
                'class' => 'DenyAllFilter',
            )
        );

        Logger::configure($config);
        $logger = Logger::getRootLogger();

        ob_start();
        $logger->trace('Test');
        $logger->debug('Test');
        $logger->info('Test');
        $logger->warn('Test');
        $logger->error('Test');
        $logger->fatal('Test');

        $actual = ob_get_clean();

        $expected = "INFO - Test" . PHP_EOL;
    }

    public function testDenyConfig()
    {
        $config = TestHelper::getEchoConfig();

        // Add filter which denies INFO events
        $config['appenders']['default']['filters'] = array(
            array(
                'class' => 'LevelMatchFilter',
                'params' => array(
                    'levelToMatch' => 'info',
                    'acceptOnMatch' => false
                )
            )
        );

        Logger::configure($config);
        $logger = Logger::getRootLogger();

        ob_start();
        $logger->trace('Test');
        $logger->debug('Test');
        $logger->info('Test');
        $logger->warn('Test');
        $logger->error('Test');
        $logger->fatal('Test');

        $actual = ob_get_clean();

        // Should log all except info
        $expected =
            "TRACE - Test" . PHP_EOL .
            "DEBUG - Test" . PHP_EOL .
            "WARN - Test"  . PHP_EOL .
            "ERROR - Test" . PHP_EOL .
            "FATAL - Test" . PHP_EOL;

        $this->assertSame($expected, $actual);
    }
}
