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
 * @author     Marco Vassura
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

require_once dirname(__FILE__).'/phpunit.php';

require_once LOG4PHP_DIR . '/LoggerLevel.php';

/**
 * Tests the LoggerLevel
 */
class LoggerLevelTest extends PHPUnit_Framework_TestCase {
        
    protected function doTestLevel($o, $code, $str, $syslog) {
        $this->assertTrue( $o instanceof LoggerLevel );
        $this->assertEquals( $o->level, $code);
        $this->assertEquals( $o->levelStr, $str);
        $this->assertEquals( $o->syslogEquivalent, $syslog);
    }

        public function testLevelOff() {
                $this->doTestLevel( LoggerLevel::getLevelOff(), LOG4PHP_LEVEL_OFF_INT, 'OFF', 0 );
                $this->doTestLevel( LoggerLevel::toLevel(LOG4PHP_LEVEL_OFF_INT), LOG4PHP_LEVEL_OFF_INT, 'OFF', 0 );
                $this->doTestLevel( LoggerLevel::toLevel('OFF'), LOG4PHP_LEVEL_OFF_INT, 'OFF', 0 );
    }
    

        public function testLevelFatal() {
                $this->doTestLevel( LoggerLevel::getLevelFatal(), LOG4PHP_LEVEL_FATAL_INT, 'FATAL', 0 );
                $this->doTestLevel( LoggerLevel::toLevel(LOG4PHP_LEVEL_FATAL_INT), LOG4PHP_LEVEL_FATAL_INT, 'FATAL', 0 );
                $this->doTestLevel( LoggerLevel::toLevel('FATAL'), LOG4PHP_LEVEL_FATAL_INT, 'FATAL', 0 );
    }

        public function testLevelError() {
                $this->doTestLevel( LoggerLevel::getLevelError(), LOG4PHP_LEVEL_ERROR_INT, 'ERROR', 3 );
                $this->doTestLevel( LoggerLevel::toLevel(LOG4PHP_LEVEL_ERROR_INT), LOG4PHP_LEVEL_ERROR_INT, 'ERROR', 3 );
                $this->doTestLevel( LoggerLevel::toLevel('ERROR'), LOG4PHP_LEVEL_ERROR_INT, 'ERROR', 3 );
    }

        public function testLevelWarn() {
                $this->doTestLevel( LoggerLevel::getLevelWarn(), LOG4PHP_LEVEL_WARN_INT, 'WARN', 4 );
                $this->doTestLevel( LoggerLevel::toLevel(LOG4PHP_LEVEL_WARN_INT), LOG4PHP_LEVEL_WARN_INT, 'WARN', 4 );
                $this->doTestLevel( LoggerLevel::toLevel('WARN'), LOG4PHP_LEVEL_WARN_INT, 'WARN', 4 );
    }

        public function testLevelInfo() {
                $this->doTestLevel( LoggerLevel::getLevelInfo(), LOG4PHP_LEVEL_INFO_INT, 'INFO', 6 );
                $this->doTestLevel( LoggerLevel::toLevel(LOG4PHP_LEVEL_INFO_INT), LOG4PHP_LEVEL_INFO_INT, 'INFO', 6 );
                $this->doTestLevel( LoggerLevel::toLevel('INFO'), LOG4PHP_LEVEL_INFO_INT, 'INFO', 6 );
    }

        public function testLevelDebug() {
                $this->doTestLevel( LoggerLevel::getLevelDebug(), LOG4PHP_LEVEL_DEBUG_INT, 'DEBUG', 7 );
                $this->doTestLevel( LoggerLevel::toLevel(LOG4PHP_LEVEL_DEBUG_INT), LOG4PHP_LEVEL_DEBUG_INT, 'DEBUG', 7 );
                $this->doTestLevel( LoggerLevel::toLevel('DEBUG'), LOG4PHP_LEVEL_DEBUG_INT, 'DEBUG', 7 );
    }

        public function testLevelAll() {
                $this->doTestLevel( LoggerLevel::getLevelAll(), LOG4PHP_LEVEL_ALL_INT, 'ALL', 7 );
                $this->doTestLevel( LoggerLevel::toLevel(LOG4PHP_LEVEL_ALL_INT), LOG4PHP_LEVEL_ALL_INT, 'ALL', 7 );
                $this->doTestLevel( LoggerLevel::toLevel('ALL'), LOG4PHP_LEVEL_ALL_INT, 'ALL', 7 );
    }
}
?>
