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
 *
 * @package log4php
 */

/**
 * 
 * Base layout class for custom logging collections
 * 
 * example implementation:
 *  
 * class LoggerMongoDbBsonIpLayout extends LoggerMongoDbBsonLayout {
 *    
 *     public function format(LoggerLoggingEvent $event) {
 *         $document       = parent::format($event);
 *         $document['ip'] = $event->getMDC('ip');
 *         
 *         return $document;
 *     }
 * }
 * 
 * This class has been originally contributed from Vladimir Gorej 
 * (http://github.com/log4mongo/log4mongo-php).
 * 
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 * @since 2.1
 */
class LoggerMongoDBBsonLayout extends LoggerLayout {
	
	public function __construct() { }
	
	public function format(LoggerLoggingEvent $event) {
		$document = $this->bsonify($event);
		return $document;
	} 	
	
	public function getContentType() {
		return "application/bson";
	}
	
		/**
	 * Bson-ify logging event into mongo bson
	 * 
	 * @param LoggerLoggingEvent $event
	 * @return ArrayObject
	 */
	public function bsonify(LoggerLoggingEvent $event) {
		$timestampSec  = (int) $event->getTimestamp();
		$timestampUsec = (int) (($event->getTimestamp() - $timestampSec) * 1000000);

		$document = new ArrayObject(array(
			'timestamp'  => new MongoDate($timestampSec, $timestampUsec),
			'level'      => $event->getLevel()->toString(),
			'thread'     => (int) $event->getThreadName(),
			'message'    => $event->getMessage(),
			'loggerName' => $event->getLoggerName() 
		));	

		$this->addLocationInformation($document, $event->getLocationInformation());
		$this->addThrowableInformation($document, $event->getThrowableInformation());
		
		return $document;
	}
	
	/**
	 * Adding, if exists, location information into bson document
	 * 
	 * @param ArrayObject $document
	 * @param LoggerLocationInfo $locationInfo	 * 
	 */
	protected function addLocationInformation(ArrayObject $document, LoggerLocationInfo $locationInfo = null) {
		if ($locationInfo != null) {
			$document['fileName']   = $locationInfo->getFileName();
			$document['method']     = $locationInfo->getMethodName();
			$document['lineNumber'] = ($locationInfo->getLineNumber() == 'NA') ? 'NA' : (int) $locationInfo->getLineNumber();
			$document['className']  = $locationInfo->getClassName();
		}		
	}
	
	/**
	 * Adding, if exists, throwable information into bson document
	 * 
	 * @param ArrayObject $document
	 * @param LoggerThrowableInformation $throwableInfo
	 */
	protected function addThrowableInformation(ArrayObject $document, LoggerThrowableInformation $throwableInfo = null) {
		if ($throwableInfo != null) {
			$document['exception'] = $this->bsonifyThrowable($throwableInfo->getThrowable());
		}
	}
	
	/**
	 * Bson-ifying php native Exception object
	 * Support for innner exceptions
	 * 
	 * @param Exception $ex
	 * @return array
	 */
	protected function bsonifyThrowable(Exception $ex) {
		
		$bsonException = array(				
			'message'    => $ex->getMessage(),
			'code'       => $ex->getCode(),
			'stackTrace' => $ex->getTraceAsString(),
		);
                        
		if (method_exists($ex, 'getPrevious') && $ex->getPrevious() !== null) {
			$bsonException['innerException'] = $this->bsonifyThrowable($ex->getPrevious());
		}
			
		return $bsonException;
	}	
} 
?>