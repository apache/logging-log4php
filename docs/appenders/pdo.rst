=====================
LoggerAppenderPDO
=====================

``LoggerAppenderPDO`` appender logs to a database using the PHP's PDO_
extension.

.. _PDO: http://php.net/manual/en/book.pdo.php

Layout
------

This appender does not require a layout.

Parameters
----------

The following parameters are available:

+---------------+--------+----------+---------+-------------------------------------------------------------+
| Parameter     | Type   | Required | Default | Description                                                 |
+===============+========+==========+=========+=============================================================+
| dsn           | string | **Yes**  | \-      | The Data Source Name (DSN) used to connect to the database. |
+---------------+--------+----------+---------+-------------------------------------------------------------+
| user          | string | No       | stdout  | The stream to write to; either "stdout" or "stderr".        |
+---------------+--------+----------+---------+-------------------------------------------------------------+
| password      | string | No       | stdout  | The stream to write to; either "stdout" or "stderr".        |
+---------------+--------+----------+---------+-------------------------------------------------------------+
| table         | string | No       | stdout  | The stream to write to; either "stdout" or "stderr".        |
+---------------+--------+----------+---------+-------------------------------------------------------------+
| insertSql     | string | No       | stdout  | The stream to write to; either "stdout" or "stderr".        |
+---------------+--------+----------+---------+-------------------------------------------------------------+
| insertPattern | string | No       | stdout  | The stream to write to; either "stdout" or "stderr".        |
+---------------+--------+----------+---------+-------------------------------------------------------------+

Parameters ``dsn``, ``user`` and ``password`` are used by PDO to connect to the
database which will be used for logging.

Data Source Name
----------------

The Data Source Name or DSN is a database-specific string which contains the
information required to connect to the database.

Some common examples of DSNs:

+------------+---------------------------------------+---------------------+
| MySQL      | ``mysql:host=localhost;dbname=logdb`` | `full reference`__  |
+------------+---------------------------------------+---------------------+
| SQLite     | ``sqlite:/path/to/log.db``            | `full reference`__  |
+------------+---------------------------------------+---------------------+
| PostgreSQL | ``pgsql:host=localhost;port=5432``    | `full reference`__  |
+------------+---------------------------------------+---------------------+

__ http://php.net/manual/en/ref.pdo-mysql.connection.php
__ http://php.net/manual/en/ref.pdo-sqlite.connection.php
__ http://php.net/manual/en/ref.pdo-pgsql.connection.php

For other available database drivers and corresponding DSN format, please see
the `PDO driver documentation <http://www.php.net/manual/en/pdo.drivers.php>`_.

Database table
--------------

.. versionadded:: 2.3.0

    The appender will no longer create a database table by itself. You have to
    create a database table yourself. The reason for this is that various
    databases use various create statements and column types. Some common
    databases are covered in this chapter.

By default the table should contain the following columns:

- timestamp DATETIME
- logger VARCHAR
- level VARCHAR
- message VARCHAR
- thread VARCHAR
- file VARCHAR
- line VARCHAR

If you wish to use an alternative table structure, see the next chapter.

Here are CREATE TABLE statements for some popular databases:

MySQL
~~~~~

.. code-block:: sql

    CREATE TABLE log4php_log (
        timestamp DATETIME,
        logger VARCHAR(256),
        level VARCHAR(32),
        message VARCHAR(4000),
        thread INTEGER,
        file VARCHAR(255),
        line VARCHAR(10)
    );

SQLite
~~~~~~

SQLite does not have a datetime type, so varchar is used instead.

.. code-block:: sql

    CREATE TABLE log4php_log (
        timestamp VARCHAR(50),
        logger VARCHAR(256),
        level VARCHAR(32),
        message VARCHAR(4000),
        thread INTEGER,
        file VARCHAR(255),
        line VARCHAR(10)
    );

PostgreSQL
~~~~~~~~~~

.. code-block:: sql

    CREATE TABLE log4php_log (
        timestamp TIMESTAMP,
        logger VARCHAR(256),
        level VARCHAR(32),
        message VARCHAR(4000),
        thread INTEGER,
        file VARCHAR(255),
        line VARCHAR(10)
    );

Advanced configuration
----------------------

Parameters ``insertSql`` and ``insertPattern`` can be used to change how events
are inserted into the database. By manipulating them, it is possible to use a
custom table structure to suit your needs.

.. warning::

    Change these settings only if you are sure you know what you are doing.

The default values of these parameters are:

+---------------+-----------------------------------------------------------------+
| Parameter     | Default value                                                   |
+===============+=================================================================+
| insertSql     | INSERT INTO __TABLE__ (timestamp, logger, level, message,       |
|               | thread, file, line) VALUES (?, ?, ?, ?, ?, ?, ?)                |
+---------------+-----------------------------------------------------------------+
| insertPattern | ``%date{Y-m-d H:i:s},%logger,%level,%message,%pid,%file,%line`` |
+---------------+-----------------------------------------------------------------+

The string ``__TABLE__`` in insertSql will be replaced with the table name
defined in table. Question marks in insertSql will be replaced by evaluated
``LoggerLayoutPattern`` format strings defined in insertPattern. See
``LoggerLayoutPattern`` documentation for format string description.

Examples
--------

Example 1
~~~~~~~~~

The simplest example is connecting to an SQLite database which does not require
any authentication.

SQLite databases are contained in simple files and don't reuquire a server to
run. This example will log to the database contained in ``/var/log/log.sqlite``.

First, create a database and a table for logging. In this example, let's create
the database at ``/tmp/log.db``.

.. code-block:: bash

    $ sqlite3 /tmp/log.db
    SQLite version 3.7.9 2011-11-01 00:52:41
    Enter ".help" for instructions
    Enter SQL statements terminated with a ";"
    sqlite> CREATE TABLE log4php_log (
       ...> timestamp VARCHAR(256),
       ...> logger VARCHAR(256),
       ...> level VARCHAR(32),
       ...> message VARCHAR(4000),
       ...> thread INTEGER,
       ...> file VARCHAR(255),
       ...> line VARCHAR(10)
       ...> );

When the database is set up, use the following configuration to set up log4php.

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderPDO">
                <param name="dsn" value="sqlite:/tmp/log.db" />
            </appender>
            <root>
                <appender_ref ref="default" />
            </root>
        </configuration>

    .. rubric:: PHP format
    .. code-block:: php

        array(
            'appenders' => array(
                'default' => array(
                    'class' => 'LoggerAppenderPDO',
                    'params' => array(
                        'dsn' => 'sqlite:/tmp/log.db',
                    ),
                ),
            ),
            'rootLogger' => array(
                'appenders' => array('default'),
            ),
        );

Now the database is ready to accept some logging data.

.. code-block:: php

    require 'log4php/Logger.php';
    Logger::configure('config.xml');

    $log = Logger::getLogger('foo');
    $log->info("foo");
    $log->info("bar");
    $log->info("baz");

Now you can check out the data in the sqlite database.

.. code-block:: bash

    $ sqlite3 /tmp/log.db
    SQLite version 3.7.9 2011-11-01 00:52:41
    Enter ".help" for instructions
    Enter SQL statements terminated with a ";"
    sqlite> select * from log4php_log;
    2012-08-18 17:14:11|foo|INFO|foo|23531|/home/ihabunek/apache/sqlite.php|5
    2012-08-18 17:14:11|foo|INFO|bar|23531|/home/ihabunek/apache/sqlite.php|6
    2012-08-18 17:14:11|foo|INFO|baz|23531|/home/ihabunek/apache/sqlite.php|7

Example 2
~~~~~~~~~

A slightly more complex example is connecting to a MySQL database which requires
user credentials to be provided. Additionally, a user-specified table name is
used.

First, a log table has to be created. For this example a database named
``logdb`` will be created, and within it a table named ``log``.


.. code-block:: bash

    $ mysql -u root -p
    Enter password: *******
    Welcome to the MySQL monitor.  Commands end with ; or \g.
    Your MySQL connection id is 47
    Server version: 5.5.24-0ubuntu0.12.04.1 (Ubuntu)

    mysql> CREATE DATABASE logdb;
    Query OK, 1 row affected (0.00 sec)

    mysql> USE logdb;
    Database changed

    mysql> CREATE TABLE log (
        -> timestamp DATETIME,
        -> logger VARCHAR(256),
        -> level VARCHAR(32),
        -> message VARCHAR(4000),
        -> thread INTEGER,
        -> file VARCHAR(255),
        -> line VARCHAR(10)
        -> );
    Query OK, 0 rows affected (0.01 sec)

The following configuration allows log4php to write to the newly created table.

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderPDO">
                <param name="dsn" value="mysql:host=localhost;dbname=logdb" />
                <param name="user" value="root" />
                <param name="password" value="secret" />
                <param name="table" value="log" />
            </appender>
            <root>
                <appender_ref ref="default" />
            </root>
        </configuration>

    .. rubric:: PHP format
    .. code-block:: php

        array(
            'appenders' => array(
                'default' => array(
                    'class' => 'LoggerAppenderPDO',
                    'params' => array(
                        'dsn' => 'mysql:host=localhost;dbname=logdb',
                        'user' => 'root',
                        'password' => 'secret',
                        'table' => 'log',
                    ),
                ),
            ),
            'rootLogger' => array(
                'appenders' => array('default'),
            ),
        );

Now the database is ready to accept some logging data.

.. code-block:: php

    require 'log4php/Logger.php';
    Logger::configure('config.xml');

    $log = Logger::getLogger('main');
    $log->info("foo");
    $log->info("bar");
    $log->info("baz");

Finally, to see the logged data.

.. code-block:: bash

    $ mysql -u root -p
    Enter password: *******
    Welcome to the MySQL monitor.  Commands end with ; or \g.
    Your MySQL connection id is 47
    Server version: 5.5.24-0ubuntu0.12.04.1 (Ubuntu)

    mysql> select * from log;
    +---------------------+--------+-------+---------+--------+---------------------------------+------+
    | timestamp           | logger | level | message | thread | file                            | line |
    +---------------------+--------+-------+---------+--------+---------------------------------+------+
    | 2012-08-18 17:30:05 | main   | INFO  | foo     |  23638 | /home/ihabunek/apache/mysql.php | 5    |
    | 2012-08-18 17:30:05 | main   | INFO  | bar     |  23638 | /home/ihabunek/apache/mysql.php | 6    |
    | 2012-08-18 17:30:05 | main   | INFO  | baz     |  23638 | /home/ihabunek/apache/mysql.php | 7    |
    +---------------------+--------+-------+---------+--------+---------------------------------+------+
    3 rows in set (0.00 sec)


..  Licensed to the Apache Software Foundation (ASF) under one or more
    contributor license agreements. See the NOTICE file distributed with
    this work for additional information regarding copyright ownership.
    The ASF licenses this file to You under the Apache License, Version 2.0
    (the "License"); you may not use this file except in compliance with
    the License. You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.