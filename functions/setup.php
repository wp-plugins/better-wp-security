<?php

function BWPS_install() {
	global $wpdb;
	
	if (get_option("BWPS_versions")) {
		$vers = unserialize(get_option("BWPS_versions"));
	} else {
		$vers = BWPS_versions();
	}
	
	$BWPSinstall = "CREATE TABLE " . BWPS_TABLE_D404 . " (
		`attempt_id` bigint(20) NOT NULL AUTO_INCREMENT,
		`computer_id` varchar(20),
		`attempt_date` int(10),
		`qstring` varchar(255),
		PRIMARY KEY  (`attempt_id`)
		);";
		$vers['TABLE_D404'] = BWPS_VERSION_TABLE_D404;

	$BWPSinstall .= "CREATE TABLE " . BWPS_TABLE_LL . " (
		`attempt_id` bigint(20) NOT NULL AUTO_INCREMENT ,
		`attempt_date` int(10),
		`user_id` bigint(20),
		`computer_id` varchar(20),
		PRIMARY KEY  (`attempt_id`)
		);";		
		$vers['TABLE_LL'] = BWPS_VERSION_TABLE_LL;

	$BWPSinstall .= "CREATE TABLE " . BWPS_TABLE_LOCKOUTS . " (
		`lockout_id` bigint(20) NOT NULL AUTO_INCREMENT,
		`computer_id` varchar(20),
		`user_id` varchar(20),
		`lockout_date` int(10),
		`mode` int(5),
		PRIMARY KEY  (`lockout_id`)
		);";
		$vers['TABLE_LOCKOUTS'] = BWPS_VERSION_TABLE_LOCKOUTS;
			
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($BWPSinstall);
	
	delete_option("BWPS_versions");
	update_option("BWPS_versions", serialize($vers));
	unset($vers);
	
	$BWPS = new BWPS();
	
	$htaccess = trailingslashit(ABSPATH).'.htaccess';
	
	if (!$BWPS->can_write($htaccess)) {
		echo "Unable to update htaccess rules";
	} else {
	
		$BWPSohide = get_option('BWPS_oldRules1');
		$BWPSoht = get_option('BWPS_oldRules2');
		
		$wprules = implode("\n", extract_from_markers($htaccess, 'WordPress' ));
				
		$BWPS->remove_section($htaccess, 'WordPress');
		
		insert_with_markers($htaccess,'Better WP Security Protect htaccess', explode( "\n", $BWPSoht));
		insert_with_markers($htaccess,'Better WP Security Hide Backend', explode( "\n", $BWPSohide));
		insert_with_markers($htaccess,'WordPress', explode( "\n", $wprules));
		
		delete_option('BWPS_oldRules1');
		delete_option('BWPS_oldRules2');
	}
}
	
function BWPS_uninstall() {
	global $wpdb;
	
	$BWPS = new BWPS();

	$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
	if (!$BWPS->can_write($htaccess)) {
		echo "Unable to update htaccess rules";
	} else {
	
		$BWPSohide = implode("\n", extract_from_markers($htaccess, 'Better WP Security Hide Backend' ));
		$BWPSoht = implode("\n", extract_from_markers($htaccess, 'Better WP Security Protect htaccess' ));
		
		update_option("BWPS_oldRules1", $BWPSohide);
		update_option("BWPS_oldRules2", $BWPSoht);
		
		$BWPS->remove_section($htaccess, 'Better WP Security Hide Admin');
		$BWPS->remove_section($htaccess, 'Better WP Security Ban IPs');
		$BWPS->remove_section($htaccess, 'Better WP Security Protect htaccess');
		$BWPS->remove_section($htaccess, 'Better WP Security Protect wp-config');
		$BWPS->remove_section($htaccess, 'Better WP Security Prevent Directory Browsing');
		$BWPS->remove_section($htaccess, 'Better WP Security Prevent Hotlinking');
		$BWPS->remove_section($htaccess, 'Better WP Security Filter Request Methods');
		$BWPS->remove_section($htaccess, 'Better WP Security Filter Query String Exploits');
		
		$BWPS->remove_section($htaccess, 'Better WP Security Hide Backend');
		
		$del_d404 = 'DROP TABLE '. BWPS_TABLE_D404 . ';';
		$del_ll = 'DROP TABLE '. BWPS_TABLE_LL . ';';
		$del_lockouts = 'DROP TABLE '. BWPS_TABLE_LOCKOUTS . ';';
		$wpdb->query($del_d404);
		$wpdb->query($del_ll);
		$wpdb->query($del_lockouts);
	}
}

function BWPS_versions() {
	$vers = array(
		'TABLE_D404' => '0',
		'TABLE_LL' => '0',
		'TABLE_LOCKOUTS' => '0',
		'AWAY' => '0',
		'BANIPS' => '0',
		'TWEAKS' => '0',
		'HIDEBE' => '0',
		'LL' => '0',
		'HTACCESS' => '0',
		'D404' => '0'
	);
	
	return $vers;
}

function BWPS_defaults() {
	$opts = array(
		"away_enable" => "0",
		"away_mode" => "0",
		"away_start" => "1",
		"away_end" => "1",
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
		"ll_enable" => "0",
		"ll_maxattemptshost" => "5",
		"ll_maxattemptsuser" => "10",
		"ll_checkinterval" => "5",
		"ll_banperiod" => "60",
		"ll_denyaccess" => "1",
		"ll_emailnotify" => "1",
		"banips_enable" => "0",
		"banips_iplist" => "",
		"d404_enable" => "0"
	);
	
	return $opts;
}