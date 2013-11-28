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

use Apache\Log4php\Tests\TestHelper;

use Apache\Log4php\Layouts\XmlLayout;
use Apache\Log4php\NDC;
use Apache\Log4php\MDC;

/**
 * @group layouts
 */
class XmlLayoutTest extends \PHPUnit_Framework_TestCase {

	public function testErrorLayout() {
		$event = TestHelper::getErrorEvent("testmessage");

		$layout = new XmlLayout();
		$layout->activateOptions();

		$actual = $layout->format($event);

		$thread = $event->getThreadName();
		$timestamp = number_format(($event->getTimeStamp() * 1000), 0, '', '');

		$expected = "<log4php:event logger=\"test\" level=\"ERROR\" thread=\"$thread\" timestamp=\"$timestamp\">" . PHP_EOL .
			"<log4php:message><![CDATA[testmessage]]></log4php:message>" . PHP_EOL .
			"<log4php:locationInfo class=\"Apache\\Log4php\\LoggingEvent\" file=\"NA\" line=\"NA\" " .
			"method=\"getLocationInformation\" />" . PHP_EOL .
			"</log4php:event>" . PHP_EOL;

		self::assertEquals($expected, $actual);
	}

	public function testWarnLayout() {
		$event = TestHelper::getWarnEvent("testmessage");

		$layout = new XmlLayout();
		$layout->activateOptions();

		$actual = $layout->format($event);

		$thread = $event->getThreadName();
		$timestamp = number_format(($event->getTimeStamp() * 1000), 0, '', '');

		$expected = "<log4php:event logger=\"test\" level=\"WARN\" thread=\"$thread\" timestamp=\"$timestamp\">" . PHP_EOL .
			"<log4php:message><![CDATA[testmessage]]></log4php:message>" . PHP_EOL .
			"<log4php:locationInfo class=\"Apache\\Log4php\\LoggingEvent\" file=\"NA\" line=\"NA\" "  .
			"method=\"getLocationInformation\" />" . PHP_EOL .
			"</log4php:event>" . PHP_EOL;

		self::assertEquals($expected, $actual);
	}

	public function testLog4JNamespaceErrorLayout() {
		$event = TestHelper::getErrorEvent("testmessage");

		$layout = new XmlLayout();
		$layout->setLog4jNamespace(true);
		$layout->activateOptions();

		$actual = $layout->format($event);

		$thread = $event->getThreadName();
		$timestamp = number_format(($event->getTimeStamp() * 1000), 0, '', '');

		$expected = "<log4j:event logger=\"test\" level=\"ERROR\" thread=\"$thread\" timestamp=\"$timestamp\">" . PHP_EOL .
			"<log4j:message><![CDATA[testmessage]]></log4j:message>" . PHP_EOL .
			"<log4j:locationInfo class=\"Apache\\Log4php\\LoggingEvent\" file=\"NA\" line=\"NA\" "  .
			"method=\"getLocationInformation\" />" . PHP_EOL .
			"</log4j:event>" . PHP_EOL;

		self::assertEquals($expected, $actual);
	}

	public function testNDC()
	{
		NDC::push('foo');
		NDC::push('bar');

		$event = TestHelper::getErrorEvent("testmessage");

		$layout = new XmlLayout();
		$layout->activateOptions();

		$actual = $layout->format($event);

		$thread = $event->getThreadName();
		$timestamp = number_format(($event->getTimeStamp() * 1000), 0, '', '');

		$expected = "<log4php:event logger=\"test\" level=\"ERROR\" thread=\"$thread\" timestamp=\"$timestamp\">" . PHP_EOL .
			"<log4php:message><![CDATA[testmessage]]></log4php:message>" . PHP_EOL .
			"<log4php:NDC><![CDATA[<![CDATA[foo bar]]>]]></log4php:NDC>"  .  PHP_EOL  .
			"<log4php:locationInfo class=\"Apache\\Log4php\\LoggingEvent\" file=\"NA\" line=\"NA\" "  .
			"method=\"getLocationInformation\" />" . PHP_EOL .
			"</log4php:event>" . PHP_EOL;

		self::assertEquals($expected, $actual);

		NDC::clear();
	}

	public function testMDC()
	{
		MDC::put('foo', 'bar');
		MDC::put('bla', 'tra');

		$event = TestHelper::getErrorEvent("testmessage");

		$layout = new XmlLayout();
		$layout->activateOptions();

		$actual = $layout->format($event);

		$thread = $event->getThreadName();
		$timestamp = number_format(($event->getTimeStamp() * 1000), 0, '', '');

		$expected = "<log4php:event logger=\"test\" level=\"ERROR\" thread=\"$thread\" timestamp=\"$timestamp\">" . PHP_EOL .
				"<log4php:message><![CDATA[testmessage]]></log4php:message>" . PHP_EOL .
				"<log4php:properties>" . PHP_EOL .
				"<log4php:data name=\"foo\" value=\"bar\" />" . PHP_EOL .
				"<log4php:data name=\"bla\" value=\"tra\" />" . PHP_EOL .
				"</log4php:properties>" . PHP_EOL .
				"<log4php:locationInfo class=\"Apache\\Log4php\\LoggingEvent\" file=\"NA\" line=\"NA\" "  .
				"method=\"getLocationInformation\" />" . PHP_EOL .
				"</log4php:event>" . PHP_EOL;

		self::assertEquals($expected, $actual);

		MDC::clear();
	}
}
