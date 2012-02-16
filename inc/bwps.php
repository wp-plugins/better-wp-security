<?php

if (!class_exists('bwps')) {

	abstract class bwps extends bit51 {
	
		function __construct() {
		
		}
		
		function db_backup() {
			global $wpdb;
			$this->errorHandler = '';
			
			$backuppath = $this->pluginpath . 'lib/phpmysqlautobackup/backups/';
			
			$options = get_option('bit51_bwps');
			
			@require($this->pluginpath . 'lib/phpmysqlautobackup/run.php');
			
			$wpdb->query('DROP TABLE `phpmysqlautobackup`;');
			$wpdb->query('DROP TABLE `phpmysqlautobackup_log`;');
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
					$message = '<div id="message" class="error"><p>' . $string . '</p></div>';
				}			
			} else { //no errors so display settings saved message
				$message = '<div id="message" class="updated"><p><strong>' . __('Settings Saved.', $this->hook) . '</strong></p></div>';
			}
			
			add_action('admin_notices', function($message) use ($message) { echo $message; });
			add_action('network_admin_notices', function($message) use ($message) { echo $message; });
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