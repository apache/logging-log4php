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

class LoggerLayoutHtmlTest extends PHPUnit_Framework_TestCase {
        
	public function testSimpleLayout() {
		$event = new LoggerLoggingEvent("LoggerLayoutHtmlTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");

		$layout = new LoggerLayoutHtml();
		$v = $layout->format($event);

		$e = PHP_EOL."<tr>
<td>".$event->getTime()."</td>
<td title=\"".$event->getThreadName()." thread\">".$event->getThreadName()."</td>
<td title=\"Level\">ERROR</td>
<td title=\"TEST category\">TEST</td>
<td title=\"Message\">testmessage</td>
</tr>".PHP_EOL;
		
		self::assertEquals($v, $e);
    }
    
}
