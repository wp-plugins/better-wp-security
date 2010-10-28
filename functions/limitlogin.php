<?php
if (!class_exists('BWPS_limitlogin')) {
	class BWPS_limitlogin {
		
		public $opts;
		public $computer_id;
		
		function __construct() {
			global $wpdb, $BWPS, $opts, $computer_id;
			
			$computer_id = $wpdb->escape($_SERVER['REMOTE_ADDR']);
			
			remove_filter('authenticate', array(&$this,'wp_authenticate_username_password'), 20, 3);
			add_filter('authenticate', array(&$this,'_wp_authenticate_username_password'), 20, 3);

			if ($opts['limitlogin_denyaccess'] == 1 && $this->checkLock()) {
				die('Security error!');
			}
			
		}
		
		function countAttempts($username = "") {
			global $wpdb, $opts, $computer_id;
			
			$username = sanitize_user($username);
			$user = get_userdatabylogin($username);
			
			if ($user) {
				$checkField = "user_id";
				$val = $user->ID;
			} else {
				$checkField = "computer_id";
				$val = $computer_id;
			}
			
			$fails = $wpdb->get_var("SELECT COUNT(attempt_ID) FROM " . $opts['limitlogin_table_fails'] . "
				WHERE attempt_date +
				" . ($opts['limitlogin_checkinterval'] * 60) . " >'" . time() . "' AND
				" . $checkField . " = '" . $val . "'"
			);
			
			return $fails;
		}

		function logAttempt($username = "") {
			global $wpdb, $opts, $computer_id;
			
			$username = sanitize_user($username);
			$user = get_userdatabylogin($username);
	
			if ($user) {
				$userId = $user->ID;
			} else {
				$userId = "";
			}
					
			$failQuery = "INSERT INTO " . $opts['limitlogin_table_fails'] . " (user_id, computer_id, attempt_date)
				VALUES ('" . $userId . "', '" . $computer_id . "', " . time() . ");";
				
			return $wpdb->query($failQuery);
		}

		function lockOut($username = "") {
			global $wpdb, $opts, $computer_id;

			$username = sanitize_user($username);
			$user = get_userdatabylogin($username);
			
			$loTime = time();
			$reTime = $loTime + ($opts['limitlogin_banperiod'] * 60);
				
			if ($user) {
				$lUser = "INSERT INTO " . $opts['limitlogin_table_lockouts'] . " (user_id, lockout_date)
					VALUES ('" . $user->ID . "', " . $loTime . ")";
					
				$wpdb->query($lUser);
				
				$mesEmail = "A Wordpress user, " . $username . ", has been locked out of the Wordpress site at "	. get_bloginfo('url') . " until " . date("l, F jS, Y \a\\t g:i:s a e",$reTime) . " due to too many failed login attempts. You may login to the site to manually release the lock if necessary.";
				
			} else {
				$lHost = "INSERT INTO " . $opts['limitlogin_table_lockouts'] . " (computer_id, lockout_date)
					VALUES ('" . $computer_id . "', " . $loTime . ")";
					
				$wpdb->query($lHost);
				
				$mesEmail = "A computer, " . $computer_id . ", has been locked out of the Wordpress site at "	. get_bloginfo('url') . " until " . date("l, F jS, Y \a\\t g:i:s a e",$reTime) . " due to too many failed login attempts. You may login to the site to manually release the lock if necessary.";
				
			}
			
			$toEmail = get_site_option("admin_email");
			$subEmail = get_bloginfo('name') . ": Site Lockout Notification";
			$mailHead = 'From: ' . get_bloginfo('name')  . ' <' . $toEmail . '>' . "\r\n\\";
			
			$sendMail = wp_mail($toEmail, $subEmail, $mesEmail, $headers);
			
		}

		function checkLock($username = "") {
			global $wpdb, $opts, $computer_id;
		
			if (strlen($username) > 0) {
				$username = sanitize_user($username);
				$user = get_userdatabylogin($username);

				if ($user) {
					$userCheck = $wpdb->get_var("SELECT user_id FROM " . $opts['limitlogin_table_lockouts']  . 
						" WHERE lockout_date < " . (time() + ($opts['limitlogin_banperiod'] * 60)). " AND user_id = '$user->ID'");
				}
			}
		
			$hostCheck = $wpdb->get_var("SELECT computer_id FROM " . $opts['limitlogin_table_lockouts']  . 
					" WHERE lockout_date < " . (time() + ($opts['limitlogin_banperiod'] * 60)). " AND computer_id = '$computer_id'");
			
			if (!$userCheck && !$hostCheck) {
				return false;
			} else {
				return true;
			}
		}

		function listLocked($ltype = "") {
			global $wpdb, $opts, $computer_id;
			
			if ($ltype == "users") {
				$checkField = "user_id";
			} else {
				$checkField = "computer_id";
			}

			$lockList = $wpdb->get_results("SELECT lockout_ID, lockout_date, " . $checkField . " AS loLabel FROM " . $opts['limitlogin_table_lockouts']  . " WHERE lockout_date < " . (time() + ($opts['limitlogin_banperiod'] * 60)). " AND " . $checkField . " != ''", ARRAY_A);
			
			if ($ltype == "users" && sizeof($lockList) > 0) {
				$user = get_userdata($lockList[0]['loLabel']);
				
				$lockList[0]['loLabel'] = $user->user_login . " (" . $user->first_name . " " . $user->last_name . ")";
			}
			
			return $lockList;
		}
		
		function dispRem($expTime) {
    		$currTime = time(); 
    		
    		$timeDif = $expTime - $currTime;
    			
		    $dispTime = floor($timeDif / 60) . " minutes and " . ($timeDif % 60) . " seconds";
		    
		    return $dispTime;
		}
		
		function _wp_authenticate_username_password($user, $username, $password) {
			global $opts;
			
			if (is_a($user, 'WP_User')) {
				return $user;
			}
			
			if (empty($username) || empty($password)) {
				$error = new WP_Error();

				if (empty($username))
					$error->add('empty_username', __('<strong>ERROR</strong>: The username field is empty.'));
				
				if (empty($password))
					$error->add('empty_password', __('<strong>ERROR</strong>: The password field is empty.'));
				
				return $error;
			}

			$userdata = get_userdatabylogin($username);

			if (!$userdata) {
				return new WP_Error('invalid_username', sprintf(__('<strong>ERROR</strong>: Invalid username. <a href="%s" title="Password Lost and Found">Lost your password</a>?'), site_url('wp-login.php?action=lostpassword', 'login')));
			}

			$userdata = apply_filters('wp_authenticate_user', $userdata, $password);
		
			if (is_wp_error($userdata)) {
				return $userdata;
			}

			if ( !wp_check_password($password, $userdata->user_pass, $userdata->ID) ) {
				return new WP_Error('incorrect_password', sprintf(__('<strong>ERROR</strong>: Incorrect password. <a href="%s" title="Password Lost and Found">Lost your password</a>?'), site_url('wp-login.php?action=lostpassword', 'login')));
			}
	
			$user =  new WP_User($userdata->ID);
			return $user;
		}
	}
}

if ( !function_exists('wp_authenticate') ) {
	function wp_authenticate($username, $password) {
		global $wpdb, $error, $opts, $limitLogin;

		$username = sanitize_user($username);
		$password = trim($password);
		
		if ($limitLogin->checkLock($username)) {
			if ($opts['limitlogin_denyaccess'] == 1) {
				die('Security error!');
			} else {
				return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later."));
			}
		}

		$user = apply_filters('authenticate', null, $username, $password);

		if ( $user == null ) {
			$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
		}

		$ignore_codes = array('empty_username', 'empty_password');
		
		
		if (isset($_POST['wp-submit']) && is_wp_error($user)) {
		
			if ($opts['limitlogin_maxattemptsuser'] >= $limitLogin->countAttempts($username)) {
				$limitLogin->logAttempt($username);	
			} else {	
				$limitLogin->logAttempt();
			}
			
			if ($opts['limitlogin_maxattemptshost'] <= $limitLogin->countAttempts()) {
				$limitLogin->lockOut();
				$locked = true;
			}
				
			if ($opts['limitlogin_maxattemptsuser'] <= $limitLogin->countAttempts($username)) {
				$limitLogin->lockOut($username);
				$locked = true;
			} 
			
			if ($locked == true) {
				if ($opts['limitlogin_denyaccess'] == 1) {
					die('Security error!');
				} else {
					return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later."));
				}
			}
		} elseif (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes) ) {
			if ($opts['limitlogin_maxattemptsuser'] >= $limitLogin->countAttempts($username)) {
				$limitLogin->logAttempt($username);	
			} else {	
				$limitLogin->logAttempt();
			}
			
			if ($opts['limitlogin_maxattemptshost'] <= $limitLogin->countAttempts()) {
				$limitLogin->lockOut();
				$locked = true;
			}
				
			if ($opts['limitlogin_maxattemptsuser'] <= $limitLogin->countAttempts($username)) {
				$limitLogin->lockOut($username);
				$locked = true;
			} 
			
			if ($locked == true) {
				if ($opts['limitlogin_denyaccess'] == 1) {
					die('Security error!');
				} else {
					return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later."));
				}
			}

			do_action('wp_login_failed', $username);
		}

		return $user;
	}
}