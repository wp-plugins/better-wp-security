<?php
/**
 * @package BWPS
 */
 
/*
	Plugin Name: Better WP Security
	Plugin URI: http://www.chriswiegman.com/projects/wordpress/better-wp-security/
	Description: A collection of numerous security fixes and modifications to help protect a standard wordpress installation.
	Version: 0.4.BETA
	Author: ChrisWiegman
	Author URI: http://www.chriswiegman.com
	License: GPLv2
	Copyright 2010  ChrisWiegman  (email : chris@chriswiegman.com)

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
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/common.php');

//Define section versions
define('BWPS_AWAY_VERSION','1');
define('BWPS_BANIPS_VERSION','1');
define('BWPS_D404_VERSION','2');
define('BWPS_HIDEBE_VERSION','3');
define('BWPS_HTACCESS_VERSION','3');
define('BWPS_LIMITLOGIN_VERSION','1');
define('BWPS_TWEAKS_VERSION','10');

//Defing table versions
define('BWPS_D404_TABLE_ATTEMPTS_VERSION','2');
define('BWPS_D404_TABLE_LOCKOUTS_VERSION','2');
define('BWPS_LIMITLOGIN_TABLE_ATTEMPTS_VERSION','1');
define('BWPS_LIMITLOGIN_TABLE_LOCKOUTS_VERSION','1');

/**
 * Adds the admin menu pages
 * @return null 
 */
function menu_items() {
	add_menu_page('Better Security - System Status and Support', 'Security', 'manage_options', 'BWPS', 'status_options');
	add_submenu_page('BWPS', 'Better WP Security - System Status and Support', 'Better WP Security', 'manage_options', 'BWPS', 'status_options');
	add_submenu_page('BWPS', 'Better WP Security - Admin User', 'Admin User', 'manage_options', 'BWPS-adminuser', 'admin_options');
	add_submenu_page('BWPS', 'Better WP Security - Away Mode', 	'Away Mode', 'manage_options', 'BWPS-away', 'away_options');
	add_submenu_page('BWPS', 'Better WP Security - Ban IPs Options', 	'Ban IPs', 'manage_options', 'BWPS-banips', 'banips_options');
	add_submenu_page('BWPS', 'Better WP Security - Block 404s', 	'Block 404s', 'manage_options', 'BWPS-404', 'd404_options');
	add_submenu_page('BWPS', 'Better WP Security - Content Directory', 'Content Directory', 'manage_options', 'BWPS-content', 'content_options');
	add_submenu_page('BWPS', 'Better WP Security - Database Prefix', 	'Database Prefix', 'manage_options', 'BWPS-database', 'database_options');
	add_submenu_page('BWPS', 'Better WP Security - Hide Backend Options', 	'Hide Backend', 'manage_options', 'BWPS-hidebe', 'hidebe_options');
	add_submenu_page('BWPS', 'Better WP Security - .htaccess Options', '.htaccess Options', 'manage_options', 'BWPS-htaccess', 'htaccess_options');
	add_submenu_page('BWPS', 'Better WP Security - Limit Logins Options', 'Limit Logins', 'manage_options', 'BWPS-limitlogin', 'limitlogin_options');
	add_submenu_page('BWPS', 'Better WP Security - System Tweaks', 'System Tweaks', 'manage_options', 'BWPS-tweaks', 'tweaks_options');
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
function d404_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/d404.php');
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
function htaccess_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/htaccess.php');
}

/**
 * Define the limit bad logins options page
 * @return null 
 */	
function limitlogin_options() {
	include(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/limitlogin.php');
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

//Register the admin menu
add_action('admin_menu',  'menu_items');

//register the install and uninstall routines
register_activation_hook(__file__, 'BWPS_install');
register_deactivation_hook(__file__, 'BWPS_uninstall');

/**
 * Define BWPS global options
 *
 * @global object 
 * @global object 
 * @global object 
 * @global object 
 */
global $BWPS_away, $BWPS_d404, $BWPS_limitlogin, $BWPS_tweaks;

//create BWPS objects
$BWPS_away = new BWPS_away();
$BWPS_d404 = new BWPS_d404();
$BWPS_limitlogin = new BWPS_limitlogin();
$BWPS_tweaks = new BWPS_tweaks();

//if the user is an admin check BWPS versions
if (is_admin()) {
	$BWPS_tweaks->checkVersions();
}