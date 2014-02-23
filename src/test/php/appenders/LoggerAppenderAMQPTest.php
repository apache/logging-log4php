<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
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
 * Testclass for the AMQP appender.
 *
 * This class has been originally contributed from Dmitry Ulyanov
 * (http://github.com/d-ulyanov/log4php-graylog2).
 *
 * @group appenders
 */
class LoggerAppenderAMQPTest extends PHPUnit_Framework_TestCase {

    /**
     * @var LoggerAppenderAMQP
     */
    private $appender;

    private $config = array(
        'host'              => 'localhost',
        'port'              => 5672,
        'login'             => 'guest',
        'password'          => 'guest',
        'vhost'             => '/logs',
        'exchangeName'      => 'logs',
        'exchangeType'      => 'direct',
        'contentEncoding'   => 'UTF-8',
        'contentType'       => 'application/json',
        'routingKey'        => 'php_website',
        'flushOnShutdown'   => 0,
        'connectionTimeout' => 0.5,
    );

    public function testRequiresLayout() {
        $appender = new LoggerAppenderAMQP();
        $this->assertTrue($appender->requiresLayout());
    }

    protected function setUp() {
        if (extension_loaded('amqp')) {
            $this->appender = $this->createAppender();
        } else {
            $this->markTestSkipped(
                'The amqp extension is not available.'
            );
        }
    }

    protected function tearDown() {
        if (extension_loaded('amqp')) {
            $this->appender = null;
        }
    }

    public function testHost() {
        $expected = $this->config['host'];
        $this->appender->setHost($expected);
        $result = $this->appender->getHost();
        $this->assertEquals($expected, $result);
    }

    public function testPort() {
        $expected = $this->config['port'];
        $this->appender->setPort($expected);
        $result = $this->appender->getPort();
        $this->assertEquals($expected, $result);
    }

    public function testLogin() {
        $expected = $this->config['login'];
        $this->appender->setLogin($expected);
        $result = $this->appender->getLogin();
        $this->assertEquals($expected, $result);
    }

    public function testPassword() {
        $expected = $this->config['password'];
        $this->appender->setPassword($expected);
        $result = $this->appender->getPassword();
        $this->assertEquals($expected, $result);
    }

    public function testVhost() {
        $expected = $this->config['vhost'];
        $this->appender->setVhost($expected);
        $result = $this->appender->getVhost();
        $this->assertEquals($expected, $result);
    }

    public function testExchangeName() {
        $expected = $this->config['exchangeName'];
        $this->appender->setExchangeName($expected);
        $result = $this->appender->getExchangeName();
        $this->assertEquals($expected, $result);
    }

    public function testExchangeType() {
        $expected = $this->config['exchangeType'];
        $this->appender->setExchangeType($expected);
        $result = $this->appender->getExchangeType();
        $this->assertEquals($expected, $result);
    }

    public function testRoutingKey() {
        $expected = $this->config['routingKey'];
        $this->appender->setRoutingKey($expected);
        $result = $this->appender->getRoutingKey();
        $this->assertEquals($expected, $result);
    }

    public function testContentEncoding() {
        $expected = $this->config['contentEncoding'];
        $this->appender->setContentEncoding($expected);
        $result = $this->appender->getContentEncoding();
        $this->assertEquals($expected, $result);
    }

    public function testFlushOnShutdown() {
        $expected = $this->config['flushOnShutdown'];
        $this->appender->setFlushOnShutdown($expected);
        $result = $this->appender->getFlushOnShutdown();
        $this->assertEquals($expected, $result);
    }

    public function testConnectionTimeout() {
        $expected = $this->config['connectionTimeout'];
        $this->appender->setConnectionTimeout($expected);
        $result = $this->appender->getConnectionTimeout();
        $this->assertEquals($expected, $result);
    }

    public function testActivateOptions() {
        $mockAppender = $this->createMockAppender(array(
                'createAMQPConnection',
                'createAMQPExchange',
                'setAMQPConnection',
                'setAMQPExchange'
            ));

        $AMQPEmptyConnection = new AMQPConnection;

        $mockAppender->expects($this->once())
            ->method('createAMQPConnection')
            ->will($this->returnValue($AMQPEmptyConnection));

        $AMQPExchangeMock = $this->getMockBuilder('AMQPExchange')
            ->setMethods(null)
            ->disableOriginalConstructor();

        $mockAppender->expects($this->once())
            ->method('createAMQPExchange')
            ->will($this->returnValue($AMQPExchangeMock));

        $mockAppender->expects($this->once())->method('setAMQPConnection');
        $mockAppender->expects($this->once())->method('setAMQPExchange');

        $mockAppender->activateOptions();
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testActivateOptionsWithInvalidHost() {
        $appender = $this->createPreparedAppender();
        $appender->setHost('unexpected-host.i');
        $appender->activateOptions();
    }

    public function testSendLogToAMQP()
    {
        $expected = "Some short log message";
        $AMQPExchangeMock = new LoggerAppenderAMQP_ExchangeStub;

        $mockAppender = $this->createMockAppender(array('getAMQPExchange'));
        $mockAppender->expects($this->once())
            ->method('getAMQPExchange')
            ->will($this->returnValue($AMQPExchangeMock));

        $mockAppender->sendLogToAMQP($expected);
        $this->assertEquals($expected, $AMQPExchangeMock->getLastMessage());
    }

    public function testStashLog()
    {
        $expected = "Some log message";
        $appender = $this->createAppender();
        $appender->stashLog($expected);

        $this->assertEquals(array($expected), PHPUnit_Framework_Assert::readAttribute($appender, 'logsStash'));
    }

    public function testProcessLogWithoutStash()
    {
        $mockAppender = $this->createMockAppender(array('sendLogToAMQP'));
        $mockAppender->expects($this->once())->method('sendLogToAMQP');
        $mockAppender->processLog("Some log message", 0);
    }

    public function testProcessLogWitStash()
    {
        $mockAppender = $this->createMockAppender(array('stashLog'));
        $mockAppender->expects($this->once())->method('stashLog');
        $mockAppender->processLog("Some log message", 1);
    }

    public function testSendLogsArrayToAMQP()
    {
        $stashedLogs = array("One stashed log");
        $mockAppender = $this->createMockAppender(array('sendLogToAMQP'));
        $mockAppender->expects($this->once())->method('sendLogToAMQP');
        $mockAppender->sendLogsArrayToAMQP($stashedLogs);
    }

    private function createAppender()
    {
        return new LoggerAppenderAMQP('amqp_appender');
    }

    private function createPreparedAppender()
    {
        $appender = $this->createAppender();

        foreach ($this->config as $option => $value) {
            $setter = "set$option";
            $appender->$setter($value);
        }

        return $appender;
    }

    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject | LoggerAppenderAMQP
     */
    private function createMockAppender(array $methods = array())
    {
        return $this->getMock('LoggerAppenderAMQP', $methods);
    }
}

class LoggerAppenderAMQP_ExchangeStub
{
    protected $stash = array();

    public function publish($message, $routing_key, $flags = AMQP_NOPARAM, array $attributes = array())
    {
        $this->stash[] = $message;
        return true;
    }

    public function cleanStash()
    {
        $this->stash = array();
    }

    public function getLastMessage()
    {
        return array_pop($this->stash);
    }
}