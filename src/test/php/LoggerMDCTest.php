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

/**
 * @group main
 */
class LoggerMDCTest extends PHPUnit_Framework_TestCase {
	
	/** A pattern with 1 key. */
	private $pattern1 = "%-5p %c: %X{key1} %m";
	
	/** A pattern with 2 keys. */
	private $pattern2 = "%-5p %c: %X{key1} %X{key2} %m";
	
	/** A pattern with 3 keys (one is numeric). */
	private $pattern3 = "%-5p %c: %X{key1} %X{key2} %X{3} %m";
	
	/** A pattern with a non-existant key. */
	private $pattern4 = "%-5p %c: %X{key_does_not_exist} %m";
	
	/** A pattern with an empty key. */
	private $pattern5 = "%-5p %c: %X{} %m";
	
	/** A pattern for testing values from $_ENV. */
	private $patternEnv = "%-5p %c: %X{env.TEST} %m";
	
	/**
	 * A pattern for testing values from $_SERVER. PHP_SELF chosen because it
	 * appears on both Linux and Windows systems. 
	 */
	private $patternServer = "%-5p %c: %X{server.PHP_SELF} %m";
	
	protected function setUp() {
		LoggerMDC::clear();
	}
	
	protected function tearDown() {
		LoggerMDC::clear();
	}
	
	public function testPatterns() {

		// Create some data to test with
		LoggerMDC::put('key1', 'valueofkey1');
		LoggerMDC::put('key2', 'valueofkey2');
		LoggerMDC::put(3, 'valueofkey3');
		
		$expected = array(
			'key1' => 'valueofkey1',
			'key2' => 'valueofkey2',
			3 => 'valueofkey3',
		);
		$actual = LoggerMDC::getMap();
		
		self::assertSame($expected, $actual);
		
		$event = new LoggerLoggingEvent("LoggerLayoutPattern", new Logger("TEST"), LoggerLevel::getLevelInfo(), "Test message");

		// Pattern with 1 key
		$actual = $this->formatEvent($event, $this->pattern1);
		$expected = "INFO  TEST: valueofkey1 Test message";
		self::assertEquals($expected, $actual);
		
		// Pattern with 2 keys
		$actual = $this->formatEvent($event, $this->pattern2);
		$expected = "INFO  TEST: valueofkey1 valueofkey2 Test message";
		self::assertEquals($expected, $actual);
		
		// Pattern with 3 keys (one numeric)
		$actual = $this->formatEvent($event, $this->pattern3);
		$expected = "INFO  TEST: valueofkey1 valueofkey2 valueofkey3 Test message";
		self::assertEquals($expected, $actual);
		
		// Pattern with non-existant key
		$actual = $this->formatEvent($event, $this->pattern4);
		$expected = "INFO  TEST:  Test message";
		self::assertEquals($expected, $actual);
		
		// Pattern with an empty key
    	$actual = $this->formatEvent($event, $this->pattern5);
		$expected = "INFO  TEST:  Test message";
		self::assertEquals($expected, $actual);
		
		// Test key removal
		LoggerMDC::remove('key1');
		$value = LoggerMDC::get('key1');
		self::assertEquals('', $value);
		
		// Pattern with 1 key, now removed
		$actual = $this->formatEvent($event, $this->pattern1);
		$expected = "INFO  TEST:  Test message";
		self::assertEquals($expected, $actual);
    }
    
    public function testEnvKey() {
    	
    	// Set an environment variable for testing
    	if (putenv('TEST=abc') === false) {
    		self::markTestSkipped("Unable to set environment variable for testing.");
    	}
    	
    	// Test reading of the set variable
    	self::assertEquals('abc', LoggerMDC::get('env.TEST'));
    	
    	// Test env variable in a pattern
    	$event = new LoggerLoggingEvent("LoggerLayoutPattern", new Logger("TEST"), LoggerLevel::getLevelInfo(), "Test message");
    	$actual = $this->formatEvent($event, $this->patternEnv);
		$expected = "INFO  TEST: abc Test message";
		self::assertEquals($expected, $actual);
		
		// Test reading a non-existant env variable
		self::assertEquals('', LoggerMDC::get('env.hopefully_this_variable_doesnt_exist'));
		
		// Test reading an empty env variable
		self::assertEquals('', LoggerMDC::get('env.'));
    }
    
    public function testServerKey() {
    	
    	// Test reading a server variable
    	$value = $_SERVER['PHP_SELF'];
    	self::assertEquals($value, LoggerMDC::get('server.PHP_SELF'));
    	
		// Test the server variable in a pattern
    	$event = new LoggerLoggingEvent("LoggerLayoutPattern", new Logger("TEST"), LoggerLevel::getLevelInfo(), "Test message");
    	$actual = $this->formatEvent($event, $this->patternServer);
		$expected = "INFO  TEST: $value Test message";
		self::assertEquals($expected, $actual);
		
		// Test reading a non-existant server variable
		self::assertEquals('', LoggerMDC::get('server.hopefully_this_variable_doesnt_exist'));
		
		// Test reading an empty server variable
		self::assertEquals('', LoggerMDC::get('server.'));
    }
    
	private function formatEvent($event, $pattern) {
		$layout = new LoggerLayoutPattern();
		$layout->setConversionPattern($pattern);
		return $layout->format($event);
	}
}

?>
