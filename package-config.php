<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// parse pom.xml to get version in sync
preg_match("/<version>(.+?)(-(incubating))?(-SNAPSHOT)?<\/version>/", file_get_contents("../../pom.xml"), $pom_version);

$name = 'Apache_log4php';
$summary = 'log4php is a PHP port of log4j framework';
$version = $pom_version[1].(empty($pom_version[3]) ? '' : $pom_version[3]);
$versionBuild = 'b1';
$apiVersion = '2.0.0';
$state = empty($pom_version[4]) ? 'stable' : 'snapshot';
$apiStability = 'stable';

$description = <<<EOT
log4php is a PHP port of log4j framework. It supports XML configuration, 
logging to files, stdout/err, syslog, socket, configurable output layouts 
and logging levels.
EOT;

$notes = 'Please see CHANGELOG and changes.xml!';

$options = array(
	'license' => 'Apache License 2.0',
	//'filelistgenerator' => 'svn',
	'ignore' => array('package.php', 'package-config.php'),
	'simpleoutput' => true,
	'baseinstalldir' => '/',
	'packagedirectory' => '.',
	'dir_roles' => array(
		'examples' => 'doc',
	),
	'exceptions' => array(
	    'changes.xml' =>  'doc',
		'CHANGELOG' => 'doc',
		'LICENSE' => 'doc',
		'README' => 'doc',
		'NOTICE' => 'doc',
	),
);

$license = array(
	'name' => 'Apache License 2.0',
	'url' => 'http://www.apache.org/licenses/LICENSE-2.0'
);

$maintainer = array();
$maintainer[]  =   array(
	'role' => 'lead',
	'handle' => 'kurdalen',
	'name' => 'Knut Urdalen',
	'email' => 'kurdalen@apache.org',
	'active' => 'yes'
);
$maintainer[]   =   array(
	'role' => 'lead',
	'handle' => 'grobmeier',
	'name' => 'Christian Grobmeier',
	'email' => 'grobmeier@apache.org',
	'active' => 'yes'
);
$maintainer[]   =   array(
    'role' => 'developer',
    'handle' => 'chammers',
    'name' => 'Christian Hammers',
    'email' => 'chammers@apache.org',
    'active' => 'yes'
);

$dependency = array();

$channel = 'pear.php.net';
$require = array(
	'php' => '5.2.0',
	'pear_installer' => '1.8.0',
);
