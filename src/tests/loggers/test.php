<?php
/**
 * Copyright 2004 The Apache Software Foundation.
 *
 * This software is published under the terms of the Apache Software
 * License version 2.0, a copy of which has been included with this
 * distribution in the LICENSE file.
 * 
 * @package tests
 * @author Marco V. <marco@apache.org>
 * @subpackage loggers
 * @version $Revision$
 * @since 0.6
 */

/**
 * @var array Test array
 */
$tests = array(
    'MyLogger_01'     => array(
        '__COMMENT__'           => 'Configured using LoggerPropertyConfigurator. Note "Hi, by MyLogger" in DEBUG/MyTest.',
        'LOG4PHP_CONFIGURATION' => './configs/MyLogger.properties'
     ),
    'MyLogger_02'     => array(
        '__COMMENT__'           => 'Configured using LoggerDOMConfigurator. Note "Hi, by MyLogger" in DEBUG/MyTest.',
        'LOG4PHP_CONFIGURATION' => './configs/MyLogger.xml'
     ),
);

require_once('../test_core.php');

?>