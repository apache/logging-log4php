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

class LoggerAppenderRollingFileTest extends PHPUnit_Framework_TestCase {
     
    protected function setUp() {
        if(file_exists('../../../target/temp/phpunit/TEST-rolling.txt')) {
        	unlink('../../../target/temp/phpunit/TEST-rolling.txt');
      	}
      	
      	if(file_exists('../../../target/temp/phpunit/TEST-rolling.txt.1')) {
        	unlink('../../../target/temp/phpunit/TEST-rolling.txt.1');
      	}
    }
    
    public function testSimpleLogging() {
    	$layout = new LoggerLayoutSimple();
    	
    	$appender = new LoggerAppenderRollingFile("mylogger"); 
		$appender->setFileName('../../../target/temp/phpunit/TEST-rolling.txt');
		$appender->setLayout($layout);
		$appender->setMaximumFileSize('1KB');
		$appender->activateOptions();
		
    	$event = new LoggerLoggingEvent('LoggerAppenderFileTest', 
    									new Logger('mycategory'), 
    									LoggerLevel::getLevelWarn(),
    									"my message123");
    	$i = 0;
    	$b = true;
    	while($b) {
    	    if($i == 1000) {
    	        $b = false;
    	    }
	    	$appender->append($event);
	    	$i++;
    	}
		
    	$event = new LoggerLoggingEvent('LoggerAppenderFileTest', 
    									new Logger('mycategory'), 
    									LoggerLevel::getLevelWarn(),
    									"my messageXYZ");
    	
    	$appender->append($event);
		
		$appender->close();
		
		$file = '../../../target/temp/phpunit/TEST-rolling.txt';
		$data = file($file);
		$line = $data[count($data)-1];
		$e = "WARN - my messageXYZ".PHP_EOL;
		self::assertEquals($e, $line);
		
		$file = '../../../target/temp/phpunit/TEST-rolling.txt.1';
		$data = file($file);
		$line = $data[count($data)-1];
		$e = "WARN - my message123".PHP_EOL;
		foreach($data as $r) {
			self::assertEquals($e, $r);
		}
    }
     
    protected function tearDown() {
//      	if(file_exists('../../../target/temp/phpunit/TEST-rolling.txt')) {
//        	unlink('../../../target/temp/phpunit/TEST-rolling.txt');
//      	}
//      	if(file_exists('../../../target/temp/phpunit/TEST-rolling.txt.1')) {
//        	unlink('../../../target/temp/phpunit/TEST-rolling.txt.1');
//      	}
//        rmdir('../../../target/temp/phpunit');
    }
}
