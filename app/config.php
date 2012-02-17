<?php
# app framework config.php
# v1.4 Brad Slattman - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

#common
define('version', '1.0');

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
define('html', app.'html/');
define('libs', app.'libs/');
define('classes', app.'classes/');

?>