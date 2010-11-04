<?php

function BWPS_install() {
	global $wpdb;

	$defaults = BWPS_defaults();
	
	$pi_version = BWPS_VERSION;
	
	if (get_option("BWPS_options")) {
		$opts = unserialize(get_option("BWPS_options"));
		delete_option("BWPS_options");
	}
	
	if (count($opts) != count($defaults)) {
		$opts = $defaults;
	}
	
	update_option("BWPS_options", serialize($opts));
	
	$upgrade_lt = (BWPS_LIMITLOGIN_TABLE_LOCKOUTS_VERSION != $opts['limitlogin_lt_Version']);
	$upgrade_at = (BWPS_LIMITLOGIN_TABLE_ATTEMPTS_VERSION != $opts['limitlogin_at_Version']);
	$upgrade_da = (BWPS_D404_TABLE_ATTEMPTS_VERSION != $opts['d404_table_attempts_Version']);
	$upgrade_dl = (BWPS_D404_TABLE_LOCKOUTS_VERSION != $opts['d404_table_lockouts_Version']);
			
	$BWPSinstall = "";
			
	$fails_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $opts['limitlogin_table_fails'] . "'") == $opts['limitlogin_table_fails']);
	$lockouts_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $opts['limitlogin_table_lockouts'] . "'") == $opts['limitlogin_table_lockouts']);
	$d404_attempts_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $opts['d404_table_attempts'] . "'") == $opts['d404_table_attempts']);
	$d404_lockouts_exists = ($wpdb->get_var("SHOW TABLES LIKE '" . $opts['d404_table_lockouts'] . "'") == $opts['d404_table_lockouts']);
	
	if (!$fails_exists || $upgrade_lt) {
		$BWPSinstall .= "CREATE TABLE " . $opts['limitlogin_table_fails'] . " (
			`attempt_id` bigint(20) NOT NULL AUTO_INCREMENT ,
			`attempt_date` int(10),
			`user_id` bigint(20),
			`computer_id` varchar(20),
			PRIMARY KEY  (`attempt_id`)
			);";		
			$opts['limitlogin_lt_Version'] = BWPS_LIMITLOGIN_TABLE_LOCKOUTS_VERSION;
	}
			
	if (!$lockouts_exists || $upgrade_at) {
		$BWPSinstall .= "CREATE TABLE " . $opts['limitlogin_table_lockouts'] . " (
			`lockout_ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`computer_id` varchar(20),
			`user_id` bigint(20),
			`lockout_date` int(10),
			PRIMARY KEY  (`lockout_ID`)
			);";
			$opts['limitlogin_at_Version'] = BWPS_LIMITLOGIN_TABLE_ATTEMPTS_VERSION;
	}
	
	if (!$d404_attempts_exists || $upgrade_da) {
		$BWPSinstall .= "CREATE TABLE " . $opts['d404_table_attempts'] . " (
			`attempt_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`computer_id` varchar(20),
			`attempt_date` int(10),
			`qstring` varchar(255),
			PRIMARY KEY  (`attempt_id`)
			);";
			$opts['d404_table_attempts_Version'] = BWPS_D404_TABLE_ATTEMPTS_VERSION;
	}
	
	if (!$d404_lockouts_exists || $upgrade_dl) {
		$BWPSinstall .= "CREATE TABLE " . $opts['d404_table_lockouts'] . " (
			`lockout_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`computer_id` varchar(20),
			`lockout_date` int(10),
			PRIMARY KEY  (`lockout_id`)
			);";
			$opts['d404_table_lockouts_Version'] = BWPS_D404_TABLE_LOCKOUTS_VERSION;
	}
			
	if ($BWPSinstall != "") {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($BWPSinstall);
	}		
	
	$opts['pi_version'] = $pi_version;
	delete_option("BWPS_options");
	update_option("BWPS_options", serialize($opts));
	unset($opts);
}
	
function BWPS_uninstall() {
	
	$BWPS = new BWPS();
	
	$BWPS->saveOptions("hidebe_enable", "0");
	$BWPS->saveOptions("banips_enable", "0");
	$BWPS->saveOptions("hidebe_canregister", "0");
	$BWPS->saveOptions("htaccess_protectht", "0");
	$BWPS->saveOptions("htaccess_protectwpc", "0");
	$BWPS->saveOptions("htaccess_dirbrowse", "0");
	$BWPS->saveOptions("htaccess_hotlink", "0");
	$BWPS->saveOptions("htaccess_qstring", "0");
	$BWPS->saveOptions("htaccess_request", "0");

	$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
	if (!$BWPS->can_write($htaccess)) {
		echo "Unable to update htaccess rules";
	} else {
		$BWPS->remove_section($htaccess, 'Better WP Security Hide Admin');
		$BWPS->remove_section($htaccess, 'Better WP Security Ban IPs');
		$BWPS->remove_section($htaccess, 'Better WP Security Protect htaccess');
		$BWPS->remove_section($htaccess, 'Better WP Security Protect wp-config');
		$BWPS->remove_section($htaccess, 'Better WP Security Prevent Directory Browsing');
		$BWPS->remove_section($htaccess, 'Better WP Security Prevent Hotlinking');
		$BWPS->remove_section($htaccess, 'Better WP Security Filter Request Methods');
		$BWPS->remove_section($htaccess, 'Better WP Security Filter Query String Exploits');
		
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
		"tweaks_removeGenerator" => "0",
		"tweaks_removeLoginMessages" => "0",
		"tweaks_randomVersion" => "0",
		"tweaks_themeUpdates" => "0",
		"tweaks_pluginUpdates" => "0",
		"tweaks_coreUpdates" => "0",
		"tweaks_removersd" => "0",
		"tweaks_removewlm" => "0",
		"tweaks_longurls" => "0",
		"tweaks_strongpass" => "0",
		"tweaks_strongpassrole" => "administrator",
		"htaccess_protectht" => "0",
		"htaccess_protectwpc" => "0",
		"htaccess_dirbrowse" => "0",
		"htaccess_hotlink" => "0",
		"htaccess_request" => "0",
		"htaccess_qstring" => "0",
		"htaccess_protectreadme" => "0",
		"htaccess_protectinstall" => "0",
		"hidebe_enable" => "0",
		"hidebe_login_slug" => "login",
		"hidebe_admin_slug" => "admin",
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
		"d404_enable" => "0",
		"d404_table_attempts" => $wpdb->prefix . "BWPS_d404_attempts",
		"d404_table_attempts_Version" => "0",
		"d404_table_lockouts" => $wpdb->prefix . "BWPS_d404_lockouts",
		"d404_table_lockouts_Version" => "0",
		"limitlogin_at_Version" => "0",
		"limitlogin_lt_Version" => "0",
		"away_Version" => "0",
		"banips_Version" => "0",
		"tweaks_Version" => "0",
		"hidebe_Version" => "0",
		"limitlogin_Version" => "0",
		"htaccess_Version" => "0",
		"d404_Version" => "0"
	);
	
	return $opts;
}