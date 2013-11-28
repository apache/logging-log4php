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
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link       http://logging.apache.org/log4php
 */

namespace Apache\Log4php\Tests\Layouts;

use Apache\Log4php\LoggingEvent;
use Apache\Log4php\Logger;
use Apache\Log4php\Level;
use Apache\Log4php\Layouts\HtmlLayout;

/**
 * @group layouts
 */
class HtmlLayoutTest extends \PHPUnit_Framework_TestCase
{
    public function testErrorLayout()
    {
        $event = new LoggingEvent("HtmlLayoutTest", new Logger("TEST"), Level::getLevelError(), "testmessage");

        $layout = new HtmlLayout();
        $v = $layout->format($event);

        $e = PHP_EOL."<tr>".PHP_EOL.
            "<td>".round(1000*$event->getRelativeTime())."</td>".PHP_EOL.
            "<td title=\"".$event->getThreadName()." thread\">".$event->getThreadName()."</td>".PHP_EOL.
            "<td title=\"Level\">ERROR</td>".PHP_EOL.
            "<td title=\"TEST category\">TEST</td>".PHP_EOL.
            "<td title=\"Message\">testmessage</td>".PHP_EOL.
            "</tr>".PHP_EOL;

        self::assertEquals($v, $e);
    }

    public function testWarnLayout()
    {
        $event = new LoggingEvent("HtmlLayoutTest", new Logger("TEST"), Level::getLevelWarn(), "testmessage");

        $layout = new HtmlLayout();
        $v = $layout->format($event);

        $e = PHP_EOL."<tr>".PHP_EOL.
            "<td>".round(1000*$event->getRelativeTime())."</td>".PHP_EOL.
            "<td title=\"".$event->getThreadName()." thread\">".$event->getThreadName()."</td>".PHP_EOL.
            "<td title=\"Level\"><font color=\"#993300\"><strong>WARN</strong></font></td>".PHP_EOL.
            "<td title=\"TEST category\">TEST</td>".PHP_EOL.
            "<td title=\"Message\">testmessage</td>".PHP_EOL.
            "</tr>".PHP_EOL;

        self::assertEquals($v, $e);
    }

    public function testContentType()
    {
        $layout = new HtmlLayout();
        $v = $layout->getContentType();
        $e = "text/html";
        self::assertEquals($v, $e);
    }

    public function testTitle()
    {
        $layout = new HtmlLayout();
        $v = $layout->getTitle();
        $e = "Log4php Log Messages";
        self::assertEquals($v, $e);

        $layout->setTitle("test");
        $v = $layout->getTitle();
        $e = "test";
        self::assertEquals($v, $e);
    }

     public function testHeader()
     {
        $layout = new HtmlLayout();
        $v = $layout->getHeader();
        self::assertTrue(strpos($v, "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">") === 0);
    }

    public function testFooter()
    {
        $layout = new HtmlLayout();
        $v = $layout->getFooter();
        self::assertTrue(strpos($v, "</table>") === 0);
    }
}
