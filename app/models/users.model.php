<?php
/*

	Copyright 2012 the app framework - slattman@gmail.com
	This file is part of the app framework.

	The app framework is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Foobar is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with the app framework.  If not, see <http://www.gnu.org/licenses/>.

*/

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