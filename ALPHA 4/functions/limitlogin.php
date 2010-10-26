<?php
include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/vars.php');

remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
add_filter('authenticate', 'BWPS_wp_authenticate_username_password', 20, 3);

function getLimitloginOptions() {
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/vars.php');
	global $wpdb;
	
	if (!get_option('BWPS_limilogin_maxattemptshost')) {
		$maxattemptshost = "5";
	} else {
		$maxattemptshost = get_option('BWPS_limilogin_maxattemptshost');
	}
	if (!get_option('BWPS_limitlogin_maxattemptsuser')) {
		$maxattemptsuser = "10";
	} else {
		$maxattemptsuser = get_option('BWPS_limitlogin_maxattemptsuser');
	}
	if (!get_option('BWPS_limilogin_banperiod')) {
		$banperiod = "60";
	} else {
		$banperiod = get_option('BWPS_limilogin_banperiod');
	}
	if (!get_option('BWPS_limilogin_checkinterval')) {
		$checkinterval = "5";
	} else {
		$checkinterval = get_option('BWPS_limilogin_checkinterval');
	}
	
	$opts = array(
		'table_hostfailstable' => $BWPS_limitlogin_table_hostfailstable,
		'table_userfailstable' => $BWPS_limitlogin_table_userfailstable,
		'table_lockouthosttable' => $BWPS_limitlogin_table_lockouthosttable,
		'table_lockoutusertable' => $BWPS_limitlogin_table_lockoutusertable,
		'maxattemptshost' => $maxattemptshost,
		'maxattemptsuser' => $maxattemptsuser,
		'banperiod' => $banperiod,
		'checkinterval' => $checkinterval,
		'computer_id' => $wpdb->escape($_SERVER['REMOTE_ADDR'])
	);
	
	return $opts;
}
		
function BWPS_verifyDbTables() {
	
}
		
function BWPS_countHostFails() {
	global $wpdb;
	$BWPS_limitlogin_options = getLimitloginOptions();

	$hostFails = $wpdb->get_var("SELECT COUNT(attempt_ID) FROM " . $BWPS_limitlogin_options['table_hostfailstable'] . "
		WHERE attempt_date +
			" . ($BWPS_limitlogin_options['checkinterval'] * 60) . " >'" . time() . "' AND
		computer_id = '" . $BWPS_limitlogin_options['computer_id'] . "'"
	);
			
	return $hostFails;
}
		
function BWPS_countUserFails($username = "") {
	global $wpdb;
	$BWPS_limitlogin_options = getLimitloginOptions();
			
	$username = sanitize_user($username);
	$user = get_userdatabylogin($username);
	
	if($user) {
		$userFails = $wpdb->get_var("SELECT COUNT(attempt_ID) FROM " . $BWPS_limitlogin_options['table_userfailstable'] . "
			WHERE attempt_date +
			" . ($BWPS_limitlogin_options['checkinterval'] * 60) . " > " . time() . " AND
			user_id = '" . $user->ID . "'"
		);		
	} else {
		$userFails = 0;
	}
			
	return $userFails;
}

function BWPS_logFailedAttempt($username = "") {
	global $wpdb;
	$BWPS_limitlogin_options = getLimitloginOptions();
			
	$username = sanitize_user($username);
	$user = get_userdatabylogin($username);
	
	if ($user) {		
		$insertUser = "INSERT INTO " . $BWPS_limitlogin_options['table_userfailstable'] . " (user_id, attempt_date)
			VALUES ('" . $user->ID . "', " . time() . ");";
				
		$flag1 = $wpdb->query($insertUser);
	} else {
		$flag1 = true;
	}
			
	$insertHost = "INSERT INTO " . $BWPS_limitlogin_options['table_hostfailstable'] . " (computer_id, attempt_date)
		VALUES ('" . $BWPS_limitlogin_options['computer_id'] . "', " . time() . ");";
	
	$flag1 = $wpdb->query($insertHost);
			
	if ($flag1 == true && $flag2 == true) {
		return true;
	} else {
		return false;
	}
}

function BWPS_lockOutUser($username = "") {
	global $wpdb;
	$BWPS_limitlogin_options = getLimitloginOptions();

	$username = sanitize_user($username);
	$user = get_userdatabylogin($username);
	
	if ($user) {
		$insertUserQuery = "INSERT INTO " . $BWPS_limitlogin_table_lockoutusertable . " (user_id, lockout_date, release_date)
			VALUES ('" . $user->ID . "', " . time() . ", date_add(" . time() . ", INTERVAL " .
			$BWPS_limitlogin_options['checkinterval'] . " MINUTE))";
		
		return $wpdb->query($insertUserQuery);
	}
}
		
function BWPS_lockOutHost() {
	global $wpdb;
	$BWPS_limitlogin_options = getLimitloginOptions();
	
	$insertHostQuery = "INSERT INTO " . $BWPS_limitlogin_table_lockouthosttable . " (computer_id, lockout_date, release_date)
		VALUES ('" . $BWPS_limitlogin_options['computer_id'] . "', " . time() . ", date_add(" . time() . ", INTERVAL " .
		$BWPS_limitlogin_options['checkinterval'] . " MINUTE))";
		
	return $wpdb->query($insertHostQuery);
}

function BWPS_isLockedDown($username = "") {
	global $wpdb;
	$BWPS_limitlogin_options = getLimitloginOptions();
		
	$username = sanitize_user($username);
	$user = get_userdatabylogin($username);
	

	if ($user) {
		$userCheck = $wpdb->get_var("SELECT user_id FROM " . $BWPS_limitlogin_options['table_lockoutusertable'] . "
			WHERE release_date > " . time() . " AND user_id = '$user->ID'");
	}
		
	$hostCheck = $wpdb->get_var("SELECT " . $BWPS_limitlogin_options['computer_id'] . " FROM " . $BWPS_limitlogin_options['table_lockouthosttable'] . "
			WHERE release_date > " . time() . "");

	if ($userCheck == true || $hostCheck == true) {
		return true;
	} else {
		return false;
	}
}

function BWPS_listLockedUsers() {
	global $wpdb;
	$BWPS_limitlogin_options = getLimitloginOptions();

	$lockedUsers = $wpdb->get_results("SELECT lockout_ID, floor((UNIX_TIMESTAMP(release_date)-UNIX_TIMESTAMP(" . time() . "))/60) AS minutes_left,
		user_id FROM " . $BWPS_limitlogin_options['table_lockoutusertable'] . " WHERE release_date > " . time() . "", ARRAY_A);

	return $lockedUsers;
}
		
function BWPS_listLockedHosts() {
	global $wpdb;
	$BWPS_limitlogin_options = getLimitloginOptions();

	$lockedUsers = $wpdb->get_results("SELECT lockout_ID, floor((UNIX_TIMESTAMP(release_date)-UNIX_TIMESTAMP(" . time() . "))/60) AS minutes_left,
		computer_id FROM " . $BWPS_limitlogin_options['table_lockouthosttable'] . " WHERE release_date > " . time() . "", ARRAY_A);

	return $lockedUsers;
}
		
function BWPS_wp_authenticate_username_password($user, $username, $password) {
	if (is_a($user, 'WP_User')) {
		return $user;
	}

	if (empty($username) || empty($password)) {
		$error = new WP_Error();

		if ( empty($username) )
			$error->add('empty_username', __('<strong>ERROR</strong>: The username field is empty.'));

		if ( empty($password) )
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
		
if (!function_exists('wp_authenticate')) {
	function wp_authenticate($username, $password) {
		global $wpdb, $error;
		$BWPS_limitlogin_options = getLimitloginOptions();

		$username = sanitize_user($username);
		$password = trim($password);

		if (BWPS_isLockedDown()) {
			return new WP_Error('incorrect_password', "<strong>ERROR</strong>: We're sorry, but this computer_id range has been blocked due to too many recent " .
				"failed login attempts.<br /><br />Please try again later.");
		}

		$user = apply_filters('authenticate', null, $username, $password);

		if ( $user == null ) {
			// TODO what should the error message be? (Or would these even happen?)
			// Only needed if all authentication handlers fail to return anything.
			$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
		}

		$ignore_codes = array('empty_username', 'empty_password');

		if (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes) ) {
			if ($BWPS_limitlogin_options['maxattemptsuser'] <= BWPS_countUserFails($username)) {
				BWPS_logFailedAttempt($username);	
			} else {
				BWPS_logFailedAttempt();
			}	
			if ($BWPS_limitlogin_options['maxattemptsuser'] <= BWPS_countUserFails($username) || $BWPS_limitlogin_options['maxattemptshost'] <= BWPS_countHostFails()) {
				BWPS_lockOutUser($username);
				return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer_id range has been blocked due to too many recent " .
					"failed login attempts.<br /><br />Please try again later."));
			}

			do_action('wp_login_failed', $username);
		}

		return $user;
	}
}