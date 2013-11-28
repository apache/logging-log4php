<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apache\Log4php\Configuration\Adapters;

use Apache\Log4php\LoggerException;

/**
 * Converts ini configuration files to a PHP array.
 *
 * These used to be called "properties" files (inherited from log4j), and that
 * file extension is still supported.
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @since 2.2
 */
class IniAdapter implements AdapterInterface
{
    /** Name to assign to the root logger. */
    const ROOT_LOGGER_NAME = "root";

    /** Prefix used for defining logger additivity. */
    const ADDITIVITY_PREFIX = "log4php.additivity.";

    /** Prefix used for defining logger threshold. */
    const THRESHOLD_PREFIX = "log4php.threshold";

    /** Prefix used for defining the root logger. */
    const ROOT_LOGGER_PREFIX = "log4php.rootLogger";

    /** Prefix used for defining a logger. */
    const LOGGER_PREFIX = "log4php.logger.";

    /** Prefix used for defining an appender. */
    const APPENDER_PREFIX = "log4php.appender.";

    /** Prefix used for defining a renderer. */
    const RENDERER_PREFIX = "log4php.renderer.";

    /** Holds the configuration. */
    private $config = array();

    /**
     * Loads and parses the INI configuration file.
     *
     * @param  string          $url Path to the config file.
     * @throws LoggerException
     */
    private function load($url)
    {
        if (!file_exists($url)) {
            throw new LoggerException("File [$url] does not exist.");
        }

        $properties = @parse_ini_file($url, true);
        if ($properties === false) {
            $error = error_get_last();
            throw new LoggerException("Error parsing configuration file: {$error['message']}");
        }

        return $properties;
    }

    /**
    * Converts the provided INI configuration file to a PHP array config.
    *
    * @param string $path Path to the config file.
    * @throws LoggerException If the file cannot be loaded or parsed.
    */
    public function convert($path)
    {
        // Load the configuration
        $properties = $this->load($path);

        // Parse threshold
        if (isset($properties[self::THRESHOLD_PREFIX])) {
            $this->config['threshold'] = $properties[self::THRESHOLD_PREFIX];
        }

        // Parse root logger
        if (isset($properties[self::ROOT_LOGGER_PREFIX])) {
            $this->parseLogger($properties[self::ROOT_LOGGER_PREFIX], self::ROOT_LOGGER_NAME);
        }

        $appenders = array();

        foreach ($properties as $key => $value) {

            if ($this->beginsWith($key, self::LOGGER_PREFIX)) {
                // Parse loggers
                $name = substr($key, strlen(self::LOGGER_PREFIX));
                $this->parseLogger($value, $name);
            } elseif ($this->beginsWith($key, self::ADDITIVITY_PREFIX)) {
                // Parse additivity
                $name = substr($key, strlen(self::ADDITIVITY_PREFIX));
                $this->config['loggers'][$name]['additivity'] = $value;
            } elseif ($this->beginsWith($key, self::APPENDER_PREFIX)) {
                // Parse appenders
                $this->parseAppender($key, $value);
            } elseif ($this->beginsWith($key, self::RENDERER_PREFIX)) {
                // Parse renderers
                $this->parseRenderer($key, $value);
            }
        }

        return $this->config;
    }

    /**
     * Parses a logger definition.
     *
     * Loggers are defined in the following manner:
     * <pre>
     * log4php.logger.<name> = [<level>], [<appender-ref>, <appender-ref>, ...]
     * </pre>
     *
     * @param string $value The configuration value (level and appender-refs).
     * @param string $name  Logger name.
     */
    private function parseLogger($value, $name)
    {
        // Value is divided by commas
        $parts = explode(',', $value);
        if (empty($value) || empty($parts)) {
            return;
        }

        // The first value is the logger level
        $level = array_shift($parts);

        // The remaining values are appender references
        $appenders = array();
        while ($appender = array_shift($parts)) {
            $appender = trim($appender);
            if (!empty($appender)) {
                $appenders[] = trim($appender);
            }
        }

        // Find the target configuration
        if ($name == self::ROOT_LOGGER_NAME) {
            $this->config['rootLogger']['level'] = trim($level);
            $this->config['rootLogger']['appenders'] = $appenders;
        } else {
            $this->config['loggers'][$name]['level'] = trim($level);
            $this->config['loggers'][$name]['appenders'] = $appenders;
        }
    }

    /**
     * Parses an configuration line pertaining to an appender.
     *
     * Parses the following patterns:
     *
     * Appender class:
     * <pre>
     * log4php.appender.<name> = <class>
     * </pre>
     *
     * Appender parameter:
     * <pre>
     * log4php.appender.<name>.<param> = <value>
     * </pre>
     *
      * Appender threshold:
     * <pre>
     * log4php.appender.<name>.threshold = <level>
     * </pre>
     *
      * Appender layout:
     * <pre>
     * log4php.appender.<name>.layout = <layoutClass>
     * </pre>
     *
     * Layout parameter:
     * <pre>
     * log4php.appender.<name>.layout.<param> = <value>
     * </pre>
     *
     * For example, a full appender config might look like:
     * <pre>
     * log4php.appender.myAppender = ConsoleAppender
     * log4php.appender.myAppender.threshold = info
     * log4php.appender.myAppender.target = stdout
     * log4php.appender.myAppender.layout = PatternLayout
     * log4php.appender.myAppender.layout.conversionPattern = "%d %c: %m%n"
     * </pre>
     *
     * After parsing all these options, the following configuration can be
     * found under $this->config['appenders']['myAppender']:
     * <pre>
     * array(
     * 	'class' => ConsoleAppender,
     * 	'threshold' => info,
     * 	'params' => array(
     * 		'target' => 'stdout'
     * 	),
     * 	'layout' => array(
     * 		'class' => 'ConsoleAppender',
     * 		'params' => array(
     * 			'conversionPattern' => '%d %c: %m%n'
     * 		)
     * 	)
     * )
     * </pre>
     *
     * @param string $key
     * @param string $value
     */
    private function parseAppender($key, $value)
    {
        // Remove the appender prefix from key
        $subKey = substr($key, strlen(self::APPENDER_PREFIX));

        // Divide the string by dots
        $parts = explode('.', $subKey);
        $count = count($parts);

        // The first part is always the appender name
        $name = trim($parts[0]);

        if ($count == 1) {
            // Only one part - this line defines the appender class
            $this->config['appenders'][$name]['class'] = $value;
            return;
        } elseif ($count == 2) {
            // Two parts - either a parameter, a threshold or layout class
            if ($parts[1] == 'layout') {
                $this->config['appenders'][$name]['layout']['class'] = $value;

                return;
            } elseif ($parts[1] == 'threshold') {
                $this->config['appenders'][$name]['threshold'] = $value;

                return;
            } else {
                $this->config['appenders'][$name]['params'][$parts[1]] = $value;

                return;
            }
        } elseif ($count == 3) {
            // Three parts - this can only be a layout parameter
            if ($parts[1] == 'layout') {
                $this->config['appenders'][$name]['layout']['params'][$parts[2]] = $value;

                return;
            }
        }

        trigger_error("log4php: Don't know how to parse line: \"$key = $value\". Skipping.", E_USER_WARNING);
    }

    /**
     * Parses a renderer definition.
     *
     * Renderers are defined as:
     * <pre>
     * log4php.renderer.<renderedClass> = <renderingClass>
     * </pre>
     *
     * @param string $key   log4php.renderer.<renderedClass>
     * @param string $value <renderingClass>
     */
    private function parseRenderer($key, $value)
    {
        // Remove the appender prefix from key
        $renderedClass = substr($key, strlen(self::APPENDER_PREFIX));
        $renderingClass = $value;

        $this->config['renderers'][] = compact('renderedClass', 'renderingClass');
    }

    /** Helper method. Returns true if $str begins with $sub. */
    private function beginsWith($str, $sub)
    {
        return (strncmp($str, $sub, strlen($sub)) == 0);
    }
}
