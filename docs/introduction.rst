============
Introduction
============

There are three main concepts in Apache log4php: loggers, appenders and layouts. These three types
of components work together to enable developers to log messages according to message type and
level, and to control at runtime how these messages are formatted and where they are reported.

Loggers
=======

A logger is a component which will take your logging request and log it. Each class in a project
can have an individual logger, or they can all use a common logger. Loggers are named entities; it
is common to name them after the class which will use it for logging.

Appenders
=========

Logging requests can be sent to multiple destinations and such destinations are called appenders.
Appenders exist for console, files, syslog, database, sockets and other output destinations. One or
more appenders can be attached to a logger. Each enabled logging request for a given logger will be
forwarded to all the appenders in that logger.

Layouts
=======

Layouts are components responsible for transforming a logging event into a string. Most appender
classes require a layout class to convert the event to a string so that it can be logged.

Levels
======

A level describes the severity of a logging message. There are six levels, show here in descending
order of severity.

+-------+----------+------------------------------------------------------------------------------------------------+
| Level | Severity | Description                                                                                    |
+=======+==========+================================================================================================+
| FATAL | Highest  | Very severe error events that will presumably lead the application to abort.                   |
+-------+----------+------------------------------------------------------------------------------------------------+
| ERROR | ...      | Error events that might still allow the application to continue running.                       |
+-------+----------+------------------------------------------------------------------------------------------------+
| WARN  | ...      | Potentially harmful situations which still allow the application to continue running.          |
+-------+----------+------------------------------------------------------------------------------------------------+
| INFO  | ...      | Informational messages that highlight the progress of the application at coarse-grained level. |
+-------+----------+------------------------------------------------------------------------------------------------+
| DEBUG | ...      | Fine-grained informational events that are most useful to debug an application.                |
+-------+----------+------------------------------------------------------------------------------------------------+
| TRACE | Lowest   | Finest-grained informational events.                                                           |
+-------+----------+------------------------------------------------------------------------------------------------+

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