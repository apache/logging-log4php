<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *	  http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   tests
 * @package	   log4php
 * @subpackage appenders
 * @license	   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link       http://logging.apache.org/log4php
 */

/**
 * Testclass for the Graylog2 appender.
 *
 * This class has been originally contributed from Dmitry Ulyanov
 * (http://github.com/d-ulyanov/log4php-graylog2).
 *
 * @group appenders
 */
class LoggerAppenderGraylog2Test extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Logger::resetConfiguration();
    }

    public function tearDown() {
        Logger::resetConfiguration();
    }

    public function testSplitMessageIntoChunks() {
        $appender = $this->createAppender();

        $shortMessage = "Test";
        $longMessage = str_repeat($shortMessage, 128);
        $chunks = $appender->splitMessageIntoChunks($longMessage, 128);
        $this->assertInternalType("array", $chunks);
        $this->assertCount(mb_strlen($shortMessage), $chunks);

        $chunks = $appender->splitMessageIntoChunks($shortMessage, mb_strlen($shortMessage) + 1);
        $this->assertInternalType("array", $chunks);
        $this->assertCount(1, $chunks);
    }

    public function testSplitUnicodeString() {
        $appender = $this->createAppender();

        $unicodeString = "Ilık süt";
        $chunks = $appender->splitUnicodeString($unicodeString, 3);

        $this->assertCount(3, $appender->splitUnicodeString($unicodeString, 3));
        $this->assertCount(8, $appender->splitUnicodeString($unicodeString, 1));
    }

    /**
     * @expectedException Exception
     */
    public function testSplitUnicodeStringException() {
        $appender = $this->createAppender();
        $appender->splitUnicodeString("Ilık süt", 0);
    }

    public function testHost() {
        $appender = $this->createAppender();
        $expected = 'example.com';
        $appender->setHost($expected);
        $this->assertEquals($expected, $appender->getHost());
    }

    public function testPort() {
        $appender = $this->createAppender();
        $expected = 65536;
        $appender->setPort($expected);
        $this->assertEquals($expected, $appender->getPort());
    }

    public function testChunkSize() {
        $appender = $this->createAppender();
        $expected = 1024;
        $appender->setChunkSize($expected);
        $this->assertEquals($expected, $appender->getChunkSize());
    }

    public function testTimeout() {
        $appender = $this->createAppender();
        $expected = 1;
        $appender->setTimeout($expected);
        $this->assertEquals($expected, $appender->getTimeout());
    }

    public function testDefaultTimeout() {
        $appender = $this->createAppender();
        $appender->activateOptions();
        $timeout = ini_get("default_socket_timeout");
        $this->assertEquals($timeout, $appender->getTimeout());
    }

    public function testGetHostAddr() {
        $appender = $this->createAppender();
        $appender->setHost('localhost');
        $this->assertEquals(gethostbyname('localhost'), $appender->getHostAddr());

        $ip = "127.0.0.1";
        $appender->setHost($ip);
        $this->assertEquals($ip, $appender->getHostAddr());
    }

    public function testCreateConnection() {
        $appender = $this->getMock('LoggerAppenderGraylog2', array('createStreamSocketClient'));
        $fp = fopen("php://temp", "r");
        $appender->expects($this->once())
            ->method('createStreamSocketClient')
            ->will($this->returnValue($fp));

        $createConnection = $this->setAccessible(get_class($appender), "createConnection");
        $connection = $createConnection->invokeArgs($appender, array("", "", 60));

        $this->assertInternalType("resource", $connection);
        fclose($connection);
    }

    public function testAppend() {
        /** @var $appender LoggerAppenderGraylog2 */
        $appender = $this->getMock('LoggerAppenderGraylog2', array(
                'createConnection',
                'closeConnection'
            ));
        $fp = fopen("php://temp", "w+");
        $appender->expects($this->once())
            ->method('createConnection')
            ->will($this->returnValue($fp));
        $appender->expects($this->once())
            ->method('closeConnection')
            ->will($this->returnValue(true));

        $shortMessage = "Some logging event";
        $event = LoggerTestHelper::getDebugEvent($shortMessage);
        $appender->doAppend($event);

        rewind($fp);
        $message = stream_get_contents($fp);
        $this->assertNotEmpty($message);
    }

    protected function getLogger() {
        Logger::configure(array(
            'appenders' => array(
                'default' => array(
                    'class' => 'LoggerAppenderGraylog2',
                ),
            ),
            'rootLogger' => array(
                'appenders' => array('default'),
            ),
        ));

        return Logger::getLogger("myLogger");
    }

    /**
     * @return LoggerAppenderGraylog2
     */
    protected function createAppender() {
        Logger::configure(array(
                'appenders' => array(
                    'default' => array(
                        'class' => 'LoggerAppenderGraylog2',
                    ),
                ),
                'rootLogger' => array(
                    'appenders' => array('default'),
                ),
            ));
        return new LoggerAppenderGraylog2();
    }

    protected function setAccessible($class, $name) {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
