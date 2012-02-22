<?php
# app framework config.php
# v1.5 Brad Slattman - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

# common
define('version', '1.0');
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
define('dbname', 'app');
define('dbuser', 'root');
define('dbpass', 'xxxxxxxxxxxxx');

# paths
define('url', '//localhost/app/');
define('app', '../app/');
define('models', app.'models/');
define('controllers', app.'controllers/');
define('views', app.'views/');
define('helpers', app.'helpers/');

# routes
$routes = array(
	'join' => array(
		'view' => true,
		'class' => 'user',
		'method' => 'join'
	),
	'login' => array(
		'view' => true,
		'class' => 'user',
		'method' => 'login'
	),
	'logout' => array(
		'view' => false,
		'class' => 'user',
		'method' => 'logout'
	),
	'members' => array(
		'view' => true,
		'class' => 'user',
		'method' => 'authenticate',
		'args' => 'user'
	)
);

# pages
define('error_page', '404.html');




?>