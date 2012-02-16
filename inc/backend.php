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
			
			$options = get_option($this->primarysettings);
			
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
			
			$options = get_option($this->primarysettings);
			
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