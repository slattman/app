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

/* start the session */
session_start();

/* load the configuration file */
require_once('../app/config.php');

/* instantiate the framework */
$app = new app();

/* shall we? */
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

	private function initialize() {
		$this->bind($_REQUEST, 'request');
		$this->bind($_SESSION, 'session');
		$method = isset($this->request->app) ? (string)$this->request->app : false;
		if ($method and method_exists(__CLASS__, $method)) {
			call_user_func_array(array($this, $method), array());
		}
		$this->load('core');
		$this->load('controllers');
		$this->load('models');
		$this->load('plugins');
	}

	private function load($type = false) {
		if ($type) {
			$dir = '../app/' . $type . '/';
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if (file_exists($dir.$file) and strstr($file, '.php') and substr_count($file, '.') == 2) {
							$class = explode('.', $file);
							$class = $class[0];
							require_once($dir . $file);
							if (in_array($class, get_declared_classes())) {
								if ($type == 'core') {
									$this->$class = new $class();
								} else {
									$this->$type->$class = new $class();
								}
							}
						}
					}
					closedir($dh);
				}
			}
		}
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

	function view() {
		global $routes;
		$view = isset($this->request->app) ? $this->request->app : 'index';
		if (array_key_exists($view, $routes)) {
			$route = $routes[$view];
			if (class_exists($route['controller'])) {
				if (method_exists($this->app()->controllers->$route['controller'], $route['method'])) {
					$method = new ReflectionMethod($route['controller'], $route['method']);
					if (isset($route['args'])) {
						if (!is_array($route['args'])) {
							$route['args'] = array($route['args']);
						}
						$method->invokeArgs($this->app()->controllers->$route['controller'], $route['args']);
					} else {
						$method->invoke($this->app()->controllers->$route['controller']);
					}
					if ($route['view'] !== true) {
						return;
					}
				}
			}
		}
		if (file_exists('../app/views/' . $view . '.html')) {
			require_once('../app/views/' . $view . '.html');
		} elseif (is_dir('../app/views/' . $view) and file_exists('../app/views/' . $view . '/index.html')) {
			require_once('../app/views/' . $view . '/index.html');
		} else {
			$this->go(ERROR_PAGE);
		}
	}

	function set($key = false, $value = false) {
		if ($key) {
			$_SESSION[$key] = $value;
			$this->bind($_SESSION, 'session');
		}
	}

	function go($page = false) {
		if ($page) {
			header("Location: " . BASE_URL . $page);
			exit;
		}
	}

	function current_view() {
		return isset($this->request->app) ? $this->request->app : 'index';
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

}
?>