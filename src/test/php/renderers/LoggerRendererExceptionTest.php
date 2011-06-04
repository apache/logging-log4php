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

/**
 * @group renderers
 */
class LoggerRendererExceptionTest extends PHPUnit_Framework_TestCase {
	
	public function testRender() {
		$exRenderer = new LoggerRendererException();
		$ex1		= new LoggerRendererExceptionTestException('Message1');
		$ex2		= new LoggerRendererExceptionTestException('Message2', 0, $ex1);
		$ex3		= new LoggerRendererExceptionTestException('Message3', 0, $ex2);
		
        $rendered   = $exRenderer->render($ex3);
        
		$expected	= 3;        
		$result		= substr_count($rendered, 'Throwable(LoggerRendererExceptionTestException): Message');		
		$this->assertEquals($expected, $result);		
        
        $expected   = 2;        
        $result     = substr_count($rendered, 'Caused by: Throwable(LoggerRendererExceptionTestException):');        
        $this->assertEquals($expected, $result);
        
        $expected   = 1;        
        $result     = substr_count($rendered, 'Caused by: Throwable(LoggerRendererExceptionTestException): Message2');        
        $this->assertEquals($expected, $result);

        $expected   = 1;        
        $result     = substr_count($rendered, 'Caused by: Throwable(LoggerRendererExceptionTestException): Message1');        
        $this->assertEquals($expected, $result);                
	}
}

if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
	class LoggerRendererExceptionTestException extends Exception { }
} else {
	class LoggerRendererExceptionTestException extends Exception {
		
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