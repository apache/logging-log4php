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

namespace Apache\Log4php\Tests;

use Apache\Log4php\NDC;

/**
 * @group main
 */
class NDCTest extends \PHPUnit_Framework_TestCase
{
    public function testItemHandling()
    {
        // Test the empty stack
        self::assertSame('', NDC::get());
        self::assertSame('', NDC::peek());
        self::assertSame(0, NDC::getDepth());
        self::assertSame('', NDC::pop());

        // Add some data to the stack
        NDC::push('1');
        NDC::push('2');
        NDC::push('3');

        self::assertSame('1 2 3', NDC::get());
        self::assertSame('3', NDC::peek());
        self::assertSame(3, NDC::getDepth());

        // Remove last item
        self::assertSame('3', NDC::pop());
        self::assertSame('1 2', NDC::get());
        self::assertSame('2', NDC::peek());
        self::assertSame(2, NDC::getDepth());

        // Remove all items
        NDC::remove();

        // Test the empty stack
        self::assertSame('', NDC::get());
        self::assertSame('', NDC::peek());
        self::assertSame(0, NDC::getDepth());
        self::assertSame('', NDC::pop());
    }

    public function testMaxDepth()
    {
        // Clear stack; add some testing data
        NDC::clear();
        NDC::push('1');
        NDC::push('2');
        NDC::push('3');
        NDC::push('4');
        NDC::push('5');
        NDC::push('6');

        self::assertSame('1 2 3 4 5 6', NDC::get());

        // Edge case, should not change stack
        NDC::setMaxDepth(6);
        self::assertSame('1 2 3 4 5 6', NDC::get());
        self::assertSame(6, NDC::getDepth());

        NDC::setMaxDepth(3);
        self::assertSame('1 2 3', NDC::get());
        self::assertSame(3, NDC::getDepth());

        NDC::setMaxDepth(0);
        self::assertSame('', NDC::get());
        self::assertSame(0, NDC::getDepth());
    }
}
