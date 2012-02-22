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
	
	function login() {
		if (isset($this->app()->request->username) and isset($this->app()->request->password)) {
			$user = $this->app()->users;
			$user->username = $this->app()->request->username;
			$user->password = sha1($this->app()->request->password);
			$user->read();
			$this->app()->set('user', array(
				'id' => $user->id,
				'username' => $user->username,
				'password' => $user->password,
				'group' => $user->group
			));
			if ($this->app()->user->is_authorized()) {
				$this->app()->go('members');
			} else {
				$this->bind(array('<span class="error">Invalid username or password.</span>'), 'messages');
			}
		}
	}

	function join() {
		if (isset($this->app()->request->username) and isset($this->app()->request->password)) {
			$user = $this->app()->users;
			$user->username = $this->app()->request->username;
			$user->password = sha1($this->app()->request->password);
			$user->group = 'user';
			$user->create();
			if (isset($user->id)) {
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
			$user = $this->app()->users;
			$user->username = $this->app()->session->user->username;
			$user->password = $this->app()->session->user->password;
			$user->read();
			if (isset($user->id)) {
				if ($group) {
					switch ($group) {
						case 'administrator': {
							if (isset($user->group)) {
								if ($user->group != 'administrator') {
									$this->app()->user->logout();
								}
							} else {
								$this->app()->user->logout();
							}
							break;
						}
						case 'user': {
							if (isset($user->group)) {
								if ($user->group != 'administrator' and 
									$user->group != 'user') {
									$this->app()->user->logout();
								}
							} else {
								$this->app()->user->logout();
							}
							break;
						}
					}
				}
			} else {
				$this->app()->user->logout();
			}
		} else {
			$this->app()->user->logout();
		}
		return;
	}
	
	function is_authorized() {
		return (isset($this->app()->session->user->id) and $this->app()->session->user->id > 0) ? true : false;
	}

}

?>