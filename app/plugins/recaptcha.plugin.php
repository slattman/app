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

define('RECAPTCHA_PUBLIC_KEY', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('RECAPTCHA_PRIVATE_KEY', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

class recaptcha {

	function recaptcha() {
		require_once('../app/plugins/recaptcha-php-1.11/recaptchalib.php');
	}

	function is_enabled() {
		return false;
	}

	function show() {
		echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY);
	}

	function is_valid() {
		$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY,
			$_SERVER["REMOTE_ADDR"],
			$_REQUEST["recaptcha_challenge_field"],
			$_REQUEST["recaptcha_response_field"]);
		return $resp->is_valid;
	}

}

?>