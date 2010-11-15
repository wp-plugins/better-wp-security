<?php
class BWPS_limitlogin extends BWPS {

	private $computer_id;
		
	function __construct() {
		global $wpdb;
			
		$this->computer_id = $wpdb->escape($_SERVER['REMOTE_ADDR']);
		
		$opts = $this->getOptions();

		if ($opts['limitlogin_denyaccess'] == 1 && $this->checkLock()) {
			die('Security error!');
		}
		
		unset($opts);
			
	}
		
	function isOn() {
		$opts = $this->getOptions();
		$flag =  $opts['limitlogin_enable'];
		unset($opts);
		return $flag;
	}
		
	function countAttempts($username = "") {
		global $wpdb;
		
		$opts = $this->getOptions();
			
		$username = sanitize_user($username);
		$user = get_userdatabylogin($username);
			
		if ($user) {
			$checkField = "user_id";
			$val = $user->ID;
		} else {
			$checkField = "computer_id";
			$val = $this->computer_id;
		}
			
		$fails = $wpdb->get_var("SELECT COUNT(attempt_ID) FROM " . $opts['limitlogin_table_fails'] . "
			WHERE attempt_date +
			" . ($opts['limitlogin_checkinterval'] * 60) . " >'" . time() . "' AND
			" . $checkField . " = '" . $val . "'"
		);
		
		unset($opts);	
		return $fails;
	}

	function logAttempt($username = "") {
		global $wpdb;
		
		$opts = $this->getOptions();
		
		$username = sanitize_user($username);
		$user = get_userdatabylogin($username);
	
		if ($user) {
			$userId = $user->ID;
		} else {
			$userId = "";
		}
		
		unset($user);
					
		$failQuery = "INSERT INTO " . $opts['limitlogin_table_fails'] . " (user_id, computer_id, attempt_date)
			VALUES ('" . $userId . "', '" . $this->computer_id . "', " . time() . ");";
		
		unset($opts);		
		return $wpdb->query($failQuery);
	}

	function lockOut($username = "") {
		global $wpdb;
		
		$opts = $this->getOptions();

		$username = sanitize_user($username);
		$user = get_userdatabylogin($username);
			
		$loTime = time();
		$reTime = $loTime + ($opts['limitlogin_banperiod'] * 60);
				
		if ($user) {
			$lUser = "INSERT INTO " . $opts['limitlogin_table_lockouts'] . " (user_id, lockout_date)
				VALUES ('" . $user->ID . "', " . $loTime . ")";
			unset($user);	
			$wpdb->query($lUser);
				
			$mesEmail = "A Wordpress user, " . $username . ", has been locked out of the Wordpress site at "	. get_bloginfo('url') . " until " . date("l, F jS, Y \a\\t g:i:s a e",$reTime) . " due to too many failed login attempts. You may login to the site to manually release the lock if necessary.";
				
		} else {
			$lHost = "INSERT INTO " . $opts['limitlogin_table_lockouts'] . " (computer_id, lockout_date)
				VALUES ('" . $this->computer_id . "', " . $loTime . ")";
					
			$wpdb->query($lHost);
				
			$mesEmail = "A computer, " . $this->computer_id . ", has been locked out of the Wordpress site at "	. get_bloginfo('url') . " until " . date("l, F jS, Y \a\\t g:i:s a e",$reTime) . " due to too many failed login attempts. You may login to the site to manually release the lock if necessary.";
				
		}
		
		if ($opts['limitlogin_emailnotify'] == 1) {
			$toEmail = get_site_option("admin_email");
			$subEmail = get_bloginfo('name') . ": Site Lockout Notification";
			$mailHead = 'From: ' . get_bloginfo('name')  . ' <' . $toEmail . '>' . "\r\n\\";
			
			$sendMail = wp_mail($toEmail, $subEmail, $mesEmail, $headers);
		}
		
		unset($opts);
			
	}

	function checkLock($username = "") {
		global $wpdb;
		
		$opts = $this->getOptions();
		
		if (strlen($username) > 0) {
			$username = sanitize_user($username);
			$user = get_userdatabylogin($username);

			if ($user) {
				$userCheck = $wpdb->get_var("SELECT user_id FROM " . $opts['limitlogin_table_lockouts']  . 
					" WHERE lockout_date < " . (time() + ($opts['limitlogin_banperiod'] * 60)). " AND user_id = '$user->ID'");
				unset($user);
			}
		} else {
			$userCheck = false;
		}
		
		$hostCheck = $wpdb->get_var("SELECT computer_id FROM " . $opts['limitlogin_table_lockouts']  . 
			" WHERE lockout_date < " . (time() + ($opts['limitlogin_banperiod'] * 60)). " AND computer_id = '$this->computer_id'");
			
		if (!$userCheck && !$hostCheck) {
			unset($opts);
			return false;
		} else {
			unset($opts);
			return true;
		}
	}

	function listLocked($ltype = "") {
		global $wpdb;
		
		$opts = $this->getOptions();
			
		if ($ltype == "users") {
			$checkField = "user_id";
		} else {
			$checkField = "computer_id";
		}

		$lockList = $wpdb->get_results("SELECT lockout_ID, lockout_date, " . $checkField . " AS loLabel FROM " . $opts['limitlogin_table_lockouts']  . " WHERE lockout_date < " . (time() + ($opts['limitlogin_banperiod'] * 60)). " AND " . $checkField . " != ''", ARRAY_A);
			
		if ($ltype == "users" && sizeof($lockList) > 0) {
			$user = get_userdata($lockList[0]['loLabel']);
				
			$lockList[0]['loLabel'] = $user->user_login . " (" . $user->first_name . " " . $user->last_name . ")";
			unset($user);
		}
		
		unset($opts);
		return $lockList;
	}
}
