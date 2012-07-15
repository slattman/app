<?php
# app framework config.php - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

# common
define('version', '2');
ini_set('display_errors', 'On');
error_reporting(E_ALL);

# configuration
define('development', true);
define('language', 'english');
define('title', 'app framework '.version);
define('description', '');
define('author', '');

# data
define('dbhost', 'localhost');
define('dbname', 'xxx');
define('dbuser', 'xxx');
define('dbpass', 'xxx');

# paths
define('abs', 'C:/wamp/www');
define('url', '//localhost/public_html/');
define('app', '../app/');

# routes
$routes = array(
	'join' => array(
		'view' => true,
		'controller' => 'user',
		'method' => 'join'
	),
	'login' => array(
		'view' => true,
		'controller' => 'user',
		'method' => 'login'
	),
	'logout' => array(
		'view' => false,
		'controller' => 'user',
		'method' => 'logout'
	),
	'members' => array(
		'view' => true,
		'controller' => 'user',
		'method' => 'authenticate',
		'args' => 'user'
	),
	'member/delete' => array(
		'view' => false,
		'controller' => 'user',
		'method' => 'delete'
	)
);

# pages
define('error', '404.html');




?>