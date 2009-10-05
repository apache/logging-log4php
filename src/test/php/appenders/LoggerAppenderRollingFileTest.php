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
    /** Directory for temporary files.
     * 
     * @var string
     */
    private $dir;
    
    protected function setUp() {
        $this->dir = dirname(__FILE__) . '/../../../../target/phpunit';
        @mkdir($this->dir);
        @unlink($this->dir.'/TEST-rolling.txt');
      	@unlink($this->dir.'/TEST-rolling.txt.1');
        @unlink($this->dir.'/TEST-rolling.txt.2');
    }
    
    public function testMaxFileSize() {
    	$appender = new LoggerAppenderRollingFile("mylogger"); 
    	
    	$e = $appender->setMaxFileSize('1KB');
    	self::assertEquals($e, '1024');
    	
    	$e = $appender->setMaxFileSize('2KB');
    	self::assertEquals($e, '2048');
    	
    	$e = $appender->setMaxFileSize('1MB');
    	self::assertEquals($e, '1048576');
    	
    	$e = $appender->setMaxFileSize('3MB');
    	self::assertEquals($e, '3145728');
    	
    	$e = $appender->setMaxFileSize('1GB');
    	self::assertEquals($e, '1073741824');
    	
    	$e = $appender->setMaxFileSize('10000');
    	self::assertEquals($e, '10000');
    	
    	$e = $appender->setMaxFileSize('BLUB');
    	self::assertEquals($e, '10000');
    	
    	$e = $appender->setMaxFileSize('100.5');
    	self::assertEquals($e, '100');
    	
    	$e = $appender->setMaxFileSize('1000.6');
    	self::assertEquals($e, '1000');
    	
    	$e = $appender->setMaxFileSize('1.5MB');
    	self::assertEquals($e, '1048576');
    }	
    
    public function testSetFileName() {
        $appender = new LoggerAppenderRollingFile("mylogger"); 
        $appender->setFileName($this->dir.'/../././phpunit/doesnotexist.log');
        $expandedFileName = self::readAttribute($appender, 'expandedFileName');
        self::assertEquals(1, preg_match('/\/target\/phpunit\/doesnotexist.log$/', $expandedFileName));
    }
    
    public function testSimpleLogging() {
    	$layout = new LoggerLayoutSimple();
    	
    	$appender = new LoggerAppenderRollingFile("mylogger"); 
		$appender->setFileName($this->dir.'/TEST-rolling.txt');
		$appender->setLayout($layout);
		$appender->setMaximumFileSize('1KB');
		$appender->setMaxBackupIndex(2);
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
		
		$file = $this->dir.'/TEST-rolling.txt';
		$data = file($file);
		$line = $data[count($data)-1];
		$e = "WARN - my messageXYZ".PHP_EOL;
		self::assertEquals($e, $line);
		
		$file = $this->dir.'/TEST-rolling.txt.1';
		$data = file($file);
		$line = $data[count($data)-1];
		$e = "WARN - my message123".PHP_EOL;
		foreach($data as $r) {
			self::assertEquals($e, $r);
		}
		
		$file = $this->dir.'/TEST-rolling.txt.2';
		$data = file($file);
		$line = $data[count($data)-1];
		$e = "WARN - my message123".PHP_EOL;
		foreach($data as $r) {
			self::assertEquals($e, $r);
		}
		
		if(file_exists($this->dir.'/TEST-rolling.txt.3')) {
		    self::assertTrue(false);
		}
    }
     
    protected function tearDown() {
      	@unlink($this->dir.'/TEST-rolling.txt');
      	@unlink($this->dir.'/TEST-rolling.txt.1');
      	@unlink($this->dir.'/TEST-rolling.txt.2');
        @rmdir($this->dir);
    }
    
}
