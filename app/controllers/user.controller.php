<?php
# app framework users.class.php - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

class user extends app {

	function __construct() {
		parent::__construct(true);
	}

	function delete() {
		if ($this->app()->controllers->user->is_authorized()) {
			$user = $this->app()->models->users;
			$user->id = $this->app()->session->user->id;
			$user->delete();
			$this->app()->set('user', new stdClass());		
			$this->app()->set('messages', array(
				'<span class="info">Your account has been removed.</span>'
			));
			$this->app()->go('login');
		}
	}
	
	function logout() {
		$this->app()->set('user', new stdClass());
		$this->app()->set('messages', array(
			'<span class="info">You have been logged out.</span>'
		));
		$this->app()->go('login');
	}
	
	function login() {
		if (isset($this->app()->request->username) and isset($this->app()->request->password)) {
			$user = $this->app()->models->users;
			$user->username = $this->app()->request->username;
			$user->password = sha1($this->app()->request->password);
			$user->read();
			$this->app()->set('user', array(
				'id' => $user->id,
				'username' => $user->username,
				'password' => $user->password,
				'group' => $user->group
			));
			if ($this->app()->controllers->user->is_authorized()) {
				$this->app()->set('messages', array(
					'<span class="info">You have been logged in.</span>'
				));
				$this->app()->go('members');
			} else {
				$this->app()->set('messages', array(
					'<span class="error">Invalid username or password.</span>'
				));
			}
		}
	}

	function join() {
		if (isset($this->app()->request->username) and isset($this->app()->request->password)) {

			if (
				!strlen($this->app()->request->username) or 
				!strlen($this->app()->request->password) or 
				strlen($this->app()->request->username) < 8 or 
				strlen($this->app()->request->password) < 8 or 
				!ctype_alnum($this->app()->request->username)
				) {
				$this->app()->set('messages', array(
					'<span class="error">Please enter a valid username and password.</span>'
				));
				$this->app()->go('join');
			}

			if ($this->app()->plugins->recaptcha->is_enabled()) {
				if (!$this->app()->plugins->recaptcha->is_valid()) {
					$this->app()->set('messages', array(
						'<span class="error">The captcha code you entered is incorrect.</span>'
					));
					$this->app()->go('join');
				}
			}

			$user = $this->app()->models->users;
			$user->username = $this->app()->request->username;
			$user->password = sha1($this->app()->request->password);
			$user->group = 'user';
			$user->create();
			
			if (isset($user->id) and $user->id > 0) {
				$this->app()->set('user', array(
					'id' => $user->id,
					'username' => $user->username,
					'password' => $user->password,
					'group' => $user->group
				));
				$this->app()->go('members');
			} else {
				$this->app()->set('messages', array(
					'<span class="error">That username is already taken.</span>'
				));
				$this->app()->go('join');
			}
		}	
	}
	
	function authenticate($group = false) {
		if (isset($this->app()->session->user->username) and isset($this->app()->session->user->password)) {
			$user = $this->app()->models->users;
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
									$this->app()->controllers->user->logout();
								}
							} else {
								$this->app()->controllers->user->logout();
							}
							break;
						}
					}
				}
			} else {
				$this->app()->controllers->user->logout();
			}
		} else {
			$this->app()->controllers->user->logout();
		}
		return;
	}
	
	function is_authorized() {
		return (isset($this->app()->session->user->id) and $this->app()->session->user->id > 0) ? true : false;
	}

}

?>