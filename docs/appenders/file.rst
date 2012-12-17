==================
LoggerAppenderFile
==================

``LoggerAppenderFile`` writes logging events to a file.

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
| file        | string  | **Yes**  | -       | Path to the target file. Relative paths are resolved  |
|             |         |          |         | based on the working directory.                       |
+-------------+---------+----------+---------+-------------------------------------------------------+
| append      | boolean | No       | true    | If set to true, the appender will append to the file, |
|             |         |          |         | otherwise the file contents will be overwritten.      |
+-------------+---------+----------+---------+-------------------------------------------------------+


Examples
--------

This example shows how to configure ``LoggerAppenderFile`` to write to
``file.log`` and to overwrite any content present in the file. The target file
will be created in the current working directory.

It is also possible to specify an absolute path to the target file, such as
``/var/log/file.log`` or ``D:/logs/file.log``


.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderFile">
                <layout class="LoggerLayoutSimple" />
                <param name="file" value="file.log" />
                <param name="append" value="false" />
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
                    'class' => 'LoggerAppenderFile',
                    'layout' => array(
                        'class' => 'LoggerLayoutSimple',
                    ),
                    'params' => array(
                        'file' => 'file.log',
                        'append' => false
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

