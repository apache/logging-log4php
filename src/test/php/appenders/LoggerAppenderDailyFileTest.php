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

class LoggerAppenderDailyFileTest extends PHPUnit_Framework_TestCase {
    
    private $t1;
    private $t2;
     
    protected function setUp() {
    	$today = date("Ymd");
        if(file_exists('../../../target/temp/phpunit/TEST-daily.txt.'.$today)) {
	        unlink('../../../target/temp/phpunit/TEST-daily.txt.'.$today);
        }
    }
    
    public function testSimpleLogging() {
    	$layout = new LoggerLayoutSimple();
    	
    	$event = new LoggerLoggingEvent('LoggerAppenderFileTest', 
    									new Logger('mycategory'), 
    									LoggerLevel::getLevelWarn(),
    									"my message");
    	
    	$appender = new LoggerAppenderDailyFile("mylogger"); 
		$appender->setFile('../../../target/temp/phpunit/TEST-daily.txt.%s');
		$appender->setLayout($layout);
		$appender->activateOptions();
		$appender->append($event);
		$appender->close();

		$this->t1 = date("Ymd");
		$v = file_get_contents('../../../target/temp/phpunit/TEST-daily.txt.'.$this->t1);		
		$e = "WARN - my message".PHP_EOL;
		self::assertEquals($e, $v);
    }
     
    public function testChangedDateFormat() {
    	$layout = new LoggerLayoutSimple();
    	
    	$event = new LoggerLoggingEvent('LoggerAppenderFileTest', 
    									new Logger('mycategory'), 
    									LoggerLevel::getLevelWarn(),
    									"my message");
    	
    	$appender = new LoggerAppenderDailyFile("mylogger"); 
    	$appender->setDatePattern('Y');
		$appender->setFile('../../../target/temp/phpunit/TEST-daily.txt.%s');
		$appender->setLayout($layout);
		$appender->activateOptions();
		$appender->append($event);
		$appender->close();

		$this->t2 = date("Y");
		$v = file_get_contents('../../../target/temp/phpunit/TEST-daily.txt.'.$this->t2);		
		$e = "WARN - my message".PHP_EOL;
		self::assertEquals($e, $v);
    } 
     
    protected function tearDown() {
    	if(file_exists('../../../target/temp/phpunit/TEST-daily.txt.'.$this->t1)) {
    		unlink('../../../target/temp/phpunit/TEST-daily.txt.'.$this->t1);
    	}
    	if(file_exists('../../../target/temp/phpunit/TEST-daily.txt.'.$this->t2)) {
    		unlink('../../../target/temp/phpunit/TEST-daily.txt.'.$this->t2);
    	}
        //rmdir('../../../target/temp/phpunit');
    }
}
