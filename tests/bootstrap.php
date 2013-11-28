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

error_reporting(E_ALL | E_STRICT);

// Required for testing logging of sessionID in pattern layout
session_start();

date_default_timezone_set('Europe/London');

// Define a temp dir where tests may write to
$tmpDir = __DIR__ . '/../target/temp/phpunit';
if (!is_dir($tmpDir)) {
    mkdir($tmpDir, 0777, true);
}
define('PHPUNIT_TEMP_DIR', realpath($tmpDir));

// Make the path to the configurations dir for easier access
$confDir = __DIR__ . '/resources/configs';
define('PHPUNIT_CONFIG_DIR', realpath($confDir));

require __DIR__ . '/../src/Autoloader.php';
require __DIR__ . '/src/TestHelper.php';

$autoloader = new Apache\Log4php\Autoloader();
$autoloader->register();
