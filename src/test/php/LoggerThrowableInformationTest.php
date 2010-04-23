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

class LoggerThrowableInformationTest extends PHPUnit_Framework_TestCase {
	
	public function testConstructor1() {
		$rep  = array(
			'Message1',
			'Message2',
			'Message3'
		);		  
		$tInfo	  = new LoggerThrowableInformation($rep);
		
		$expected = $rep;
		$result	  = $tInfo->getStringRepresentation();
		$this->assertEquals($expected, $result);
	}
	
	public function testConstructor2() {
		$ex	   = new LoggerThrowableInformationTestException('Message1');
		$tInfo = new LoggerThrowableInformation($ex);
		
		$expected = array('Message1');
		$result	  = $tInfo->getStringRepresentation();
		$this->assertEquals($expected, $result);
	}
	
	public function testConstructor3() {
		$ex		= new LoggerThrowableInformationTestException('Message1');
		$logger = Logger::getLogger('test');
		$tInfo	= new LoggerThrowableInformation($ex, $logger);
		
		$expected = array('Message1');
		$result	  = $tInfo->getStringRepresentation();
		$this->assertEquals($expected, $result);	  
	}
	
	public function testInvalidConstructor() {
		try {
			$tInfo = new LoggerThrowableInformation('test');
		} catch (InvalidArgumentException $ex) {
			return;
		}
		
		$this->fail('Invalid constructor params should raise Exception');
	}
	
	public function testExceptionChain() {
		$ex1 = new LoggerThrowableInformationTestException('Message1');
		$ex2 = new LoggerThrowableInformationTestException('Message2', 0, $ex1);
		$ex3 = new LoggerThrowableInformationTestException('Message3', 0, $ex2);

		$tInfo	  = new LoggerThrowableInformation($ex3);
		$expected = array(
			'Message3',
			'Message2',
			'Message1'
		);
		$result	 = $tInfo->getStringRepresentation();
		$this->assertEquals($expected, $result);
	}
}


if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
	class LoggerThrowableInformationTestException extends Exception { }
} else {
	class LoggerThrowableInformationTestException extends Exception {
		
		protected $previous;
		
		public function __construct($message = '', $code = 0, Exception $previous = null) {
			parent::__construct($message, $code);
			$this->previous = $previous;
		}
		
		public function getPrevious() {
			return $this->previous;
		}
	}
}
?>