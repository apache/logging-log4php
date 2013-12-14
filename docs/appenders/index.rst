=========
Appenders
=========

Logging requests can be sent to multiple destinations, such as files, databases,
syslog and others. Such destinations are called appenders. Appenders are
attached to `Loggers <./loggers.html>`_ and each logger can have multiple
attached appenders.

Appender reference
------------------

The following appender classes are available:

.. toctree::
   :maxdepth: 1

   console
   daily-file
   echo
   file
   firephp
   mail
   mail-event
   mongodb

Configuring appenders
---------------------

.. code-block:: xml

    <configuration xmlns="http://logging.apache.org/log4php/">
        <appender name="default" class="LoggerAppenderFile">
            <layout class="LoggerLayoutSimple" />
            <param name="file" value="/var/log/my.log" />
            <param name="append" value="true" />
        </appender>
        <root>
            <appender_ref ref="default" />
        </root>
    </configuration>

From the configuration you can see that an appender has the following
properties:

* A **name** which uniquely identifies it, in this case *default*.

* A **class** which specifies which appender class will be used to handle the
  requests. Since we wish to log to a file, ``LoggerAppenderFile`` is used in
  this case.

* A **layout** which transforms the logging events to string which can be
  logged. A layout is required by most appenders, but some do not require it,
  such as the database appender. If a layout is not defined, the appenders will
  use a default layout.

* Zero or more **parameters** which configure the appender behaviour. In this
  example, the *file* parameter governs the path to the file which will be used
  for logging, and *append* defines that log messages should be appended to the
  file, instead of truncating it.

Linking appenders to loggers
----------------------------

A logger can be linked to one or more appenders. Also, multiple loggers can
share the same appender.

Consider the following configuration:

.. code-block:: xml

    <log4php:configuration xmlns:log4php="http://logging.apache.org/log4php/">
        <appender name="primus" class="LoggerAppenderConsole" />
        <appender name="secundus" class="LoggerAppenderFile">
            <param name="file" value="/var/log/my.log" />
        </appender>
        <logger name="main">
            <appender_ref ref="primus" />
            <appender_ref ref="secundus" />
        </logger>
        <logger name="alternative">
            <appender_ref ref="primus" />
        </logger>
    </log4php:configuration>

This configures two appenders, called *primus* and *secundus*, and two loggers 
named *main* and *alternative*. The logger *main* is linked to *primus* and 
*secundus* and will therefore forward logging events to both of them. In other 
words, it will log both to console and to a file. Logger *alternative* is only 
linked to appender *primus* and will therefore only log to the console.

Appender threshold
------------------

An appender can be assigned a threshold level. All logging requests with level 
lower than this threshold will be ignored.

For example, if you set ``WARN`` as a threshold, then ``INFO``, ``DEBUG`` and 
``TRACE`` level events recieved by the appender will not be logged, but 
``WARN``, ``ERROR`` and ``FATAL`` will.

An example of setting an appender threshold:

.. code-block:: xml

    <configuration xmlns="http://logging.apache.org/log4php/">
        <appender name="default" class="LoggerAppenderEcho" threshold="WARN" />
        <root>
            <appender_ref ref="default" />
        </root>
    </configuration>

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