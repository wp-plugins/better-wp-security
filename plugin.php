<?php
/*
Plugin Name: Better WP Security
Plugin URI: http://www.chriswiegman.com/projects/wordpress/better-wp-security/
Description: A collection of numerous security fixes and modifications to help protect a standard wordpress installation.
Version: ALPHA 2
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


/*
 * Remove Wordpress Generator Meta Tag if checked
 */
if (get_option("BWPS_removeGenerator")) {
	 remove_action('wp_head', 'wp_generator'); //remove generator tag
}

/*
 * Remove error messages from login page
 */
if (get_option("BWPS_removeLoginMessages")) {
	add_filter('login_errors', create_function('$a', "return null;")); //hide login errors
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
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/options.php');
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
		
	if (!is_writeable_ACLSafe($htaccess)) { //verify the .htaccess file is writeable
		echo "Unable to update htaccess rules";
	} else {
		wpsc_remove_marker($htaccess, 'Better WP Security Hide Admin');
		wpsc_remove_marker($htaccess, 'Better WP Security Ban IPs');
	}
}

/*
 * Add uninstall hook
 */
register_deactivation_hook( __FILE__, 'BWPS_uninstall' );
