<?php
class BWPS_status extends BWPS {

	function getStatus() {
		$this->checkWPVersion();
		$this->checkAdminUser();
		$this->checkTablePre();
		$this->checkhtaccess();
		$this->checkLimitlogin();
		$this->checkAway();
		$this->checkhidebe();
		$this->checkStrongPass();
		$this->checkHead();
		$this->checkUpdates();
		$this->checklongurls();
		$this->checkranver();
		$this->check404();
		$this->checkSSL();
		$this->checkContentDir();
	}
	
	function checkContentDir() {
		echo "<p>\n";
		if (!strstr(WP_CONTENT_DIR,'wp-content') || !strstr(WP_CONTENT_URL,'wp-content')) {
			echo "<span style=\"color: green;\">You have renamed the wp-content directory of your site.</span>\n";
		} else {
			echo "<span style=\"color: red;\">You should rename the wp-content directory of your site. <a href=\"admin.php?page=BWPS-content\">Click here to do so</a>.</span>\n";
		}
		echo "</p>\n";
	}
	
	function checkSSL() {
		echo "<p>\n";
		if (FORCE_SSL_ADMIN == true && FORCE_SSL_LOGIN == true) {
			echo "<span style=\"color: green;\">You are requiring a secure connection for logins and the admin area.</span>\n";
		} else {
			echo "<span style=\"color: orange;\">You are not requiring a secure connection for longs or the admin area. <a href=\"admin.php?page=BWPS-tweaks\">Click here to fix this</a>.</span>\n";
		}
		echo "</p>\n";
	}
	
	function check404() {
		$opts = $this->getOptions();
			
		echo "<p>\n";
		if ($opts['d404_enable'] == 1) {
			echo "<span style=\"color: green;\">Your site is secured from attacks by XSS.</span>\n";
		} else {
			echo "<span style=\"color: red;\">Your site is still vulnerable to some XSS attacks. <a href=\"admin.php?page=BWPS-d404\">Click here to fix this</a>.</span>\n";
		}
		echo "</p>\n";
		
		unset($opts);
	}
	
	function checkranver() {
		$opts = $this->getOptions();
			
		echo "<p>\n";
		if ($opts['tweaks_randomVersion'] == 1) {
			echo "<span style=\"color: green;\">Version information is obscured to all non admin users.</span>\n";
		} else {
			echo "<span style=\"color: red;\">Users may still be able to get version information from various plugins and themes. <a href=\"admin.php?page=BWPS-tweaks\">Click here to fix this</a>.</span>\n";
		}
		echo "</p>\n";
		
		unset($opts);
	}
	
	function checklongurls() {
		$opts = $this->getOptions();
			
		echo "<p>\n";
		if ($opts['tweaks_longurls'] == 1) {
			echo "<span style=\"color: green;\">Your installation does not accept long URLs.</span>\n";
		} else {
			echo "<span style=\"color: red;\">Your installation accepts long (over 255 character) URLS. This can lead to vulnerabilities. <a href=\"admin.php?page=BWPS-tweaks\">Click here to fix this</a>.</span>\n";
		}
		echo "</p>\n";
		
		unset($opts);
	}
	
	function checkLogin() {
		$opts = $this->getOptions();
			
		echo "<p>\n";
		if ($opts['tweaks_removeLoginMessages'] == 1) {
			echo "<span style=\"color: green;\">No error messages are displayed on failed login.</span>\n";
		} else {
			echo "<span style=\"color: red;\">Error messages are displayed to users on failed login. <a href=\"admin.php?page=BWPS-tweaks\">Click here to remove them</a>.</span>\n";
		}
		echo "</p>\n";
		
		unset($opts);
	}
	
	function checkUpdates() {
		$opts = $this->getOptions();
		
		$hcount = intval($opts["tweaks_themeUpdates"]) + intval($opts["tweaks_pluginUpdates"]) + intval($opts["tweaks_coreUpdates"]);
	
		echo "<p>\n";
		if ($hcount == 3) {
			echo "<span style=\"color: green;\">Non-administrators cannot see available updates.</span>\n";
		} elseif ($hcount > 0) {
			echo "<span style=\"color: orange;\">Non-administrators can see some updates. <a href=\"admin.php?page=BWPS-tweaks\">Click here to fully fix it</a>.</span>\n";
		} else {
			echo "<span style=\"color: red;\">Non-administrators can see all updates. <a href=\"admin.php?page=BWPS-tweaks\">Click here to fix it</a>.</span>\n";
		}
		echo "</p>\n";
		
		unset($opts);
	}
	
	function checkHead() {
		$opts = $this->getOptions();
		
		$hcount = intval($opts["tweaks_removeGenerator"]) + intval($opts["tweaks_removersd"]) + intval($opts["tweaks_removewlm"]);
	
		echo "<p>\n";
		if ($hcount == 3) {
			echo "<span style=\"color: green;\">Your Wordpress header is revealing as little information as possible.</span>\n";
		} elseif ($hcount > 0) {
			echo "<span style=\"color: orange;\">Your Wordpress header is still revealing some information to users. <a href=\"admin.php?page=BWPS-tweaks\">Click here to fully fix it</a>.</span>\n";
		} else {
			echo "<span style=\"color: red;\">Your Wordpress header is showing too much information to users. <a href=\"admin.php?page=BWPS-tweaks\">Click here to fix it</a>.</span>\n";
		}
		echo "</p>\n";
		
		unset($opts);
	}
	
	function checkStrongPass() {
		$opts = $this->getOptions();
		
		$isOn = $opts['tweaks_strongpass'];
		$role = $opts['tweaks_strongpassrole']; 
	
		echo "<p>\n";
		if ($isOn == 1 && $role == 'subscriber') {
			echo "<span style=\"color: green;\">You are enforcing strong passwords for all users</span>\n";
		} elseif ($isOn == 1) {
			echo "<span style=\"color: orange;\">You are enforcing strong passwords, but not for all users. <a href=\"admin.php?page=BWPS-tweaks\">Click here to fix</a>.</span>\n";
		} else {
			echo "<span style=\"color: red;\">You are not enforcing strong passwords. <a href=\"admin.php?page=BWPS-tweaks\">Click here to enforce strong passwords.</a>.</span>\n";
		}
		echo "</p>\n";
		
		unset($opts);
	}
	
	function checkhidebe() {
		$opts = $this->getOptions();
			
		echo "<p>\n";
		if ($opts['hidebe_enable'] == 1) {
			echo "<span style=\"color: green;\">Your Wordpress admin area is hidden.</span>\n";
		} else {
			echo "<span style=\"color: red;\">Your Wordpress admin area  file is NOT hidden. <a href=\"admin.php?page=BWPS-hidebe\">Click here to secure it</a>.</span>\n";
		}
		echo "</p>\n";
		
		unset($opts);
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
		global $wpdb;
		
		echo "<p>\n";

		if ($table_prefix == 'wp_') {
			echo "<span style=\"color: red;\">Your table prefix should not be <em>wp_</em>.  <a href=\"admin.php?page=BWPS-database\">Click here to change it</a>.</span>\n";
		}else{
			echo "<span style=\"color: green;\">Your table prefix is <em>" . $wpdb->prefix . "</em>.</span>\n";
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