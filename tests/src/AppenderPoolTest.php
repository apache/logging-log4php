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

namespace Apache\Log4php\Tests;

use Apache\Log4php\AppenderPool;

use Mockery as m;

/**
 * @group filters
 */
class AppenderPoolTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        AppenderPool::clear();
    }

    public function tearDown()
    {
        AppenderPool::clear();
    }

     /**
      * @expectedException PHPUnit_Framework_Error
      * @expectedExceptionMessage log4php: Cannot add unnamed appender to pool.
      */
    public function testAppenderHasNoName()
    {
        $mockAppender = m::mock('Apache\\Log4php\\Appenders\\ConsoleAppender')
            ->shouldReceive('getName')->andReturn('')
            ->shouldReceive('close')
            ->mock();

        AppenderPool::add($mockAppender);
    }

    public function testAppenderIsAdded()
    {
        $mockAppender = m::mock('Apache\\Log4php\\Appenders\\ConsoleAppender')
            ->shouldReceive('getName')->andReturn('foo')
            ->shouldReceive('close')
            ->mock();

        AppenderPool::add($mockAppender);

        $expected = 1;
        $actual = count(AppenderPool::getAppenders());
        $this->assertEquals($expected, $actual);
    }

    /**
      * @expectedException PHPUnit_Framework_Error
      * @expectedExceptionMessage log4php: Appender [foo] already exists in pool. Overwriting existing appender.
      */
    public function testDuplicateAppenderName()
    {
        $mockAppender = m::mock('Apache\\Log4php\\Appenders\\ConsoleAppender')
            ->shouldReceive('getName')->andReturn('foo')
            ->shouldReceive('close')
            ->mock();

        AppenderPool::add($mockAppender);
        AppenderPool::add($mockAppender);
    }
}
