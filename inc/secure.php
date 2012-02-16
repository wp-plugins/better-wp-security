<?php

if (!class_exists('bwps_secure')) {

	class bwps_secure extends bit51_bwps {
	
		function __construct() {
		
		}
		
		function checklock($username = '') {
			global $wpdb;
					
			$options = get_option($this->primarysettings);
			
			if (strlen($username) > 0) { //if a username was entered check to see if it's locked out
				$username = sanitize_user($username);
				$user = get_user_by('login', $username);
		
				if ($user) {
					$userCheck = $wpdb->get_var("SELECT `user` FROM `" . $wpdb->base_prefix . "bwps_lockouts` WHERE `exptime` > " . time(). " AND `user` = " . $user->ID . " AND `active` = 1;");
					unset($user);
				}
			} else { //no username to be locked out
				$userCheck = false;
			}
					
			//see if the host is locked out
			$hostCheck = $wpdb->get_var("SELECT `host` FROM `" . $wpdb->base_prefix . "bwps_lockouts` WHERE `exptime` > " . time(). " AND `host` = '" . $wpdb->escape($_SERVER['REMOTE_ADDR']) . "' AND `active` = 1;");
				
			//return false if both the user and the host are not locked out	
			if (!$userCheck && !$hostCheck) {
				return false;
			} else {
				return true;
			}
		}
		
		function lock_out() {
		
		}
	
	}

}