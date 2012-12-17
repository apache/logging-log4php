===============
LoggerLayoutXml
===============

``LoggerLayoutXml`` formats the messages as an XML document.

Parameters
==========

+-----------------+---------+----------+---------+-------------------------------------------------+
| Parameter       | Type    | Required | Default | Description                                     |
+=================+=========+==========+=========+=================================================+
| locationInfo    | boolean | No       | true    | If set to true, adds the file name and line     |
|                 |         |          |         | number at which the log statement originated.   |
+-----------------+---------+----------+---------+-------------------------------------------------+
| log4jNamespace  | boolean | No       | false   | If set to true then log4j XML namespace will be |
|                 |         |          |         | used instead of the log4php namespace. This can |
|                 |         |          |         | be useful when using log viewers which can only |
|                 |         |          |         | parse the log4j namespace such as Apache        |
|                 |         |          |         | Chainsaw.                                       |
+-----------------+---------+----------+---------+-------------------------------------------------+

Examples
========

Default configuration example
-----------------------------

The default configuration of ``LoggerLayoutXml`` will use the log4php XML 
namespace and include the location information.

Configuration file:

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderEcho">
                <layout class="LoggerLayoutXml" />
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
                        'class' => 'LoggerLayoutXml',
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

.. code-block:: xml

    <log4php:eventSet xmlns:log4php="http://logging.apache.org/log4php/" version="0.3" includesLocationInfo="true">

    <log4php:event logger="myLogger" level="INFO" thread="4464" timestamp="1317215164482">
    <log4php:message><![CDATA[Lorem ipsum dolor sit amet, consectetur adipiscing elit.]]]]><![CDATA[>]]><![CDATA[</log4php:message>
    <log4php:locationInfo class="main" file="D:\Temp\log4php\layout_pattern.php" line="5" method="main" />
    </log4php:event>

    <log4php:event logger="myLogger" level="DEBUG" thread="4464" timestamp="1317215164513">
    <log4php:message><![CDATA[Donec a diam lectus.]]]]><![CDATA[>]]><![CDATA[</log4php:message>
    <log4php:locationInfo class="main" file="D:\Temp\log4php\layout_pattern.php" line="6" method="main" />
    </log4php:event>

    <log4php:event logger="myLogger" level="WARN" thread="4464" timestamp="1317215164514">
    <log4php:message><![CDATA[Sed sit amet ipsum mauris.]]]]><![CDATA[>]]><![CDATA[</log4php:message>
    <log4php:locationInfo class="main" file="D:\Temp\log4php\layout_pattern.php" line="7" method="main" />
    </log4php:event>

    </log4php:eventSet>

Overriding default options
--------------------------

This example show how to configure ``LoggerLayoutXml`` to exclude the location
information and use the log4j XML namespace.

Configuration file:

.. code-block:: xml

    <configuration xmlns="http://logging.apache.org/log4php/">
        <appender name="default" class="LoggerAppenderEcho">
            <layout class="LoggerLayoutXml">
                <param name="locationInfo" value="false" />
                <param name="log4jNamespace" value="true" />
            </layout>
        </appender>
        <root>
            <appender_ref ref="default" />
        </root>
    </configuration>

Using this configuration will produce the following output:

.. code-block:: xml

    <log4j:eventSet xmlns:log4j="http://jakarta.apache.org/log4j/" version="0.3" includesLocationInfo="false">
    <log4j:event logger="myLogger" level="INFO" thread="3156" timestamp="1317216571470">
    <log4j:message><![CDATA[Lorem ipsum dolor sit amet, consectetur adipiscing elit.]]]]><![CDATA[>]]><![CDATA[</log4j:message>
    </log4j:event>

    <log4j:event logger="myLogger" level="DEBUG" thread="3156" timestamp="1317216571471">
    <log4j:message><![CDATA[Donec a diam lectus.]]]]><![CDATA[>]]><![CDATA[</log4j:message>
    </log4j:event>

    <log4j:event logger="myLogger" level="WARN" thread="3156" timestamp="1317216571471">
    <log4j:message><![CDATA[Sed sit amet ipsum mauris.]]]]><![CDATA[>]]><![CDATA[</log4j:message>
    </log4j:event>

    </log4j:eventSet>
