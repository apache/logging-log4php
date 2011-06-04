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
 * @group layouts
 */
class LoggerLayoutPatternTest extends PHPUnit_Framework_TestCase {

	/** Pattern used for testing. */
	private $pattern = "%d{Y-m-d H:i:s.u} %-5p %c (%C): %m in %F at %L%n";
	
	public function testErrorLayout() {
		$event = new LoggerLoggingEvent("LoggerLayoutXml", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");

		$v = $this->formatEvent($event, $this->pattern);
		$dt = $this->getEventDateTime($event);
		$e = "$dt ERROR TEST (LoggerLayoutXml): testmessage in NA at NA".PHP_EOL;
		self::assertEquals($e, $v);
    }
    
    public function testWarnLayout() {
		$event = new LoggerLoggingEvent("LoggerLayoutXml", new Logger("TEST"), LoggerLevel::getLevelWarn(), "testmessage");

		$v = $this->formatEvent($event, $this->pattern);
		$dt = $this->getEventDateTime($event);
		$e = "$dt WARN  TEST (LoggerLayoutXml): testmessage in NA at NA".PHP_EOL;
		self::assertEquals($e, $v);
    }
    
    public function testInfoLayout() {
		$event = new LoggerLoggingEvent("LoggerLayoutXml", new Logger("TEST"), LoggerLevel::getLevelInfo(), "testmessage");

		$v = $this->formatEvent($event, $this->pattern);
		$dt = $this->getEventDateTime($event);
		$e = "$dt INFO  TEST (LoggerLayoutXml): testmessage in NA at NA".PHP_EOL;
		self::assertEquals($e, $v);
    }
    
    public function testDebugLayout() {
		$event = new LoggerLoggingEvent("LoggerLayoutXml", new Logger("TEST"), LoggerLevel::getLevelDebug(), "testmessage");

		$v = $this->formatEvent($event, $this->pattern);
		$dt = $this->getEventDateTime($event);
		$e = "$dt DEBUG TEST (LoggerLayoutXml): testmessage in NA at NA".PHP_EOL;
		self::assertEquals($e, $v);
    }
    
    public function testTraceLayout() {
		$event = new LoggerLoggingEvent("LoggerLayoutXml2", new Logger("TEST"), LoggerLevel::getLevelTrace(), "testmessage");
		
		$v = $this->formatEvent($event, $this->pattern);
		$dt = $this->getEventDateTime($event);
		$e = "$dt TRACE TEST (LoggerLayoutXml2): testmessage in NA at NA".PHP_EOL;
		self::assertEquals($e, $v);
    }
    
    public function testClassnamePattern() {
		$event = new LoggerLoggingEvent("LoggerLayoutPatternTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");

		$v = $this->formatEvent($event, '%C');
		
		$dt = $this->getEventDateTime($event);
		$e = "LoggerLayoutPatternTest";
	
		self::assertEquals($e, $v);
    }
    
    /** 
     *  Returns the datetime of an event in "Y-m-d H:i:s.u" format. This is required because
     *  the PHP date() function does not handle the microseconds on windows (returns zeros).  
     *  
     *  @see http://www.php.net/manual/en/function.date.php#93752
     */
    private function getEventDateTime($event) {
    	
		$ts = $event->getTimeStamp();
		$micros = round(($ts - floor($ts)) * 1000); // microseconds to 3 decimal places
		$micros = str_pad($micros, 3, '0', STR_PAD_LEFT);
		return date('Y-m-d H:i:s', $ts).".$micros";
    }
    
	private function formatEvent($event, $pattern) {
		$layout = new LoggerLayoutPattern();
		$layout->setConversionPattern($pattern);
		return $layout->format($event);
	}
    
}
