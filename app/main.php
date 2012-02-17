<?php
# app framework main.php
# v1.4 Brad Slattman - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

######################################################################################
# initiate the app framework

# start the session
session_start();

# load the configuration file
require_once('../app/config.php');

# shall we?
$app = new app();

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
		if (is_dir(classes)) {
			if ($dh = opendir(classes)) {
				while (($class = readdir($dh)) !== false) {
					$class = str_replace('.class.php', '', $class);
					if (!is_dir($class)) require_once(classes.$class.'.class.php');
					if (in_array($class, get_declared_classes())) {
						$this->$class = new $class();
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
		if ($page) header("Location: $page");
	}

	function html() {
		$html = isset($this->request->app) && $this->request->app ? $this->request->app : 'index';
		if (file_exists(html.$html.'.html')) {
			require_once(html.$html.'.html');
		} elseif (is_dir(html.$html)) {
			require_once(html.$html.'/index.html');
		} elseif (file_exists(html.'index.html')) {
			require_once(html.'index.html');
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

	function error($error) {
		die(htmlspecialchars($error));
	}

	function debug() {
		echo "<pre>";
		print_r($this);
		echo "</pre>";
	}

	function shutdown() {
		unset($app);
		unset($this);
	}

	###############################################################################
	# 3rd party libraries

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
		require_once(libs.'htmlpurifier/HTMLPurifier.standalone.php');
		return HTMLPurifier::purify($dirty_html);
	}

}
?>