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
 * @subpackage others
 * @version $Revision$
 * @since 0.6
 */

/**
 * @var array Test array
 */
$tests = array(
    'Serialized'     => array(
        '__COMMENT__'           => 'echo the current hierarchy serialized', 
        'LOG4PHP_CONFIGURATION' => './configs/serialized.xml'
     ),
);

require_once('../test_core.php');

?>