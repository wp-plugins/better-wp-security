<?php
/**
 * Create BWPS object.
 *
 * @package BWPS
 */
 
 //Require files for related subclasses
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/auth.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/away.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/d404.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/limitlogin.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/setup.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/tweaks.php');

class BWPS {

	/**
 	 * Returns the array of BWPS options
 	 * @return object 
 	 */
	function getOptions() {
		global $wpdb;
		
		if (!get_option("BWPS_options")) { //if options are not in the database retreive default options
			$opts = BWPS_defaults();
			update_option("BWPS_options", serialize($opts));
		} else { //get options from database and add db prefix to tablenames
			$opts = unserialize(get_option("BWPS_options"));
			$opts['limitlogin_table_fails'] = $wpdb->prefix . $opts['limitlogin_table_fails'];
			$opts['limitlogin_table_lockouts'] = $wpdb->prefix . $opts['limitlogin_table_lockouts'];
			$opts['d404_table_attempts'] = $wpdb->prefix . $opts['d404_table_attempts'];
			$opts['d404_table_lockouts'] = $wpdb->prefix . $opts['d404_table_lockouts'];
		}
		
		return $opts;
	}
		
	/**
 	 * Saves a new option to the database and returns an updated array of options
 	 * @return object 
 	 */
	function saveOptions($opt, $val) {
		global $wpdb;
		
		$opts = $this->getOptions(); 
			
		$opts[$opt] = $val;
				
		delete_option("BWPS_options");
		update_option("BWPS_options", serialize($opts));
			
		return $this->getOptions();;
	}

	/**
 	 * Check $path and return whether it is writable
 	 * @return Boolean
 	 * @param String file 
 	 */
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
		
		function BWPS_upgradeWarning() {
			$preMess = '<div id="message" class="error"><p>' . __('Due to changes in the latest Better WP Security release you must update your') . ' <strong>';
			$postMess = '</strong></p></div>';
			
			if ($opts['away_Version'] != BWPS_AWAY_VERSION && $opts['away_Version'] > 0 && !isset($_POST['BWPS_away_save'])) {
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-away">' . _e('Better WP Security - Away Mode Settings.') . '</a>' . $postMess;
			}
			if ($opts['banips_Version'] != BWPS_BANIPS_VERSION && $opts['banips_Version'] > 0 && !isset($_POST['BWPS_banips_save'])) {
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-banips">' . _e('Better WP Security - Ban IPs Settings.') . '</a>' . $postMess;
			}
			if ($opts['tweaks_Version'] != BWPS_TWEAKS_VERSION && $opts['tweaks_Version'] > 0 && !isset($_POST['BWPS_tweaks_save'])) {
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-tweaks">' . _e('Better WP Security - System Tweaks.') . '</a>' . $postMess;
			}
			if ($opts['hidebe_Version'] != BWPS_HIDEBE_VERSION && $opts['hidebe_Version'] > 0 && !isset($_POST['BWPS_hidebe_save'])) {
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-hidebe">' . _e('Better WP Security - Hide Backend Settings.') . '</a>' . $postMess;
			}
			if ($opts['limitlogin_Version'] != BWPS_LIMITLOGIN_VERSION && $opts['limitlogin_Version'] > 0 && !isset($_POST['BWPS_limitlogin_save'])) {
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-limitlogin">' . _e('Better WP Security - Limit Login Settings.') . '</a>' . $postMess;
			}
			if ($opts['htaccess_Version'] != BWPS_HTACCESS_VERSION && $opts['htaccess_Version'] > 0 && !isset($_POST['BWPS_htaccess_save'])) {
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-htaccess">' . _e('Better WP Security - .htaccess Options.') . '</a>' . $postMess;
			}
			if ($opts['d404_Version'] != BWPS_D404_VERSION && $opts['d404_Version'] > 0 && !isset($_POST['BWPS_d404_save'])) {
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-d404">' . _e('Better WP Security - Detect d404 Options.') . '</a>' . $postMess;
			}
		}
		
		add_action('admin_notices', 'BWPS_upgradeWarning');
		
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
	
	function dispRem($expTime) {
		$currTime = time(); 
    		$timeDif = $expTime - $currTime;
		$dispTime = floor($timeDif / 60) . " minutes and " . ($timeDif % 60) . " seconds";
		return $dispTime;
	}
}
