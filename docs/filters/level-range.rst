======================
LoggerFilterLevelRange
======================

This filter accepts or denies logging events if their log level is within the 
specified range.

Parameters
----------

This filter has the following parameters:

+---------------+-------------+----------+---------+----------------------------------------------+
| Parameter     | Type        | Required | Default | Description                                  |
+===============+=============+==========+=========+==============================================+
| levelMin      | LoggerLevel | No       |         | The minimum level to match. If set, events   |
|               |             |          |         | with lower levels will not be matched.       |
+---------------+-------------+----------+---------+----------------------------------------------+
| levelMax      | LoggerLevel | No       |         | The maximum level to match. If set, events   |
|               |             |          |         | with higher levels will not be matched.      |
+---------------+-------------+----------+---------+----------------------------------------------+
| acceptOnMatch | boolean     | No       | true    | If true, the matching log levels will be     |
|               |             |          |         | accepted, otherwise they will be deined.     |
+---------------+-------------+----------+---------+----------------------------------------------+

Example
-------

The following configuration denies event with level lower than warn.

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderEcho">
                <filter class="LoggerFilterLevelRange">
                    <param name="levelMax" value="warn" />
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
                            'class' => 'LoggerFilterLevelRange',
                            'params' => array(
                                'levelMax' => 'warn',
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
