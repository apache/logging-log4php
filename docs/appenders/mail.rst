==================
LoggerAppenderMail
==================

``LoggerAppenderMail`` appends log events via email.

This appender does not send individual emails for each logging requests but will
collect them in a buffer and send them all in a single email once the appender
is closed (i.e. when the script exists). Because of this, it may not appropriate
for long running scripts, in which case `LoggerAppenderMailEvent`_ might be a
better choice.

.. _LoggerAppenderMailEvent: mail-event.html

.. warning::

    When working in Windows, make sure that the ``SMTP`` and ``smpt_port``
    values in php.ini are set to the correct values for your email server
    (address and port).


Layout
------

This appender requires a layout. If no layout is specified in configuration,
``LoggerLayoutSimple`` will be used by default.

Parameters
----------

The following parameters are available:

+-----------+---------+----------+---------+---------------------------------------------------------+
| Parameter | Type    | Required | Default | Description                                             |
+===========+=========+==========+=========+=========================================================+
| to        | string  | **Yes**  |         | Email address(es) to which the log will be sent.        |
|           |         |          |         | Multiple email addresses may be specified by separating |
|           |         |          |         | them with a comma.                                      |
+-----------+---------+----------+---------+---------------------------------------------------------+
| from      | string  | **Yes**  |         | Email address which will be used in the From field.     |
+-----------+---------+----------+---------+---------------------------------------------------------+
| subject   | string  | No       | Log4php | Subject of the email message.                           |
|           |         |          | Report  |                                                         |
+-----------+---------+----------+---------+---------------------------------------------------------+

Examples
--------

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderMail">
                <layout class="LoggerLayoutSimple" />
                <param name="to" value="foo@example.com,baz@example.com" />
                <param name="from" value="logger@example.com" />
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
                    'class' => 'LoggerAppenderMail',
                    'layout' => array(
                        'class' => 'LoggerLayoutSimple',
                    ),
                    'params' => array(
                        'to' => 'foo@example.com,baz@example.com',
                        'from' => 'logger@example.com'
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
