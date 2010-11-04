<?php
class BWPS_status extends BWPS {

	function getStatus() {
		$this->checkWPVersion();
		$this->checkAdminUser();
		$this->checkTablePre();
		$this->checkhtaccess();
		$this->checkLimitlogin();
		$this->checkAway();
	}
	
	function checkWPVersion() {
		global $wp_version;
		
		$currVersion = "3.0.1";
		
		echo "<p>\n";
		
		if (!is_numeric(intval($wp_version))) {
			echo "<span style=\"color: orange;\">Your WordPress version: <strong><em>" . $wp_version . "</em></strong> Your using a non-stable version of Wordpress. Switch to a stable version to avoid potential security issues.</span>\n";
		} else {
			if ($wp_version >= $currVersion) {
				echo "<span style=\"color: green;\">Your WordPress version: <strong><em>" . $wp_version . "</em></strong> Your Wordpress version is stable and current.</span>\n";
			} else {
				echo "<span style=\"color: red;\">Your WordPress version: <strong><em>" . $wp_version . "</em></strong> You need version " . $currVersion . ".  You should <a href=\"http://wordpress.org/download/\">upgrade</a> immediately.</span>\n";
			}
		}
		echo "</p>\n";
	}
	
	function checkTablePre(){
		global $table_prefix;

		echo "<p>\n";

		if ($table_prefix == 'wp_') {
			echo "<span style=\"color: red;\">Your table prefix should not be <em>wp_</em>.  <a href=\"admin.php?page=BWPS-database\">Click here to change it</a>.</span>\n";
		}else{
			echo "<span style=\"color: green;\">Your table prefix is <em>" . $table_prefix . "</em>.</span>\n";
		}

		echo "</p>\n";
	}
	
	function checkAdminUser() {
		global $wpdb;

		$adminUser = $wpdb->get_var("SELECT user_login FROM " . $wpdb->users . " WHERE user_login='admin'");
		
		echo "<p>\n";
		
		if ($adminUser =="admin") {
			echo "<span style=\"color: red;\">Tge <em>admin</em> user still exists.  <a href=\"admin.php?page=BWPS-adminuser\">Click here to rename it</a>.</span>\n";
		} else {
			echo "<span style=\"color: green;\">The <em>admin</em> user has been removed.</span>\n";
		}
		
		echo "</p>\n";
	}
	
	function checkhtaccess() {
	
		$opts = $this->getOptions();
		
		$htcount = intval($opts["htaccess_protectht"]) + intval($opts["htaccess_protectwpc"]) + intval($opts["htaccess_dirbrowse"]) + intval($opts["htaccess_hotlink"]) + intval($opts["htaccess_request"]) + intval($opts["htaccess_qstring"]) + intval($opts["htaccess_protectreadme"]) + intval($opts["htaccess_protectinstall"]);
	
		echo "<p>\n";
		if ($htcount == 8) {
			echo "<span style=\"color: green;\">Your .htaccess file is fully secured.</span>\n";
		} elseif ($htcount > 0) {
			echo "<span style=\"color: orange;\">Your .htaccess file is partially secured. <a href=\"admin.php?page=BWPS-htaccess\">Click here to fully secure it</a>.</span>\n";
		} else {
			echo "<span style=\"color: red;\">Your .htaccess file is NOT secured. <a href=\"admin.php?page=BWPS-htaccess\">Click here to secure it</a>.</span>\n";
		}
		echo "</p>\n";
		
		unset($opts);
	}
	
	function checkLimitlogin() {
	
		$opts = $this->getOptions();
	
		echo "<p>\n";
		if ($opts['limitlogin_enable'] == 1) {
			echo "<span style=\"color: green;\">Your site is not vulnerable to brute force attacks.</span>\n";
		} else {
			echo "<span style=\"color: red;\">Your site is vulnerable to brute force attacks. <a href=\"admin.php?page=BWPS-limitlogin\">Click here to secure it</a>.</span>\n";
		}
		echo "</p>\n";
		
		unset($opts);
	}
	
	function checkAway() {
	
		$opts = $this->getOptions();
	
		echo "<p>\n";
		
		if ($opts['away_enable'] == 1) {
			echo "<span style=\"color: green;\">Your Wordpress admin area is not available when you won't be needing it.</span>\n";
		} else {
			echo "<span style=\"color: orange;\">Your Wordpress admin area is available 24/7. Do you really update 24 hours a day? <a href=\"admin.php?page=BWPS-away\">Click here to limit admin availability</a>.</span>\n";
		}
		echo "</p>\n";
		
		unset($opts);
	}
}