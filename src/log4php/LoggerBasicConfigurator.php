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

require_once(LOG4PHP_DIR . '/spi/LoggerConfigurator.php');
require_once(LOG4PHP_DIR . '/LoggerLayout.php');
require_once(LOG4PHP_DIR . '/LoggerAppender.php');
require_once(LOG4PHP_DIR . '/LoggerManager.php');

/**
 * Use this class to quickly configure the package.
 *
 * <p>For file based configuration see {@link LoggerPropertyConfigurator}. 
 * <p>For XML based configuration see {@link LoggerDOMConfigurator}.
 *
 * @author Marco V. <marco@apache.org>
 * @version $Revision$
 * @package log4php
 * @since 0.5
 */
class LoggerBasicConfigurator extends LoggerConfigurator {

    function LoggerBasicConfigurator() 
    {
        return;
    }

    /**
     * Add a {@link LoggerAppenderConsole} that uses 
     * the {@link LoggerLayoutTTCC} to the root category.
     * 
     * @param string $url not used here
     * @static  
     */
    function configure($url = null)
    {
        $root =& LoggerManager::getRootLogger();
        
        $appender =& LoggerAppender::singleton('A1', 'LoggerAppenderConsole');
        $layout = LoggerLayout::factory('LoggerLayoutTTCC');
        $appender->setLayout($layout);

        $root->addAppender($appender);
    }

    /**
     * Reset the default hierarchy to its defaut. 
     * It is equivalent to
     * <code>
     * LoggerManager::resetConfiguration();
     * </code>
     *
     * @see LoggerHierarchy::resetConfiguration()
     * @static
     */
    function resetConfiguration()
    {
        LoggerManager::resetConfiguration();
    }
}
?>