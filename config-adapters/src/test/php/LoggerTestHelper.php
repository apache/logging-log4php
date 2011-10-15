<?php

/** A set of helper functions for running tests. */
class LoggerTestHelper {
	
	/** Returns a test logging event with level set to TRACE. */
	public static function getTraceEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelTrace(), $message);
	}
	
	/** Returns a test logging event with level set to DEBUG. */
	public static function getDebugEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelDebug(), $message);
	}
	
	/** Returns a test logging event with level set to INFO. */
	public static function getInfoEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelInfo(), $message);
	}
	
	/** Returns a test logging event with level set to WARN. */
	public static function getWarnEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelWarn(), $message);
	}
	
	/** Returns a test logging event with level set to ERROR. */
	public static function getErrorEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelError(), $message);
	}
	
	/** Returns a test logging event with level set to FATAL. */
	public static function getFatalEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelFatal(), $message);
	}
	
	/** 
	 * Returns an array of logging events, one for each level, sorted ascending
	 * by severitiy. 
	 */
	public static function getAllEvents($message = 'test') {
		return array(
			self::getTraceEvent($message),
			self::getDebugEvent($message),
			self::getInfoEvent($message),
			self::getWarnEvent($message),
			self::getErrorEvent($message),
			self::getFatalEvent($message),
		);
	}
	
	/** Returns an array of all existing levels, sorted ascending by severity. */
	public static function getAllLevels() {
		return array(
			LoggerLevel::getLevelTrace(),
			LoggerLevel::getLevelDebug(),
			LoggerLevel::getLevelInfo(),
			LoggerLevel::getLevelWarn(),
			LoggerLevel::getLevelError(),
			LoggerLevel::getLevelFatal(),
		);
	}
	
	/** Returns a string representation of a filter decision. */
	public static function decisionToString($decision) {
		switch($decision) {
			case LoggerFilter::ACCEPT: return 'ACCEPT';
			case LoggerFilter::NEUTRAL: return 'NEUTRAL';
			case LoggerFilter::DENY: return 'DENY';
		}
	}
	
	/** Returns a simple configuration with one echo appender tied to root logger. */
	public static function getEchoConfig() {
		return array(
	        'threshold' => 'ALL',
	        'rootLogger' => array(
	            'level' => 'trace',
	            'appenders' => array('default'),
			),
	        'appenders' => array(
	            'default' => array(
	                'class' => 'LoggerAppenderEcho',
	                'layout' => array(
	                    'class' => 'LoggerLayoutSimple',
					),
				),
			),
		);
	}
}

?>