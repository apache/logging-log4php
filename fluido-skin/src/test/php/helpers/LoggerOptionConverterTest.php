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
 * @subpackage helpers
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

define('MY_CONSTANT_CONSTANT', 'DEFINE');
define('MY_CONSTANT_CONSTANT_OTHER', 'DEFINE_OTHER');

/**
 * @group helpers
 */
class LoggerOptionConverterTest extends PHPUnit_Framework_TestCase {

    public function testToBoolean() {
        self::assertEquals(true, LoggerOptionConverter::toBoolean(null, true));
        self::assertEquals(true, LoggerOptionConverter::toBoolean(null));
        self::assertEquals(true, LoggerOptionConverter::toBoolean(true));
        self::assertEquals(true, LoggerOptionConverter::toBoolean("1"));
        self::assertEquals(true, LoggerOptionConverter::toBoolean("true"));
        self::assertEquals(true, LoggerOptionConverter::toBoolean("on"));
        self::assertEquals(true, LoggerOptionConverter::toBoolean("yes"));
        
        self::assertEquals(false, LoggerOptionConverter::toBoolean(null, false));
        self::assertEquals(false, LoggerOptionConverter::toBoolean(false));
        self::assertEquals(false, LoggerOptionConverter::toBoolean(""));
        self::assertEquals(false, LoggerOptionConverter::toBoolean("0"));
        self::assertEquals(false, LoggerOptionConverter::toBoolean("false"));
        self::assertEquals(false, LoggerOptionConverter::toBoolean("off"));
        self::assertEquals(false, LoggerOptionConverter::toBoolean("no"));
    }
    
    
    public function testSubstituteVars() {
    	$props['OTHER_CONSTANT'] = "OTHER";
    	$props['MY_CONSTANT'] = "TEST";
    	$props['NEXT_CONSTANT'] = "NEXT";
        
        $result = LoggerOptionConverter::substVars('Value of key is ${MY_CONSTANT}.', $props);
        self::assertEquals('Value of key is TEST.', $result);
        
        $result = LoggerOptionConverter::substVars('Value of key is ${MY_CONSTANT} or ${OTHER_CONSTANT}.', $props);
        self::assertEquals('Value of key is TEST or OTHER.', $result);
        
        $result = LoggerOptionConverter::substVars('Value of key is ${MY_CONSTANT_CONSTANT}.', $props);
        self::assertEquals('Value of key is DEFINE.', $result);
        
        $result = LoggerOptionConverter::substVars('Value of key is ${MY_CONSTANT_CONSTANT} or ${MY_CONSTANT_CONSTANT_OTHER}.', $props);
        self::assertEquals('Value of key is DEFINE or DEFINE_OTHER.', $result);
    }
}