<?php
/*
	Plugin Name: Better WP Security
	Plugin URI: http://bit51.com/software/better-wp-security/
	Description: Helps protect your Wordpress single or multi-site installation from attackers. Hardens standard Wordpress security by hiding vital areas of your site, protecting access to important files via htaccess, preventing brute-force login attempts, detecting attack attempts, and more.
	Version: Dev
	Text Domain: better_wp_security
	Domain Path: /languages
	Author: Bit51.com
	Author URI: http://bit51.com
	License: GPLv2
	Copyright 2012 Bit51.com  (email : info@bit51.com)
*/

//Require common Bit51 library
require_once(plugin_dir_path(__FILE__) . 'lib/bit51/bit51.php');

if (!class_exists('bit51_bwps')) {

	class bit51_bwps extends bit51 {
	
		public $pluginversion 	= '3.0'; //current plugin version
	
		//important plugin information
		public $hook 				= 'better_wp_security';
		public $pluginbase			= 'better-wp-security/better-wp-security.php';
		public $pluginname			= 'Better WP Security';
		public $homepage			= 'http://bit51.com/software/better-wp-security/';
		public $supportpage 		= 'http://forums.bit51.com/topic/better-wp-security/';
		public $wppage 			= 'http://wordpress.org/extend/plugins/better-wp-security/';
		public $accesslvl			= 'manage_options';
		public $paypalcode			= 'QD87YEWSUYL7E';
		public $plugindata 		= 'bit51_bwps_data';
		public $primarysettings	= 'bit51_bwps';
		public $settings			= array(
			'bit51_bwps_options'	=> array(
				'bit51_bwps' 			=> array(
					'backup_email' 			=> '1',
					'backup_int' 			=> 'daily',
					'backup_enabled'		=> '0',
					'backups_to_retain'		=> '10',
					"ll_error_message" => "error",
					"ll_enabled" => "0",
					"ll_maxattemptshost" => "5",
					"ll_maxattemptsuser" => "10",
					"ll_checkinterval" => "5",
					"ll_banperiod" => "60",
					"ll_denyaccess" => "1",
					"ll_emailnotify" => "1"
				)
			)
		);

		function __construct() {
			global $bwps;
		
			//set path information
			define('BWPS_PP', plugin_dir_path(__FILE__));
			define('BWPS_PU', plugin_dir_url(__FILE__));
		
			//load the text domain
			load_plugin_textdomain('better_wp_security', false, dirname(plugin_basename( __FILE__ )) . '/languages');
		
			//require admin page
			require_once(plugin_dir_path(__FILE__) . 'inc/admin.php');
			require_once(plugin_dir_path(__FILE__) . 'inc/backend.php');
			new bwps_backend();
			
			//require setup information
			require_once(plugin_dir_path(__FILE__) . 'inc/setup.php');
			register_activation_hook( __FILE__, array('bwps_setup', 'on_activate'));
			register_deactivation_hook( __FILE__, array('bwps_setup', 'on_deactivate'));
			register_uninstall_hook( __FILE__, array('bwps_setup', 'on_uninstall'));
			
			require_once(plugin_dir_path(__FILE__) . 'inc/auth.php');
			require_once(plugin_dir_path(__FILE__) . 'inc/secure.php');
			$bwps = new bwps_secure();
			
		}	
	}
}

//create plugin object
new bit51_bwps();

//require setup information

