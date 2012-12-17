=======================
LoggerFilterStringMatch
=======================

This filter allows or denies logging events if their message contains a given 
string.

Parameters
----------

This filter has the following parameters:

+---------------+-----------+----------+---------+--------------------------------------+
| Parameter     | Type      | Required | Default | Description                          |
+===============+===========+==========+=========+======================================+
| stringToMatch | string    | **Yes**  | -       | The string to match                  |
+---------------+-----------+----------+---------+--------------------------------------+
| acceptOnMatch | boolean   | No       | true    | If true, the matching log events are |
|               |           |          |         | accepted, denied otherwise.          |
+---------------+-----------+----------+---------+--------------------------------------+

The following filter configuration denies events which contain the string 
"not-interesting" in their message.

Example
-------

The following filter configuration denies events which contain the string 
"not-interesting" in their message.

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="default" class="LoggerAppenderEcho">
                <filter class="LoggerFilterStringMatch">
                    <param name="stringToMatch" value="not-interesting" />
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
                            'class' => 'LoggerFilterStringMatch',
                            'params' => array(
                                'stringToMatch' => 'not-interesting',
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
