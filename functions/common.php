<?php
include_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/setup.php');
include_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/functions/away.php');
include_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/functions/hidebe.php');
include_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/functions/limitlogin.php');
include_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/functions/general.php');
include_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/functions/banips.php');

function bwps_defaultVersion() {
	return 'alpha6';
}
	
if (!class_exists('BWPS')) {
	class BWPS {
	
		private $opts;
		
		function __construct() {
			global $opts;
			
			$this->checkDefaults();
			
			$opts = $this->getOptions();

			add_action('admin_menu', array(&$this, 'optsmenu'));
			
			new BWPS_general();
			
			global $limitLogin, $away;
			
			$away = new BWPS_away();
			
			$limitLogin = new BWPS_limitlogin();
			
			if ($opts['savedVersion'] != $opts['currentVersion'] && !isset($_POST['BWPS_save'])) {
				function BWPS_upgradeWarning() {
					echo '<div id="message" class="error"><p>You must update your Better WP Security Rules. Please Check you Better WP security options and press Save</p></div>';
				}
				add_action('admin_notices', 'BWPS_upgradeWarning');
			}
		}
		
		function checkDefaults() {
			if (!get_option("BWPS_options")) {
				$opts = bwps_defaults();
			}
		}

		function getOptions() {
			$opts = unserialize(get_option("BWPS_options"));
			
			$currentVersion = bwps_defaultVersion();
			
			$opts['currentVersion'] = $currentVersion;

			return $opts;
		}
		
		function saveOptions($opt, $val) {
			global $wpdb,$opts; 
			
				$opts[$opt] = $val;
				
				delete_option("BWPS_options");
				update_option("BWPS_options", serialize($opts));
				
				$opts = $this->getOptions();
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
		
		function status_options() {
			include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/status.php');
		}
		
		function general_options() {
			include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/general.php');
		}
		
		function hidebe_options() {
			include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/hidebe.php');
		}
		
		function limitlogin_options() {
			include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/limitlogin.php');
		}
		
		function banips_options() {
			include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/banips.php');
		}
		
		function support_options() {
			include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/support.php');
		}
		
		function away_options() {
			include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/away.php');
		}
	
		function optsmenu() {
			add_menu_page('Better Security - System Status', 'Better Security', 'manage_options', 'bwps', array(&$this,'status_options'));
			add_submenu_page('bwps', 'Better WP Security - System Status', 	'System Status', 'manage_options', 'bwps', array(&$this,'status_options'));
			add_submenu_page('bwps', 'Better WP Security - Away Mode', 	'Away Mode', 'manage_options', 'bwps-away', array(&$this,'away_options'));
			add_submenu_page('bwps', 'Better WP Security - Ban IPs Options', 	'Ban IPs', 'manage_options', 'bwps-banips', array(&$this,'banips_options'));
			add_submenu_page('bwps', 'Better WP Security - Hide Backend Options', 	'Hide Backend', 'manage_options', 'bwps-hidebe', array(&$this,'hidebe_options'));
			add_submenu_page('bwps', 'Better WP Security - General Fetures', 	'General Fetures', 'manage_options', 'bwps-general', array(&$this,'general_options'));
			add_submenu_page('bwps', 'Better WP Security - Limit Logins Options', 	'Limit Logins', 'manage_options', 'bwps-limitlogin', array(&$this,'limitlogin_options'));
			add_submenu_page('bwps', 'Better WP Security - Support', 	'Support', 'manage_options', 'bwps-support', array(&$this,'support_options'));
		}
		
		function getLocalTime() {
			return strtotime(get_date_from_gmt(date('Y-m-d H:i:s',time())));
		}
	}
}

if ( !function_exists('wp_authenticate') ) {
	function wp_authenticate($username, $password) {
		global $wpdb, $error, $opts, $limitLogin, $away;

		$username = sanitize_user($username);
		$password = trim($password);
		
		if ($away->isOn() && $away->check()) {
			wp_redirect(get_option('siteurl'));
		}
		
		if ($limitLogin->isOn() && $limitLogin->checkLock($username)) {
			if ($opts['limitlogin_denyaccess'] == 1 || $opts['general_removeLoginMessages'] == 1) {
				wp_redirect(get_option('siteurl'));
			} else {
				return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later."));
			}
		}

		$user = apply_filters('authenticate', null, $username, $password);

		if ( $user == null ) {
			$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
		}

		$ignore_codes = array('empty_username', 'empty_password');
		
		if ($limitLogin->isOn()) {
			if (isset($_POST['wp-submit']) && is_wp_error($user)) {

				if ($opts['limitlogin_maxattemptsuser'] >= $limitLogin->countAttempts($username)) {
					$limitLogin->logAttempt($username);	
				} else {	
					$limitLogin->logAttempt();
				}
				
				if ($opts['limitlogin_maxattemptshost'] <= $limitLogin->countAttempts()) {
					$limitLogin->lockOut();
					$locked = true;
				}
				
				if ($opts['limitlogin_maxattemptsuser'] <= $limitLogin->countAttempts($username)) {
					$limitLogin->lockOut($username);
					$locked = true;
				} 
			
				if ($locked == true) {
					if ($opts['limitlogin_denyaccess'] == 1 || $opts['general_removeLoginMessages'] == 1) {
						wp_redirect(get_option('siteurl'));
					} else {
						return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later."));
					}
				}
			} elseif (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes) ) {
				if ($opts['limitlogin_maxattemptsuser'] >= $limitLogin->countAttempts($username)) {
					$limitLogin->logAttempt($username);	
				} else {	
					$limitLogin->logAttempt();
				}
			
				if ($opts['limitlogin_maxattemptshost'] <= $limitLogin->countAttempts()) {
					$limitLogin->lockOut();
					$locked = true;
				}
				
				if ($opts['limitlogin_maxattemptsuser'] <= $limitLogin->countAttempts($username)) {
					$limitLogin->lockOut($username);
					$locked = true;
				} 
			
				if ($locked == true) {
					if ($opts['limitlogin_denyaccess'] == 1 || $opts['general_removeLoginMessages'] == 1) {
						wp_redirect(get_option('siteurl'));
					} else {
						return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry , but this computer has been blocked due to too many recent failed login attempts.<br /><br />Please try again later."));
					}
				}
			}

			do_action('wp_login_failed', $username);
		}

		return $user;
	}
}
