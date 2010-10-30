<?php
/*
Plugin Name: Better WP Security
Plugin URI: http://www.chriswiegman.com/projects/wordpress/better-wp-security/
Description: A collection of numerous security fixes and modifications to help protect a standard wordpress installation.
Version: ALPHA 4
Author: ChrisWiegman
Author URI: http://www.chriswiegman.com
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/vars.php');
include_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/functions/common.php');

/*
 * Install the database tables
 */
function BWPS_install() {
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/vars.php');
	
	global $wpdb;
	
	$BWPS_limitlogin_installed_ver = get_option("BWPS_savedVersion");
	$BWPS_limitlogin_upgrade_tables = ($BWPS_currentVersion != $BWPS_limitlogin_installed_ver);
			
	$tableSql = "";
			
	// Check tables exist
	$table_hostfailstable_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $BWPS_limitlogin_table_hostfailstable . "'") == $BWPS_limitlogin_table_hostfailstable);
	$table_userfailstable_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $BWPS_limitlogin_table_userfailstable . "'") == $BWPS_limitlogin_table_userfailstable);
	$table_lockouthosttable_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $BWPS_limitlogin_table_lockouthosttable . "'") == $BWPS_limitlogin_table_lockouthosttable);
	$table_lockoutusertable_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $BWPS_limitlogin_table_lockoutusertable . "'") == $BWPS_limitlogin_table_lockoutusertable);
			
	// Create host attempts table
	if (!$table_hostfailstable_exists || $BWPS_limitlogin_upgrade_tables) {
		$tableSql .= "CREATE TABLE " . $BWPS_limitlogin_table_hostfailstable . " (
			`attempt_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`attempt_date` int(10) NULL,
			`computer_id` varchar(20) NOT NULL default '',
			PRIMARY KEY  (`attempt_id`)
			);";		
	}
			
	// Create user attempts table
	if (!$table_userfailstable_exists || $BWPS_limitlogin_upgrade_tables) {
		$tableSql .= "CREATE TABLE " . $BWPS_limitlogin_table_userfailstable . " (
			`attempt_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`attempt_date` int(10) NULL,
			PRIMARY KEY  (`attempt_id`)
			);";	
	}
			
	if (!$table_lockouthosttable_exists || $BWPS_limitlogin_upgrade_tables) {
		$tableSql .= "CREATE TABLE " . $BWPS_limitlogin_table_lockouthosttable . " (
			`lockout_ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`computer_id` varchar(20) NOT NULL default '',
			`lockout_date` int(10) NULL,
			`release_date` int(10) NULL,
			PRIMARY KEY  (`lockout_ID`)
			);";
	}
			
	if (!$table_lockoutusertable_exists || $BWPS_limitlogin_upgrade_tables) {
		$tableSql .= "CREATE TABLE " . $BWPS_limitlogin_table_lockoutusertable . " (
			`lockout_ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`lockout_date` int(10) NULL,
			`release_date` int(10) NULL,
			PRIMARY KEY  (`lockout_ID`)
			);";
	}
			
	if ($tableSql != "") {
		//require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		//dbDelta($tableSql);	
	}		
	
	update_option("BWPS_savedVersion", $BWPS_currentVersion);
}


register_activation_hook(__FILE__,'BWPS_install');

/*
 *Check to make sure they have upgraded correctly
 */
if (get_option("BWPS_savedVersion") != $BWPS_currentVersion && !isset($_POST['BWPS_save'])) {
	function BWPS_upgradeWarning() {
		echo '<div id="message" class="error"><p>You must update your Better WP Security Rules. Please Check you Better WP security options and press Save</p></div>';
	}
	add_action('admin_notices', 'BWPS_upgradeWarning');
}

/*
 * Creates a menu item for the options page
 */
function BWPS_menu() {
	add_menu_page('Better WP Security Options', 'Better WP Security', 'manage_options',__FILE__,'BWPS_options');
}

/*
 * Presents the options page
 */
function BWPS_options () {
	include_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/options.php');
}

/*
 * Add action to present options page
 */
add_action('admin_menu', 'BWPS_menu');

/*
 * Cleanup for uninstall
 */
function BWPS_uninstall() {
	
	//first delete all options
	delete_option("BWPS_hideadmin_enable");
	delete_option("BWPS_banips_enable");
	delete_option("BWPS_hideadmin_canregister");
	
	//remove any .htaccess rules and notify if there are problems
	$htaccess = trailingslashit(ABSPATH).'.htaccess'; //get htaccess info
		
	if (!BWPS_can_write($htaccess)) { //verify the .htaccess file is writeable
		echo "Unable to update htaccess rules";
	} else {
		BWPS_remove_section($htaccess, 'Better WP Security Hide Admin');
		BWPS_remove_section($htaccess, 'Better WP Security Ban IPs');
	}
}

/*
 * Add uninstall hook
 */
register_deactivation_hook( __FILE__, 'BWPS_uninstall' );

/*
 * Execute general functions
 */
include_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/functions/general.php');

/*
 * Execute limit login
 */
if (get_option("BWPS_limilogin_enable") == 1) {
	include_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/functions/limitlogin.php');
	
}
 