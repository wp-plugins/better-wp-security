<?php
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/auth.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/setup.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/away.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/hidebe.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/limitlogin.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/general.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/banips.php');


class BWPS {

	private $opts;
		
	function __construct() {
		$opts = $this->getOptions();
			
		$this->checkVersions();
	}

	function getOptions() {
	
		if (!get_option("BWPS_options")) {
			$opts = BWPS_defaults();
			update_option("BWPS_options", serialize($opts));
		} else {
			$opts = unserialize(get_option("BWPS_options"));
		}
		
		$opts['currentVersion'] = BWPS_VERSION;
		return $opts;
	}
		
	function saveOptions($opt, $val) {
		global $wpdb;
		
		$opts = $this->getOptions(); 
			
		$opts[$opt] = $val;
				
		delete_option("BWPS_options");
		update_option("BWPS_options", serialize($opts));
			
		$opts = $this->getOptions();
			
		return $opts;
	}

	// from legolas558 d0t users dot sf dot net at http://www.php.net/is_writable
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
			
		if ($opts['general_Version'] != BWPS_GENERAL_VERSION && $opts['general_Version'] > 0 && !isset($_POST['BWPS_general_save'])) {
			function BWPS_generalWarning() {
				echo '<div id="message" class="error"><p>Due to changes in the latest Better WP Security release  you must update your <strong><a href="/wp-admin/admin.php?page=BWPS-general">Better WP Security - General Settings.</a></strong></p></div>';
			}
			add_action('admin_notices', 'BWPS_generalWarning');
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
	}
		
	function getLocalTime() {
		return strtotime(get_date_from_gmt(date('Y-m-d H:i:s',time())));
	}
}
