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

class LoggerAppenderSocketTest extends PHPUnit_Framework_TestCase {
        
	public function testSocketSerialized() {
		$appender = new LoggerAppenderSocket("myname ");
		
		$layout = new LoggerLayoutSimple();
		$appender->setLayout($layout);
		$appender->setDry(true);
//		$appender->setUseXml(true);
		$appender->activateOptions();
		$event = new LoggerLoggingEvent("LoggerAppenderSocketTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		 
		ob_start();
		$appender->append($event);
		$v = ob_get_contents();
		ob_end_clean();
		$s = serialize($event);
		
		$e = "DRY MODE OF SOCKET APPENDER: ".$s;
		self::assertEquals($e, $v);
    }
    
    public function testSocketXml() {
		$appender = new LoggerAppenderSocket("myname ");
		
		$appender->setDry(true);
		$appender->setUseXml(true);
		$appender->setLocationInfo(true);
		$appender->activateOptions();
		$event = new LoggerLoggingEvent("LoggerAppenderSocketTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		 
		ob_start();
		$appender->append($event);
		$v = ob_get_contents();
		ob_end_clean();
		
		$layout = new LoggerLayoutXml();
		$layout->setLog4jNamespace(false);
		$layout->activateOptions();
		$a = $layout->format($event);
		$e = "DRY MODE OF SOCKET APPENDER: ".$a;
		self::assertEquals($e, $v);
    }
    
    /** Tests Exception due to unreachable remote host.
     * 
     * @expectedException LoggerException
     */
    public function testSocketProblem() {
        $appender = new LoggerAppenderSocket("myname ");
        $appender->setDry(false);
        $appender->setRemoteHost("does.not.exists");
        $appender->setPort(1234);
        $appender->activateOptions();
        $event = new LoggerLoggingEvent("LoggerAppenderSocketTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
        
        $appender->append($event);
    }
}
