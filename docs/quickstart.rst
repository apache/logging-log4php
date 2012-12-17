===========
Quick start
===========

First, install Apache log4php.

You may also like to read the `introduction <introduction.html>`_ chapter to 
familiarise yoursef with the basic concepts used throughout the documentation 
and examples.

A trivial example
-----------------

Just want logging to stdout?

.. code-block:: php

    include('Logger.php');
    $logger = Logger::getLogger("main");
    $logger->info("This is an informational message.");
    $logger->warn("I'm not feeling so good...");

This produces the following output:

.. code-block:: bash

    INFO - This is an informational message.
    WARN - I'm not feeling so good...

A simple example
-----------------

This example shows how to configure log4php using an XML configuration file. The framework will be
configured to log messages to a file, but only those with level greater or equal to ``WARN``.

First, create a configuration file named ``config.xml`` containing:

.. code-block:: xml
    :linenos:

    <configuration xmlns="http://logging.apache.org/log4php/">
        <appender name="myAppender" class="LoggerAppenderFile">
            <param name="file" value="myLog.log" />
        </appender>
        <root>
            <level value="WARN" />
            <appender_ref ref="myAppender" />
        </root>
    </configuration>

This configuration file does the following:

- *line 2*: Creates an appender named ``myAppender`` using appender class ``LoggerAppenderFile``
  which is used for logging to a file.
- *line 3*: Sets the ``file`` parameter, which tells the appender to which file to write.
- *line 6*: Sets the root logger level to ``WARN``. This means that logging requests with the level
  lower than ``WARN`` will not be logged by the root logger.
- *line 7*: Links ``myAppender`` to the root logger so that all events recieved by the root
  logger will be forwarded to ``myAppender`` and written into the log file.

To try it out, run the following code:

.. code-block:: php

    // Insert the path where you unpacked log4php
    include('log4php/Logger.php');

    // Tell log4php to use our configuration file.
    Logger::configure('config.xml');

    // Fetch a logger, it will inherit settings from the root logger
    $log = Logger::getLogger('myLogger');

    // Start logging
    $log->trace("My first message.");   // Not logged because TRACE < WARN
    $log->debug("My second message.");  // Not logged because DEBUG < WARN
    $log->info("My third message.");    // Not logged because INFO < WARN
    $log->warn("My fourth message.");   // Logged because WARN >= WARN
    $log->error("My fifth message.");   // Logged because ERROR >= WARN
    $log->fatal("My sixth message.");   // Logged because FATAL >= WARN

This will create a file named ``myLog.log`` containing the following output:

.. code-block:: bash

    WARN - My fourth message.
    ERROR - My fifth message.
    FATAL - My sixth message.

An advanced example
-------------------

This example covers named loggers, layouts and best practices in object-oriented programming.

Create a configuration file named ``config.xml`` with the following content:

.. code-block:: xml

    <configuration xmlns="http://logging.apache.org/log4php/">

        <appender name="myConsoleAppender" class="LoggerAppenderConsole" />

        <appender name="myFileAppender" class="LoggerAppenderFile">
            <layout class="LoggerLayoutPattern">
                <param name="conversionPattern" value="%date [%logger] %message%newline" />
            </layout>
            <param name="file" value="myLog.log" />
        </appender>

        <logger name="Foo">
            <appender_ref ref="myFileAppender" />
        </logger>

        <root>
            <level value="DEBUG" />
            <appender_ref ref="myConsoleAppender" />
        </root>
    </configuration>

The configuration defines two appenders: one writes to the console, and the other to a file.

The
console appender doesn't have a layout defined, so it will revert to default layout
(``LoggerLayoutSimple``). The file appender uses a different layout
(``LoggerLayoutPattern``) which will result in different formatting of the logging
events.

The console appender is linked to the root logger. The file appender is linked to the logger named
``Foo``, however ``Foo`` also inherits appenders from the root logger (in this case the console
appender). This means that logging events sent to the ``Foo`` logger will be logged both to the
console and the file.

Consider the following code snippet:

.. code-block:: php

    // Include and configure log4php
    include('log4php/Logger.php');
    Logger::configure('config.xml');

    /**
     * This is a classic usage pattern: one logger object per class.
     */
    class Foo
    {
        /** Holds the Logger. */
        private $log;

        /** Logger is instantiated in the constructor. */
        public function __construct()
        {
            // The __CLASS__ constant holds the class name, in our case "Foo".
            // Therefore this creates a logger named "Foo" (which we configured in the config file)
            $this->log = Logger::getLogger(__CLASS__);
        }

        /** Logger can be used from any member method. */
        public function go()
        {
            $this->log->info("We have liftoff.");
        }
    }

    $foo = new Foo();
    $foo->go();

This produces the following output in the console:

.. code-block:: bash

    INFO - We have liftoff.

And the following in the log file:

.. code-block:: bash

    01/06/11 18:43:39,545 [5428] INFO Foo - We have liftoff.

Note the different layout, this is because ``LoggerLayoutTTCC`` was used as layout for the file 
appender.
