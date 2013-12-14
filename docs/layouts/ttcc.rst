================
LoggerLayoutTTCC
================

.. warning::

    LoggerLayoutTTCC is deprecated and will be removed in a future release. Please
    use `LoggerLayoutPattern <pattern.html>`_ instead.

The TTCC layout format was taken from Apache log4j, and originally consisted of
Time, Thread, Category and nested diagnostic Context information, hence the
name.

``LoggerLayoutTTCC`` contains equivalent information:

* Time
* Process ID
* Logger name
* Nested diagnostic context

Output of ``LoggerLayoutTTCC`` is identical to that of LoggerLayoutPattern_
with the *conversion pattern* set to ``%d{m/d/y H:i:s,u} [%t] %p %c %x - %m%n``.

Parameters
----------

The following parameters are available:


.. list-table::
    :widths: 20 10 10 10 50
    :header-rows: 1

    * - Parameter
      - Type
      - Required
      - Default
      - Description
    * - threadPrinting
      - boolean
      - No
      - true
      - If set to true, the process ID will be included in output.
    * - categoryPrefixing
      - boolean
      - No
      - true
      - If set to true, the logger name will be included in output.
    * - contextPrinting
      - boolean
      - No
      - true
      - If set to true, the nested diagnostic context will be included in output.
    * - microSecondsPrinting
      - boolean
      - No
      - true
      - If set to true, the microseconds will be included in output.

Examples
--------

Configuration:

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderEcho">
                <layout class="LoggerLayoutTTCC" />
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
                        'class' => 'LoggerLayoutTTCC',
                    )
                )
            ),
            'rootLogger' => array(
                'appenders' => array('default')
            ),
        )

For this example, some Nested Diagnostic Context is added also. Running the
following code:

.. code-block:: php

    Logger::configure("config.xml");
    LoggerNDC::push("Some Context");

    $logger = Logger::getLogger('myLogger');
    $logger->info("Lorem ipsum dolor sit amet, consectetur adipiscing elit.");
    $logger->debug("Donec a diam lectus.");
    $logger->warn("Sed sit amet ipsum mauris.");

Produces the following output:

.. code-block:: bash

    02/20/12 23:36:39,772 [9820] INFO myLogger Some Context - Lorem ipsum dolor sit amet, consectetur adipiscing elit.
    02/20/12 23:36:39,773 [9820] DEBUG myLogger Some Context - Donec a diam lectus.
    02/20/12 23:36:39,773 [9820] WARN myLogger Some Context - Sed sit amet ipsum mauris.
