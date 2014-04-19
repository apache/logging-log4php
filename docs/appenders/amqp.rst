==================
LoggerAppenderAMQP
==================

``LoggerAppenderAMQP`` appends log events to a AMQP instance.

The Advanced Message Queuing Protocol (AMQP) is an open standard application
layer protocol for message-oriented middleware. The defining features of AMQP
are message orientation, queuing, routing (including point-to-point and
publish-and-subscribe), reliability and security.

Layout
------

This appender requires a layout. If no layout is specified in configuration,
``LoggerLayoutSimple`` will be used by default.

Parameters
----------
The following parameters are available:

+------------------------+---------+----------+---------------------+-----------------------------------------------+
| Parameter              | Type    | Required | Default             | Description                                   |
+========================+=========+==========+=====================+===============================================+
| host                   | string  | No       | localhost           | Server on which AMQP instance is located.     |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| port                   | integer | No       | 5672                | Port on which the instance is bound.          |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| vhost                  | string  | No       | /                   | The name of the "virtual host".               |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| login                  | string  | No       | guest               | Login used to connect to the AMQP server.     |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| password               | string  | No       | guest               | Password used to connect to the AMQP server.  |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| exchangeName           | string  | **Yes**  |                     | Name of AMQP exchange which used to routing   |
|                        |         |          |                     | logs.                                         |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| exchangeType           | string  | No       | direct              | Type of AMQP exchange.                        |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| routingKey             | string  | **Yes**  |                     | Routing key which used to routing logs.       |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| connectionTimeout      | float   | No       | 0.5                 | How long a connection can take to be opened   |
|                        |         |          |                     | before timing out (in seconds).               |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| contentType            | string  | No       | text/plain          | Content-type header.                          |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| contentEncoding        | string  | No       | UTF-8               | Content-encoding header.                      |
+------------------------+---------+----------+---------------------+-----------------------------------------------+
| flushOnShutdown        | boolean | No       | false               | Send logs immediately or stash it and send on |
|                        |         |          |                     | shutdown.                                     |
+------------------------+---------+----------+---------------------+-----------------------------------------------+

Examples
--------
This example shows how to configure ``LoggerAppenderAMQP`` to log to a remote
server.

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderAMQP">
                <param name="host" value="example.com" />
                <param name="vhost" value="/logs" />
                <param name="login" value="my_login" />
                <param name="password" value="my_secret_password" />
                <param name="exchangeName" value="my_exchange" />
                <param name="routingKey" value="php_application" />
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
                    'class' => 'LoggerAppenderAMQP',
                    'params' => array(
                        'host' => 'example.com',
                        'vhost' => '/logs',
                        'login' => 'my_login',
                        'password' => 'my_secret_password',
                        'exchangeName' => 'my_exchange',
                        'routingKey' => 'php_application',
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
