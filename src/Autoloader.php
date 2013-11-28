<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *		http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apache\Log4php;

/**
 * PSR-4 compliant autoloader implementation.
 */
class Autoloader
{
    const BASE_NAMESPACE = 'Apache\\Log4php\\';

    public function autoload($class)
    {
        // Base directory for the namespace prefix
        $baseDir = __DIR__ . '/../src/';

        // Skip classes which are not in base namespace
        $len = strlen(self::BASE_NAMESPACE);
        if (strncmp(self::BASE_NAMESPACE, $class, $len) !== 0) {
            return;
        }

        // Locate the class in base dir, based on namespace
        $classPath = str_replace('\\', '/', substr($class, $len));
        $file = $baseDir . $classPath . '.php';

        // If the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    }

    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }
}
