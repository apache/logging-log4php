<?php
/**
 * Copyright 2004 The Apache Software Foundation.
 *
 * This software is published under the terms of the Apache Software
 * License version 2.0, a copy of which has been included with this
 * distribution in the LICENSE file.
 *
 * @package log4php
 * @subpackage spi
 */

/**
 * @ignore
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__));

require_once(LOG4PHP_DIR . '/LoggerLog.php');

/**
 * Extend and implement this abstract class to create new instances of 
 * {@link Logger} or a sub-class of {@link Logger}.
 *
 * @author Marco V. <marco@apache.org>
 * @version $Revision$
 * @package log4php
 * @subpackage spi
 * @since 0.5 
 * @abstract
 */
class LoggerFactory {

    /**
     * @abstract
     * @param string $name
     * @return Logger
     */
    function makeNewLoggerInstance($name)
    {
        LoggerLog::warn("LoggerFactory:: makeNewLoggerInstance() is abstract.");
        return null;
    }

}
?>