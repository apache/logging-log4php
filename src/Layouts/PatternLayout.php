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

namespace Apache\Log4php\Layouts;

use Apache\Log4php\Helpers\PatternParser;
use Apache\Log4php\LoggerException;
use Apache\Log4php\LoggingEvent;

/**
 * A flexible layout configurable with a pattern string.
 *
 * Configurable parameters:
 *
 * * converionPattern - A string which controls the formatting of logging
 *   events. See docs for full specification.
 */
class PatternLayout extends AbstractLayout {

	/** Default conversion pattern */
	const DEFAULT_CONVERSION_PATTERN = '%date %-5level %logger %message%newline';

	/** Default conversion TTCC Pattern */
	const TTCC_CONVERSION_PATTERN = '%d [%t] %p %c %x - %m%n';

	/** The conversion pattern. */
	protected $pattern = self::DEFAULT_CONVERSION_PATTERN;

	/** Maps conversion keywords to the relevant converter (default implementation). */
	protected static $defaultConverterMap = array(
		'c' => 'LoggerConverter',
		'lo' => 'LoggerConverter',
		'logger' => 'LoggerConverter',

		'C' => 'ClassConverter',
		'class' => 'ClassConverter',

		'cookie' => 'CookieConverter',

		'd' => 'DateConverter',
		'date' => 'DateConverter',

		'e' => 'EnvironmentConverter',
		'env' => 'EnvironmentConverter',

		'ex' => 'ThrowableConverter',
		'exception' => 'ThrowableConverter',
		'throwable' => 'ThrowableConverter',

		'F' => 'FileConverter',
		'file' => 'FileConverter',

		'l' => 'LocationConverter',
		'location' => 'LocationConverter',

		'L' => 'LineConverter',
		'line' => 'LineConverter',

		'm' => 'MessageConverter',
		'msg' => 'MessageConverter',
		'message' => 'MessageConverter',

		'M' => 'MethodConverter',
		'method' => 'MethodConverter',

		'n' => 'NewLineConverter',
		'newline' => 'NewLineConverter',

		'p' => 'LevelConverter',
		'le' => 'LevelConverter',
		'level' => 'LevelConverter',

		'r' => 'RelativeConverter',
		'relative' => 'RelativeConverter',

		'req' => 'RequestConverter',
		'request' => 'RequestConverter',

		's' => 'ServerConverter',
		'server' => 'ServerConverter',

		'ses' => 'SessionConverter',
		'session' => 'SessionConverter',

		'sid' => 'SessionIdConverter',
		'sessionid' => 'SessionIdConverter',

		't' => 'ProcessConverter',
		'pid' => 'ProcessConverter',
		'process' => 'ProcessConverter',

		'x' => 'NdcConverter',
		'ndc' => 'NdcConverter',

		'X' => 'MdcConverter',
		'mdc' => 'MdcConverter',
	);

	/** Maps conversion keywords to the relevant converter. */
	protected $converterMap = array();

	/**
	 * Head of a chain of Converters.
	 * @var AbstractConverter
	 */
	private $head;

	/** Returns the default converter map. */
	public static function getDefaultConverterMap() {
		return self::$defaultConverterMap;
	}

	/** Constructor. Initializes the converter map. */
	public function __construct() {
		$this->converterMap = self::$defaultConverterMap;
	}

	/**
	 * Sets the conversionPattern option. This is the string which
	 * controls formatting and consists of a mix of literal content and
	 * conversion specifiers.
	 * @param array $conversionPattern
	 */
	public function setConversionPattern($conversionPattern) {
		$this->pattern = $conversionPattern;
	}

	/**
	 * Processes the conversion pattern and creates a corresponding chain of
	 * pattern converters which will be used to format logging events.
	 */
	public function activateOptions() {
		if (!isset($this->pattern)) {
			throw new LoggerException("Mandatory parameter 'conversionPattern' is not set.");
		}

		$parser = new PatternParser($this->pattern, $this->converterMap);
		$this->head = $parser->parse();
	}

	/**
	 * Produces a formatted string as specified by the conversion pattern.
	 *
	 * @param LoggingEvent $event
	 * @return string
	 */
	public function format(LoggingEvent $event) {
		$sbuf = '';
		$converter = $this->head;
		while ($converter !== null) {
			$converter->format($sbuf, $event);
			$converter = $converter->next;
		}
		return $sbuf;
	}
}