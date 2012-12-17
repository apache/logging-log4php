=============
Configuration
=============

Most components of log4php have various settings which determing their behaviour. They can all be
configured programatically, but a much more common way is by providing the configuration options
in a file.

Log4php understands three configuration formats: XML, PHP and Properties, all of which are covered
in more details in the following sections.

The configuration is passed to log4php by calling the static method ``Logger::configure()``
before issuing any logging requests. In case log4php is not configured by the time a logging request
is issued, log4php will configure itself using the `default configuration`_.

XML format
==========

XML is the most common configuration format, and it is the most prominently featured in the
documentation and examples.

A simple configuration looks like this:

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

Detailed instructions on configuring each component is outlined in the corresponding compomnent's
documentation:

.. toctree::
    :maxdepth: 1

    appenders/index
    layouts/index

PHP format
==========

Configuration can also be stored in a PHP array. This is the format used internally by log4php. All
other formats are converted to a PHP array before being used by the configurator. Because of this, 
the PHP configuration format should be used when performance is important since it will avoid the 
overhead of parsing the ini or XML file.

This format can be used in one of two ways:

The configuration array can directly be passed to ``Logger::configure()``.

.. code-block:: php

    Logger::configure(array(
        'rootLogger' => array(
            'appenders' => array('default'),
        ),
        'appenders' => array(
            'default' => array(
                'class' => 'LoggerAppenderFile',
                'layout' => array(
                    'class' => 'LoggerLayoutSimple'
                ),
                'params' => array(
                    'file' => '/var/log/my.log',
                    'append' => true
                )
            )
        )
    ));

Alternatively a file can be created which holds the PHP configuration array. The file must have the
``php`` extension and it should *return* the configuration array. For example, a file named 
``config.php`` with the following content:

.. code-block:: php

    return array(
        'rootLogger' => array(
            'appenders' => array('default'),
        ),
        'appenders' => array(
            'default' => array(
                'class' => 'LoggerAppenderFile',
                'layout' => array(
                    'class' => 'LoggerLayoutSimple'
                ),
                'params' => array(
                    'file' => '/var/log/my.log',
                    'append' => true
                )
            )
        )
    );

This file can then be used to configure log4php:

.. code-block:: php

    Logger::configure('config.php');

.. note::

    To translate a XML or properties configuration file to PHP, run the following code:

    .. code-block:: php

        $configurator = new LoggerConfiguratorDefault();
        $config = $configurator->parse('/path/to/config.xml');

INI format
==========

The properties configuration format is a legacy method of configuring log4php. It was inherited from
`Apache log4j <http://logging.apache.org/log4j/1.2/manual.html>`_ and uses the same format. The only
difference is that lines begin with ``log4php`` instead of ``log4j``.

.. deprecated:: 2.2.0
    This format has been deprecated. Support will not be removed for the foreseeable future, however
    it may not be updated to include newly introduced features. It is recommended that you use 
    either the `XML format`_ or `PHP format`_ for configuration.

The properites configuration format does not support filters.

The following is a high level overview of this format:

.. code-block:: ini

    # Appender named "default"
    log4php.appender.default = LoggerAppenderEcho
    log4php.appender.default.layout = LoggerLayoutSimple

    # Appender named "file"
    log4php.appender.file = LoggerAppenderDailyFile
    log4php.appender.file.layout = LoggerLayoutPattern
    log4php.appender.file.layout.conversionPattern = %d{ISO8601} [%p] %c: %m (at %F line %L)%n
    log4php.appender.file.datePattern = Ymd
    log4php.appender.file.file = target/examples/daily_%s.log
    log4php.appender.file.threshold = warn

    # Root logger, linked to "default" appender
    log4php.rootLogger = DEBUG, default

    # Logger named "foo", linked to "default" appender
    log4php.logger.foo = warn, default

    # Logger named "foo.bar", linked to "file" appender
    log4php.logger.foo.bar = debug, file
    log4php.additivity.foo.bar = true

    # Logger named "foo.bar.baz", linked to both "file" and "default" appenders
    log4php.logger.foo.bar.baz = trace, default, file
    log4php.additivity.foo.bar.baz = false

    # Renderers for Fruit and Beer classes
    log4php.renderer.Fruit = FruitRenderer
    log4php.renderer.Beer = BeerRenderer

    # Setting base threshold
    log4php.threshold = debug

Default configuration
=====================

If no configuration is provided before the initial logging request is issued, log4php will configure
using the default configuration. This consists of a single ``LoggerAppenderEcho`` appender,
using ``LoggerLayoutSimple``, attached to the root logger and set to the DEBUG level.

The default configuration in PHP format is:

.. code-block:: php

    array(
        'rootLogger' => array(
            'appenders' => array('default'),
        ),
        'appenders' => array(
            'default' => array(
                'class' => 'LoggerAppenderConsole',
                'layout' => array(
                    'class' => 'LoggerLayoutSimple'
                )
            )
        )
    )

.. note::

    You can fetch the default configuration as a PHP array by running:

    .. code-block:: php

        LoggerConfiguratorDefault::getDefaultConfiguration();

Programmatic configuration
==========================

It is possible to configure log4php fully programmatically. This requires the user to implement
their own configurator object. Configurators must implement the ``LoggerConfigurator``
interface.

Here is an example:

.. code-block:: php

    class MyConfigurator implements LoggerConfigurator {

        public function configure(LoggerHierarchy $hierarchy, $input = null) {

            // Create an appender which logs to file
            $appFile = new LoggerAppenderFile('foo');
            $appFile->setFile('D:/Temp/log.txt');
            $appFile->setAppend(true);
            $appFile->setThreshold('all');
            $appFile->activateOptions();

            // Use a different layout for the next appender
            $layout = new LoggerLayoutPattern();
            $layout->setConversionPattern("%date %logger %msg%newline");
            $layout->activateOptions();

            // Create an appender which echoes log events, using a custom layout
            // and with the threshold set to INFO
            $appEcho = new LoggerAppenderEcho('bar');
            $appEcho->setLayout($layout);
            $appEcho->setThreshold('info');
            $appEcho->activateOptions();

            // Add both appenders to the root logger
            $root = $hierarchy->getRootLogger();
            $root->addAppender($appFile);
            $root->addAppender($appEcho);
        }
    }

To use the configurator, pass it as a second parameter to ``Logger::configure()`` (either
the name of the class as a string or an instance). Any value passed as ``$configuration``
will be available in the configure() method of the LoggerConfigurator as ``$input``.

.. code-block:: php

    // User defined configuration (optional)
    $configuration = array(
        'foo' => 1,
        'bar' => 2
    );

    // Passing the configurator as string
    Logger::configure($configuration, 'MyConfigurator');

    // Passing the configurator as an instance
    Logger::configure($configuration, new MyConfigurator());

.. note::

    Always call ``activateOptions()`` on all appenders, filters and layouts after setting their
    configuration parameters. Otherwise, the configuration may not be properly activated.

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
