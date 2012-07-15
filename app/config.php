<?php
# app framework - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

# common
ini_set('display_errors', 'On');
error_reporting(E_ALL);

# configuration
define('APP_VERSION', '2.0.001');

# data
define('DB_HOST', 'localhost');
define('DB_NAME', 'xxx');
define('DB_USER', 'xxx');
define('DB_PASS', 'xxx');

# paths
define('BASE_URL', '//localhost/public_html/');
define('ERROR_PAGE', '404.html');

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

?>