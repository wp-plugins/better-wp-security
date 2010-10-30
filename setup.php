<?php
function bwps_install() {
	global $wpdb;
	
	$currentVersion = bwps_defaultVersion();
	
	if (get_option("BWPS_options")) {
		$opts = unserialize(get_option("BWPS_options"));
		delete_option("BWPS_options");
	} else {
		$opts = bwps_defaults();
	}
	
	update_option("BWPS_options", serialize($opts));
	
	$upgrade_tables = ($currentVersion != $opts['savedVersion']);
			
	$bwpsinstall = "";
			
	// Check tables exist
	$fails_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $opts['limitlogin_table_fails'] . "'") == $opts['limitlogin_table_fails']);
	$lockouts_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $opts['limitlogin_table_lockouts'] . "'") == $opts['limitlogin_table_lockouts']);
			
	// Create host attempts table
	if (!$fails_exists || $upgrade_tables) {
		$bwpsinstall .= "CREATE TABLE " . $opts['limitlogin_table_fails'] . " (
			`attempt_id` bigint(20) NOT NULL AUTO_INCREMENT ,
			`attempt_date` int(10),
			`user_id` bigint(20),
			`computer_id` varchar(20),
			PRIMARY KEY  (`attempt_id`)
			);";		
	}
			
	if (!$lockouts_exists || $upgrade_tables) {
		$bwpsinstall .= "CREATE TABLE " . $opts['limitlogin_table_lockouts'] . " (
			`lockout_ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`computer_id` varchar(20),
			`user_id` bigint(20),
			`lockout_date` int(10),
			PRIMARY KEY  (`lockout_ID`)
			);";
	}
			
	if ($bwpsinstall != "") {
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($bwpsinstall);
	}		
	
	$opts['savedVersion'] = $currentVersion;
	delete_option("BWPS_options");
	update_option("BWPS_options", serialize($opts));
}
	
function bwps_uninstall() {
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
	}
}

function bwps_defaults() {
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
		"savedVersion" => ""
	);
	
	return $opts;
}