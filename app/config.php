<?php
# app framework config.php
# v1.5 Brad Slattman - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

#common
define('version', '1.0');

ini_set('display_errors', 'On');
error_reporting(E_ALL);

#configuration
define('development', true);
define('language', 'english');
define('title', 'app framework '.version);
define('description', '');
define('author', '');

#data
define('dbhost', 'localhost');
define('dbname', 'app');
define('dbuser', 'root');
define('dbpass', 'magic');

#paths
define('url', '//localhost/app/');
define('app', '../app/');
define('models', app.'models/');
define('controllers', app.'controllers/');
define('views', app.'views/');
define('helpers', app.'helpers/');

?>