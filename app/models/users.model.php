<?php
# app framework - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

class users extends data {
	public $table = 'users';
	public $fields = array('id', 'email', 'password', 'group');
	public $index = 'id';
	public $id;
	public $email;
	public $password;
	public $group;
	
}

?>