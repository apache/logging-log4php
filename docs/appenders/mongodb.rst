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

+--------------+---------+----------+---------------------+-----------------------------------------------+
| Parameter    | Type    | Required | Default             | Description                                   |
+==============+=========+==========+=====================+===============================================+
| host         | string  | No       | mongodb://localhost | Server on which mongodb instance is located.  |
+--------------+---------+----------+---------------------+-----------------------------------------------+
| port         | integer | No       | 27017               | Port on which the instance is bound.          |
+--------------+---------+----------+---------------------+-----------------------------------------------+
| databaseName | string  | No       | log4php_mongodb     | Name of the database to which to log.         |
+--------------+---------+----------+---------------------+-----------------------------------------------+
| username     | string  | No       |                     | Username used to connect to the database.     |
+--------------+---------+----------+---------------------+-----------------------------------------------+
| password     | string  | No       |                     | Password used to connect to the database.     |
+--------------+---------+----------+---------------------+-----------------------------------------------+
| timeout      | integer | No       | 3000                | For how long the driver should try to connect |
|              |         |          |                     | to the database (in milliseconds).            |
+--------------+---------+----------+---------------------+-----------------------------------------------+

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
                <param name="host" value="mongodb://example.com" />
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
                        'host' => 'mongodb://example.com',
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
