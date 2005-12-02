<?php
/**
 * Copyright 2004 The Apache Software Foundation.
 *
 * This software is published under the terms of the Apache Software
 * License version 2.0, a copy of which has been included with this
 * distribution in the LICENSE file.
 *
 * @package log4php
 * @subpackage appenders
 */

/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__) . '/..');
 
/**
 */
require_once(LOG4PHP_DIR . '/LoggerAppenderSkeleton.php');
require_once(LOG4PHP_DIR . '/LoggerLog.php');


define('LOG4PHP_LOGGER_APPENDER_CONSOLE_STDOUT', 'php://stdout');
define('LOG4PHP_LOGGER_APPENDER_CONSOLE_STDERR', 'php://stderr');

/**
 * ConsoleAppender appends log events to STDOUT or STDERR using a layout specified by the user. 
 * 
 * <p>Optional parameter is {@link $target}. The default target is Stdout.</p>
 * <p><b>Note</b>: Use this Appender with command-line php scripts. 
 * On web scripts this appender has no effects.</p>
 * <p>This appender requires a layout.</p>  
 *
 * @author  Marco Vassura
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderConsole extends LoggerAppenderSkeleton {

    /**
     * Can be 'php://stdout' or 'php://stderr'. But it's better to use keywords <b>STDOUT</b> and <b>STDERR</b> (case insensitive). 
     * Default is STDOUT
     * @var string    
     */
    var $target = 'php://stdout';
    
    /**
     * @var boolean
     * @access private     
     */
    var $requiresLayout = true;

    /**
     * @var mixed the resource used to open stdout/stderr
     * @access private     
     */
    var $fp = false;
    
    /**
     * Constructor.
     *
     * @param string $name appender name
     */
    function LoggerAppenderConsole($name)
    {
        $this->LoggerAppenderSkeleton($name);
    }

    /**
     * Set console target.
     * @param mixed $value a constant or a string
     */
    function setTarget($value)
    {
        $v = trim($value);
        if ($v == LOG4PHP_LOGGER_APPENDER_CONSOLE_STDOUT or strtoupper($v) == 'STDOUT') {
            $this->target = LOG4PHP_LOGGER_APPENDER_CONSOLE_STDOUT;
        } elseif ($v == LOG4PHP_LOGGER_APPENDER_CONSOLE_STDERR or strtoupper($v) == 'STDERR') {
            $this->target = LOG4PHP_LOGGER_APPENDER_CONSOLE_STDERR;
        } else {
            LoggerLog::debug(
                "LoggerAppenderConsole::targetWarn() ".
                "Invalid target. Using '".LOG4PHP_LOGGER_APPENDER_CONSOLE_STDOUT."' by default."
            );        
        }
    }

    function getTarget()
    {
        return $this->target;
    }

    function activateOptions()
    {
        LoggerLog::debug("LoggerAppenderConsole::activateOptions()");
            
        $this->fp = @fopen($this->getTarget(), 'w');
    
        if ($this->fp and $this->layout !== null)
            @fwrite($this->fp, $this->layout->getHeader());

        $this->closed = (bool)($this->fp === false);
    }
    
    /**
     * @see LoggerAppender::close()
     */
    function close()
    {
        LoggerLog::debug("LoggerAppenderConsole::close()");
        
        if ($this->fp and $this->layout !== null) {
            @fwrite($this->fp, $this->layout->getFooter());
        }        
        @fclose($this->fp);
        $this->closed = true;
    }

    function append($event)
    {
        if ($this->fp and $this->layout !== null) {
    
            LoggerLog::debug("LoggerAppenderConsole::append()");
        
            @fwrite($this->fp, $this->layout->format($event));
        } 
    }
}

?>