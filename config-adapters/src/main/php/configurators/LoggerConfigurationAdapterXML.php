<?php

/**
 * Loads configuration from an XML file and converts it to a PHP array.
 */
class LoggerConfigurationAdapterXML implements LoggerConfigurationAdapter
{
	/** Path to the XML schema used for validation. */
	const SCHEMA_PATH = '/../xml/log4php.xsd';
	
	private $config = array(
		'appenders' => array(),
		'loggers' => array(),
		'renderers' => array(),
	);
	
	public function convert($url)
	{
		$xml = $this->loadXML($url);
		
		$this->parseConfiguration($xml);

		// Parse the <root> node
		if (isset($xml->root)) {		
			$this->parseRootLogger($xml->root);
		}
		
		// Process <logger> nodes
		foreach($xml->logger as $logger) {
			$this->parseLogger($logger);
		} 
		
		// Process <appender> nodes
		foreach($xml->appender as $appender) {
			$this->parseAppender($appender);
		}
		
		// Process <renderer> nodes
		foreach($xml->renderer as $rendererNode) {
			$this->parseRenderer($rendererNode);
		}
		
		return $this->config;
	}
	
	/**
	 * Loads and validates the XML.
	 * @param string $url Input XML.
	 */
	private function loadXML($url) {
		
		// Load the config file
		$config = @file_get_contents($url);
		if ($config === false) {
			$error = error_get_last();
			throw new LoggerException("Cannot load config file: {$error['message']}");
		}
		
		// Validate XML against schema
		$internal = libxml_use_internal_errors(true);
		
		$this->validateXML($config);
		
		libxml_clear_errors();
		libxml_use_internal_errors($internal);
		
		// Load XML
		$xml = simplexml_load_string($config);
		if ($xml === false) {
			throw new LoggerException("Failed parsing XML configuration file.");
		}
		return $xml;
	}
	
	/**
	 * DOMDocument is used here for validation because SimpleXML doesn't 
	 * implement this feature.
	 * @param string $input The configuration XML.
	 */
	private function validateXML($url) {
		$schema = dirname(__FILE__) . self::SCHEMA_PATH;
		try {
			$dom = new DOMDocument();
			$dom->loadXML($url);
		} catch(Exception $e) {
			throw new LoggerException("Failed parsing XML configuration file.");
		}
		
		$success = $dom->schemaValidate($schema);
		if ($success === false) {
			$errors = libxml_get_errors();
			foreach($errors as $error) {
				$message = trim($error->message) . " On line {$error->line} of the configuration file.";
				$this->warn($message);
			}
			throw new LoggerException("The XML configuration file failed validation.");
		}
	}
	
	/**
	 * Parses the <configuration> node.
	 */
	private function parseConfiguration(SimpleXMLElement $xml) {
		$attributes = $xml->attributes();
		if (isset($attributes['threshold'])) {
			$this->config['threshold'] = (string) $attributes['threshold'];
		}
	}
	
	/** Parses an <appender> node. */
	private function parseAppender(SimpleXMLElement $node) {
		$name = $this->getAttributeValue($node, 'name');

		$appender = array();
		$appender['class'] = $this->getAttributeValue($node, 'class');
		
		$attrs = $node->attributes();
		if (isset($attrs['threshold'])) {
			$appender['threshold'] = (string) $attrs['threshold'];
		}
		
		if (isset($node->layout)) {
			$appender['layout']= $this->parseLayout($node->layout, $name);
		}
		
		if (count($node->param) > 0) {
			$appender['params'] = $this->parseParameters($node);
		}
		
		foreach($node->filter as $filterNode) {
			$appender['filters'][] = $this->parseFilter($filterNode);
		}
		
		$this->config['appenders'][$name] = $appender;
	}
	
	/** Parses a <layout> node. */
	private function parseLayout(SimpleXMLElement $node, $appenderName) {
		$layout = array();
		$layout['class'] = $this->getAttributeValue($node, 'class');
		
		if (count($node->param) > 0) {
			$layout['params'] = $this->parseParameters($node);
		}
		
		return $layout;
	}
	/** Parses any <param> child nodes returning them in an array. */
	private function parseParameters($node) {
		$params = array();

		foreach($node->param as $paramNode) {
			$attrs = $paramNode->attributes();
			$name = (string) $attrs['name'];
			$value = (string) $attrs['value'];
			
			$params[$name] = $value;
		}

		return $params;
	}
	
	/** Parses a <root> node. */
	private function parseRootLogger(SimpleXMLElement $node) {
		$logger = array();
		
		var_dump($node->level['value']);
		
		if (isset($node->level)) {
			$logger['level'] = $this->getAttributeValue($node->level, 'value');
		}
		
		$logger['appenders'] = array();
		foreach($node->appender_ref as $appender) {
			$logger['appenders'][] = $this->getAttributeValue($appender, 'ref');
		}
		
		$this->config['rootLogger'] = $logger;
	}
	
	/** Parses a <logger> node. */
	private function parseLogger(SimpleXMLElement $node) {
		$logger = array();
		$attributes = $node->attributes();
		
		$name = (string) $attributes['name'];
		
		if (isset($node->level)) {
			$logger['level'] = $this->getAttributeValue($node->level, 'value');
		}
		
		$logger['appenders'] = $this->parseAppenderReferences($node, $name);

		if (isset($this->config['loggers'][$name])) {
			$this->warn("Duplicate logger definition for $name. Overwriting.");
		}
		
		$this->config['loggers'][$name] = $logger;
	}
	
	/** 
	 * Parses a <logger> node for appender references and returns them in an array.
	 */
	private function parseAppenderReferences(SimpleXMLElement $node, $name) {
		$refs = array();
		foreach($node->appender_ref as $ref) {
			$refs[] = $this->getAttributeValue($ref, 'ref');
		}
		
		return $refs;
	}
	
	private function parseFilter($filterNode) {
		$filter = array();
		$filter['class'] = $this->getAttributeValue($filterNode, 'class');
		
		if (count($filterNode->param) > 0) {
			$filter['params'] = $this->parseParameters($filterNode);
		}
		
		return $filter;
	}
	
	/** Parses a <renderer> node. */
	private function parseRenderer(SimpleXMLElement $node) {
		$renderedClass = $this->getAttributeValue($node, 'renderedClass');
		$renderingClass = $this->getAttributeValue($node, 'renderingClass');
		
		$this->config['renderers'][] = compact('renderedClass', 'renderingClass');
	}
	
	// ******************************************
	// ** Helper methods                       **
	// ******************************************
	
	private function getAttributeValue(SimpleXMLElement $node, $name) {
		$attrs = $node->attributes();
		return isset($attrs[$name]) ? (string) $attrs[$name] : null;
	}
	
	private function warn($message) {
		trigger_error("log4php: " . $message, E_USER_WARNING);
	}

}

?>