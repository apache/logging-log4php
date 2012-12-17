==================
LoggerAppenderEcho
==================

``LoggerAppenderEcho`` writes logging events using PHP's
`echo() <http://php.net/manual/en/function.echo.php>`_ function.

Echo outputs may be buffered.

Layout
------

This appender requires a layout. If no layout is specified in configuration,
``LoggerLayoutSimple`` will be used by default.

Parameters
----------

The following parameters are available:

+----------------+---------+----------+---------+--------------------------------------------------+
| Parameter      | Type    | Required | Default | Description                                      |
+================+=========+==========+=========+==================================================+
| htmlLineBreaks | boolean | No       | false   | If set to true, a ``<br />`` element will be     |
|                |         |          |         | inserted before each line break in the logged    |
|                |         |          |         | message.                                         |
+----------------+---------+----------+---------+--------------------------------------------------+

Examples
--------


This example shows how to configure ``LoggerAppenderEcho`` using the
``TTCC layout`` with ``htmlLineBreaks`` turned on.

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderEcho">
                <layout class="LoggerLayoutSimple" />
                <param name="htmlLineBreaks" value="true" />
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
                    'class' => 'LoggerAppenderEcho',
                    'layout' => array(
                        'class' => 'LoggerLayoutSimple',
                    ),
                    'params' => array(
                        'htmlLineBreaks' => 'true',
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
