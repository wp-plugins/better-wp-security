<?php

if (!class_exists('bwps')) {

	abstract class bwps extends bit51 {
	
		function __construct() {
		
		}
		
		/**
		 * Function to determine whether a given username exists
		 **/
		function user_exists($username) {
			global $wpdb;
			
			//return false if username is null
			if ($username == '') {
				return false;
			}
			
			//queary the user table to see if the user is there
			$user = $wpdb->get_var("SELECT user_login FROM `" . $wpdb->users . "` WHERE user_login='" . sanitize_text_field($username) . "'");
			
			if ($user == $username) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * Show error messages or settings saved message.
		 **/
		function showmessages($errors) {
			
			if (function_exists('apc_store')) { 
				apc_clear_cache(); //Let's clear APC (if it exists) when big stuff is saved.
			}
			
			if (is_wp_error($errors)) { //see if object is even an error
				$errors = $errors->get_error_messages(); //get all errors if it is
				foreach ($errors as $error => $string) {
					echo '<div id="message" class="error"><p>' . $string . '</p></div>';
				}			
			} else { //no errors so display settings saved message
				echo '<div id="message" class="updated"><p><strong>' . __('Settings Saved.', 'better-wp-security') . '</strong></p></div>';
			}
		}
		
		/**
		 * Determines if site is a fresh install or existing site.
		 */
		function is_new_site() {
			global $wpdb;
			
			$lastpost = $wpdb->get_var("SELECT MAX(ID) FROM `" . $wpdb->posts . "`");
			
			if (is_multisite()) {
				$blogcount = $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "blogs`");
			} else {
				$blogcount = 1;
			}
			
			if ($lastpost > 3 || $blogcount > 1) {
				return false;
			} else {
				return true;
			}
		}
		
		/**
		 * Returns the path to wp-config.php
		 * @return string
		 */
		function getConfig() {
			if (file_exists(trailingslashit(ABSPATH) . 'wp-config.php')) {
				return trailingslashit(ABSPATH) . 'wp-config.php';
			} else {
				return trailingslashit(dirname(ABSPATH)) . 'wp-config.php';
			}
		}
	}	
}

?>