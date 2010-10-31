<?php

function BWPS_install() {
	global $wpdb;

	$versions = BWPS_versions();
	$defaults = BWPS_defaults();
	
	$pi_version = $versions['pi_version'];
	
	if (get_option("BWPS_options")) {
		$opts = unserialize(get_option("BWPS_options"));
		delete_option("BWPS_options");
	}
	
	if (count($opts) != count($defaults)) {
		$opts = $defaults;
	}
	
	update_option("BWPS_options", serialize($opts));
	
	$upgrade_lt = ($versions['limitlogin_lt_Version'] != $opts['limitlogin_lt_Version']);
	$upgrade_at = ($versions['limitlogin_at_Version'] != $opts['limitlogin_at_Version']);
			
	$BWPSinstall = "";
			
	// Check tables exist
	$fails_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $opts['limitlogin_table_fails'] . "'") == $opts['limitlogin_table_fails']);
	$lockouts_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $opts['limitlogin_table_lockouts'] . "'") == $opts['limitlogin_table_lockouts']);
	
	// Create host attempts table
	if (!$fails_exists || $upgrade_lt) {
		$BWPSinstall .= "CREATE TABLE " . $opts['limitlogin_table_fails'] . " (
			`attempt_id` bigint(20) NOT NULL AUTO_INCREMENT ,
			`attempt_date` int(10),
			`user_id` bigint(20),
			`computer_id` varchar(20),
			PRIMARY KEY  (`attempt_id`)
			);";		
			$opts['limitlogin_lt_Version'] = $versions['limitlogin_lt_Version'];
	}
			
	if (!$lockouts_exists || $upgrade_at) {
		$BWPSinstall .= "CREATE TABLE " . $opts['limitlogin_table_lockouts'] . " (
			`lockout_ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`computer_id` varchar(20),
			`user_id` bigint(20),
			`lockout_date` int(10),
			PRIMARY KEY  (`lockout_ID`)
			);";
			$opts['limitlogin_at_Version'] = $versions['limitlogin_at_Version'];
	}
			
	if ($BWPSinstall != "") {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($BWPSinstall);
	}		
	
	$opts['pi_version'] = $pi_version;
	delete_option("BWPS_options");
	update_option("BWPS_options", serialize($opts));
}
	
function BWPS_uninstall() {
	global $BWPS;
	
	//first delete all options
	$BWPS->saveOptions("hidebe_enable", "0");
	$BWPS->saveOptions("banips_enable", "0");
	$BWPS->saveOptions("hidebe_canregister", "0");

	//remove any .htaccess rules and notify if there are problems
	$htaccess = trailingslashit(ABSPATH).'.htaccess'; //get htaccess info
		
	if (!$BWPS->can_write($htaccess)) { //verify the .htaccess file is writeable
		echo "Unable to update htaccess rules";
	} else {
		$BWPS->remove_section($htaccess, 'Better WP Security Hide Admin');
		$BWPS->remove_section($htaccess, 'Better WP Security Ban IPs');
		
		$BWPS->remove_section($htaccess, 'Better WP Security Hide Backend');
	}
}

function BWPS_defaults() {
	global $wpdb;
	
	$opts = array(
		"away_enable" => "0",
		"away_mode" => "0",
		"away_start" => "1",
		"away_end" => "1",
		"limitlogin_table_fails" => $wpdb->prefix . "BWPS_bad_logins",
		"limitlogin_table_lockouts" => $wpdb->prefix . "BWPS_lockouts",
		"general_removeGenerator" => "0",
		"general_removeLoginMessages" => "0",
		"general_randomVersion" => "0",
		"hidebe_enable" => "0",
		"hidebe_login_slug" => "login",
		"hidebe_login_redirect" => get_option('siteurl').'/wp-admin/',
		"hidebe_logout_slug" => "logout",
		"hidebe_admin_slug" => "admin",
		"hidebe_login_custom" => "",
		"hidebe_register_slug" => "register",
		"hidebe_canregister" => get_option('users_can_register'),
		"limitlogin_enable" => "0",
		"limitlogin_maxattemptshost" => "5",
		"limitlogin_maxattemptsuser" => "10",
		"limitlogin_checkinterval" => "5",
		"limitlogin_banperiod" => "60",
		"limitlogin_denyaccess" => "1",
		"limitlogin_emailnotify" => "1",
		"banips_enable" => "0",
		"banips_iplist" => "",
		"limitlogin_at_Version" => "0",
		"limitlogin_lt_Version" => "0",
		"away_Version" => "0",
		"banips_Version" => "0",
		"general_Version" => "0",
		"hidebe_Version" => "0",
		"limitlogin_Version" => "0"
	);
	
	return $opts;
}