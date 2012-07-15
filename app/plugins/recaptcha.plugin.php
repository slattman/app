<?php

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