<?php
/**
 * Create BWPS object.
 *
 * @package BWPS
 */
 
 //Require files for related subclasses
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/auth.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/limitlogin.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/setup.php');
require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/tweaks.php');

class BWPS {

	/**
	 * Execute startup tasks
	 */
	function __construct() {
		global $wpdb;
		
		$opts = $this->getOptions();
		
		if ($opts['d404_enable'] == 1) { //if detect 404 mode is enabled
		
			$computer_id = $wpdb->escape($_SERVER['REMOTE_ADDR']);
			
			if ($this->d404_checkLock($computer_id)) { //if locked out
				die(__('Please come back later'));
			}
			
			add_action('wp_head', array(&$this,'d404_check')); //register action
		}
		unset($opts);
	}

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
			if (!strstr($opts['limitlogin_table_fails'],$wpdb->prefix)) {
				$opts['limitlogin_table_fails'] = $wpdb->prefix . $opts['limitlogin_table_fails'];
				$opts['limitlogin_table_lockouts'] = $wpdb->prefix . $opts['limitlogin_table_lockouts'];
				$opts['d404_table_attempts'] = $wpdb->prefix . $opts['d404_table_attempts'];
				$opts['d404_table_lockouts'] = $wpdb->prefix . $opts['d404_table_lockouts'];
			}
		}
		
		return $opts;
	}
		
	/**
 	 * Saves a new option to the database and returns an updated array of options
 	 * @return object 
 	 * @param String
 	 * @param String
 	 */
	function saveOptions($opt, $val) {
		global $wpdb;
		
		$opts = $this->getOptions(); 
			
		$opts[$opt] = $val;
		
		//get the string length of the database prefix
		$prelen = strlen($wpdb->prefix);
		
		//make sure to remove the database prefix from the table names before saving
		$opts['limitlogin_table_fails'] = substr($opts['limitlogin_table_fails'],$prelen,strlen($opts['limitlogin_table_fails']));
		$opts['limitlogin_table_lockouts'] = substr($opts['limitlogin_table_lockouts'],$prelen,strlen($opts['limitlogin_table_lockouts']));
		$opts['d404_table_attempts'] = substr($opts['d404_table_attempts'],$prelen,strlen($opts['d404_table_attempts']));
		$opts['d404_table_lockouts'] = substr($opts['d404_table_lockouts'],$prelen,strlen($opts['d404_table_lockouts']));
				
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
		if ($path{strlen($path)-1} == '/') { //if we have a dir with a trailing slash
			return BWPS_can_write($path.uniqid(mt_rand()).'.tmp');
		} elseif (is_dir($path)) { //now make sure we have a directory
			return BWPS_can_write($path.'/'.uniqid(mt_rand()).'.tmp');
		}

		$rm = file_exists($path);
		$f = @fopen($path, 'a');
	
		if ($f===false) { //if we can't open the file
			return false;
		}
	
		fclose($f);
	
		if (!$rm) { //make sure to delete any temp files
			unlink($path);
		}
	
		return true; //return true
	}

	/**
 	 * Remove a given section of code from .htaccess
 	 * @return Boolean 
 	 * @param String
 	 * @param String
 	 */
	function remove_section($filename, $marker) {
		if (!file_exists($filename) || $this->can_write($filename)) { //make sure the file is valid and writable
	
			$markerdata = explode("\n", implode( '', file( $filename))); //parse each line of file into array

			$f = fopen($filename, 'w'); //open the file
			
			if ($markerdata) { //as long as there are lines in the file
				$state = true;
				
				foreach ($markerdata as $n => $markerline) { //for each line in the file
				
					if (strpos($markerline, '# BEGIN ' . $marker) !== false) { //if we're at the beginning of the section
						$state = false;
					}
					
					if ($state == true) { //as long as we're not in the section keep writing
						if ($n + 1 < count($markerdata)) //make sure to add newline to appropriate lines
							fwrite($f, "{$markerline}\n");
						else
							fwrite($f, "{$markerline}");
					}
					
					if (strpos($markerline, '# END ' . $marker) !== false) { //see if we're at the end of the section
						$state = true;
					}
				}
			}
			return true;
		} else {
			return false; //return false if we can't write the file
		}
	}
	
	/**
	 * Check supsection versions and prompt user if update is needed.
	 */	
	function checkVersions() {
	
		$opts = $this->getOptions();
		
		/**
	 	 * Display warning message
	 	 */
		function BWPS_upgradeWarning() {
			$preMess = '<div id="message" class="error"><p>' . __('Due to changes in the latest Better WP Security release you must update your') . ' <strong>';
			$postMess = '</strong></p></div>';
			
			if ($opts['away_Version'] != BWPS_AWAY_VERSION && $opts['away_Version'] > 0 && !isset($_POST['BWPS_away_save'])) { //see if away section needs updating
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-away">' . __('Better WP Security - Away Mode Settings.') . '</a>' . $postMess;
			}
			if ($opts['banips_Version'] != BWPS_BANIPS_VERSION && $opts['banips_Version'] > 0 && !isset($_POST['BWPS_banips_save'])) { //see if banips section needs updating
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-banips">' . __('Better WP Security - Ban IPs Settings.') . '</a>' . $postMess;
			}
			if ($opts['tweaks_Version'] != BWPS_TWEAKS_VERSION && $opts['tweaks_Version'] > 0 && !isset($_POST['BWPS_tweaks_save'])) { //see if tweaks section needs updating
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-tweaks">' . __('Better WP Security - System Tweaks.') . '</a>' . $postMess;
			}
			if ($opts['hidebe_Version'] != BWPS_HIDEBE_VERSION && $opts['hidebe_Version'] > 0 && !isset($_POST['BWPS_hidebe_save'])) { //see if hidebe section needs updating
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-hidebe">' . __('Better WP Security - Hide Backend Settings.') . '</a>' . $postMess;
			}
			if ($opts['limitlogin_Version'] != BWPS_LIMITLOGIN_VERSION && $opts['limitlogin_Version'] > 0 && !isset($_POST['BWPS_limitlogin_save'])) { //see if limitlogin section needs updating
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-limitlogin">' . __('Better WP Security - Limit Login Settings.') . '</a>' . $postMess;
			}
			if ($opts['htaccess_Version'] != BWPS_HTACCESS_VERSION && $opts['htaccess_Version'] > 0 && !isset($_POST['BWPS_htaccess_save'])) { //see if htaccess section needs updating
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-htaccess">' . __('Better WP Security - .htaccess Options.') . '</a>' . $postMess;
			}
			if ($opts['d404_Version'] != BWPS_D404_VERSION && $opts['d404_Version'] > 0 && !isset($_POST['BWPS_d404_save'])) { //see if d404 section needs updating
				echo $preMess . '<a href="/wp-admin/admin.php?page=BWPS-d404">' . __('Better WP Security - Detect d404 Options.') . '</a>' . $postMess;
			}
		}
		
		add_action('admin_notices', 'BWPS_upgradeWarning'); //register wordpress action
		
		unset($opts);
	}
		
	/**
	 * Returns local time
	 * @return String
	 */
	function getLocalTime() {
		return strtotime(get_date_from_gmt(date('Y-m-d H:i:s',time())));
	}
	
	/**
	 * 
	 */
	function uDomain($address) {
		preg_match("/^(http:\/\/)?([^\/]+)/i", $address, $matches);
		$host = $matches[2];
		preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
		$newAddress =  "http://(.*)" . $matches[0] ;
		
		return $newAddress;
	}
	
	/**
	 * Display time remaining for given future time
	 * @return String
	 * @param integer
	 */
	function dispRem($expTime) {
		$currTime = time(); 
    		$timeDif = $expTime - $currTime;
		$dispTime = floor($timeDif / 60) . " minutes and " . ($timeDif % 60) . " seconds";
		return $dispTime;
	}
	
	/**
	 * Check if given mode is turned on
	 * @return Boolean
	 * @param String
	 */
	function isOn($mode) {
		$opts = $this->getOptions();
		if ($mode == 'away') {
			$flag =  $opts['away_enable'];
		}
		unset($opts);
		return $flag;
	}
	
	/**
	 * Check to see if time restrictions allow login
	 * @return Boolean
	 */
	function away_check() {
		$opts = $this->getOptions();
			
		if ($opts['away_enable'] == 1) { //if away mode is enabled continue
			
			$lTime = $this->getLocalTime(); //get local time
			
			if ($opts['away_mode'] == 1) { //see if its daily
				if (date('a',$lTime) == "pm" && date('g',$lTime) != "12") {
					$linc = 12;
				}elseif (date('a',$lTime) == "am" && date('g',$lTime) == "12") {
					$linc = -12;
				} else {
					$linc = 0;
				}
				
				$local = ((date('g',$lTime) + $linc) * 60) + date('i',$lTime);
			
				if (date('a',$opts['away_start']) == "pm" && date('g',$opts['away_start']) != "12") {
					$sinc = 12;
				}elseif (date('a',$opts['away_start']) == "am" && date('g',$opts['away_start']) == "12") {
					$sinc = -12;
				} else {
					$sinc = 0;
				}
				
				$start = ((date('g',$opts['away_start']) + $sinc) * 60) + date('i',$opts['away_start']);
				
				if (date('a',$opts['away_end']) == "pm" && date('g',$opts['away_end']) != "12") {
					$einc = 12;
				} elseif (date('a',$opts['away_end']) == "am" && date('g',$opts['away_end']) == "12") {
					$einc = -12;
				} else {
					$einc = 0;
				}
				
				$end = ((date('g',$opts['away_end']) + $einc) * 60) + date('i',$opts['away_end']);
				
				if ($start >= $end) { 
					if ($local >= $start || $local < $end) {
						unset($opts);
						return true;
					}
				} else {
					if ($local >= $start && $local < $end) {
						unset($opts);
						return true;
					}
				}
			} else {	
				if ($lTime >= $opts['away_start'] && $lTime <= $opts['away_end']) {
					unset($opts);
					return true;
				}
			}
		}
		unset($opts);
		return false;
	}
	
	/**
	 * Execute if is a 404 page
	 */
	function d404_check() {
		global $wpdb;
		
		if (is_404()) { //if we're on a 404 page
			$computer_id = $wpdb->escape($_SERVER['REMOTE_ADDR']);
			$this->d404_log($computer_id);
			if ($this->d404_countAttempts($computer_id) >= 20 && !$this->d404_checkLock($computer_id) && !is_user_logged_in()) { //if we've seen too many 404s from an anonymous user lock them out
				$this->d404_lockout($computer_id);
			}
		}
	}
	
	/**
	 * Log the 404
	 * @return Boolean
	 * @param String
	 */
	function d404_log($computer_id) {
		global $wpdb;
		
		$opts = $this->getOptions();
		
		$qstring = $wpdb->escape($_SERVER['REQUEST_URI']);
					
		$hackQuery = "INSERT INTO " . $opts['d404_table_attempts'] . " (computer_id, qstring, attempt_date)
			VALUES ('" . $computer_id . "', '" . $qstring . "', " . time() . ");";
			echo $hackQuery;
		unset($opts);		
		return $wpdb->query($hackQuery);
	}
	
	/**
	 * Count how many 404s from host in previous 5 minutes
	 * @return integer
	 * @param String
	 */
	function d404_countAttempts($computer_id) {
		global $wpdb;
		
		$opts = $this->getOptions();
			
		$reTime = 300;
				
		$count = $wpdb->get_var("SELECT COUNT(attempt_ID) FROM " . $opts['d404_table_attempts'] . "
			WHERE attempt_date +
			" . $reTime . " >'" . time() . "' AND
			computer_id = '" . $computer_id . "';"
		);
		
		unset($opts);
		
		return $count;
			
	}
	
	/**
	 * Lock out the host
	 * @param String
	 */
	function d404_lockOut($computer_id) {
		global $wpdb;
		
		$opts = $this->getOptions();
				
		$lHost = "INSERT INTO " . $opts['d404_table_lockouts'] . " (computer_id, lockout_date)
			VALUES ('" . $computer_id . "', " . time() . ")";
					
		$wpdb->query($lHost);			
		
		unset($opts);
			
	}
	
	/**
	 * Determine if host is locked out
	 * @return Boolean
	 * @param String
	 */
	function d404_checkLock($computer_id) {
		global $wpdb;
		
		$opts = $this->getOptions();
		
		$hostCheck = $wpdb->get_var("SELECT computer_id FROM " . $opts['d404_table_lockouts']  . 
			" WHERE lockout_date < " . (time() + 1800) . " AND computer_id = '" . $computer_id . "';");
		
		unset($opts);
		
		if ($hostCheck) { //if host is locked out
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * List locked out users
	 * @return array
	 */
	function d404_listLocked() {
		global $wpdb;
		
		$opts = $this->getOptions();
			

		$lockList = $wpdb->get_results("SELECT lockout_ID, lockout_date, computer_id FROM " . $opts['d404_table_lockouts']  . " WHERE lockout_date < " . (time() + 1800) . ";", ARRAY_A);
					
		unset($opts);
		return $lockList;
	}

	
}
