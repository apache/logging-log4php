<?php

return array(
	'rootLogger' => array(
		'level' => 'info',
		'appenders' => array('default')
	),
	'appenders' => array(
		'default' => array(
			'class' => 'LoggerAppenderEcho',
			'layout' => array(
				'class' => 'LoggerLayoutSimple'
			 )
		)
	)
)
;

?>