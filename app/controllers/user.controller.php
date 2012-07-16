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

class user extends app {

	function __construct() {
		parent::__construct(true);
	}

	function delete() {
		if ($this->is_authorized()) {
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
		if (isset($this->app()->request->email) and isset($this->app()->request->password)) {
			/* if ($this->app()->plugins->recaptcha->is_enabled()) {
				if (!$this->app()->plugins->recaptcha->is_valid()) {
					$this->app()->set('messages', array(
						'<span class="error">The captcha code you entered is incorrect.</span>'
					));
					$this->app()->go('join');
				}
			} */
			$user = $this->app()->models->users;
			$user->email = $this->app()->request->email;
			$user->password = sha1($this->app()->request->password);
			$user->read();
			$this->app()->set('user', array(
				'id' => $user->id,
				'email' => $user->email,
				'password' => $user->password,
				'group' => $user->group
			));
			if ($this->is_authorized()) {
				$this->app()->set('messages', array(
					'<span class="info">You have been logged in.</span>'
				));
				$this->app()->go('members');
			} else {
				$this->app()->set('messages', array(
					'<span class="error">Invalid email or password.</span>'
				));
			}
		}
	}

	function join() {
		if (isset($this->app()->request->email) and isset($this->app()->request->password)) {
			if (
				!strlen($this->app()->request->email) or 
				!strlen($this->app()->request->password) or 
				$this->is_not_valid_email() or 
				strlen($this->app()->request->password) < 8) {
				$this->app()->set('messages', array(
					'<span class="error">Please enter a valid email and password.</span>'
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
			$user->email = $this->app()->request->email;
			$user->password = sha1($this->app()->request->password);
			$user->group = 'user';
			$user->create();
			if (isset($user->id) and $user->id > 0) {
				$this->login();
			} else {
				$this->app()->set('messages', array(
					'<span class="error">That email is already being used.</span>'
				));
				$this->app()->go('join');
			}
		}	
	}
	
	function authenticate($group = false) {
		if (isset($this->app()->session->user->email) and isset($this->app()->session->user->password)) {
			$user = $this->app()->models->users;
			$user->email = $this->app()->session->user->email;
			$user->password = $this->app()->session->user->password;
			$user->read();
			if (isset($user->id)) {
				if ($group) {
					switch ($group) {
						case 'administrator': {
							if (isset($user->group)) {
								if ($user->group != 'administrator') {
									$this->logout();
								}
							} else {
								$this->logout();
							}
							break;
						}
						case 'user': {
							if (isset($user->group)) {
								if ($user->group != 'administrator' and 
									$user->group != 'user') {
									$this->logout();
								}
							} else {
								$this->logout();
							}
							break;
						}
					}
				}
			} else {
				$this->logout();
			}
		} else {
			$this->logout();
		}
		return;
	}

	function is_not_valid_email() {
		if ($this->app()->request->email) {
			if (filter_var($this->app()->request->email, FILTER_VALIDATE_EMAIL)) {
				return false;
			}
		}
		return true;
	}
	
	function is_authorized() {
		return (isset($this->app()->session->user->id) and $this->app()->session->user->id > 0) ? true : false;
	}

}

?>