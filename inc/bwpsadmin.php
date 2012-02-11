<?php

if (!class_exists('bwpsadmin')) {

	class bwpsadmin {
	
		function __construct() {
		
		}
		
		/**
		 * Function to determine whether a given username exists
		 **/
		function user_exists($username) {
			global $wpdb;
			
			$user = $wpdb->get_var("SELECT user_login FROM `" . $wpdb->users . "` WHERE user_login='" . sanitize_text_field($username) . "'");
				
			if ($user == $username) {
				return true;
			} else {
				return false;
			}
		}	
	}	
}

?>