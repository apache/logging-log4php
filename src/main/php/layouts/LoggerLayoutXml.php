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
 * The output of the LoggerXmlLayout consists of a series of log4php:event elements. 
 * 
 * ## Configurable parameters: ##
 * 
 * - **locationInfo** - If set to true, adds the file name and line number at 
 *   which the log statement originated. Slightly slower, defaults to false.
 * - **log4jNamespace** - If set to true then log4j namespace will be used
 *   instead of log4php namespace. This can be usefull when using log viewers 
 *   which can only parse the log4j namespace such as Apache Chainsaw. 
 * 
 * It does not output a complete well-formed XML file. The output is designed 
 * to be included as an external entity in a separate file to form a correct 
 * XML file.
 * 
 * @package log4php
 * @subpackage layouts
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/layouts/xml.html Layout documentation
 */
class LoggerLayoutXml extends LoggerLayout {
	const LOG4J_NS_PREFIX ='log4j';
	const LOG4J_NS = 'http://jakarta.apache.org/log4j/';
	
	const LOG4PHP_NS_PREFIX = 'log4php';
	const LOG4PHP_NS = 'http://logging.apache.org/log4php/';
	
	const CDATA_START = '<![CDATA[';
	const CDATA_END = ']]>';
	const CDATA_PSEUDO_END = ']]&gt;';
	const CDATA_EMBEDDED_END = ']]>]]&gt;<![CDATA[';

	/**
	 * Whether to log location information (file and line number).
	 * @var boolean
	 */
	protected $locationInfo = true;
  
	/**
	 * Whether to use log4j namespace instead of log4php.
	 * @var boolean 
	 */
	protected $log4jNamespace = false;
	
	/** The namespace in use. */
	protected $namespace = self::LOG4PHP_NS;
	
	/** The namespace prefix in use */
	protected $namespacePrefix = self::LOG4PHP_NS_PREFIX;

	public function activateOptions() {
		if ($this->getLog4jNamespace()) {
			$this->namespace        = self::LOG4J_NS;
			$this->namespacePrefix  = self::LOG4J_NS_PREFIX;
		} else {
			$this->namespace        = self::LOG4PHP_NS;
			$this->namespacePrefix  = self::LOG4PHP_NS_PREFIX;
		}
	}
	
	public function getHeader() {
		return "<{$this->namespacePrefix}:eventSet ".
			"xmlns:{$this->namespacePrefix}=\"{$this->namespace}\" ".
			"version=\"0.3\" ".
			"includesLocationInfo=\"".($this->getLocationInfo() ? "true" : "false")."\"".
			">" . PHP_EOL;
	}

	public function format(LoggerLoggingEvent $event) {
		$ns = $this->namespacePrefix;
		
		$loggerName = $event->getLoggerName();
		$timeStamp = number_format((float)($event->getTimeStamp() * 1000), 0, '', '');
		$thread = $event->getThreadName();
		$level = $event->getLevel()->toString();

		$buf  = "<$ns:event logger=\"{$loggerName}\" level=\"{$level}\" thread=\"{$thread}\" timestamp=\"{$timeStamp}\">".PHP_EOL;
		$buf .= "<$ns:message>"; 
		$buf .= $this->encodeCDATA($event->getRenderedMessage()); 
		$buf .= "</$ns:message>".PHP_EOL;

		$ndc = $event->getNDC();
		if(!empty($ndc)) {
			$buf .= "<$ns:NDC><![CDATA[";
			$buf .= $this->encodeCDATA($ndc);
			$buf .= "]]></$ns:NDC>".PHP_EOL;
		}
		
		$mdcMap = $event->getMDCMap();
		if (!empty($mdcMap)) {
			$buf .= "<$ns:properties>".PHP_EOL;
			foreach ($mdcMap as $name=>$value) {
				$buf .= "<$ns:data name=\"$name\" value=\"$value\" />".PHP_EOL;
			}
			$buf .= "</$ns:properties>".PHP_EOL;
		}

		if ($this->getLocationInfo()) {
			$locationInfo = $event->getLocationInformation();
			$buf .= "<$ns:locationInfo ". 
					"class=\"" . $locationInfo->getClassName() . "\" ".
					"file=\"" .  htmlentities($locationInfo->getFileName(), ENT_QUOTES) . "\" ".
					"line=\"" .  $locationInfo->getLineNumber() . "\" ".
					"method=\"" . $locationInfo->getMethodName() . "\" ";
			$buf .= "/>".PHP_EOL;
		}

		$buf .= "</$ns:event>".PHP_EOL;
		
		return $buf;
	}
	
	public function getFooter() {
		return "</{$this->namespacePrefix}:eventSet>" . PHP_EOL;
	}
	
	/**
	 * Returns the value of the 'locationInfo' parameter.
	 * @return boolean
	 */
	public function getLocationInfo() {
		return $this->locationInfo;
	}
  
	/**
	 * Sets the 'locationInfo' parameter.
	 * @param boolean $locationInfo
	 */
	public function setLocationInfo($flag) {
		$this->setBoolean('locationInfo', $flag);
	}
  
	/**
	 * Returns the value of the 'log4jNamespace' parameter.
	 * @return boolean
	 */
	 public function getLog4jNamespace() {
	 	return $this->log4jNamespace;
	 }

	/**
	 * Sets the 'log4jNamespace' parameter.
	 * @param boolean $locationInfo
	 */
	public function setLog4jNamespace($flag) {
		$this->setBoolean('log4jNamespace', $flag);
	}
	
	/** 
	 * Encases a string in CDATA tags, and escapes any existing CDATA end 
	 * tags already present in the string.
	 * @param string $string 
	 */
	private function encodeCDATA($string) {
		$string = str_replace(self::CDATA_END, self::CDATA_EMBEDDED_END, $string);
		return self::CDATA_START . $string . self::CDATA_END;
	}
}
