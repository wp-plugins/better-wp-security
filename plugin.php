<?php
/**
 * @package BWPS
 */
/*
Plugin Name: Better WP Security
Plugin URI: http://www.chriswiegman.com/projects/wordpress/better-wp-security/
Description: A collection of numerous security fixes and modifications to help protect a standard wordpress installation.
Version: ALPHA 7
Author: ChrisWiegman
Author URI: http://www.chriswiegman.com
License: GPLv2
*/
/*	Copyright 2010  ChrisWiegman  (email : chris@chriswiegman.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Fou
    ndation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
require_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/functions/common.php');

define('BWPS_VERSION','ALPHA7');
define('BWPS_AWAY_VERSION','1');
define('BWPS_BANIPS_VERSION','1');
define('BWPS_GENERAL_VERSION','1');
define('BWPS_HIDEBE_VERSION','1');
define('BWPS_LIMITLOGIN_TABLE_ATTEMPTS_VERSION','1');
define('BWPS_LIMITLOGIN_TABLE_LOCKOUTS_VERSION','1');
define('BWPS_LIMITLOGIN_VERSION','1');
 
global $BWPS_away, $BWPS_limitlogin, $BWPS_general;

$BWPS_general = new BWPS_general();
$BWPS_away = new BWPS_away();
$BWPS_limitlogin = new BWPS_limitlogin();

register_activation_hook(__file__, 'BWPS_install');
register_deactivation_hook(__file__, 'BWPS_uninstall');

function status_options() {
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/pages/status.php');
}
		
function general_options() {
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/pages/general.php');
}
		
function hidebe_options() {
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/pages/hidebe.php');
}
		
function limitlogin_options() {
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/pages/limitlogin.php');
}
	
function banips_options() {
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/pages/banips.php');
}
		
function away_options() {
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/pages/away.php');
}
	
function optsmenu() {
	add_menu_page('Better Security - System Status and Support', 'Better WP Security', 'manage_options', 'BWPS', 'status_options');
	add_submenu_page('BWPS', 'Better WP Security - System Status and Support', 	'Better WP Security', 'manage_options', 'BWPS', 'status_options');
	add_submenu_page('BWPS', 'Better WP Security - Away Mode', 	'Away Mode', 'manage_options', 'BWPS-away', 'away_options');
	add_submenu_page('BWPS', 'Better WP Security - Ban IPs Options', 	'Ban IPs', 'manage_options', 'BWPS-banips', 'banips_options');
	add_submenu_page('BWPS', 'Better WP Security - Hide Backend Options', 	'Hide Backend', 'manage_options', 'BWPS-hidebe', 'hidebe_options');
	add_submenu_page('BWPS', 'Better WP Security - General Fetures', 	'General Fetures', 'manage_options', 'BWPS-general', 'general_options');
	add_submenu_page('BWPS', 'Better WP Security - Limit Logins Options', 	'Limit Logins', 'manage_options', 'BWPS-limitlogin', 'limitlogin_options');
}

add_action('admin_menu',  'optsmenu');