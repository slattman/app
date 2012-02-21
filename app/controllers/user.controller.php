<?php
# app framework users.class.php
# v1.5 Brad Slattman - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

class user extends app {

	function __construct() {
		parent::__construct(true);
	}

	function logout() {
		$this->app()->set('user', new stdClass());
		$this->app()->go('login');
	}
	
	function join() {
		if (isset($this->app()->request->username) and isset($this->app()->request->password)) {
			$username = $this->app()->request->username;
			$password = sha1($this->app()->request->password);
		}
		if (isset($username) and isset($password)) {
			$user = $this->app()->users;
			$user->id = '';
			$user->username = $username;
			$user->password = sha1($password);
			$user->group = 'user';
			$user->create();
			if ($user->id !== 0) {
				$this->app()->set('user', array(
					'id' => $user->id,
					'username' => $user->username,
					'password' => $user->password,
					'group' => $user->group
				));
				$this->app()->go('members');
			} else {
				$this->bind(array('<span class="error">That username is already taken.</span>'), 'messages');
			}
		}
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
			$user = $this->app()->users;
			$user->username = $username;
			$user->password = $password;
			$user->read();
			$this->app()->set('user', array(
				'id' => $user->id,
				'username' => $user->username,
				'password' => $user->password,
				'group' => $user->group
			));
		}
		if (!$user->id) {
			$this->bind(array('<span class="error">Invalid username or password.</span>'), 'messages');
		}
		if ($group) {
			switch ($group) {
				case 'administrator': {
					if (isset($this->app()->session->user->group)) {
						if ($this->app()->session->user->group != 'administrator') {
							$this->app()->user->logout();
						}
					} else {
						$this->app()->user->logout();
					}
					break;
				}
				case 'user': {
					if (isset($this->app()->session->user->group)) {
						if ($this->app()->session->user->group != 'administrator' and 
							$this->app()->session->user->group != 'user') {
							$this->app()->user->logout();
						}
					} else {
						$this->app()->user->logout();
					}
					break;
				}
			}
		}
		return;
	}
}

?>