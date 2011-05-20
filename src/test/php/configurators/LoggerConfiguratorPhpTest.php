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
 * @subpackage renderers
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

class TestClass {
    public $test1 = 'test1';
    public $test2 = 'test2';
    public $test3 = 'test3';
}

class TestRenderer implements LoggerRendererObject {
	public function render($o) {
		return "{$o->test1},{$o->test2},{$o->test3}";
	}
}

class LoggerConfiguratorPhpTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {

    }

    protected function tearDown() {
        Logger::resetConfiguration();
    }

    public function testConfigure() {
        Logger::configure(dirname(__FILE__) . '/test1.php', 'LoggerConfiguratorPhp');
        $root = Logger::getRootLogger();
        self::assertEquals(LoggerLevel::getLevelWarn(), $root->getLevel());
        $appender = $root->getAppender("default");
        self::assertTrue($appender instanceof LoggerAppenderEcho);
        $layout = $appender->getLayout();
        self::assertTrue($layout instanceof LoggerLayoutSimple);
        $logger = Logger::getLogger('mylogger');
        self::assertEquals(LoggerLevel::getLevelInfo(), $logger->getLevel());
        $logger = Logger::getLogger('tracer');
        self::assertEquals(LoggerLevel::getLevelTrace(), $logger->getLevel());
    }

    public function testConfigureArray() {
        Logger::configure(array (
            'renderers' => array(
                'TestClass' => 'TestRenderer',
            ),
            'threshold' => 'ALL',
            'rootLogger' => array (
                'level' => 'WARN',
                'appenders' => array (
                    'default', 'filetest'
                ),
            ),
            'loggers' => array (
                'mylogger' => array (
                    'level' => 'INFO',
                    'appenders' => array (
                        'default'
                    ),
                ),
                'tracer' => array (
                    'level' => 'TRACE',
                    'appenders' => array (
                        'default'
                    ),
                ),
            ),
            'appenders' => array (
                'default' => array (
                    'class' => 'LoggerAppenderEcho',
                    'layout' => array (
                        'class' => 'LoggerLayoutSimple'
                    ),
                ),
                'filetest' => array (
                    'class' => 'LoggerAppenderFile',
                    'file' => '../../../target/temp/xy.log',
                    'layout' => array (
                        'class' => 'LoggerLayoutSimple'
                    ),
                ),
            ),
            
        ), 'LoggerConfiguratorPhp');
        $root = Logger::getRootLogger();
        self::assertEquals(LoggerLevel::getLevelWarn(), $root->getLevel());
        $appender = $root->getAppender("default");
        self::assertTrue($appender instanceof LoggerAppenderEcho);
        $layout = $appender->getLayout();
        self::assertTrue($layout instanceof LoggerLayoutSimple);
        
        $appender = $root->getAppender("filetest");
        self::assertTrue($appender instanceof LoggerAppenderFile);
        $layout = $appender->getLayout();
        self::assertTrue($layout instanceof LoggerLayoutSimple);
        $file = $appender->getFile();
        self::assertEquals('../../../target/temp/xy.log', $file);
        
        $logger = Logger::getLogger('mylogger');
        self::assertEquals(LoggerLevel::getLevelInfo(), $logger->getLevel());
        $logger = Logger::getLogger('tracer');
        self::assertEquals(LoggerLevel::getLevelTrace(), $logger->getLevel());

        $map = Logger::getHierarchy()->getRendererMap();
        $actual = $map->findAndRender(new TestClass());
        self::assertEquals('test1,test2,test3', $actual);

    }
}