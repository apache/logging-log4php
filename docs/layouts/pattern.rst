===================
LoggerLayoutPattern
===================

LoggerLayoutPattern is a flexible layout configurable via a conversion pattern.

Parameters
==========

The following parameters are available:

+-------------------+---------+----------+------------------+------------------------------------+
| Parameter         | Type    | Required | Default          | Description                        |
+===================+=========+==========+==================+====================================+
| conversionPattern | boolean | No       | %message%newline | String which controls the output.  |
|                   |         |          |                  | See full specification below.      |
+-------------------+---------+----------+------------------+------------------------------------+

.. list-table::
   :widths: 20 10 10 20 40
   :header-rows: 1

   * - Parameter
     - Type
     - Required
     - Default
     - Description
   * - conversionPattern
     - boolean
     - No
     - %message%newline
     - String which controls the output. See full specification below.

Conversion patterns
===================

**Conversion pattern** is a string which controls the formatting of logging
events. It controls how logging events will be transformed into strings by the
layout.

The conversion pattern is closely related to the conversion pattern of the PHP
`sprintf <http://www.php.net/manual/en/function.sprintf.php>`_ function. It is
composed of literal text and format control expressions called *conversion
specifiers*.

A conversion specifier begins with a percent sign (%) and is followed by a
*conversion word*. Some conversion words require one or more *options* to be
given. These are specified in braces after the conversion word. An example of a
conversion specifier is ``%message`` which will be converted into the message
from the logging event which is being logged.



Conversion specifiers
---------------------

.. function:: %c{length}
.. function:: %lo{length}
.. function:: %logger{length}

    Name of the Logger which generated the logging request.

    Optionally, a desired output length can be specified. If given, the
    converter will attempt to abbreviate the logger name without losing too much
    information in the process. If zero length is specified, only the rightmost
    name fragment will be output.

    Specifying the desired length 0 means that only the class name will be
    returned without the corresponding namespace.

    Several examples of the shortening algorithm in action:

    .. list-table::
       :header-rows: 1

       * - Conversion specifier
         - Logger name
         - Result
       * - ``%logger``
         - org\\apache\\logging\\log4php\\Foo
         - org\\apache\\logging\\log4php\\Foo
       * - ``%logger{0}``
         - org\\apache\\logging\\log4php\\Foo
         - Foo
       * - ``%logger{10}``
         - org\\apache\\logging\\log4php\\Foo
         - o\\a\\l\\l\\Foo
       * - ``%logger{20}``
         - org\\apache\\logging\\log4php\\Foo
         - o\\a\\l\\log4php\\Foo
       * - ``%logger{25}``
         - org\\apache\\logging\\log4php\\Foo
         - o\\a\\logging\\log4php\\Foo
       * - ``%logger{30}``
         - org\\apache\\logging\\log4php\\Foo
         - org\\apache\\logging\\log4php\\Foo

    Note that rightmost segment will never be shortened. It is possible that the
    resulting string will be longer than the specified desired length.
    For backward compatibility, a dot can be used as a namespace separator, as
    well as the backslash.

.. function:: %C{length}
.. function:: %class{length}

    The fully qualified class name of the caller issuing the logging request.
    Just like **%logger**, a desired length can be defined as an option.

.. function:: %cookie{key}

    A value from the $_COOKIE superglobal array corresponding to the given key.
    If no key is given, will return all values in key=value format.

.. function:: %d{pattern}
.. function:: %date{pattern}

    The date/time of the logging event. Accepts a pattern string as an option.
    The pattern syntax is the same as used by the
    `PHP date <http://php.net/manual/en/function.date.php>`_ function.

    If no pattern is given, the date format will default to the ISO8601 datetime
    format, which is the same as giving the pattern: ``c``.

    +-------------------------------+-------------------------------------------+
    | Conversion specifier          | Result                                    |
    +===============================+===========================================+
    | %d                            | 2011-12-27T12:01:32+01:00                 |
    +-------------------------------+-------------------------------------------+
    | %date                         | 2011-12-27T12:01:32+01:00                 |
    +-------------------------------+-------------------------------------------+
    | %date{ISO8601}                | 2011-12-27T12:01:32+01:00                 |
    +-------------------------------+-------------------------------------------+
    | %date{Y-m-d H:i:s,u}          | 2011-12-27 12:01:32,610                   |
    +-------------------------------+-------------------------------------------+
    | %date{l jS \of F Y h:i:s A}   | Tuesday 27th of December 2011 12:01:32 PM |
    +-------------------------------+-------------------------------------------+

.. function:: %e{key}
.. function:: %env{key}

    A value from the $_ENV superglobal array corresponding to the given key.

    If no key is given, will return all values in key=value format.

.. function:: %ex
.. function:: %exception
.. function:: %throwable

    The exception associated with the logging event, along with it's stack
    trace. If there is no exception, evalutates to an empty string.

.. function:: %F
.. function:: %file

    Name of the file from which the logging request was issued.

.. function:: %l
.. function:: %location

    Location information of the caller which generated the logging event.

    Identical to ``%C.%M(%F:%L)``

.. function:: %L
.. function:: %line

    The line number at which the logging request was issued.

.. function:: %m
.. function:: %msg
.. function:: %message

    The message associated with the logging event.

.. function:: %M
.. function:: %method

    The method or function name from which the logging request was issued.

.. function:: %n
.. function:: %newline

    A platform dependent line-break character(s).

    Note that a line break will not be printed unless explicitely specified.

.. function:: %p
.. function:: %le
.. function:: %level

    The level of the logging event.

.. function:: %r
.. function:: %relative

    The number of milliseconds elapsed since the start of the application until
    the creation of the logging event.

.. function:: %req{key}
.. function:: %request{key}

    A value from the $_REQUEST superglobal array corresponding to the given key.

    If no key is given, will return all values in key=value format.

.. function:: %s{key}
.. function:: %server{key}

    A value from the $_SERVER superglobal array corresponding to the given key.

    If no key is given, will return all values in key=value format.

.. function:: %ses{key}
.. function:: %session{key}

    A value from the $_SESSION superglobal array corresponding to the given key.

    If no key is given, will return all values in key=value format.

.. function:: %sid
.. function:: %sessionid

    The active session ID, or an empty string if not in session.

    Equivalent to calling ``session_id()``.

.. function:: %t
.. function:: %pid
.. function:: %process

    The ID of the process that generated the logging event.

.. function:: %x
.. function:: %ndc

    The NDC (Nested Diagnostic Context) associated with the thread that
    generated the logging event.

.. function:: %X{key}
.. function:: %mdc{key}

    A value from the Mapped Diagnostic Context (MDC) corresponding to the given
    key.

Format modifiers
----------------

By default the relevant information is output as-is. However, with the aid of
format modifiers it is possible to change the minimum and maximum width and the
justifications of each data field.

Both format modifiers are optional, and are placed between the percent sign (%)
and the conversion word. These are, in order:

#. A **minimum width specifier**, a number which determines the minimum width of
   the resulting string. If the resulting string is shorter that the given
   number, it will be padded with spaces to the desired length. By default, the
   string is right-justified (padded from left), but adding a "-" sign before
   the specifier will make it left-justified.

#. A **maximum widht specifier**, a dot (".") followed by a number which
   determines the maximum allowed width of the resulting string. If the
   resulting string is shorter than the given number, it will be truncated to
   the maximum width. By default the string is truncated from the right, but
   adding a "-" sign before the specifier will cause it to truncate from the
   left.

The following table demonstrates various uses of format modifiers:

.. list-table::
    :header-rows: 1
    :widths: 10 10 10 10 10 50

    * - Format modifier
      - Padding
      - Trimming
      - Min. width
      - Max. width
      - Comment
    * - ``%logger``
      - none
      - none
      - none
      - none
      - Output the logger name as-is.
    * - ``%20logger``
      - right
      - none
      - 20
      - none
      - Left pad with spaces if the logger name is less than 20 characters long.
    * - ``%-20logger``
      - left
      - none
      - 20
      - none
      - Right pad with spaces if the logger name is less than 20 characters
        long.
    * - ``%.30logger``
      - none
      - right
      - none
      - 30
      - Trim from the end if the logger name is longer than 30 characters.
    * - ``%.-30logger``
      - none
      - left
      - none
      - 30
      - Trim from the beginning if the logger name is longer than 30 characters.
    * - ``%20.30logger``
      - right
      - right
      - 20
      - 30
      - Left pad with spaces if the logger name is shorter than 20 characters.
        However, if the logger name is longer than 30 characters, then trim from
        the end.
    * - ``%-20.30logger``
      - left
      - right
      - 20
      - 30
      - Right pad with spaces if the logger name is shorter than 20 characters.
        However, if the logger name is longer than 30 characters, then trim from
        the end.

The following table lists a couple of examples for using format modifiers.

Note that the square brackets are only added to the conversion pattern to
visually delimit the output.

+--------------------+------------------------+------------------+-------------------------------+
| Conversion pattern | Logger name            | Result           | Note                          |
+====================+========================+==================+===============================+
| [%10logger]        | Foo                    | ``[       Foo]`` | Added padding, right aligned. |
+--------------------+------------------------+------------------+-------------------------------+
| [%-10logger]       | Foo                    | ``[Foo       ]`` | Added padding, left aligned.  |
+--------------------+------------------------+------------------+-------------------------------+
| [%.10logger]       | org.apache.log4php.Foo | ``[org.apache]`` | Trimmed from right.           |
+--------------------+------------------------+------------------+-------------------------------+
| [%.-10logger]      | org.apache.log4php.Foo | ``[og4php.Foo]`` | Trimmed from left.            |
+--------------------+------------------------+------------------+-------------------------------+

Examples
--------

The following configuration configures a ``LoggerAppenderEcho`` which uses the
pattern layout. All examples will use the same code and configuration, only the
conversion pattern will change from example to example.

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderEcho">
                <layout class="LoggerLayoutPattern">
                    <param name="conversionPattern" value="%date %logger %-5level %msg%n" />
                </layout>
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
                        'class' => 'LoggerLayoutPattern',
                        'params' => array(
                            'conversionPattern' => '%date %logger %-5level %msg%n'
                        )
                    )
                )
            ),
            'rootLogger' => array(
                'appenders' => array('default')
            ),
        )

Example code:

.. code-block:: php

    Logger::configure("config.xml");
    $logger = Logger::getLogger('myLogger');
    $logger->info("Lorem ipsum dolor sit amet, consectetur adipiscing elit.");
    $logger->debug("Donec a diam lectus.");
    $logger->warn("Sed sit amet ipsum mauris.");

A simple example
~~~~~~~~~~~~~~~~

Conversion pattern: ``%date %logger %-5level %msg%n``

Running the example code produces the following output:

.. code-block:: bash

    2012-02-27T19:42:17+01:00 myLogger INFO  Lorem ipsum dolor sit amet, consectetur adipiscing elit.
    2012-02-27T19:42:17+01:00 myLogger DEBUG Donec a diam lectus.
    2012-02-27T19:42:17+01:00 myLogger WARN  Sed sit amet ipsum mauris.

In this example, ``%date`` is converted to the event datetime in default format
(corresponding to the ISO-8601 specification), and ``%-5level`` produces the
event level right padded to 5 characters. Since longest level name is 5
characters long, this ensures that the message always starts at the same
character position which improves log readability.

Notice that the newline between logging events (%n) has to be explicitely
defined. Otherwise all logging events will be logged in the same line.

Formatting the date
~~~~~~~~~~~~~~~~~~~

The ``%date`` conversion word can take the desired date format as an option. For
example, if you're European, the d.m.Y date format might be more familiar. Also,
adding milliseconds.

Conversion pattern: ``%date{d.m.Y H:i:s,u} %logger %-5level %msg%n``

Running the example code produces the following output:

.. code-block:: bash

    27.02.2012 20:14:41,624 myLogger INFO  Lorem ipsum dolor sit amet, consectetur adipiscing elit.
    27.02.2012 20:14:41,625 myLogger DEBUG Donec a diam lectus.
    27.02.2012 20:14:41,626 myLogger WARN  Sed sit amet ipsum mauris.

Logging HTTP requests
~~~~~~~~~~~~~~~~~~~~~

If log4php is used to log HTTP requests, a pattern like this might be useful:

``%date [%pid] From:%server{REMOTE_ADDR}:%server{REMOTE_PORT} Request:[%request] Message: %msg%n``

Request ``/test.php?foo=bar`` it will produce the output similar to:

.. code-block:: bash

    2012-01-02T14:19:33+01:00 [22924] From:194.152.205.71:11257 Request:[foo=bar] Message: Lorem ipsum dolor sit amet, consectetur adipiscing elit.
    2012-01-02T14:19:33+01:00 [22924] From:194.152.205.71:11257 Request:[foo=bar] Message: Donec a diam lectus.
    2012-01-02T14:19:33+01:00 [22924] From:194.152.205.71:11257 Request:[foo=bar] Message: Sed sit amet ipsum mauris.

``%server{REMOTE_ADDR}`` is equivalent to PHP code ``$_SERVER['REMOTE_ADDR']``.

Logging exceptions
~~~~~~~~~~~~~~~~~~

If you wish to log any exception passed to the logging methods, you should add
the ``%ex`` specifier to the end of your conversion pattern, after ``%newline``.
This way, if an exception is loggerd it will be addded to your log below your
message.

For example: ``%date %logger %message%newline%ex``

In the following code, suppose that the work() method can throw an exception.
This wolud be a good way to deal with it:

.. code-block:: php

    $log = Logger::getLogger('foo');
    $log->info("Let's try this");

    try
    {
        $foo = new Foo();
        $foo->work(123);
    }
    catch(Exception $ex)
    {
        // Exception is passed as the second parameter
        $log->error("That didn't work", $ex);
    }
    $log->info("Done.");

If work() throws an exception, your log might look something like this:

.. code-block:: bash

    2012-10-08T10:11:18+02:00 foo Let's try this
    2012-10-08T10:11:18+02:00 foo That didn't work
    exception 'Exception' with message 'Doesn't work' in D:\work\exceptions.php:38
    Stack trace:
    #0 D:\work\exceptions.php(29): Bar->work(123)
    #1 D:\work\exceptions.php(48): Foo->work(123)
    #2 {main}
    2012-10-08T10:11:18+02:00 foo Done.

The exception, along with the full stack trace ends up in your log. This also
works with nested exceptions, the full stack trace is added.
