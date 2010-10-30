<?php
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
 * Display a random version # to all non-admins
 */
if (get_option("BWPS_randomVersion")) {
	BWPS_randomVersion();
}

/*
 * Display a random version # to all non-admins
 */
function BWPS_randomVersion() {
	global $wp_version, $ver;

	$newVersion = rand(100,500);

	if (!is_admin()) {
		$wp_version = $newVersion;
	}
}