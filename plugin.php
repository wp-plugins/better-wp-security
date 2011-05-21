<?php
/**
 * Main BWPS plugin file defines plugin and registers with Wordpress
 *
 * @package BWPS
 */
 
/*
	Plugin Name: Better WP Security
	Plugin URI: http://www.chriswiegman.com/projects/better-wp-security/
	Description: Helps protect your Wordpress single or multi-site installation from attackers. Hardens standard Wordpress security by hiding vital areas of your site, protecting access to important files via htaccess, preventing brute-force login attempts, detecting attack attempts, and more.
	Version: Dev
	Author: Chris Wiegman
	Author URI: http://www.chriswiegman.com
	License: GPLv2
	Copyright 2011  ChrisWiegman  (email : chris@chriswiegman.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU tweaks Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU tweaks Public License for more details.

	You should have received a copy of the GNU tweaks Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//Require the code to the rest of the plugin
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/bwps.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/auth.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/setup.php');

//access the wpdb object
global $wpdb;

//Define section versions
define('BWPS_VERSION_AWAY','1');
define('BWPS_VERSION_BANIPS','1');
define('BWPS_VERSION_IDETECT','2');
define('BWPS_VERSION_HIDEBE','3');
define('BWPS_VERSION_HTACCESS','4');
define('BWPS_VERSION_LL','1');
define('BWPS_VERSION_TWEAKS','10');

//Defing table versions
define('BWPS_VERSION_TABLE_D404','1');
define('BWPS_VERSION_TABLE_LL','1');
define('BWPS_VERSION_TABLE_LOCKOUTS','1');

//Define table names
if (is_multisite()) {
	$dpre = substr($wpdb->prefix,0,strpos($wpdb->prefix,'_') + 1);
} else {
	$dpre =  $wpdb->prefix;
}
define('BWPS_TABLE_D404', $dpre . 'BWPS_d404');
define('BWPS_TABLE_LL', $dpre . 'BWPS_ll');
define('BWPS_TABLE_LOCKOUTS', $dpre . 'BWPS_lockouts');

/**
 * Adds the admin menu pages
 * @return null 
 */
function menu_items() {
	//Add main menu page
	add_menu_page(__('Better Security - System Status and Support', 'better-wp-security'), __('Security', 'better-wp-security'), 'manage_options', 'BWPS', 'status_options', plugin_dir_url(__FILE__) . 'images/padlock.png');
	
	//Add submenu pages
	add_submenu_page('BWPS','Better WP Security - ' . __('System Status and Support', 'better-wp-security'),'Better WP Security', 'manage_options', 'BWPS', 'status_options');
	add_submenu_page('BWPS','Better WP Security - ' . __('Admin User', 'better-wp-security'), __('Admin User', 'better-wp-security'), 'manage_options', 'BWPS-adminuser', 'admin_options');
	add_submenu_page('BWPS','Better WP Security - ' . __('Away Mode', 'better-wp-security'), __('Away Mode', 'better-wp-security'), 'manage_options', 'BWPS-away', 'away_options');
	add_submenu_page('BWPS','Better WP Security - ' . __('Ban IPs Options', 'better-wp-security'), __('Ban IPs', 'better-wp-security'), 'manage_options', 'BWPS-banips', 'banips_options');
	add_submenu_page('BWPS','Better WP Security - ' . __('Content Directory', 'better-wp-security'), __('Content Directory', 'better-wp-security'), 'manage_options', 'BWPS-content', 'content_options');
	add_submenu_page('BWPS','Better WP Security - ' . __('Database Prefix', 'better-wp-security'), __('Database Prefix', 'better-wp-security'), 'manage_options', 'BWPS-database', 'database_options');
	add_submenu_page('BWPS','Better WP Security - ' . __('Hide Backend Options', 'better-wp-security'), __('Hide Backend', 'better-wp-security'), 'manage_options', 'BWPS-hidebe', 'hidebe_options');
	add_submenu_page('BWPS','Better WP Security - ' . __('.htaccess Protection', 'better-wp-security'), __('.htaccess Protection', 'better-wp-security'), 'manage_options', 'BWPS-hta', 'hta_protection');
	add_submenu_page('BWPS','Better WP Security - ' . __('Intrusion Detection', 'better-wp-security'), __('Intrusion Detection', 'better-wp-security'), 'manage_options', 'BWPS-idetect', 'idetect_options');
	add_submenu_page('BWPS','Better WP Security - ' . __('Limit Logins Options', 'better-wp-security'), __('Limit Logins', 'better-wp-security'), 'manage_options', 'BWPS-ll', 'll_options');
	add_submenu_page('BWPS','Better WP Security - ' . __('System Tweaks', 'better-wp-security'), __('System Tweaks', 'better-wp-security'), 'manage_options', 'BWPS-tweaks', 'tweaks_options');
	add_submenu_page('BWPS','Better WP Security - ' . __('Clean Database', 'better-wp-security'), __('Clean Database', 'better-wp-security'), 'manage_options', 'BWPS-clean', 'clean_options');
}

/**
 * Define the change admin users options page
 * @return null 
 */
function admin_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/admin.php');
}

/**
 * Define the away mode options page
 * @return null 
 */
function away_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/away.php');
}

/**
 * Define the ban ip options page
 * @return null 
 */	
function banips_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/banips.php');
}

/**
 * Define the change content directory options page
 * @return null 
 */
function content_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/content.php');
}

/**
 * Define the detect 404 options page
 * @return null 
 */
function idetect_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/idetect.php');
}

/**
 * Define the change database prefix options page
 * @return null 
 */
function database_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/database.php');
}

/**
 * Define the hide backend options page
 * @return null 
 */
function hidebe_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/hidebe.php');
}

/**
 * Define the protect .htacces options page
 * @return null 
 */
function hta_protection() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/hta.php');
}

/**
 * Define the limit bad logins options page
 * @return null 
 */	
function ll_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/ll.php');
}

/**
 * Define the status page
 * @return null 
 */
function status_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/status.php');
}

/**
 * Define the system tweaks page
 * @return null 
 */		
function tweaks_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/tweaks.php');
}

/**
 * Define the clean database page
 * @return null 
 */		
function clean_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/clean.php');
}

//Register the admin menu
if (is_multisite()) { 
	add_action( 'network_admin_menu', 'menu_items' ); 
} else {
	add_action('admin_menu',  'menu_items');
}

/**
 * Defines the plugin action link to appear on the plugin menu
 *
 * @param link array
 * @param file array
 * @return link array
 */
function BWPS_plugin_action_links($links, $file) {
	static $this_plugin;
			
	if (!$this_plugin ) { //make sure plugin is active
		$this_plugin = plugin_basename(__FILE__);
	 }
	 
	if ($file == $this_plugin) { //if plugin is active add a link
		$settings_link = '<a href="/wp-admin/admin.php?page=BWPS">' . __('Settings', 'better-wp-security') . '</a>';
		array_unshift($links, $settings_link);
	}
	
	return $links;
}

//Add filter for options choice on plugin page
add_filter('plugin_action_links','BWPS_plugin_action_links', 10, 2 );

//register the install and uninstall routines
register_activation_hook(__file__, 'BWPS_install');
register_deactivation_hook(__file__, 'BWPS_uninstall');

//Register languages
$lang_dir = trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/languages/';
load_plugin_textdomain( 'better-wp-security', null, $lang_dir );

/**
 * Define BWPS globals
 *
 * @global object 
 */
global $BWPS;

//create BWPS object
$BWPS = new BWPS();