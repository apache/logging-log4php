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

use Apache\Log4php\ThrowableInformation;

class ThrowableInformationTestException extends \Exception { }

/**
 * @group main
 */
class ThrowableInformationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $ex = new \Exception();
        $tInfo = new ThrowableInformation($ex);

        $result = $tInfo->getStringRepresentation();
        $this->assertInternalType('array', $result);
    }

    public function testExceptionChain()
    {
        $ex1 = new ThrowableInformationTestException('Message1');
        $ex2 = new ThrowableInformationTestException('Message2', 0, $ex1);
        $ex3 = new ThrowableInformationTestException('Message3', 0, $ex2);

        $tInfo = new ThrowableInformation($ex3);
        $result	= $tInfo->getStringRepresentation();
        $this->assertInternalType('array', $result);
    }

    public function testGetThrowable()
    {
        $ex = new ThrowableInformationTestException('Message1');
        $tInfo = new ThrowableInformation($ex);
        $result = $tInfo->getThrowable();
        $this->assertEquals($ex, $result);
    }
}
