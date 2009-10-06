<?php
// START SNIPPET: doxia
require_once dirname(__FILE__).'/../../main/php/Logger.php';

Logger::configure(dirname(__FILE__).'/../resources/cache.properties');

$cache = 'target/examples/hierarchy.cache';

if(!file_exists($cache)) {
	$dir = dirname($cache);
	if(!is_dir($dir)) {
		mkdir($dir, 0777, true);
	}
	$old_logger = Logger::getRootLogger();
	file_put_contents($cache, serialize($old_logger));
}
$logger = unserialize(file_get_contents($cache));

$logger->debug('Debug message from cached logger');
// END SNIPPET: doxia
