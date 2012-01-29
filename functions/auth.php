<?php
if ( !function_exists('wp_authenticate') ) {
	function wp_authenticate($username, $password) {
		global $BWPS;
		
		$opts = $BWPS->getOptions();

		$username = sanitize_user($username);
		$password = trim($password);
		
		if ($BWPS->isOn('away') && $BWPS->away_check()) {
			wp_redirect(get_option('siteurl'));
		}
		
		if ($BWPS->isOn('ll') && $BWPS->ll_checkLock($username)) {
			if ($opts['ll_denyaccess'] == 1 || $opts['general_removeLoginMessages'] == 1) {
				wp_redirect(get_option('siteurl'));
			} else {
				unset($opts);
				return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later.", 'better-wp-security'));
			}
		}

		$user = apply_filters('authenticate', null, $username, $password);

		if ( $user == null ) {
			$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.', 'better-wp-security'));
		}

		$ignore_codes = array('empty_username', 'empty_password');
		
		if ($BWPS->isOn('ll')) {
			if (isset($_POST['wp-submit']) && is_wp_error($user)) {

				if ($opts['ll_maxattemptsuser'] >= $BWPS->ll_countAttempts($username)) {
					$BWPS->ll_logAttempt($username);	
				} else {	
					$BWPS->ll_logAttempt();
				}
				
				if ($opts['ll_maxattemptshost'] <= $BWPS->ll_countAttempts()) {
					$BWPS->ll_lockOut();
					$locked = true;
				}
				
				if ($opts['ll_maxattemptsuser'] <= $BWPS->ll_countAttempts($username)) {
					$BWPS->ll_lockOut($username);
					$locked = true;
				} 
			
				if ($locked == true) {
					if ($opts['ll_denyaccess'] == 1 || $opts['general_removeLoginMessages'] == 1) {
						wp_redirect(get_option('siteurl'));
					} else {
						unset($opts);
						return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later.", 'better-wp-security'));
					}
				}
			} elseif (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes) ) {
				if ($opts['ll_maxattemptsuser'] >= $BWPS->ll_countAttempts($username)) {
					$BWPS->ll_logAttempt($username);	
				} else {	
					$BWPS->ll_logAttempt();
				}
			
				if ($opts['ll_maxattemptshost'] <= $BWPS->ll_countAttempts()) {
					$BWPS->ll_lockOut();
					$locked = true;
				}
				
				if ($opts['ll_maxattemptsuser'] <= $BWPS->ll_countAttempts($username)) {
					$BWPS->ll_lockOut($username);
					$locked = true;
				} 
			
				if ($locked == true) {
					if ($opts['ll_denyaccess'] == 1 || $opts['general_removeLoginMessages'] == 1) {
						wp_redirect(get_option('siteurl'));
					} else {
						unset($opts);
						return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later.", 'better-wp-security'));
					}
				}
			}

			do_action('wp_login_failed', $username);
		}
		unset($opts);
		return $user;
	}
}