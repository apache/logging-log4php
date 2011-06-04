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
 * @subpackage appenders
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

/** 
 * @group appenders
 *        
 * FIXME: Currently clutters the phpunit output as output buffering seems not
 *        to work for fwrite(STDOUT, ...)
 */
class LoggerAppenderConsoleTest extends PHPUnit_Framework_TestCase {
    
	private $event;
	
	public function setUp()
	{
		$logger = new Logger('mycategory');
		$level = LoggerLevel::getLevelWarn();
		$this->event = new LoggerLoggingEvent(__CLASS__, $logger, $level, "my message");
	}
	
	public function testRequiresLayout() {
		$appender = new LoggerAppenderConsole(); 
		self::assertTrue($appender->requiresLayout());
	}
	
    public function testSimpleStdOutLogging() {
    	$layout = new LoggerLayoutSimple();
    	
    	$appender = new LoggerAppenderConsole("mylogger"); 
    	$appender->setTarget('STDOUT');
		$appender->setLayout($layout);
		$appender->activateOptions();
		$appender->append($this->event);
		$appender->close();
    }

    public function testSimpleStdErrLogging() {
    	$layout = new LoggerLayoutSimple();
    	
    	$appender = new LoggerAppenderConsole("mylogger"); 
		$appender->setTarget('STDERR');
		$appender->setLayout($layout);
		$appender->activateOptions();
		$appender->append($this->event);
		$appender->close();
    }    

    public function testSimpleDefaultLogging() {
    	$layout = new LoggerLayoutSimple();
    	
    	$appender = new LoggerAppenderConsole("mylogger"); 
		$appender->setLayout($layout);
		$appender->activateOptions();
		$appender->append($this->event);
		$appender->close();
    }
}
