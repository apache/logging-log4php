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
require_once(LOG4PHP_DIR . '/spi/LoggerFactory.php');
require_once('./MyLogger.php');

/**
 * A factory that makes new {@link MyLogger} objects.
 *
 * @package tests
 * @subpackage loggers
 * @author Marco Vassura
 * @version $Revision$
 * @since 0.6
 */
class MyLoggerFactory extends LoggerFactory {

    /**
     *  The constructor should be public as it will be called by
     * configurators in different packages.  
     */
    function MyLoggerFactory()
    {
        return;
    }

    /**
     * @param string $name
     * @return Logger
     */
    function makeNewLoggerInstance($name)
    {
        return new MyLogger($name);
    }
}
?>