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
require_once LOG4PHP_DIR . '/LoggerBasicConfigurator.php';

class LoggerBasicConfiguratorTest extends PHPUnit_Framework_TestCase {
        
        protected function setUp() {
                LoggerBasicConfigurator::configure();
        }
        
        protected function tearDown() {
                LoggerBasicConfigurator::resetConfiguration();
        }
        
        public function testConfigure() {
                $root = LoggerManager::getRootLogger();
                $appender = $root->getAppender('A1');
                self::assertType('LoggerAppenderConsole', $appender);
                $layout = $appender->getLayout();
                self::assertType('LoggerLayoutTTCC', $layout);
        }
        
        public function testResetConfiguration() {
                throw new PHPUnit_Framework_IncompleteTestError();
                
                $this->testConfigure();
                
                //$root = LoggerManager::getRootLogger();
                
                $hierarchy = LoggerHierarchy::singleton();
                
                var_dump(count($hierarchy->getCurrentLoggers()));
                
                LoggerBasicConfigurator::resetConfiguration();
                
                var_dump(count($hierarchy->getCurrentLoggers()));
                
                /*
                $logger = LoggerManager::getLogger('A1');

                $layout = $logger->getLayout();
                var_dump($layout);
                
                var_dump($logger->getName());
                */
                //$appender = LoggerManager::getRootLogger()->getAppender('A1');
                //var_dump($appender);
                
        }
        
        /*public function testRootLogger() {
                $root = LoggerManager::getRootLogger();
                $a = $root->getAppender('A1');
                self::assertType('LoggerAppenderConsole', $a);
                $l = $a->getLayout();
                self::assertType('LoggerLayoutTTCC', $l);
        }*/

}
?>
