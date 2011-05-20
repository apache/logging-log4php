<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *	   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * LoggerConfiguratorPhp class
 *
 * This class allows configuration of log4php through a PHP array or an external file that
 * returns a PHP array. If you use the PHP array option, you can simply give an array instead
 * of an URL parameter.
 *
 * An example for this configurator is:
 *
 * {@example ../../examples/php/configurator_php.php 19}
 * 
 * Which includes the following snippet:
 * 
 * {@example ../../examples/resources/configurator_php.php 18}
 *
 * @package log4php
 * @subpackage configurators
 * @since 2.0
 */
class LoggerConfiguratorPhp implements LoggerConfigurator {
	
	public function configure(LoggerHierarchy $hierarchy, $url = '') {
		return $this->doConfigure($url, $hierarchy);
	}
	
	private function doConfigure($url, LoggerHierarchy $hierarchy) {
		if (!is_array($url)) {
 			$config = require $url;
 		} else {
 			$config = $url;
 		}
		
		// set threshold
		if(isset($config['threshold'])) {
			$hierarchy->setThreshold(LoggerOptionConverter::toLevel($config['threshold'], LoggerLevel::getLevelAll()));
		}

		// add renderes
		if (isset($config['renderers'])) {
			foreach ($config['renderers'] as $renderedClass => $renderingClass) {
				$hierarchy->getRendererMap()->addRenderer($renderedClass, $renderingClass);
			}
		}
		
		// parse and create appenders
		if(isset($config['appenders'])) {
			
			foreach($config['appenders'] as $appenderName => $appenderProperties) {
				
				$appender = LoggerAppenderPool::getAppenderFromPool($appenderName, $appenderProperties['class']);
				
				// unset so that the property wont be drawn up again
				unset($appenderProperties['class']);
				
				if($appender->requiresLayout()) {
					
					if(isset($appenderProperties['layout'])) {
						
						if(isset($appenderProperties['layout']['class']) and !empty($appenderProperties['layout']['class'])) {
							$layoutClass = $appenderProperties['layout']['class'];
						} else {
							$layoutClass = 'LoggerLayoutSimple';
						}
						
						$layout = LoggerReflectionUtils::createObject($layoutClass);
						if($layout === null) {
							$layout = LoggerReflectionUtils::createObject('LoggerLayoutSimple');
						}
						
						if(isset($appenderProperties['file']) && method_exists($appender, 'setFileName')) { 
 						    $appender->setFile($appenderProperties['file'], true); 
						}
						
						if($layout instanceof LoggerLayoutPattern) {
							$layout->setConversionPattern($appenderProperties['layout']['conversionPattern']);
						}
						
						$appender->setLayout($layout);
						
						// unset so that the property wont be drawn up again
						unset($appenderProperties['layout']);
					} else {
						// TODO: throw exception?
					}
					
				}
				// set remaining properties and activate appender
				$setter = new LoggerReflectionUtils($appender);
				foreach ($appenderProperties as $key => $val) {
					$setter->setProperty($key, $val);
 				}
				$setter->activate();
			}
			
		}
		
		// parse and create root logger
		if(isset($config['rootLogger'])) {
			$rootLogger = $hierarchy->getRootLogger();
			if(isset($config['rootLogger']['level'])) {
				$rootLogger->setLevel(LoggerOptionConverter::toLevel($config['rootLogger']['level'], LoggerLevel::getLevelDebug()));
				if(isset($config['rootLogger']['appenders'])) {
					foreach($config['rootLogger']['appenders'] as $appenderName) {
						$appender = LoggerAppenderPool::getAppenderFromPool($appenderName);
						if($appender !== null) {
							$rootLogger->addAppender($appender);
						}
					}
				}	
			}
		}
		
		// parse and create loggers
		if(isset($config['loggers'])) {
			foreach($config['loggers'] as $loggerName => $loggerProperties) {
				if(is_string($loggerName)) {
					$logger = $hierarchy->getLogger($loggerName);
					
					if(isset($loggerProperties['level'])) {
						$logger->setLevel(LoggerOptionConverter::toLevel($loggerProperties['level'], LoggerLevel::getLevelDebug()));
						if(isset($loggerProperties['appenders'])) {
							foreach($loggerProperties['appenders'] as $appenderName) {
								$appender = LoggerAppenderPool::getAppenderFromPool($appenderName);
								if($appender !== null) {
									$logger->addAppender($appender);
								}
							}
						}	
					}
				} else {
					// TODO: throw exception
				}
			}
		}
		
		return true;
	}
	
}
