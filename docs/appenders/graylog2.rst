======================
LoggerAppenderGraylog2
======================

``LoggerAppenderGraylog2`` appends log events to a Graylog2 server.

Graylog2_ is a scalable, open source system for log management and data analytics.

.. _Graylog2: http://graylog2.org/

Layout
------

This appender requires a layout. If no layout is specified in configuration,
``LoggerLayoutGelf`` will be used by default.

Parameters
----------

The following parameters are available:

+------------------------+---------+----------+--------------------------+------------------------------------------+
| Parameter              | Type    | Required | Default                  | Description                              |
+========================+=========+==========+==========================+==========================================+
| host                   | string  | No       | localhost                | Server on which graylog2-server instance |
|                        |         |          |                          | is located.                              |
+------------------------+---------+----------+--------------------------+------------------------------------------+
| port                   | integer | No       | 12201                    | Port on which the instance is bound.     |
+------------------------+---------+----------+--------------------------+------------------------------------------+
| timeout                | integer | No       | 'default_socket_timeout' | Read/write timeout in seconds.           |
|                        |         |          | from php.ini             |                                          |
+------------------------+---------+----------+--------------------------+------------------------------------------+
| chunkSize              | integer | No       | 8152                     | Message chunk size in bytes.             |
+------------------------+---------+----------+--------------------------+------------------------------------------+

Examples
--------
This example shows how to configure ``LoggerAppenderGraylog2`` to log to a remote
graylog2 server.

.. container:: tabs

    .. rubric:: XML format
.. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderGraylog2">
                <param name="host" value="localhost" />
                <param name="port" value="12201" />
                <param name="timeout" value="3" />
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
                    'class' => 'LoggerAppenderGraylog2',
                    'params' => array(
                        'host' => 'example.com',
                        'port' => 12201,
                        'timeout' => 3,
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
