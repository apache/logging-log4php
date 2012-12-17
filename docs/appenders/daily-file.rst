=======================
LoggerAppenderDailyFile
=======================

``LoggerAppenderDailyFile`` writes logging events to a file which is rolled over
depending on the date/time of the logging event. By default, the file is rolled
over daily, hence the appender name. However, the appender can just as easily be
configured to roll over once a month, or even every minute if desired.

Unlike ``LoggerAppenderFile``, the target file is not static, and can change
during script execution as the time passes. Destination file is determined by
two parameters: ``file`` and ``datePattern``.

The path specified in the ``file`` parameter should contain the string ``%s``.
Each time an event is logged, this string will be substituted with the event's
date/time formatted according to ``datePattern`` and the event will be logged to
the resulting file path.

The date/time is formatted according to format string specified in the
``datePattern`` parameter. The format uses the same rules as the PHP `date()
<http://php.net/manual/en/function.date.php>`_ function. Any format string
supported by ``date()`` function may be used as a date pattern.

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
| file        | string  | **Yes**  | -       | Path to the target file. Should contain a ``%s``      |
|             |         |          |         | which gets substituted by the date.                   |
+-------------+---------+----------+---------+-------------------------------------------------------+
| append      | boolean | No       | true    | If set to true, the appender will append to the file, |
|             |         |          |         | otherwise the file contents will be overwritten.      |
+-------------+---------+----------+---------+-------------------------------------------------------+
| datePattern | string  | No       | Ymd     | Format for the date in the file path, follows         |
|             |         |          |         | formatting rules used by the PHP date() function.     |
+-------------+---------+----------+---------+-------------------------------------------------------+

Examples
--------

Consider the following configuration:

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderDailyFile">
                <layout class="LoggerLayoutSimple" />
                <param name="file" value="file-%s.log" />
                <param name="datePattern" value="Y-m-d" />
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
                    'class' => 'LoggerAppenderDailyFile',
                    'layout' => array(
                        'class' => 'LoggerLayoutSimple',
                    ),
                    'params' => array(
                        'datePattern' => 'Y-m-d',
                        'file' => 'file-%s.log',
                    ),
                ),
            ),
            'rootLogger' => array(
                'appenders' => array('default'),
            ),
        );

In this example, the date pattern is set to ``Y-m-d`` (year, month, day) and 
the target file to ``daily.%s.log``. 

Each time this appender receives a logging event, it will: 

* Format the event date/time according to the configured date pattern. Let's say 
  this sample is run during 10th of July 2012, then the formatted date is 
  ``2012-07-10`` 
* Replace the ``%s`` in the filename with the formated date to get the target 
  file. In this case, the target file will be ``daily.2012-07-10.log``. 
* Write to the target file. 

If you continue logging using the given configuration, the appender will 
continue to log to ``daily.2012-07-10.log``, until the date changes. At that 
point it will start logging to ``daily.2012-07-11.log``. 

Similarly, date pattern ``Y-m`` will result in filenames like 
``file-2012-07.log``, which will result in monthly rollover. 

Hours, minutes and seconds can also be used. Pattern ``Y-m-d.H.i.s`` will result 
in filenames similar to ``file-2012-07-03.10.37.15.log``. In this case, a new 
file will be created each second. 

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