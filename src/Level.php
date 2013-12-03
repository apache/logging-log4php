<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apache\Log4php;

/**
 * Defines the minimum set of levels recognized by the system, which are:
 *
 * - <var>OFF</var>
 * - <var>FATAL</var>
 * - <var>ERROR</var>
 * - <var>WARN</var>
 * - <var>INFO</var>
 * - <var>DEBUG</var>
 * - <var>ALL</var>
 *
 * The {@link Level} class may be subclassed to define a larger level set.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php
 * @since 0.5
 */
class Level
{
    const OFF = 2147483647;
    const FATAL = 50000;
    const ERROR = 40000;
    const WARN = 30000;
    const INFO = 20000;
    const DEBUG = 10000;
    const TRACE = 5000;
    const ALL = -2147483647;

    /** Integer level value. */
    private $level;

    /** Contains a list of instantiated levels. */
    private static $levelMap;

    /** String representation of the level. */
    private $levelStr;

    /**
     * Equivalent syslog level.
     * @var integer
     */
    private $syslogEquivalent;

    /**
     * Constructor
     *
     * @param integer $level
     * @param string  $levelStr
     * @param integer $syslogEquivalent
     */
    private function __construct($level, $levelStr, $syslogEquivalent)
    {
        $this->level = $level;
        $this->levelStr = $levelStr;
        $this->syslogEquivalent = $syslogEquivalent;
    }

    /**
     * Compares two logger levels.
     *
     * @param  Level   $other
     * @return boolean
     */
    public function equals($other)
    {
        if ($other instanceof Level) {
            if ($this->level == $other->level) {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Returns an Off Level
     * @return Level
     */
    public static function getLevelOff()
    {
        if (!isset(self::$levelMap[Level::OFF])) {
            self::$levelMap[Level::OFF] = new Level(Level::OFF, 'OFF', LOG_ALERT);
        }

        return self::$levelMap[Level::OFF];
    }

    /**
     * Returns a Fatal Level
     * @return Level
     */
    public static function getLevelFatal()
    {
        if (!isset(self::$levelMap[Level::FATAL])) {
            self::$levelMap[Level::FATAL] = new Level(Level::FATAL, 'FATAL', LOG_ALERT);
        }

        return self::$levelMap[Level::FATAL];
    }

    /**
     * Returns an Error Level
     * @return Level
     */
    public static function getLevelError()
    {
        if (!isset(self::$levelMap[Level::ERROR])) {
            self::$levelMap[Level::ERROR] = new Level(Level::ERROR, 'ERROR', LOG_ERR);
        }

        return self::$levelMap[Level::ERROR];
    }

    /**
     * Returns a Warn Level
     * @return Level
     */
    public static function getLevelWarn()
    {
        if (!isset(self::$levelMap[Level::WARN])) {
            self::$levelMap[Level::WARN] = new Level(Level::WARN, 'WARN', LOG_WARNING);
        }

        return self::$levelMap[Level::WARN];
    }

    /**
     * Returns an Info Level
     * @return Level
     */
    public static function getLevelInfo()
    {
        if (!isset(self::$levelMap[Level::INFO])) {
            self::$levelMap[Level::INFO] = new Level(Level::INFO, 'INFO', LOG_INFO);
        }

        return self::$levelMap[Level::INFO];
    }

    /**
     * Returns a Debug Level
     * @return Level
     */
    public static function getLevelDebug()
    {
        if (!isset(self::$levelMap[Level::DEBUG])) {
            self::$levelMap[Level::DEBUG] = new Level(Level::DEBUG, 'DEBUG', LOG_DEBUG);
        }

        return self::$levelMap[Level::DEBUG];
    }

    /**
     * Returns a Trace Level
     * @return Level
     */
    public static function getLevelTrace()
    {
        if (!isset(self::$levelMap[Level::TRACE])) {
            self::$levelMap[Level::TRACE] = new Level(Level::TRACE, 'TRACE', LOG_DEBUG);
        }

        return self::$levelMap[Level::TRACE];
    }

    /**
     * Returns an All Level
     * @return Level
     */
    public static function getLevelAll()
    {
        if (!isset(self::$levelMap[Level::ALL])) {
            self::$levelMap[Level::ALL] = new Level(Level::ALL, 'ALL', LOG_DEBUG);
        }

        return self::$levelMap[Level::ALL];
    }

    /**
     * Return the syslog equivalent of this level as an integer.
     * @return integer
     */
    public function getSyslogEquivalent()
    {
        return $this->syslogEquivalent;
    }

    /**
     * Returns *true* if this level has a higher or equal
     * level than the level passed as argument, *false*
     * otherwise.
     *
     * @param  Level   $other
     * @return boolean
     */
    public function isGreaterOrEqual($other)
    {
        return $this->level >= $other->level;
    }

    /**
     * Returns the string representation of this level.
     * @return string
     */
    public function toString()
    {
        return $this->levelStr;
    }

    /**
     * Returns the string representation of this level.
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Returns the integer representation of this level.
     * @return integer
     */
    public function toInt()
    {
        return $this->level;
    }

    /**
     * Convert the input argument to a level. If the conversion fails, then
     * this method returns the provided default level.
     *
     * @param  mixed $arg     The value to convert to level.
     * @param  Level $default Value to return if conversion is not possible.
     * @return Level
     */
    public static function toLevel($arg, $defaultLevel = null)
    {
        if (is_int($arg)) {
            if ($arg === self::ALL) {
                return self::getLevelAll();
            } elseif ($arg === self::TRACE) {
                return self::getLevelTrace();
            } elseif ($arg === self::DEBUG) {
                return self::getLevelDebug();
            } elseif ($arg === self::INFO) {
                return self::getLevelInfo();
            } elseif ($arg === self::WARN) {
                return self::getLevelWarn();
            } elseif ($arg === self::ERROR) {
                return self::getLevelError();
            } elseif ($arg === self::FATAL) {
                return self::getLevelFatal();
            } elseif ($arg === self::OFF) {
                return self::getLevelOff();
            }
        } else {
            $arg = strtoupper($arg);
            if ($arg === 'ALL') {
                return self::getLevelAll();
            } elseif ($arg === 'TRACE') {
                return self::getLevelTrace();
            } elseif ($arg === 'DEBUG') {
                return self::getLevelDebug();
            } elseif ($arg === 'INFO') {
                return self::getLevelInfo();
            } elseif ($arg === 'WARN') {
                return self::getLevelWarn();
            } elseif ($arg === 'ERROR') {
                return self::getLevelError();
            } elseif ($arg === 'FATAL') {
                return self::getLevelFatal();
            } elseif ($arg === 'OFF') {
                return self::getLevelOff();
            }
        }

        return $defaultLevel;
    }
}
