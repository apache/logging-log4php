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
 * @since 0.5
 */

/**
 * @var array Test array
 */
$tests = array(
    'LoggerLevel_01'     => array(
        'LOG4PHP_CONFIGURATION' => './configs/LoggerLevel_01.properties'
     ),
    'LoggerLevel_02'     => array(
        'LOG4PHP_CONFIGURATION' => './configs/LoggerLevel_02.xml'
     ),
    'LoggerLevel_03'     => array(
        'LOG4PHP_CONFIGURATION' => './configs/LoggerLevel_03.properties',
        'INCLUDES'              => './MyLoggerLevel.php'
     ),
);

require_once('../test_core.php');

?>