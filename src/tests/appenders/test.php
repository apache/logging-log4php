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
 * @subpackage appenders
 * @version $Revision$
 * @since 0.3
 */

/**
 * @var array Test array
 */
$tests = array(
    'LoggerAppenderConsole'     => array( 
        '__COMMENT__' => 'Output to php://stderr. In web enviroment you wont see anything'
    ),
    
    'LoggerAppenderDailyFile'   => array(
        '__COMMENT__' => 'Echo + write a test_LoggerAppenderDailyFile_%s.txt file to TMP env dir',
    ),
    
    'LoggerAppenderDb'          => array(
        '__COMMENT__' => 'Echo + write to log4php table in mysql://localhost/test. Tries to create the table.',    
    ),
    'LoggerAppenderEcho'        => array(
        '__COMMENT__' => 'Echo with Html layout',
    ),
    'LoggerAppenderFile'        => array(
        '__COMMENT__' => 'Echo + write a test_LoggerAppenderFile.xml file to TMP env dir with Xml layout',    
    ),
    'LoggerAppenderMail'        => array(
        '__COMMENT__' => 'Echo + mail to root@localhost',    
    ),
    'LoggerAppenderMailEvent'   => array( 
        '__COMMENT__' => 'Echo + (1 event = 1 mail) to root@localhost',
    ),
    'LoggerAppenderNull'        => array(
        '__COMMENT__' => 'Writes to ... null (debug turned on)',    
    ),
    'LoggerAppenderNullThreshold'        => array(
        '__COMMENT__' => 'Writes to ... null (debug turned on) with threshold',    
    ),
    
    'LoggerAppenderPhp'         => array( 
        '__COMMENT__' => 'Writes with trigger_error(). Execution will stop with an ERROR log.',    
    ),
    'LoggerAppenderRollingFile' => array(
        '__COMMENT__' => 'Echo + write a test_LoggerAppenderRollingFile.txt file to TMP env dir. 3 files max 10kB',
    ),

    'LoggerAppenderSocket'      => array( 
        '__COMMENT__' => 'Echo + send to tcp://127.0.0.1 port 4446',    
        'TEST_SEND_HTML'    => false,
        'CONTENT_TYPE'      => 'application/xml',
        'HTML_HEADER'       => '<?xml version="1.0" encoding="ISO-8859-1"?>'
    ),
    'LoggerAppenderSocketLog4j'      => array( 
        '__COMMENT__' => 'Echo + send to tcp://127.0.0.1 port 2222 with log4j namespace',
        'TEST_SEND_HTML'    => false,
        'CONTENT_TYPE'      => 'application/xml',
        'HTML_HEADER'       => '<?xml version="1.0" encoding="ISO-8859-1"?>'
    ),
    'LoggerAppenderSyslog'      => array( 
        '__COMMENT__' => 'Echo + send to syslog/NT events',
    ),                    
);

require_once('../test_core.php');
?>