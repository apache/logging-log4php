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
 */

/**
 * This layout outputs events in a JSON-encoded GELF format.
 *
 * This class was originally contributed by Dmitry Ulyanov.
 *
 * ## Configurable parameters: ##
 *
 * - **host** - Server on which logs are collected.
 * - **shortMessageLength** - Maximum length of short message.
 * - **locationInfo** - If set to true, adds the file name and line number at
 *   which the log statement originated. Slightly slower, defaults to false.
 *
 * @package log4php
 * @subpackage layouts
 * @since 2.4.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/layouts/html.html Layout documentation
 * @link http://github.com/d-ulyanov/log4php-graylog2 Dmitry Ulyanov's original submission.
 * @link http://graylog2.org/about/gelf GELF documentation.
 */
class LoggerLayoutGelf extends LoggerLayout {
    /**
     *  GELF log levels according to syslog priority
     */
    const LEVEL_EMERGENCY = 0;
    const LEVEL_ALERT = 1;
    const LEVEL_CRITICAL = 2;
    const LEVEL_ERROR = 3;
    const LEVEL_WARNING = 4;
    const LEVEL_NOTICE = 5;
    const LEVEL_INFO = 6;
    const LEVEL_DEBUG = 7;

    /**
     * Version of Graylog2 GELF protocol (1.1 since 11/2013)
     */
    const GELF_PROTOCOL_VERSION = '1.1';

    /**
     * Whether to log location information (file and line number).
     * @var boolean
     */
    protected $locationInfo = false;

    /**
     * Maximum length of short message
     * @var int
     */
    protected $shortMessageLength = 255;

    /**
     * Server on which logs are collected
     * @var string
     */
    protected $host;

    /**
     * Maps log4php levels to equivalent Gelf levels
     * @var array
     */
    protected $levelMap = array(
        LoggerLevel::TRACE  => self::LEVEL_DEBUG,
        LoggerLevel::DEBUG  => self::LEVEL_DEBUG,
        LoggerLevel::INFO   => self::LEVEL_INFO,
        LoggerLevel::WARN   => self::LEVEL_WARNING,
        LoggerLevel::ERROR  => self::LEVEL_ERROR,
        LoggerLevel::FATAL  => self::LEVEL_CRITICAL,
    );

    public function activateOptions() {
        $this->setHost(gethostname());
        return parent::activateOptions();
    }

    /**
     * @param LoggerLoggingEvent $event
     * @return string
     */
    public function format(LoggerLoggingEvent $event) {
        $messageAsArray = array(
            // Basic fields
            'version'       => self::GELF_PROTOCOL_VERSION,
            'host'          => $this->getHost(),
            'short_message' => $this->getShortMessage($event),
            'full_message'  => $this->getFullMessage($event),
            'timestamp'     => $event->getTimeStamp(),
            'level'         => $this->getGelfLevel($event->getLevel()),
            // Additional fields
            '_facility'     => $event->getLoggerName(),
            '_thread'       => $event->getThreadName(),
        );

        if ($this->getLocationInfo()) {
            $messageAsArray += $this->getEventLocationFields($event);
        }

        $messageAsArray += $this->getEventMDCFields($event);

        return json_encode($messageAsArray);
    }

    /**
     * Returns event location information as array
     * @param LoggerLoggingEvent $event
     * @return array
     */
    public function getEventLocationFields(LoggerLoggingEvent $event) {
        $locInfo = $event->getLocationInformation();

        return array(
            '_file'   => $locInfo->getFileName(),
            '_line'   => $locInfo->getLineNumber(),
            '_class'  => $locInfo->getClassName(),
            '_method' => $locInfo->getMethodName()
        );
    }

    /**
     * Returns event MDC data as array
     * @param LoggerLoggingEvent $event
     * @return array
     */
    public function getEventMDCFields(LoggerLoggingEvent $event) {
        $fields = array();

        foreach ($event->getMDCMap() as $key => $value) {
            $fieldName = "_".$key;
            if ($this->isAdditionalFieldNameValid($fieldName)) {
                $fields[$fieldName] = $value;
            }
        }

        return $fields;
    }

    /**
     * Checks is field name valid according to Gelf specification
     * @param string $fieldName
     * @return bool
     */
    public function isAdditionalFieldNameValid($fieldName) {
        return (preg_match("@^_[\w\.\-]*$@", $fieldName) AND $fieldName != '_id');
    }

    /**
     * Sets the 'locationInfo' parameter.
     * @param boolean $locationInfo
     */
    public function setLocationInfo($locationInfo) {
        $this->setBoolean('locationInfo', $locationInfo);
    }

    /**
     * Returns the value of the 'locationInfo' parameter.
     * @return boolean
     */
    public function getLocationInfo() {
        return $this->locationInfo;
    }

    /**
     * @param LoggerLoggingEvent $event
     * @return string
     */
    public function getShortMessage(LoggerLoggingEvent $event) {
        $shortMessage = mb_substr($event->getRenderedMessage(), 0, $this->getShortMessageLength());
        return $this->cleanNonUtfSymbols($shortMessage);
    }

    /**
     * @param LoggerLoggingEvent $event
     * @return string
     */
    public function getFullMessage(LoggerLoggingEvent $event) {
        return $this->cleanNonUtfSymbols(
            $event->getRenderedMessage()
        );
    }

    /**
     * @param LoggerLevel $level
     * @return int
     */
    public function getGelfLevel(LoggerLevel $level) {
        $int = $level->toInt();

        if (isset($this->levelMap[$int])) {
            return $this->levelMap[$int];
        } else {
            return self::LEVEL_ALERT;
        }
    }

    /**
     * @param int $shortMessageLength
     */
    public function setShortMessageLength($shortMessageLength) {
        $this->setPositiveInteger('shortMessageLength', $shortMessageLength);
    }

    /**
     * @return int
     */
    public function getShortMessageLength() {
        return $this->shortMessageLength;
    }

    /**
     * @param string $host
     */
    public function setHost($host) {
        $this->setString('host', $host);
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @param string $message
     * @return string
     */
    protected function cleanNonUtfSymbols($message) {
        /**
         * Reject overly long 2 byte sequences, as well as characters
         * above U+10000 and replace with ?
         */
        $message = preg_replace(
            '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
            '|[\x00-\x7F][\x80-\xBF]+'.
            '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
            '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
            '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
            '?',
            $message
        );

        /**
         * Reject overly long 3 byte sequences and UTF-16 surrogates
         * and replace with ?
         */
        $message = preg_replace(
            '/\xE0[\x80-\x9F][\x80-\xBF]'.
            '|\xED[\xA0-\xBF][\x80-\xBF]/S',
            '?',
            $message
        );

        return $message;
    }
}
