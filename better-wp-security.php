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

//define some paths right in the beginning
define('BWPS_URL', plugin_dir_url(__FILE__));
define('BWPS_PATH', plugin_dir_path(__FILE__));

//load the text domain
load_plugin_textdomain('better_wp_security', false, dirname(plugin_basename( __FILE__ )) . '/languages');

//Require common Bit51 library
require_once(plugin_dir_path(__FILE__) . 'lib/bit51/bit51.php');
require_once(plugin_dir_path(__FILE__) . 'inc/bwpsglobal.php');

if (!class_exists('bit51_bwps')) {

	class bit51_bwps extends Bit51 {
	
		var $pluginversion 	= '3.0'; //current plugin version
	
		//important plugin information
		var $hook 				= 'better_wp_security';
		var $pluginpath			= BWPS_PATH;
		var $pluginbase			= 'better-wp-security/better-wp-security.php';
		var $pluginurl			= BWPS_URL;
		var $pluginname			= 'Better WP Security';
		var $homepage			= 'http://bit51.com/software/better-wp-security/';
		var $supportpage 		= 'http://forums.bit51.com/topic/better-wp-security/';
		var $wppage 			= 'http://wordpress.org/extend/plugins/better-wp-security/';
		var $accesslvl			= 'manage_options';
		var $paypalcode			= 'QD87YEWSUYL7E';
		var $plugindata 		= 'bit51_bwps_data';
		var $primarysettings	= 'bit51_bwps';
		var $settings			= array(
			'bit51_bwps_options' => array(
				'bit51_bwps' => array(
					'callback' => 'bwps_val_options',
					'enabled' => '1'
				)
			)
		);

		function __construct() {
			
		}	
	}
}

//create plugin object
new bit51_bwps();

//require admin page
require_once(plugin_dir_path(__FILE__) . 'inc/admin.php');

//require setup information
require_once(plugin_dir_path(__FILE__) . 'inc/setup.php');
register_activation_hook( __FILE__, array('rat_setup', 'on_activate'));
register_deactivation_hook( __FILE__, array('rat_setup', 'on_deactivate'));
register_uninstall_hook( __FILE__, array('rat_setup', 'on_uninstall'));
