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

require_once LOG4PHP_DIR . '/layouts/LoggerLayoutTTCC.php';
require_once LOG4PHP_DIR . '/appenders/LoggerAppenderConsole.php';
require_once LOG4PHP_DIR . '/LoggerHierarchy.php';

class LoggerHierarchyTest extends PHPUnit_Framework_TestCase {
        
        private $hierarchy;
        
        protected function setUp() {
                $this->hierarchy = LoggerHierarchy::singleton();
        }
        
        public function testIfLevelIsInitiallyLevelAll() {
                $this->assertEquals($this->hierarchy->getRootLogger()->getLevel()->levelStr, 'ALL');
        }

        public function testIfNameIsRoot() {
                $this->assertEquals($this->hierarchy->getRootLogger()->getName(), 'root');
        }

        public function testIfParentIsNull() {
                $this->assertSame($this->hierarchy->getRootLogger()->getParent(), null);
        }

        public function testSetParent() {
                $this->hierarchy->getRootLogger()->setParent('dummy');
                $this->testIfParentIsNull();
        }
        
        public function testResetConfiguration() {
                $root = $this->hierarchy->getRootLogger();
        $appender = new LoggerAppenderConsole('A1');
        $root->addAppender($appender);
        $logger = $this->hierarchy->getLogger('test');
        $this->assertEquals(sizeof($this->hierarchy->getCurrentLoggers()), 1);
        $this->hierarchy->resetConfiguration();
        $this->assertEquals($this->hierarchy->getRootLogger()->getLevel()->levelStr, 'DEBUG');
        $this->assertEquals($this->hierarchy->getThreshold()->levelStr, 'ALL');
        $this->assertEquals(sizeof($this->hierarchy->getCurrentLoggers()), 1);
        foreach($this->hierarchy->getCurrentLoggers() as $l) {
                $this->assertEquals($l->getLevel(), null);
                $this->assertTrue($l->getAdditivity());
                $this->assertEquals(sizeof($l->getAllAppenders()), 0);
        }
        }

}
?>
