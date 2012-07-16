<?php
/*

	Copyright 2012 the app framework - slattman@gmail.com
	This file is part of the app framework.

	The app framework is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	The app framework is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with the app framework.  If not, see <http://www.gnu.org/licenses/>.

*/

/* common */
ini_set('display_errors', 'On');
error_reporting(E_ALL);

/* configuration */
define('APP_VERSION', '2.0.001');

/* data */
define('DB_HOST', 'localhost');
define('DB_NAME', 'xxx');
define('DB_USER', 'xxx');
define('DB_PASS', 'xxx');

/* paths */
define('BASE_URL', '//localhost/public_html/');
define('ERROR_PAGE', '404.html');

/* routes */
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