==================
LoggerLayoutSimple
==================

``LoggerLayoutSimple`` is a basic layout which outputs only the level followed
by the message.

It is interesting to note that the output of ``LoggerLayoutSimple`` is identical
to that of `LoggerLayoutPattern <patern.html>`_ with the `conversion pattern` 
set to ``%p - %m%n``.

Parameters
----------

This layout does not have any configurable parameters.

Examples
--------

Sample configuration:

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderEcho">
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
                    'class' => 'LoggerAppenderEcho',
                    'layout' => array(
                        'class' => 'LoggerLayoutSimple',
                    )
                )
            ),
            'rootLogger' => array(
                'appenders' => array('default')
            ),
        )



Running the following code:

.. code-block:: php

    Logger::configure("layout_xml.xml");
    $log = Logger::getRootLogger();
    $log->info("My first message.");
    $log->debug("My second message.");
    $log->warn("My third message.");

Produces the following output:

.. code-block:: bash

    INFO - My first message.
    DEBUG - My second message.
    WARN - My third message.
