<?php
# app framework users.class.php
# v1.4 Brad Slattman - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

class users extends app {

	function __construct() {
		parent::__construct(true);
	}

	function logout() {
		$this->app()->set('user', new stdClass());
		$this->app()->go('login');
	}
	
	function authenticate($group = false) {

		if (isset($this->app()->session->user->username) and isset($this->app()->session->user->password)) {
			$username = $this->app()->session->user->username;
			$password = $this->app()->session->user->password;
		} elseif (isset($this->app()->request->username) and isset($this->app()->request->password)) {
			$username = $this->app()->request->username;
			$password = sha1($this->app()->request->password);
		}
		if (isset($username) and isset($password)) {
			$this->app()->data->query(
				"select * from users where username = ? and password = ?", 
				'ss', 
				$username, 
				$password
			);
		}
		if (isset($this->app()->data->results)) {
			$this->app()->set('user', $this->app()->data->results);
		}
		if ($group) {
			switch ($group) {
				case 'administrator': {
					if (isset($this->app()->session->user->group)) {
						if ($this->app()->session->user->group != 'administrator') {
							$this->app()->go('login');
						}
					} else {
						$this->app()->go('login');
					}
					break;
				}
				case 'user': {
					if (isset($this->app()->session->user->group)) {
						if ($this->app()->session->user->group != 'administrator' and 
							$this->app()->session->user->group != 'user') {
							$this->app()->go('login');
						}
					} else {
						$this->app()->go('login');
					}
					break;
				}
			}
		}
		return;
	}
}

?>