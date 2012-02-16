<?php

if (!class_exists('bwps_backend')) {

	class bwps_backend extends bwps_admin {
	
		function __construct() {
						
			if (is_admin() || (is_multisite() && is_network_admin())) {
			
				//add scripts and css
				add_action('admin_print_scripts', array(&$this, 'config_page_scripts'));
				add_action('admin_print_styles', array(&$this, 'config_page_styles'));
			
				if (is_multisite()) { 
					add_action('network_admin_menu', array(&$this, 'register_settings_page')); 
				} else {
					add_action('admin_menu',  array(&$this, 'register_settings_page'));
				}
			
				//add settings
				add_action('admin_init', array(&$this, 'register_settings'));
			
				//add action link
				add_filter('plugin_action_links', array(&$this, 'add_action_link'), 10, 2);
			
				//add donation reminder
				add_action('admin_init', array(&$this, 'ask'));	
			
				if (isset($_POST['bwps_page'])) {
					add_action('admin_init', array(&$this, 'form_dispatcher'));
				}
			}
			
			add_action('init', array(&$this, 'backup_scheduler'));
						
		}
		
		function backup_scheduler() {
		
			add_action('bwps_backup', array(&$this, 'db_backup'));
			
			$options = get_option('bit51_bwps');
			
			if ($options['backup_enabled'] == 1) {
				if (!wp_next_scheduled('bwps_backup')) {
					wp_schedule_event(time(), $options['backup_int'], 'bwps_backup');
				}
			} else {
				if (wp_next_scheduled('bwps_backup')) {
					wp_clear_scheduled_hook('bwps_backup');
				}
			}
		}
		
		function db_backup() {
			global $wpdb;
			$this->errorHandler = '';
			
			$backuppath = BWPS_PP . 'lib/phpmysqlautobackup/backups/';
			
			$options = get_option('bit51_bwps');
			
			@require(BWPS_PP . 'lib/phpmysqlautobackup/run.php');
			
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
				$message = '<div id="message" class="updated"><p><strong>' . $errors . '</strong></p></div>';
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