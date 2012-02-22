<?php
# app framework main.php
# v1.5 Brad Slattman - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

######################################################################################

# start the session
session_start();

# load the configuration file
require_once('../app/config.php');

# instantiate the framework
$app = new app();

# shall we?
class app {

	function __construct($initialized = false) {
		if (!$initialized) {
			$this->initialize();
		}
	}
	
	function app() {
		global $app;
		return $app;
	}
	
	function initialize() {
		$this->bind($_REQUEST, 'request');
		$this->bind($_SESSION, 'session');
		$method = isset($this->request->app) ? (string)$this->request->app : false;
		if ($method and method_exists(__CLASS__, $method)) {
			call_user_func_array(array($this, $method), array());
		}
		$this->load(controllers);
		$this->load(models);
	}

	function load($dir = false) {
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if (file_exists($dir.$file) and strstr($file, '.php') and substr_count($file, '.') == 2) {
						$class = explode('.', $file);
						$class = $class[0];
						require_once($dir.$file);
						if (in_array($class, get_declared_classes())) {
							$this->$class = new $class();
						}
					}
				}
				closedir($dh);
			}
		}
	}
	
	function set($key = false, $value = false) {
		if ($key) {
			$_SESSION[$key] = $value;
			$this->bind($_SESSION, 'session');
		}
	}

	function go($page = false) {
		if ($page) header("Location: ".url.$page);
	}

	function view() {
		global $routes;
		$view = isset($this->request->app) ? $this->request->app : 'index';
		if (array_key_exists($view, $routes)) {
			$route = $routes[$view];
			if (class_exists($route['class'])) {
				if (method_exists($this->app()->$route['class'], $route['method'])) {
					$method = new ReflectionMethod($route['class'], $route['method']);
					if (isset($route['args'])) {
						if (!is_array($route['args'])) {
							$route['args'] = array($route['args']);
						}
						$method->invokeArgs($this->app()->$route['class'], $route['args']);
					} else {
						$method->invoke($this->app()->$route['class']);
					}
					if ($route['view'] !== true) {
						return;
					}
				}
			}
		}
		if (file_exists(views.$view.'.html')) {
			require_once(views.$view.'.html');
		} elseif (is_dir(views.$view) and file_exists(views.$view.'/index.html')) {
			require_once(views.$view.'/index.html');
		} else {
			$this->go(error);
		}
	}
	
	function current_view() {
		return isset($this->request->app) ? $this->request->app : 'index';
	}
	
	function bind($array = array(), $key = false) {
		$object = new stdClass();
		if (count($array)) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					$object->$k = $this->bind($v);
				} else {
					$object->$k = $v;
				}
			}
		}
		if ($key) {
			$this->$key = (object)$object;
		} else {
			return (object)$object;
		}
	}

	function error($error) {
		die(htmlspecialchars($error));
	}

	function debug($die = false) {
		echo "<pre>";
		print_r($this);
		echo "</pre>";
		if ($die) die;
	}

	function shutdown() {
		unset($app);
		unset($this);
	}

	###############################################################################
	# 3rd party helpers

	/*
	HTML Purifier 4.2.0 - Standards Compliant HTML Filtering
	Copyright (C) 2006-2008 Edward Z. Yang
	
	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.
	
	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.
	
	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
	 */
	function purify($dirty_html) {
		require_once(helpers.'htmlpurifier/HTMLPurifier.standalone.php');
		return HTMLPurifier::purify($dirty_html);
	}

}
?>