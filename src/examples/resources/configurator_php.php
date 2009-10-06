<?php

return array(
        'threshold' => 'ALL',
        'rootLogger' => array(
            'level' => 'INFO',
            'appenders' => array('default'),
        ),
        'loggers' => array(
            'dev' => array(
                'level' => 'DEBUG',
                'appenders' => array('default'),
            ),
        ),
        'appenders' => array(
            'default' => array(
                'class' => 'LoggerAppenderEcho',
                'layout' => array(
                    'class' => 'LoggerLayoutPattern',
                    'conversionPattern' => "%d{Y-m-d H:i:s} %-5p %c %X{username}: %m in %F at %L%n",
                ),
            ),
        ),
    );

?>