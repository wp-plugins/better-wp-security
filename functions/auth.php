<?php
if ( !function_exists('wp_authenticate') ) {
	function wp_authenticate($username, $password) {
		global $BWPS_limitlogin, $BWPS;
		
		$opts = $BWPS->getOptions();

		$username = sanitize_user($username);
		$password = trim($password);
		
		if ($BWPS->isOn('away') && $BWPS->away_check()) {
			wp_redirect(get_option('siteurl'));
		}
		
		if ($BWPS_limitlogin->isOn() && $BWPS_limitlogin->checkLock($username)) {
			if ($opts['limitlogin_denyaccess'] == 1 || $opts['general_removeLoginMessages'] == 1) {
				wp_redirect(get_option('siteurl'));
			} else {
				unset($opts);
				return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later."));
			}
		}

		$user = apply_filters('authenticate', null, $username, $password);

		if ( $user == null ) {
			$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
		}

		$ignore_codes = array('empty_username', 'empty_password');
		
		if ($BWPS_limitlogin->isOn()) {
			if (isset($_POST['wp-submit']) && is_wp_error($user)) {

				if ($opts['limitlogin_maxattemptsuser'] >= $BWPS_limitlogin->countAttempts($username)) {
					$BWPS_limitlogin->logAttempt($username);	
				} else {	
					$BWPS_limitlogin->logAttempt();
				}
				
				if ($opts['limitlogin_maxattemptshost'] <= $BWPS_limitlogin->countAttempts()) {
					$BWPS_limitlogin->lockOut();
					$locked = true;
				}
				
				if ($opts['limitlogin_maxattemptsuser'] <= $BWPS_limitlogin->countAttempts($username)) {
					$BWPS_limitlogin->lockOut($username);
					$locked = true;
				} 
			
				if ($locked == true) {
					if ($opts['limitlogin_denyaccess'] == 1 || $opts['general_removeLoginMessages'] == 1) {
						wp_redirect(get_option('siteurl'));
					} else {
						unset($opts);
						return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later."));
					}
				}
			} elseif (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes) ) {
				if ($opts['limitlogin_maxattemptsuser'] >= $BWPS_limitlogin->countAttempts($username)) {
					$BWPS_limitlogin->logAttempt($username);	
				} else {	
					$BWPS_limitlogin->logAttempt();
				}
			
				if ($opts['limitlogin_maxattemptshost'] <= $BWPS_limitlogin->countAttempts()) {
					$BWPS_limitlogin->lockOut();
					$locked = true;
				}
				
				if ($opts['limitlogin_maxattemptsuser'] <= $BWPS_limitlogin->countAttempts($username)) {
					$BWPS_limitlogin->lockOut($username);
					$locked = true;
				} 
			
				if ($locked == true) {
					if ($opts['limitlogin_denyaccess'] == 1 || $opts['general_removeLoginMessages'] == 1) {
						wp_redirect(get_option('siteurl'));
					} else {
						unset($opts);
						return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later."));
					}
				}
			}

			do_action('wp_login_failed', $username);
		}
		unset($opts);
		return $user;
	}
}