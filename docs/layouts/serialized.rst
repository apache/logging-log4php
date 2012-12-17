======================
LoggerLayoutSerialized
======================

``LoggerLayoutSerialized`` formats the logging event using the PHP's
`serialize() <http://php.net/manual/en/function.serialize.php>`_ function.

Parameters
----------

The following parameters are available:

+--------------+---------+----------+---------+-------------------------------------------------+
| Parameter    | Type    | Required | Default | Description                                     |
+==============+=========+==========+=========+=================================================+
| locationInfo | boolean | No       | true    | If set to true, event's location information    |
|              |         |          |         | will be initialized before serialization.       |
|              |         |          |         | Enabling this parameter makes logging slower    |
|              |         |          |         | and should be used only if required.            |
+--------------+---------+----------+---------+-------------------------------------------------+

Examples
--------

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderEcho">
                <layout class="LoggerLayoutSerialized" />
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
                        'class' => 'LoggerLayoutSerialized',
                    )
                )
            ),
            'rootLogger' => array(
                'appenders' => array('default')
            ),
        )

Running the following code:

.. code-block:: php

    Logger::configure("config.xml");
    $logger = Logger::getLogger('myLogger');
    $logger->info("Lorem ipsum dolor sit amet, consectetur adipiscing elit.");
    $logger->debug("Donec a diam lectus.");
    $logger->warn("Sed sit amet ipsum mauris.");

Produces the following output:

.. code-block:: bash

    O:18:"LoggerLoggingEvent":10:{s:24:" LoggerLoggingEvent fqcn";s:6:"Logger";s:32:" LoggerLoggingEvent categoryName";s:8:"myLogger";s:8:" * level";O:11:"LoggerLevel":3:{s:18:" LoggerLevel level";i:20000;s:21:" LoggerLevel levelStr";s:4:"INFO";s:29:" LoggerLevel syslogEquivalent";i:6;}s:23:" LoggerLoggingEvent ndc";N;s:37:" LoggerLoggingEvent ndcLookupRequired";b:1;s:27:" LoggerLoggingEvent message";s:56:"Lorem ipsum dolor sit amet, consectetur adipiscing elit.";s:35:" LoggerLoggingEvent renderedMessage";N;s:30:" LoggerLoggingEvent threadName";N;s:9:"timeStamp";d:1319380554.782227;s:32:" LoggerLoggingEvent locationInfo";N;}
    O:18:"LoggerLoggingEvent":10:{s:24:" LoggerLoggingEvent fqcn";s:6:"Logger";s:32:" LoggerLoggingEvent categoryName";s:8:"myLogger";s:8:" * level";O:11:"LoggerLevel":3:{s:18:" LoggerLevel level";i:10000;s:21:" LoggerLevel levelStr";s:5:"DEBUG";s:29:" LoggerLevel syslogEquivalent";i:7;}s:23:" LoggerLoggingEvent ndc";N;s:37:" LoggerLoggingEvent ndcLookupRequired";b:1;s:27:" LoggerLoggingEvent message";s:20:"Donec a diam lectus.";s:35:" LoggerLoggingEvent renderedMessage";N;s:30:" LoggerLoggingEvent threadName";N;s:9:"timeStamp";d:1319380554.78247;s:32:" LoggerLoggingEvent locationInfo";N;}
    O:18:"LoggerLoggingEvent":10:{s:24:" LoggerLoggingEvent fqcn";s:6:"Logger";s:32:" LoggerLoggingEvent categoryName";s:8:"myLogger";s:8:" * level";O:11:"LoggerLevel":3:{s:18:" LoggerLevel level";i:30000;s:21:" LoggerLevel levelStr";s:4:"WARN";s:29:" LoggerLevel syslogEquivalent";i:4;}s:23:" LoggerLoggingEvent ndc";N;s:37:" LoggerLoggingEvent ndcLookupRequired";b:1;s:27:" LoggerLoggingEvent message";s:26:"Sed sit amet ipsum mauris.";s:35:" LoggerLoggingEvent renderedMessage";N;s:30:" LoggerLoggingEvent threadName";N;s:9:"timeStamp";d:1319380554.78268;s:32:" LoggerLoggingEvent locationInfo";N;}
