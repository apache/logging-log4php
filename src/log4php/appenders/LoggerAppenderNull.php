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
 
require_once(LOG4PHP_DIR . '/LoggerAppenderSkeleton.php');
require_once(LOG4PHP_DIR . '/LoggerLog.php');

/**
 * A NullAppender merely exists, it never outputs a message to any device.  
 *
 * @author Marco V. <marco@apache.org>
 * @version $Revision$
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderNull extends LoggerAppenderSkeleton {

    /**
     * @access private
     */
    var $requiresLayout = false;
    
    /**
     * Constructor.
     *
     * @param string $name appender name
     */
    function LoggerAppenderNull($name)
    {
        $this->LoggerAppenderSkeleton($name);
    }

    function activateOptions()
    { 
        $this->closed = false;
    }
    
    function close()
    {
        $this->closed = true;
    }
    
    /**
     * Do nothing. How I Love it !! :)
     */
    function append($event)
    {
        LoggerLog::debug("LoggerAppenderNull::append()");
    }
}

?>