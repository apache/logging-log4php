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
 * @subpackage filters
 * @version $Revision$
 * @since 0.3
 */

/**
 * @var array Test array
 */
$tests = array(
    'LoggerDenyAllFilter'       => array( 
        '__COMMENT__'  => 'Block all.'
    ),
    'LoggerStringMatchFilter'   => array( 
        '__COMMENT__'  => 'discard events with the string "test" in message.'
    ),
    'LoggerLevelMatchFilter'   => array( 
        '__COMMENT__'  => 'discard events with "DEBUG" level.'
    ),
    'LoggerLevelRangeFilter'   => array( 
        '__COMMENT__'  => 'report only "WARN" and "ERROR" events.'
    ),
);

require_once('../test_core.php');

?>