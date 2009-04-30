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
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

class LoggerHierarchyTest extends PHPUnit_Framework_TestCase {
        
	private $hierarchy;
        
	protected function setUp() {
		$this->hierarchy = LoggerHierarchy::singleton();
	}
	
	public function testIfLevelIsInitiallyLevelDebug() {
		self::assertEquals('DEBUG', $this->hierarchy->getRootLogger()->getLevel()->levelStr);
	}

	public function testIfNameIsRoot() {
		self::assertEquals('root', $this->hierarchy->getRootLogger()->getName());
	}

	public function testIfParentIsNull() {
		self::assertSame(null, $this->hierarchy->getRootLogger()->getParent());
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
		self::assertEquals(sizeof($this->hierarchy->getCurrentLoggers()), 1);
		$this->hierarchy->resetConfiguration();
		self::assertEquals($this->hierarchy->getRootLogger()->getLevel()->levelStr, 'DEBUG');
		self::assertEquals($this->hierarchy->getThreshold()->levelStr, 'ALL');
		self::assertEquals(sizeof($this->hierarchy->getCurrentLoggers()), 1);
		foreach($this->hierarchy->getCurrentLoggers() as $l) {
			self::assertEquals($l->getLevel(), null);
			self::assertTrue($l->getAdditivity());
			self::assertEquals(sizeof($l->getAllAppenders()), 0);
		}
	}

}
