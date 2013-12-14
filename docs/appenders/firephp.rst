=====================
LoggerAppenderFirePHP
=====================

``LoggerAppenderFirePHP`` logs events via the
`FirePHP <http://www.firephp.org/>`_ serverside library. The messages are logged
in HTTP headers and can be viewed using the
`Developer compainion <http://developercompanion.com/>`_ plugin for Firefox.

Requires the FirePHP server-side library 1.0 or greater. Download it from
`here <http://sourcemint.com/github.com/firephp/firephp/1>`_

.. warning::

    This appender is still experimental. Behaviour may change in future versions
    without notification.

Layout
------

This appender requires a layout. If no layout is specified in configuration,
``LoggerLayoutSimple`` will be used by default.

Parameters
----------

The following parameters are available:

+-------------+---------+----------+---------+-------------------------------------------------------+
| Parameter   | Type    | Required | Default | Description                                           |
+=============+=========+==========+=========+=======================================================+
| target      | string  | No       | page    | The target to which messages will be sent. Possible   |
|             |         |          |         | options are 'page' (default), 'request', 'package'    |
|             |         |          |         | and 'controller'. For more details, see FirePHP       |
|             |         |          |         | documentation.                                        |
+-------------+---------+----------+---------+-------------------------------------------------------+

Examples
--------

Sample configuration:

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderFirePHP">
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
                    'class' => 'LoggerAppenderFirePHP',
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
