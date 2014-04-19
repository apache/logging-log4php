================
LoggerLayoutGelf
================

LoggerLayoutGelf formats the log as JSON string according to GELF specification.

Parameters
----------

The following parameters are available:

+--------------------+---------+----------+---------------+----------------------------------------+
| Parameter          | Type    | Required | Default       | Description                            |
+====================+=========+==========+===============+========================================+
| locationInfo       | boolean | No       | false         | If set to true, adds the file name and |
|                    |         |          |               | line number at which the log statement |
|                    |         |          |               | originated.                            |
+--------------------+---------+----------+---------------+----------------------------------------+
| host               | string  | No       | Result        | Name of server on which logs are       |
|                    |         |          | of            | collected                              |
|                    |         |          | gethostname() |                                        |
+--------------------+---------+----------+---------------+----------------------------------------+
| shortMessageLength | integer | No       | 255           | Maximum length of "short_message"      |
|                    |         |          |               | attribute.                             |
+--------------------+---------+----------+---------------+----------------------------------------+

Examples
--------

.. container:: tabs

    .. rubric:: XML format
.. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderEcho">
                <layout class="LoggerLayoutGelf">
                    <param name="locationInfo" value="true" />
                    <param name="host" value="example.com" />
                    <param name="shortMessageLength" value="100" />
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
                        'class' => 'LoggerLayoutGelf',
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

Produces the following output:

.. code-block:: bash

    {
        "version":"1.1",
        "host":"some-backend.host",
        "short_message":"Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
        "full_message":"Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
        "timestamp":1397928743.1809,
        "level":6,
        "_facility":"myLogger",
        "_thread":"8064",
        "_file":"NA",
        "_line":"NA",
        "_class":"YourClassName",
        "_method":"YourMethodName",
    }
