=======
Filters
=======

Filtering is a mechanism which allows the user to configure more precisely which
logging events will be logged by an appender, and which will be ignored.

Multiple filters can be defined on any appender; they will form a filter chain.
When a logging event is passed onto an appender, the event will first pass
through the filter chain. Each filter in the chain will examine the logging
event and make a decision to either:


* **ACCEPT** the logging event - The event will be logged without consulting the
  remaining filters in the chain.
* **DENY** the logging event - The event will be not logged without consulting
  the remaining filters in the chain.
* Remain **NEUTRAL** - No decision is made, therefore the next filter in the
  chain is consulted. If there are no remaining filters in the chain, the event
  is logged.

Filter reference
================

.. toctree::
   :maxdepth: 1

   deny-all
   level-match
   level-range
   string-match

Configuring filters
===================

Filters are configurable in the XML and PHP configuration format. They cannot be configured using
the properties configuration format.

Like appenders and layouts, depending on the class used, filters may have configurable parameters
which determine their behaviour.

Here is a configuration example:

.. container:: tabs

    .. rubric:: XML format
    .. code-block:: xml

        <configuration xmlns="http://logging.apache.org/log4php/">
            <appender name="defualt" class="LoggerAppenderEcho">
                <layout class="LoggerLayoutSimple"/>
                <filter class="LoggerFilterStringMatch">
                    <param name="stringToMatch" value="interesting" />
                    <param name="acceptOnMatch" value="true" />
                </filter>
                <filter class="LoggerFilterLevelRange">
                    <param name="levelMin" value="debug" />
                    <param name="levelMax" value="error" />
                </filter>
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
                    'class' => 'LoggerAppenderEcho'
                    'layout' => array(
                        'class' => 'LoggerLayoutSimple'
                    ),
                    'filters' => array(
                        array(
                            'class' => 'LoggerFilterStringMatch',
                            'params' => array(
                                'stringToMatch' => 'interesting',
                                'acceptOnMatch' => true,
                            )
                        ),
                        array(
                            'class' => 'LoggerFilterLevelRange',
                            'params' => array(
                                'levelMin' => 'debug',
                                'levelMax' => 'error',
                            )
                        )
                    )
                )
            ),
            'rootLogger' => array(
                'appenders' => array('default'),
            )
        )

In this example, there are two filters defined for the *default* appender.

The first filter ``LoggerFilterStringMatch`` searches for the string
"interesting" in the logging event's message. If the string is found, the filter
will ACCEPT the logging event, and the event will be logged. If the string is
not found, the filter will remain NEUTRAL, and the event will be passed on to
the next filter.

The second filter ``LoggerFilterLevelRange`` ACCEPTS all events which have a
level between DEBUG and ERROR (in other words, levels DEBUG, INFO, WARN and
ERROR). It DENIES all other events.

Therefore, this filter configuration will log events which which have a level
between DEBUG and ERROR, except of theose which have the string "interesting" in
the message. Those will be logged regardless of their level.
