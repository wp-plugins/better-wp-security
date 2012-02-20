<?php

if (!function_exists('wp_authenticate')) {

	function wp_authenticate($username, $password) {
		global $bwps;
		
		if($bwps->checkaway()) {
			wp_redirect(get_option('siteurl'));
		}
	
		$options = get_option('bit51_bwps');
	
		$username = sanitize_user($username);
		$password = trim($password);
	
		if ($options['ll_enabled'] == 1 && $bwps->checklock($username)) {
			$bwps->logevent('1', $username);
			return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , your ability to login has been suspended due to too many recent failed login attempts.<br /><br />Please try again later.", $bwps->hook));
		} else {
			$user = apply_filters('authenticate', null, $username, $password);
		}

		if ($user == null) {
			$bwps->logevent('1');
			// TODO what should the error message be? (Or would these even happen?)
			// Only needed if all authentication handlers fail to return anything.
			$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
		}

		$ignore_codes = array('empty_username', 'empty_password');
		
		if (isset($_POST['wp-submit']) && $options['ll_enabled'] == 1 && is_wp_error($user)) {
			$bwps->logevent('1', $username);
		} elseif (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes) ) {
			$bwps->logevent('1', $username);
			do_action('wp_login_failed', $username);
		}

		return $user;
	}
}
