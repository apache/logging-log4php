<?php
/**
 * Copyright 2004 The Apache Software Foundation.
 *
 * This software is published under the terms of the Apache Software
 * License version 2.0, a copy of which has been included with this
 * distribution in the LICENSE file.
 * 
 * @package log4php
 */

/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__));
 
require_once(LOG4PHP_DIR . '/LoggerCategory.php');
require_once(LOG4PHP_DIR . '/LoggerManager.php');

/**
 * Main class for logging operations  
 *
 * @author       Marco Vassura
 * @version      $Revision$
 * @package log4php
 */
class Logger extends LoggerCategory {

    /**
     * Constructor
     * @param string $name logger name 
     */    
    function Logger($name)
    {
        $this->LoggerCategory($name);
    }
    
    /**
     * Get a Logger by name (Delegate to {@link LoggerManager})
     * @param string $name logger name
     * @param LoggerFactory $factory a {@link LoggerFactory} instance or null
     * @return Logger
     * @static 
     */    
    function &getLogger($name, $factory = null)
    {
        return LoggerManager::getLogger($name, $factory);
    }
    
    /**
     * get the Root Logger (Delegate to {@link LoggerManager})
     * @return LoggerRoot
     * @static 
     */    
    function &getRootLogger()
    {
        return LoggerManager::getRootLogger();    
    }
}
?>