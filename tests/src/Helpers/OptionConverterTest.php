<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
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

namespace Apache\Log4php\Tests\Helpers;

use Apache\Log4php\Helpers\OptionConverter;
use Apache\Log4php\Appenders\FileAppender;

define('MY_CONSTANT_CONSTANT', 'DEFINE');
define('MY_CONSTANT_CONSTANT_OTHER', 'DEFINE_OTHER');

/**
 * @group helpers
 */
class OptionConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testToBoolean()
    {
        self::assertTrue(OptionConverter::toBooleanEx(1));
        self::assertTrue(OptionConverter::toBooleanEx("1"));
        self::assertTrue(OptionConverter::toBooleanEx(true));
        self::assertTrue(OptionConverter::toBooleanEx("true"));
        self::assertTrue(OptionConverter::toBooleanEx("on"));
        self::assertTrue(OptionConverter::toBooleanEx("yes"));

        self::assertFalse(OptionConverter::toBooleanEx(0));
        self::assertFalse(OptionConverter::toBooleanEx("0"));
        self::assertFalse(OptionConverter::toBooleanEx(false));
        self::assertFalse(OptionConverter::toBooleanEx("false"));
        self::assertFalse(OptionConverter::toBooleanEx("off"));
        self::assertFalse(OptionConverter::toBooleanEx("no"));
    }

    /**
     * Test fail on NULL.
      * @expectedException Apache\Log4php\LoggerException
      * @expectedExceptionMessage Given value [NULL] cannot be converted to boolean.
     */
    public function testToBooleanFailure1()
    {
        OptionConverter::toBooleanEx(null);
    }

    /**
     * Test fail on invalid string.
     * @expectedException Apache\Log4php\LoggerException
     * @expectedExceptionMessage Given value ['foo'] cannot be converted to boolean.
     */
    public function testToBooleanFailure2()
    {
        OptionConverter::toBooleanEx('foo');
    }

    public function testToInteger()
    {
        self::assertSame(1, OptionConverter::toIntegerEx('1'));
        self::assertSame(1, OptionConverter::toIntegerEx(1));
        self::assertSame(0, OptionConverter::toIntegerEx('0'));
        self::assertSame(0, OptionConverter::toIntegerEx(0));
        self::assertSame(-1, OptionConverter::toIntegerEx('-1'));
        self::assertSame(-1, OptionConverter::toIntegerEx(-1));
    }

    /**
    * Test fail on NULL.
    * @expectedException Apache\Log4php\LoggerException
    * @expectedExceptionMessage Given value [NULL] cannot be converted to integer.
    */
    public function testToIntegerFailure1()
    {
        OptionConverter::toIntegerEx(null);
    }

    /**
     * Test fail on empty string.
     * @expectedException Apache\Log4php\LoggerException
     * @expectedExceptionMessage Given value [''] cannot be converted to integer.
     */
    public function testToIntegerFailure2()
    {
        OptionConverter::toIntegerEx('');
    }

    /**
     * Test fail on invalid string.
     * @expectedException Apache\Log4php\LoggerException
     * @expectedExceptionMessage Given value ['foo'] cannot be converted to integer.
     */
    public function testToIntegerFailure3()
    {
        OptionConverter::toIntegerEx('foo');
    }

    /**
     * Test fail on boolean.
     * @expectedException Apache\Log4php\LoggerException
     * @expectedExceptionMessage Given value [true] cannot be converted to integer.
     */
    public function testToIntegerFailure4()
    {
        OptionConverter::toIntegerEx(true);
    }

    /**
     * Test fail on boolean.
     * @expectedException Apache\Log4php\LoggerException
     * @expectedExceptionMessage Given value [false] cannot be converted to integer.
     */
    public function testToIntegerFailure5()
    {
        OptionConverter::toIntegerEx(false);
    }

    public function testSubstituteConstants()
    {
        define('OTHER_CONSTANT', 'OTHER');
        define('MY_CONSTANT', 'TEST');
        define('NEXT_CONSTANT', 'NEXT');

        $result = OptionConverter::substConstants('Value of key is ${MY_CONSTANT}.');
        self::assertEquals('Value of key is TEST.', $result);

        $result = OptionConverter::substConstants('Value of key is ${MY_CONSTANT} or ${OTHER_CONSTANT}.');
        self::assertEquals('Value of key is TEST or OTHER.', $result);

        $result = OptionConverter::substConstants('Value of key is ${MY_CONSTANT_CONSTANT}.');
        self::assertEquals('Value of key is DEFINE.', $result);

        $result = OptionConverter::substConstants('Value of key is ${MY_CONSTANT_CONSTANT} or ${MY_CONSTANT_CONSTANT_OTHER}.');
        self::assertEquals('Value of key is DEFINE or DEFINE_OTHER.', $result);
    }

    public function testActualSubstituteConstants()
    {
        $a = new FileAppender();
        $a->setFile('${PHPUNIT_TEMP_DIR}/log.txt');
        $actual = $a->getFile();
        $expected = PHPUNIT_TEMP_DIR . '/log.txt';
        self::assertSame($expected, $actual);
    }
}
