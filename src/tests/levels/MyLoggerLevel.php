<?php
/**
 * Copyright 2004 The Apache Software Foundation.
 *
 * This software is published under the terms of the Apache Software
 * License version 2.0, a copy of which has been included with this
 * distribution in the LICENSE file.
 * 
 * @package tests
 * @author Marco Vassura
 * @subpackage levels
 * @version $Revision$
 * @since 0.6
 */
 
/**
 */
define('LOG4PHP_LEVEL_TRACE_INT',        LOG4PHP_LEVEL_DEBUG_INT - 1); 
define('LOG4PHP_LEVEL_LETHAL_INT',       LOG4PHP_LEVEL_FATAL_INT + 1);


/**
 * This class introduces a new level level called TRACE. TRACE has
 * lower level than DEBUG.
 *
 * @package tests
 * @subpackage levels
 * @author Marco Vassura
 * @version $Revision$
 * @since 0.6
 */
class MyLoggerLevel extends LoggerLevel {

    /**
     * @param integer $level
     * @param string $strLevel
     * @param integer $syslogEquiv
     */
    function MyLoggerLevel($level, $strLevel, $syslogEquiv)
    {
        $this->LoggerLevel($level, $strLevel, $syslogEquiv);
    }
    
    /**
     * Returns a TRACE Level
     * @static
     * @return LoggerLevel
     */
    function getLevelTrace()
    {
        static $level;
        if (!isset($level)) $level = new MyLoggerLevel(LOG4PHP_LEVEL_TRACE_INT, 'TRACE', 7);
        return $level;
    }
    
    /**
     * Returns a LETHAL Level
     * @static
     * @return LoggerLevel
     */
    function getLevelLethal()
    {
        static $level;
        if (!isset($level)) $level = new MyLoggerLevel(LOG4PHP_LEVEL_LETHAL_INT, 'LETHAL', 0);
        return $level;
    }

    /**
     * Convert the string passed as argument to a level. If the
     * conversion fails, then this method returns a TRACE Level.
     *
     * @param mixed $arg
     * @param LoggerLevel $defaultLevel
     * @static 
     */
    function toLevel($arg, $defaultLevel = null)
    {
        if ($arg === null) {
            return $defaultLevel;
        }
        if (is_int($arg)) {
            switch($arg) {
                case LOG4PHP_LEVEL_TRACE_INT:   return MyLoggerLevel::getLevelTrace();
                case LOG4PHP_LEVEL_LETHAL_INT:  return MyLoggerLevel::getLevelLethal();
                default:                        return LoggerLevel::toLevel($arg, $defaultLevel);
            }
        } else {
            switch(strtoupper($arg)) {
                case 'TRACE':   return MyLoggerLevel::getLevelTrace();
                case 'LETHAL':  return MyLoggerLevel::getLevelLethal();
                default:        return LoggerLevel::toLevel($arg, $defaultLevel);
            }
        }
    } 
}
?>