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
 * @package    log4php
 * @subpackage spi
 * @author     Marco Vassura
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

/**  */
require_once 'PHPUnit/Framework/TestCase.php';

require_once LOG4PHP_DIR.'/appenders/LoggerAppenderNull.php';
require_once LOG4PHP_DIR.'/spi/LoggerLoggingEvent.php';
require_once LOG4PHP_DIR.'/LoggerHierarchy.php';
require_once LOG4PHP_DIR.'/LoggerLayout.php';

class LoggerLoggingEventTestCaseAppender extends LoggerAppenderNull {
        
        protected $requiresLayout = true;

        protected function append($event) {
                $this->layout->format($event);
        }

}

class LoggerLoggingEventTestCaseLayout extends LoggerLayout { 
        
        public function activateOptions() {
                return;
        }
        
        public function format($event) {
                LoggerLoggingEventTest::$locationInfo = $event->getLocationInformation();
        }
}

class LoggerLoggingEventTest extends PHPUnit_Framework_TestCase {
        
        public static $locationInfo;

        public function testConstructWithLoggerName() {
                $l = LoggerLevel :: getLevelDebug();
                $e = new LoggerLoggingEvent('fqcn', 'TestLogger', $l, 'test');
                $this->assertEquals($e->getLoggerName(), 'TestLogger');
        }

        public function testConstructWithTimestamp() {
                $l = LoggerLevel :: getLevelDebug();
                $timestamp = microtime(true);
                $e = new LoggerLoggingEvent('fqcn', 'TestLogger', $l, 'test', $timestamp);
                $this->assertEquals($e->getTimeStamp(), $timestamp);
        }

        public function testGetStartTime() {
                $time = LoggerLoggingEvent :: getStartTime();
                $this->assertType('float', $time);
                $time2 = LoggerLoggingEvent :: getStartTime();
                $this->assertEquals($time, $time2);
        }

        public function testGetLocationInformation() {
                $hierarchy = LoggerHierarchy :: singleton();
                $root = $hierarchy->getRootLogger();

                $a = new LoggerLoggingEventTestCaseAppender('A1');
                $a->setLayout( new LoggerLoggingEventTestCaseLayout() );
                $root->addAppender($a);
                
                $logger = $hierarchy->getLogger('test');

                $line = __LINE__; $logger->debug('test');
                $hierarchy->shutdown();
                
                $li = self::$locationInfo;
                
                $this->assertEquals($li->getClassName(), get_class($this));
                $this->assertEquals($li->getFileName(), __FILE__);
                $this->assertEquals($li->getLineNumber(), $line);
                $this->assertEquals($li->getMethodName(), __FUNCTION__);

        }

}
?>

