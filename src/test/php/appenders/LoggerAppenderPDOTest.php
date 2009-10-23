<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * @category   tests   
 * @package    log4php
 * @subpackage appenders
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

class LoggerAppenderPDOTest extends PHPUnit_Framework_TestCase {
    const dsn = 'sqlite:../../../target/pdotest.sqlite';
    const file = '../../../target/pdotest.sqlite';
        
    /** To start with an empty database for each single test. */
    public function setUp() {
        if (file_exists(self::file)) unlink(self::file);
    }

    /** Clean up after the last test was run. */
    public static function tearDownAfterClass() {
        if (file_exists(self::file)) unlink(self::file);
    }

    /** Tests new-style logging using prepared statements and the default SQL definition. */
    public function testSimpleWithDefaults() {
		if(!extension_loaded('pdo_sqlite')) {
			self::markTestSkipped("Please install 'pdo_sqlite' in order to run this test");
		}
		
        // Log event
		$event = new LoggerLoggingEvent("LoggerAppenderPDOTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
        $appender = new LoggerAppenderPDO("myname");
        $appender->setDSN(self::dsn);
        $appender->activateOptions();
        $appender->append($event);
        $appender->close();

        // Test the default pattern %d,%c,%p,%m,%t,%F,%L
        $db = new PDO(self::dsn);
        $query = "SELECT * FROM log4php_log";
        $sth = $db->query($query);
        $row = $sth->fetch(PDO::FETCH_NUM);
        self::assertTrue(is_array($row), "No rows found.");
        self::assertEquals(7, count($row));
        self::assertEquals(1, preg_match('/^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d,\d\d\d$/', $row[0])); // %d = date
        self::assertEquals('TEST', $row[1]); // %c = category
        self::assertEquals('ERROR', $row[2]); // %p = priority
        self::assertEquals('testmessage', $row[3]); // %m = message
        self::assertEquals(posix_getpid(), $row[4]); // %t = thread
        self::assertEquals('NA', $row[5]); // %F = file, NA due to phpunit magic
        self::assertEquals('NA', $row[6]); // %L = line, NA due to phpunit magic
    }


    /** Tests new style prepared statment logging with customized SQL. */
    public function testCustomizedSql() {
        if(!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped("Please install 'pdo_sqlite' in order to run this test");
        }
		
        // Prepare appender
		$appender = new LoggerAppenderPDO("myname");
        $appender->setDSN(self::dsn);
        $appender->setTable('unittest2');
        $appender->setInsertSql("INSERT INTO unittest2 (file, line, thread, timestamp, logger, level, message) VALUES (?,?,?,?,?,?,?)");
        $appender->setInsertPattern("%F,%L,%t,%d,%c,%p,%m");
		$appender->activateOptions();

        // Action!
        $event = new LoggerLoggingEvent("LoggerAppenderPDOTest2", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		$appender->append($event);
		
        // Check
        $db = new PDO(self::dsn);
        $result = $db->query("SELECT * FROM unittest2");
        $row = $result->fetch(PDO::FETCH_OBJ);
        self::assertTrue(is_object($row));
        self::assertEquals("NA", $row->file); // "NA" due to phpunit magic
        self::assertEquals("NA", $row->line); // "NA" due to phpunit magic
        self::assertEquals(posix_getpid(), $row->thread);
        self::assertEquals(1, preg_match('/^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d.\d\d\d$/', $row->timestamp));
        self::assertEquals('TEST', $row->logger);
        self::assertEquals('ERROR', $row->level);
        self::assertEquals('testmessage', $row->message);
    }
		
    /** Tests old-style logging using the $sql variable. */
    public function testOldStyle() {
        if(!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped("Please install 'pdo_sqlite' in order to run this test");
		}
		
        // Create table with different column order
        $db = new PDO(self::dsn);
        $db->exec('CREATE TABLE unittest3 (ts timestamp, level varchar(32), msg varchar(64))');

        // Prepare appender
        $appender = new LoggerAppenderPDO("myname");
        $appender->setDSN(self::dsn);
        $appender->setCreateTable(false);
        $appender->setSql("INSERT INTO unittest3 (ts, level, msg) VALUES ('%d', '%p', '%m')");
        $appender->activateOptions();

        // Action!
        $event = new LoggerLoggingEvent("LoggerAppenderPDOTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
        $appender->append($event);

        // Check
        $db = new PDO(self::dsn);
        $result = $db->query("SELECT * FROM unittest3");
        self::assertFalse($result === false);
        $row = $result->fetch(PDO::FETCH_OBJ);
        self::assertTrue(is_object($row));
        self::assertEquals(1, preg_match('/^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d.\d\d\d$/', $row->ts));
        self::assertEquals('ERROR', $row->level);
        self::assertEquals('testmessage', $row->msg);
    }
    
    /** Tests if log4php throws an Exception if the appender does not work. 
     * @expectedException LoggerException
     */
    public function testException() {
        $dsn = 'doenotexist';
        $appender = new LoggerAppenderPDO("myname");
        $appender->setDSN($dsn);
        $appender->setCreateTable(true);
            $appender->activateOptions();
        }
}
