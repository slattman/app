<?php
# app framework users.class.php
# v2 Brad Slattman - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

class users extends data {
	public $table = 'users';
	public $fields = array('id', 'username', 'password', 'group');
	public $index = 'id';
	public $id;
	public $username;
	public $password;
	public $group;
	
}

?>