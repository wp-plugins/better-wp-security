<?php
/*
	Plugin Name: Better WP Security
	Plugin URI: http://bit51.com/software/better-wp-security/
	Description: Helps protect your Wordpress installation from attackers. Hardens standard Wordpress security by hiding vital areas of your site, protecting access to important files via htaccess, preventing brute-force login attempts, detecting attack attempts, and more.
	Version: 3.0.12
	Text Domain: better_wp_security
	Domain Path: /languages
	Author: Bit51.com
	Author URI: http://bit51.com
	License: GPLv2
	Copyright 2012 Bit51.com  (email : info@bit51.com)
*/

//Require common Bit51 library
require_once( plugin_dir_path( __FILE__ ) . 'lib/bit51/bit51.php' );

if ( ! class_exists( 'bit51_bwps' ) ) {

	class bit51_bwps extends bit51 {
	
		public $pluginversion 	= '3013'; //current plugin version
	
		//important plugin information
		public $hook 				= 'better_wp_security';
		public $pluginbase			= 'better-wp-security/better-wp-security.php';
		public $pluginname			= 'Better WP Security';
		public $homepage			= 'http://bit51.com/software/better-wp-security/';
		public $supportpage 		= 'http://forums.bit51.com/topic/better-wp-security/';
		public $wppage 				= 'http://wordpress.org/extend/plugins/better-wp-security/';
		public $accesslvl			= 'manage_options';
		public $paypalcode			= 'V647NGJSBC882';
		public $plugindata 			= 'bit51_bwps_data';
		public $primarysettings		= 'bit51_bwps';
		public $settings			= array(
			'bit51_bwps_options'	=> array(
				'bit51_bwps' 			=> array(
					'initial_backup'		=> '0',
					'am_enabled'			=> '0',
					'am_type' 				=> '0',
					'am_startdate' 			=> '1',
					'am_enddate' 			=> '1',
					'am_starttime' 			=> '1',
					'am_endtime' 			=> '1',
					'backup_email' 			=> '1',
					'backup_time'			=> '1',
					'backup_interval'		=> '1',
					'backup_enabled'		=> '0',
					'backup_last'			=> '',
					'backup_next'			=> '',
					'backups_to_retain'		=> '10',
					'bu_enabled' 			=> '0',
					'bu_banlist' 			=> '',
					'bu_banagent'			=> '',
					'hb_enabled'			=> '0',
					'hb_login'				=> 'login',
					'hb_register'			=> 'register',
					'hb_admin'				=> 'admin',
					'hb_key'				=> '',
					'll_enabled' 			=> '0',
					'll_maxattemptshost' 	=> '5',
					'll_maxattemptsuser' 	=> '10',
					'll_checkinterval' 		=> '5',
					'll_banperiod' 			=> '15',
					'll_emailnotify' 		=> '1',
					'id_enabled' 			=> '0',
					'id_emailnotify' 		=> '1',
					'id_checkinterval' 		=> '5',
					'id_threshold' 			=> '20',
					'id_banperiod' 			=> '15',
					'id_whitelist' 			=> '',
					'st_ht_files'			=> '0',
					'st_ht_browsing'		=> '0',
					'st_ht_request'			=> '0',
					'st_ht_query'			=> '0',
					'st_generator'			=> '0',
					'st_manifest'			=> '0',
					'st_edituri'			=> '0',
					'st_themenot'			=> '0',
					'st_pluginnot'			=> '0',
					'st_corenot'			=> '0',
					'st_enablepassword'		=> '0',
					'st_passrole'			=> 'administrator',
					'st_loginerror'			=> '0',
					'st_fileperm'			=> '0',
					'st_randomversion'		=> '0',
					'st_longurl'			=> '0',
					'st_fileedit'			=> '0',
					'st_forceloginssl'		=> '0',
					'st_forceadminssl'		=> '0'
				)
			)
		);

		function __construct() {
		
			global $bwps;
		
			//set path information
			define( 'BWPS_PP', plugin_dir_path( __FILE__ ) );
			define( 'BWPS_PU', plugin_dir_url( __FILE__ ) );
		
			//load the text domain
			load_plugin_textdomain( 'better_wp_security', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		
			//require admin pages
			if ( is_admin() || (is_multisite() && is_network_admin() ) ) {
				require_once( plugin_dir_path( __FILE__ ) . 'inc/admin/construct.php' );
			}
			
			//require setup information
			require_once( plugin_dir_path( __FILE__ ) . 'inc/setup.php' );
			register_activation_hook( __FILE__, array( 'bwps_setup', 'on_activate' ) );
			register_deactivation_hook( __FILE__, array( 'bwps_setup', 'on_deactivate' ) );
			register_uninstall_hook( __FILE__, array( 'bwps_setup', 'on_uninstall' ) );
			
			//require_once(plugin_dir_path( __FILE__ ) . 'inc/auth.php' );
			require_once(plugin_dir_path( __FILE__ ) . 'inc/secure.php' );
			$bwps = new bwps_secure();
			
			//make sure update script runs
			$options = get_option( $this->plugindata );
			
			if ( $options['version'] != $this->pluginversion  || get_option( 'BWPS_options' ) != false ) {
				new bwps_setup( 'activate' );
			}
			
		}	
		
	}
	
}

//create plugin object
new bit51_bwps();
