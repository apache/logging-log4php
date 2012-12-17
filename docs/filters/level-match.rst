======================
LoggerFilterLevelMatch
======================

LoggerFilterLevelMatch either accepts the specified logger level or denies it.

Parameters
----------

This filter has the following parameters:

+---------------+-------------+----------+---------+------------------------------------+
| Parameter     | Type        | Required | Default | Description                        |
+===============+=============+==========+=========+====================================+
| levelToMatch  | LoggerLevel | **Yes**  | -       | The level to match                 |
+---------------+-------------+----------+---------+------------------------------------+
| acceptOnMatch | boolean     | No       | true    | If true, the matching log level is |
|               |             |          |         | accepted, denied otherwise.        |
+---------------+-------------+----------+---------+------------------------------------+

Example
-------

The following filter configuration will deny all logging events with level 
DEBUG. It will remain neutral for others.

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderEcho">
                <filter class="LoggerFilterLevelMatch">
                    <param name="levelToMatch" value="debug" />
                    <param name="acceptOnMatch" value="false" />
                </filter>
            </appender>
            <root>
                <level value="trace" />
                <appender_ref ref="default" />
            </root>
        </configuration>

    .. rubric:: PHP format
    .. code-block:: php

        array(
            'appenders' => array(
                'default' => array(
                    'class' => 'LoggerAppenderEcho',
                    'filters' => array(
                        array(
                            'class' => 'LoggerFilterLevelMatch',
                            'params' => array(
                                'levelToMatch' => 'debug',
                                'acceptOnMatch' => false
                            )
                        )
                    )
                )
            ),
            'rootLogger' => array(
                'appenders' => array('default'),
            )
        )
