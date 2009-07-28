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
 * @subpackage renderers
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

class LoggerConfiguratorBasicTest extends PHPUnit_Framework_TestCase {
        
	protected function setUp() {
	    LoggerConfiguratorBasic::configure();
	}
        
	protected function tearDown() {
		LoggerConfiguratorBasic::resetConfiguration();
	}
        
	public function testConfigure() {
		$root = LoggerManager::getRootLogger();
		$appender = $root->getAppender('A1');
		self::assertType('LoggerAppenderConsole', $appender);
		$layout = $appender->getLayout();
		self::assertType('LoggerLayoutTTCC', $layout);
	}

	public function testResetConfiguration() {
		$root = LoggerManager::getRootLogger();
		$appender = $root->getAppender('A1');
		self::assertType('LoggerAppenderConsole', $appender);
		$layout = $appender->getLayout();
		self::assertType('LoggerLayoutTTCC', $layout);
		
		// As PHPUnit runs all tests in one run, there might be some loggers left over
		// from previous runs. ResetConfiguration() only clears the appenders, it does
		// not remove the categories!
		LoggerConfiguratorBasic::resetConfiguration();
		$hierarchy = LoggerHierarchy::singleton();
        foreach ($hierarchy->getCurrentLoggers() as $logger) {
            self::assertEquals(0, count($logger->getAllAppenders()));
        }		

        // This on the other hand really removes the categories:
        $hierarchy->clear(); 
		self::assertEquals(0, count($hierarchy->getCurrentLoggers()));
		self::assertEquals(0, count($hierarchy->getCurrentLoggers()));
	}
}
