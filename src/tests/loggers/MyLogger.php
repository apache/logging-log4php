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
 * @subpackage loggers
 * @version $Revision$
 * @since 0.6
 */
 
/**
 */
require_once('./MyLoggerFactory.php');

/**
 * A simple example showing logger subclassing. 
 *
 * @package tests
 * @subpackage loggers
 * @author Marco Vassura
 * @version $Revision$
 * @since 0.6
 */
class MyLogger extends Logger {

    var $fqcn = "MyLogger.";

    /**
     * Just calls the parent constuctor.
     *
     * @param string $name
     */
    function MyLogger($name)
    {
        $this->Logger($name);
    }

    /**
     * Overrides the standard debug method by appending " world" at the
     * end of each message.
     *
     * @param mixed $message
     * @param mixed $caller  
     */
    function debug($message, $caller = null)
    {
        if (is_string($message)) {
            $this->log(LoggerLevel::getLevelDebug(), $message . ' Hi, by MyLogger.', $caller);
        } else {    
            $this->log(LoggerLevel::getLevelDebug(), $message, $caller);
        }        
    }

    /**
     * This method overrides {@link Logger#getInstance} by supplying
     * its own factory type as a parameter.
     *
     * @param string $name
     * @return Logger
     * @static
     */
    function getInstance($name)
    {
        return Logger::getLogger($name, MyLogger::getMyFactory()); 
    }
  
    /**
     * This method overrides {@link Logger#getLogger} by supplying
     * its own factory type as a parameter.
     *
     * @param string $name
     * @return Logger
     * @static
     *
     */
    function getLogger($name)
    {
        return Logger::getLogger($name, MyLogger::getMyFactory()); 
    }

    /**
     * @return LoggerFactory
     * @static
     */
    function getMyFactory()
    {
        static $factory;
        
        if (!isset($factory))
            $factory = new MyLoggerFactory();

        return $factory;
    }
}
?>