<?php

/** A set of helper functions for running tests. */
class LoggerTestHelper {
	
	public static function getTraceEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelTrace(), $message);
	}
	
	public static function getDebugEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelDebug(), $message);
	}
	
	public static function getInfoEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelInfo(), $message);
	}
	
	public static function getWarnEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelWarn(), $message);
	}
	
	public static function getErrorEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelError(), $message);
	}
	
	public static function getFatalEvent($message = 'test') {
		return new LoggerLoggingEvent(__CLASS__, new Logger("test"), LoggerLevel::getLevelFatal(), $message);
	}
	
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
}






?>