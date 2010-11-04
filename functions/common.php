<?php
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/auth.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/setup.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/away.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/limitlogin.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/tweaks.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/hacker.php');


class BWPS {

	function getOptions() {
		if (!get_option("BWPS_options")) {
			$opts = BWPS_defaults();
			update_option("BWPS_options", serialize($opts));
		} else {
			$opts = unserialize(get_option("BWPS_options"));
		}
		
		return $opts;
	}
		
	function saveOptions($opt, $val) {
		global $wpdb;
		
		$opts = $this->getOptions(); 
			
		$opts[$opt] = $val;
				
		delete_option("BWPS_options");
		update_option("BWPS_options", serialize($opts));
			
		return $this->getOptions();;
	}

	function can_write($path) {		 
		if ($path{strlen($path)-1} == '/') {
			return BWPS_can_write($path.uniqid(mt_rand()).'.tmp');
		} elseif (is_dir($path)) {
			return BWPS_can_write($path.'/'.uniqid(mt_rand()).'.tmp');
		}

		$rm = file_exists($path);
		$f = @fopen($path, 'a');
	
		if ($f===false) {
			return false;
		}
	
		fclose($f);
	
		if (!$rm) {
			unlink($path);
		}
	
		return true;
	}

	function remove_section($filename, $marker) {
		if (!file_exists($filename) || $this->can_write($filename)) {
			if (!file_exists($filename)) {
				return '';
			} else {
				$markerdata = explode("\n", implode( '', file( $filename)));
			}

			$f = fopen($filename, 'w');
			$foundit = false;
			if ($markerdata) {
				$state = true;
				foreach ($markerdata as $n => $markerline) {
					if (strpos($markerline, '# BEGIN ' . $marker) !== false)
						$state = false;
					if ($state) {
						if ($n + 1 < count($markerdata))
							fwrite($f, "{$markerline}\n");
						else
							fwrite($f, "{$markerline}");
					}
					if (strpos($markerline, '# END ' . $marker) !== false) {
						$state = true;
					}
				}
			}
			return true;
		} else {
			return false;
		}
	}
		
	function checkVersions() {
	
		$opts = $this->getOptions();
		
		if ($opts['away_Version'] != BWPS_AWAY_VERSION && $opts['away_Version'] > 0 && !isset($_POST['BWPS_away_save'])) {
			function BWPS_awayWarning() {
				echo '<div id="message" class="error"><p>Due to changes in the latest Better WP Security release you must update your <strong><a href="/wp-admin/admin.php?page=BWPS-away">Better WP Security - Away Mode Settings.</a></strong></p></div>';
			}
			add_action('admin_notices', 'BWPS_awayWarning');
		}
			
		if ($opts['banips_Version'] != BWPS_BANIPS_VERSION && $opts['banips_Version'] > 0 && !isset($_POST['BWPS_banips_save'])) {
			function BWPS_banipsWarning() {
				echo '<div id="message" class="error"><p>Due to changes in the latest Better WP Security release  you must update your <strong><a href="/wp-admin/admin.php?page=BWPS-banips">Better WP Security - Ban IPs Settings.</a></strong></p></div>';
			}
			add_action('admin_notices', 'BWPS_banipsWarning');
		}
		
		if ($opts['tweaks_Version'] != BWPS_TWEAKS_VERSION && $opts['tweaks_Version'] > 0 && !isset($_POST['BWPS_tweaks_save'])) {
			function BWPS_tweaksWarning() {
				echo '<div id="message" class="error"><p>Due to changes in the latest Better WP Security release  you must update your <strong><a href="/wp-admin/admin.php?page=BWPS-tweaks">Better WP Security - System Tweaks.</a></strong></p></div>';
			}
			add_action('admin_notices', 'BWPS_tweaksWarning');
		}
			
		if ($opts['hidebe_Version'] != BWPS_HIDEBE_VERSION && $opts['hidebe_Version'] > 0 && !isset($_POST['BWPS_hidebe_save'])) {
			function BWPS_hidebeWarning() {
				echo '<div id="message" class="error"><p>Due to changes in the latest Better WP Security release  you must update your <strong><a href="/wp-admin/admin.php?page=BWPS-hidebe">Better WP Security - Hide Backend Settings.</a></strong></p></div>';
			}
			add_action('admin_notices', 'BWPS_hidebeWarning');
		}
			
		if ($opts['limitlogin_Version'] != BWPS_LIMITLOGIN_VERSION && $opts['limitlogin_Version'] > 0 && !isset($_POST['BWPS_limitlogin_save'])) {
			function BWPS_limitloginWarning() {
				echo '<div id="message" class="error"><p>Due to changes in the latest Better WP Security release  you must update your <strong><a href="/wp-admin/admin.php?page=BWPS-limitlogin">Better WP Security - Limit Login Settings.</a></strong></p></div>';
			}
			add_action('admin_notices', 'BWPS_limitloginWarning');
		}
		
		if ($opts['htaccess_Version'] != BWPS_HTACCESS_VERSION && $opts['htaccess_Version'] > 0 && !isset($_POST['BWPS_htaccess_save'])) {
			function BWPS_htaccessWarning() {
				echo '<div id="message" class="error"><p>Due to changes in the latest Better WP Security release  you must update your <strong><a href="/wp-admin/admin.php?page=BWPS-htaccess">Better WP Security - .htaccess Options.</a></strong></p></div>';
			}
			add_action('admin_notices', 'BWPS_htaccessWarning');
		}
		
		unset($opts);
	}
		
	function getLocalTime() {
		return strtotime(get_date_from_gmt(date('Y-m-d H:i:s',time())));
	}
	
	function uDomain($address) {
		preg_match("/^(http:\/\/)?([^\/]+)/i", $address, $matches);
		$host = $matches[2];
		preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
		$newAddress =  "http://(.*)" . $matches[0] ;
		
		return $newAddress;
	}
}
