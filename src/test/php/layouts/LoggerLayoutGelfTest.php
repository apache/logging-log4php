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
 * @link       http://logging.apache.org/log4php
 */

/**
 * @group layouts
 */
class LoggerLayoutGelfTest extends PHPUnit_Framework_TestCase {

    public function testErrorLayout() {
        $message = "some-error-message";

        $validMDCField = "mdc1";
        $invalidMDCField = "%";
        LoggerMDC::put($validMDCField, 1);
        LoggerMDC::put($invalidMDCField, 1);
        $event = LoggerTestHelper::getErrorEvent($message);

        $layout = new LoggerLayoutGelf();
        $layout->activateOptions();
        $layout->setLocationInfo(true);

        $actual = $layout->format($event);
        $encodedMessage = json_decode($actual, 1);

        // Is message a valid json
        $this->assertNotNull($encodedMessage);

        // Check basic fields
        $this->assertNotEmpty($encodedMessage["version"]);
        $this->assertNotEmpty($encodedMessage["host"]);
        $this->assertNotEmpty($encodedMessage["short_message"]);
        $this->assertNotEmpty($encodedMessage["full_message"]);
        $this->assertNotEmpty($encodedMessage["timestamp"]);
        $this->assertNotEmpty($encodedMessage["level"]);

        // Check additional fields
        $this->assertNotEmpty($encodedMessage["_facility"]);
        $this->assertNotEmpty($encodedMessage["_thread"]);

        // Check additional location fields
        $this->assertNotEmpty($encodedMessage["_file"]);
        $this->assertNotEmpty($encodedMessage["_line"]);
        $this->assertNotEmpty($encodedMessage["_class"]);
        $this->assertNotEmpty($encodedMessage["_method"]);

        $this->assertInternalType("string", $encodedMessage["version"]);
        $this->assertInternalType("string", $encodedMessage["host"]);
        $this->assertInternalType("string", $encodedMessage["short_message"]);
        $this->assertInternalType("string", $encodedMessage["full_message"]);
        $this->assertInternalType("float", $encodedMessage["timestamp"]);
        $this->assertInternalType("integer", $encodedMessage["level"]);

        $this->assertArrayHasKey("_".$validMDCField, $encodedMessage);
        $this->assertArrayNotHasKey("_".$invalidMDCField, $encodedMessage);

        LoggerMDC::clear();
    }

    public function additionalFieldNameTest() {
        $layout = new LoggerLayoutGelf();

        $validFieldNames = array("file", "_line", "facility_1");
        $invalidFieldNames = array("123test", "-field", "_id");

        foreach ($validFieldNames as $name) {
            $this->assertTrue($layout->isAdditionalFieldNameValid($name));
        }

        foreach ($invalidFieldNames as $name) {
            $this->assertFalse($layout->isAdditionalFieldNameValid($name));
        }
    }

    public function levelMappingTest() {
        $layout = new LoggerLayoutGelf();

        $this->assertEquals(LoggerLayoutGelf::LEVEL_DEBUG, $layout->getGelfLevel(LoggerLevel::TRACE));
        $this->assertEquals(LoggerLayoutGelf::LEVEL_DEBUG, $layout->getGelfLevel(LoggerLevel::DEBUG));
        $this->assertEquals(LoggerLayoutGelf::LEVEL_INFO, $layout->getGelfLevel(LoggerLevel::INFO));
        $this->assertEquals(LoggerLayoutGelf::LEVEL_WARNING, $layout->getGelfLevel(LoggerLevel::WARN));
        $this->assertEquals(LoggerLayoutGelf::LEVEL_ERROR, $layout->getGelfLevel(LoggerLevel::ERROR));
        $this->assertEquals(LoggerLayoutGelf::LEVEL_CRITICAL, $layout->getGelfLevel(LoggerLevel::FATAL));

        // If level not found - map returns ALERT level
        $this->assertEquals(LoggerLayoutGelf::LEVEL_ALERT, $layout->getGelfLevel(LoggerLevel::OFF));
    }

    public function testDefaultHost() {
        $layout = new LoggerLayoutGelf();
        $layout->activateOptions();
        $expected = gethostname();
        $this->assertEquals($expected, $layout->getHost());
    }

    public function testHost() {
        $layout = new LoggerLayoutGelf();
        $expected = "test-host";
        $layout->setHost($expected);
        $this->assertEquals($expected, $layout->getHost());
    }

    public function testShortMessageLength() {
        $layout = new LoggerLayoutGelf();
        $expected = 1024;
        $layout->setShortMessageLength($expected);
        $this->assertEquals($expected, $layout->getShortMessageLength());
    }

    public function testShortMessage() {
        $shortMessage = "short-message";
        $event = LoggerTestHelper::getErrorEvent($shortMessage);

        $layout = new LoggerLayoutGelf();
        $layout->activateOptions();

        $this->assertEquals($shortMessage, $layout->getShortMessage($event));
        $this->assertEquals($shortMessage, $layout->getFullMessage($event));
    }

    public function testShortMessageCutting() {
        $message = str_repeat("long-message \n", 100);
        $event = LoggerTestHelper::getErrorEvent($message);

        $layout = new LoggerLayoutGelf();
        $layout->activateOptions();

        $expectedShortMessageLength = $layout->getShortMessageLength();
        $this->assertEquals($expectedShortMessageLength, mb_strlen($layout->getShortMessage($event)));
        $this->assertEquals($message, $layout->getFullMessage($event));

    }

    public function testNonUnicodeCharsReplacement() {
        $invalidUtf8String = str_repeat(chr(193), 10);
        $event = LoggerTestHelper::getErrorEvent($invalidUtf8String);

        $layout = new LoggerLayoutGelf();
        $layout->activateOptions();
        $this->assertEquals(
            str_repeat("?", mb_strlen($invalidUtf8String)),
            $layout->getShortMessage($event)
        );
    }
}
