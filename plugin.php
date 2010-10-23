<?php
/*
Plugin Name: Better WP Security
Plugin URI: http://www.chriswiegman.com/projects/wordpress/better-wp-security/
Description: 
Version: 0.1
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

if (get_option("BWPS_removeGenerator")) {
	 remove_action('wp_head', 'wp_generator'); //remove generator tag
}

if (get_option("BWPS_removeLoginMessages")) {
	add_filter('login_errors', create_function('$a', "return null;")); //hide login errors
}

add_action('admin_menu', 'BWPS_menu');

function BWPS_menu() {
	add_menu_page('Better WP Security Options', 'Better WP Security', 'manage_options',__FILE__,'BWPS_options');
}

function BWPS_options () {
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/options.php');
}