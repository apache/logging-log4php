===================
LoggerFilterDenyAll
===================

``LoggerFilterDenyAll`` simply denies all logging events.

Parameters
----------

This filter has not configurable parameters.

Example
-------

This filter is useful for denying any events which were not accepted by any of 
the previous filters.

In the following example we will create a configuration which will log only 
*INFO* level events. This is accomplished by chaining two filters. First is 
``LoggerFilterLevelMatch`` which accepts *INFO* level events and second is
``LoggerFilterDenyAll`` which denies any events which were not accepted by
the first filter.

The corresponding configuration is:

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="defualt" class="LoggerAppenderEcho">
                <filter class="LoggerFilterLevelMatch">
                    <param name="levelToMatch" value="INFO" />
                    <param name="acceptOnMatch" value="true" />
                </filter>
                <filter class="LoggerFilterDenyAll" />
            </appender>
            <root>
                <level value="TRACE" />
                <appender_ref ref="defualt" />
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
                                'levelToMatch' => 'INFO',
                                'acceptOnMatch' => true
                            )
                        ),
                        array(
                            'class' => 'LoggerFilterDenyAll',
                        ),
                    )
                )
            ),
            'rootLogger' => array(
                'appenders' => array('default'),
            )
        )

The results can be seen by running the following code sample.

.. code-block:: php

    Logger::configure('config.xml');
    $log = Logger::getLogger('example');

    $log->trace('tracing');
    $log->debug('debugging');
    $log->info('informing');
    $log->warn('warning');
    $log->error('erring');
    $log->fatal('fatality');

The resulting output will be:

.. code-block:: bash

    INFO - informing

As you can see, all events were blocked except for INFO.