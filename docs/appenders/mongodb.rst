=====================
LoggerAppenderMongoDB
=====================

``LoggerAppenderMongoDB`` appends log events to a MongoDB instance.

MongoDB_ is a scalable, high-performance, open source, document-oriented database.

.. _MongoDB: http://www.mongodb.org/

Layout
------
This appender does not require a layout.

Parameters
----------
The following parameters are available:

Note: additional parameters supported by the driver can be supplied by **connectionString**

+------------------------+---------+----------+---------------------+-----------------------------------------------+
| Parameter              | Type    | Required | Default             | Description                                   |
+========================+=========+==========+=====================+===============================================+
| connectionString       | string  | No       |                     | Connection string defining one or multiple    |
|                        |         |          |                     | hosts.                                        |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| host                   | string  | No       | localhost           | Server on which mongodb instance is located.  |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| port                   | integer | No       | 27017               | Port on which the instance is bound.          |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| databaseName           | string  | No       | log4php_mongodb     | Name of the database to which to log.         |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| collectionName         | string  | No       | logs                | Name of the collection to which to log.       |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| username               | string  | No       |                     | Username used to connect to the database.     |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| password               | string  | No       |                     | Password used to connect to the database.     |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| timeout                | integer | No       | 3000                | **DEPRECATED** For how long the driver should |
|                        |         |          |                     | try to connect to the database                |
|                        |         |          |                     | (in milliseconds). Use connectionTimeout      |
|                        |         |          |                     | instead                                       |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| connectionTimeout      | integer | No       |                     | How long a connection can take to be opened   |
|                        |         |          |                     | before timing out.                            |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| socketTimeout          | integer | No       |                     | How long a send or receive on a socket can    |
|                        |         |          |                     | take before timing out.                       |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| capped                 | boolean | No       | false               | Whether the collection should be a fixed size.|
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| cappedMax              | integer | No       | 1000                | Maximum number of elements to store.          |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| cappedSize             | integer | No       | 1000000             | Size of capped collection in bytes.           |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| writeConcern           | string  | No       | 1                   | Controls how many nodes must acknowledge the  |
|                        |         |          |                     | write instruction before the driver continues.|
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| writeConcernJournaled  | boolean | No       | false               | The write will be acknowledged by primary and |
|                        |         |          |                     | the journal flushed to disk.                  |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| writeConcernTimeout    | integer | No       | 3000                | Controls how many milliseconds the server     |
|                        |         |          |                     | waits for the write concern to be satisfied.  |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| replicaSet             | string  | No       |                     | The name of the replica set to connect to.    |
|                        |         |          |                     | Primary will be automatically determined.     |
+------------------------+---------+----------+---------------------+-----------------------------------------------+

.. versionadded:: 2.2.0
    The ``timeout`` parameter.

Examples
--------
This example shows how to configure ``LoggerAppenderMongoDB`` to log to a remote
database.

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderMongoDB">
                <param name="host" value="example.com" />
                <param name="username" value="logger" />
                <param name="password" value="secret" />
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
                    'class' => 'LoggerAppenderMongoDB',
                    'params' => array(
                        'host' => 'example.com',
                        'username' => 'logger',
                        'password' => 'secret',
                    ),
                ),
            ),
            'rootLogger' => array(
                'appenders' => array('default'),
            ),
        );

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
