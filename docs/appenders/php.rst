==================
LoggerAppenderPhp
==================

``LoggerAppenderPhp`` logs events by creating a PHP user-level message using 
the PHP's `trigger_error()`_ function.

.. _`trigger_error()`: http://www.php.net/manual/en/function.trigger-error.php

The error type depends on the event's severity level:

- ``E_USER_NOTICE`` when the event's level is equal to or less than INFO
- ``E_USER_WARNING`` when the event's level is equal to WARN
- ``E_USER_ERROR`` when the event's level is equal to or greater than ERROR

Layout
------

This appender requires a layout. If no layout is specified in configuration,
``LoggerLayoutSimple`` will be used by default.

Parameters
----------

This appender has no configurable parameters.

Examples
--------

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderPhp">
                <layout class="LoggerLayoutSimple" />
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
                    'class' => 'LoggerAppenderPhp',
                    'layout' => array(
                        'class' => 'LoggerLayoutSimple',
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
