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

namespace Apache\Log4php\Tests\Pattern;

use Apache\Log4php\Helpers\FormattingInfo;
use Apache\Log4php\Logger;
use Apache\Log4php\MDC;
use Apache\Log4php\NDC;

use Apache\Log4php\Pattern\CookieConverter;
use Apache\Log4php\Pattern\DateConverter;
use Apache\Log4php\Pattern\EnvironmentConverter;
use Apache\Log4php\Pattern\LevelConverter;
use Apache\Log4php\Pattern\LiteralConverter;
use Apache\Log4php\Pattern\LoggerConverter;
use Apache\Log4php\Pattern\MdcConverter;
use Apache\Log4php\Pattern\MessageConverter;
use Apache\Log4php\Pattern\NdcConverter;
use Apache\Log4php\Pattern\NewLineConverter;
use Apache\Log4php\Pattern\ProcessConverter;
use Apache\Log4php\Pattern\RelativeConverter;
use Apache\Log4php\Pattern\RequestConverter;
use Apache\Log4php\Pattern\ServerConverter;
use Apache\Log4php\Pattern\SessionConverter;
use Apache\Log4php\Pattern\SessionIdConverter;
use Apache\Log4php\Pattern\SuperglobalConverter;

use Apache\Log4php\Tests\TestHelper;

/** Converter referencing non-existant superglobal variable. */
class InvalidSuperglobalConverter extends SuperglobalConverter
{
    protected $name = '_FOO';
}

/**
 * @group pattern
 */
class PatternConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * A logging event for testing.
     * @var LoggingEvent
     */
    private $event;

    /**
     * Fromatting info used with the logging event.
     * @var LoggerFormattingInfos
     */
    private $info;

    public function __construct()
    {
        $this->event = TestHelper::getInfoEvent('foobar');
        $this->info = new FormattingInfo();
    }

    public function testCookie()
    {
        // Fake a couple of cookies
        $_COOKIE['test1'] = 'value1';
        $_COOKIE['test2'] = 'value2';

        $converter = new CookieConverter($this->info, 'test1');
        $actual = $converter->convert($this->event);
        $expected = 'value1';
        self::assertSame($expected, $actual);

        $converter = new CookieConverter($this->info, 'test2');
        $actual = $converter->convert($this->event);
        $expected = 'value2';
        self::assertSame($expected, $actual);

        $converter = new CookieConverter($this->info);
        $actual = $converter->convert($this->event);
        $expected = "test1=value1, test2=value2";
        self::assertSame($expected, $actual);
    }

    public function testDate()
    {
        $converter = new DateConverter($this->info, 'c');
        $actual = $converter->convert($this->event);
        $expected = date('c', $this->event->getTimeStamp());
        self::assertSame($expected, $actual);

        // Format defaults to 'c'
        $converter = new DateConverter($this->info);
        $actual = $converter->convert($this->event);
        $expected = date('c', $this->event->getTimeStamp());
        self::assertSame($expected, $actual);

        $converter = new DateConverter($this->info, '');
        $actual = $converter->convert($this->event);
        $expected = date('c', $this->event->getTimeStamp());
        self::assertSame($expected, $actual);

        // Test ABSOLUTE
        $converter = new DateConverter($this->info, 'ABSOLUTE');
        $actual = $converter->convert($this->event);
        $expected = date('H:i:s', $this->event->getTimeStamp());
        self::assertSame($expected, $actual);

        // Test DATE
        $converter = new DateConverter($this->info, 'DATE');
        $actual = $converter->convert($this->event);
        $expected = date('d M Y H:i:s.', $this->event->getTimeStamp());

        $timestamp = $this->event->getTimeStamp();
        $ms = floor(($timestamp - floor($timestamp)) * 1000);
        $ms = str_pad($ms, 3, '0', STR_PAD_LEFT);

        $expected .= $ms;

        self::assertSame($expected, $actual);
    }

    public function testEnvironment()
    {
        // Fake a couple of environment values
        $_ENV['test1'] = 'value1';
        $_ENV['test2'] = 'value2';

        $converter = new EnvironmentConverter($this->info, 'test1');
        $actual = $converter->convert($this->event);
        $expected = 'value1';
        self::assertSame($expected, $actual);

        $converter = new EnvironmentConverter($this->info, 'test2');
        $actual = $converter->convert($this->event);
        $expected = 'value2';
        self::assertSame($expected, $actual);
    }

    public function testLevel()
    {
        $converter = new LevelConverter($this->info);
        $actual = $converter->convert($this->event);
        $expected = $this->event->getLevel()->toString();
        self::assertEquals($expected, $actual);
    }

    public function testLiteral()
    {
        $converter = new LiteralConverter('foo bar baz');
        $actual = $converter->convert($this->event);
        $expected = 'foo bar baz';
        self::assertEquals($expected, $actual);
    }

    public function testLoggerWithoutOption()
    {
        $event = TestHelper::getInfoEvent('foo', 'TestLoggerName');
        $converter = new LoggerConverter($this->info);

        $actual = $converter->convert($event);
        $expected = 'TestLoggerName';
        self::assertEquals($expected, $actual);
    }

    public function testLoggerWithOption0()
    {
        $event = TestHelper::getInfoEvent('foo', 'TestLoggerName');
        $converter = new LoggerConverter($this->info, '0');

        $actual = $converter->convert($event);
        $expected = 'TestLoggerName';
        self::assertEquals($expected, $actual);
    }

    public function testLocation()
    {
        $config = TestHelper::getEchoPatternConfig("%file:%line:%class:%method");
        Logger::configure($config);

        // Test by capturing output. Logging methods of a Logger object must
        // be used for the location info to be formed correctly.
        ob_start();
        $log = Logger::getLogger('foo');
        $log->info('foo'); $line = __LINE__; // Do NOT move this to next line.
        $actual = ob_get_contents();
        ob_end_clean();

        $expected = implode(':', array(__FILE__, $line, __CLASS__, __FUNCTION__));
        self::assertSame($expected, $actual);

        Logger::resetConfiguration();
    }

    public function testLocation2()
    {
        $config = TestHelper::getEchoPatternConfig("%location");
        Logger::configure($config);

        // Test by capturing output. Logging methods of a Logger object must
        // be used for the location info to be formed correctly.
        ob_start();
        $log = Logger::getLogger('foo');
        $log->info('foo'); $line = __LINE__; // Do NOT move this to next line.
        $actual = ob_get_contents();
        ob_end_clean();

        $class = __CLASS__;
        $func = __FUNCTION__;
        $file = __FILE__;

        $expected = "$class.$func($file:$line)";
        self::assertSame($expected, $actual);

        Logger::resetConfiguration();
    }

    public function testMessage()
    {
        $expected = "This is a message.";
        $event = TestHelper::getInfoEvent($expected);
        $converter = new MessageConverter($this->info);
        $actual = $converter->convert($event);
        self::assertSame($expected, $actual);
    }

    public function testMDC()
    {
        MDC::put('foo', 'bar');
        MDC::put('bla', 'tra');

        // Entire context
        $converter = new MdcConverter($this->info);
        $actual = $converter->convert($this->event);
        $expected = 'foo=bar, bla=tra';
        self::assertSame($expected, $actual);

        // Just foo
        $converter = new MdcConverter($this->info, 'foo');
        $actual = $converter->convert($this->event);
        $expected = 'bar';
        self::assertSame($expected, $actual);

        // Non existant key
        $converter = new MdcConverter($this->info, 'doesnotexist');
        $actual = $converter->convert($this->event);
        $expected = '';
        self::assertSame($expected, $actual);

        MDC::clear();
    }

    public function testNDC()
    {
        NDC::push('foo');
        NDC::push('bar');
        NDC::push('baz');

        $converter = new NdcConverter($this->info);
        $expected = 'foo bar baz';
        $actual = $converter->convert($this->event);
        self::assertEquals($expected, $actual);
    }

    public function testNewline()
    {
        $converter = new NewLineConverter($this->info);
        $actual = $converter->convert($this->event);
        $expected = PHP_EOL;
        self::assertSame($expected, $actual);
    }

    public function testProcess()
    {
        $converter = new ProcessConverter($this->info);
        $actual = $converter->convert($this->event);
        $expected = getmypid();
        self::assertSame($expected, $actual);
    }

    public function testRelative()
    {
        $converter = new RelativeConverter($this->info);
        $expected = number_format($this->event->getTimeStamp() - $this->event->getStartTime(), 4);
        $actual = $converter->convert($this->event);
        self::assertSame($expected, $actual);
    }

    public function testRequest()
    {
        // Fake a couple of request values
        $_REQUEST['test1'] = 'value1';
        $_REQUEST['test2'] = 'value2';

        // Entire request
        $converter = new RequestConverter($this->info);
        $actual = $converter->convert($this->event);
        $expected = 'test1=value1, test2=value2';
        self::assertSame($expected, $actual);

        // Just test2
        $converter = new RequestConverter($this->info, 'test2');
        $actual = $converter->convert($this->event);
        $expected = 'value2';
        self::assertSame($expected, $actual);
    }

    public function testServer()
    {
        // Fake a server value
        $_SERVER['test1'] = 'value1';

        $converter = new ServerConverter($this->info, 'test1');
        $actual = $converter->convert($this->event);
        $expected = 'value1';
        self::assertSame($expected, $actual);
    }

    public function testSession()
    {
        // Fake a session value
        $_SESSION['test1'] = 'value1';

        $converter = new SessionConverter($this->info, 'test1');
        $actual = $converter->convert($this->event);
        $expected = 'value1';
        self::assertSame($expected, $actual);
    }

    public function testSessionID()
    {
        $converter = new SessionIdConverter($this->info);
        $actual = $converter->convert($this->event);
        $expected = session_id();
        self::assertSame($expected, $actual);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage log4php: InvalidSuperglobalConverter: Cannot find superglobal variable $_FOO
     */
    public function testNonexistantSuperglobal()
    {
        $converter = new InvalidSuperglobalConverter($this->info);
        $actual = $converter->convert($this->event);
    }

    public function testFormattingTrimRight()
    {
        $event = TestHelper::getInfoEvent('0123456789');

        $info = new FormattingInfo();
        $info->max = 5;

        $converter = new MessageConverter($info);
        $actual = "";
        $converter->format($actual, $event);
        $expected = "01234";
        self::assertSame($expected, $actual);
    }

    public function testFormattingTrimLeft()
    {
        $event = TestHelper::getInfoEvent('0123456789');

        $info = new FormattingInfo();
        $info->max = 5;
        $info->trimLeft = true;

        $converter = new MessageConverter($info);
        $actual = "";
        $converter->format($actual, $event);
        $expected = "56789";
        self::assertSame($expected, $actual);
    }

    public function testFormattingPadEmpty()
    {
        $event = TestHelper::getInfoEvent('');

        $info = new FormattingInfo();
        $info->min = 5;

        $converter = new MessageConverter($info);
        $actual = "";
        $converter->format($actual, $event);
        $expected = "     ";
        self::assertSame($expected, $actual);
    }

    public function testFormattingPadLeft()
    {
        $event = TestHelper::getInfoEvent('0');

        $info = new FormattingInfo();
        $info->min = 5;

        $converter = new MessageConverter($info);
        $actual = "";
        $converter->format($actual, $event);
        $expected = "    0";
        self::assertSame($expected, $actual);
    }

    public function testFormattingPadRight()
    {
        $event = TestHelper::getInfoEvent('0');

        $info = new FormattingInfo();
        $info->min = 5;
        $info->padLeft = false;

        $converter = new MessageConverter($info);
        $actual = "";
        $converter->format($actual, $event);
        $expected = "0    ";
        self::assertSame($expected, $actual);
    }
}
