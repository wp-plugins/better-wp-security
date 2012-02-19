<?php

if (!class_exists('bwps_secure')) {

	class bwps_secure extends bit51_bwps {
	
		function __construct() {
			
			add_action('init', array(&$this, 'siteinit'));
			add_action('wp_head', array(&$this,'check404'));
		
		}
		
		function siteinit() {
			global $current_user;
			
			$options = get_option($this->primarysettings);
			
			if (($options['id_enabled'] == 1 ||$options['ll_enabled'] == 1) && $this->checklock($current_user->user_login)) {
				wp_clear_auth_cookie();
				die(__('error', $this->hook));
			}
			
		}
		
		function check404() {
			global $wpdb;
			
			if (is_404()) { //if we're on a 404 page
				$this->logevent(2);
			}
		}
		
		function checklock($username = '') {
			global $wpdb;
			
			$options = get_option($this->primarysettings);
			
			if (strlen($username) > 0) { //if a username was entered check to see if it's locked out
				$username = sanitize_user($username);
				$user = get_user_by('login', $username);
		
				if ($user) {
					$userCheck = $wpdb->get_var("SELECT `user` FROM `" . $wpdb->base_prefix . "bwps_lockouts` WHERE `exptime` > " . time(). " AND `user` = " . $user->ID . " AND `active` = 1;");
					unset($user);
				}
			} else { //no username to be locked out
				$userCheck = false;
			}
					
			//see if the host is locked out
			$hostCheck = $wpdb->get_var("SELECT `host` FROM `" . $wpdb->base_prefix . "bwps_lockouts` WHERE `exptime` > " . time(). " AND `host` = '" . $wpdb->escape($_SERVER['REMOTE_ADDR']) . "' AND `active` = 1;");
				
			//return false if both the user and the host are not locked out	
			if (!$userCheck && !$hostCheck) {
				return false;
			} else {
				return true;
			}
		}
		
		function logevent($type, $username='') {
			global $wpdb;
			
			$options = get_option($this->primarysettings);
			
			$host = $wpdb->escape($_SERVER['REMOTE_ADDR']);
			
			$username = sanitize_user($username);
			$user = get_user_by('login', $username);
			
			if ($type == 2) {
				$url = $wpdb->escape($_SERVER['REQUEST_URI']);
				$referrer = $wpdb->escape($_SERVER['HTTP_REFERER']);
			} else {
				$url = '';
				$referrer = '';
			}
				
			$wpdb->insert(
				$wpdb->base_prefix . 'bwps_log',
				array(
					'type' => $type,
					'timestamp' => time(),
					'host' => $host,
					'user' => absint($user->ID) > 0 ? $user->ID : 0,
					'url' => $url,
					'referrer' => $referrer
				)
			);
			
			if ($type == 1) {
				$period = $options['ll_checkinterval'] * 60;
				
				$hostcount = $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "bwps_log` WHERE type=1 AND host='" . $host . "' AND timestamp > " . (time() - $period) . ";");
				
				if (absint($user->ID) > 0) {
					$usercount = $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "bwps_log` WHERE type=1 AND user=" . $user->ID . " AND timestamp > " . (time() - $period) . ";");					
				} else {
					$usercount = 0;
				}
				
				if ($usercount >= $options['ll_maxattemptsuser']) {
					$this->lockout(1, $user->ID);
				} elseif  ($hostcount >= $options['ll_maxattemptshost']) {
					$this->lockout(1);
				}
			} else {
				$period = $options['id_checkinterval'] * 60;
				
				$hostcount = $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "bwps_log` WHERE type=2 AND host='" . $host . "' AND timestamp > " . (time() - $period) . ";");
				
				if ($hostcount >= $options['id_threshold']) {
					$this->lockout(2);
				}
			}	
		}
		
		function checklist($list, $rawhost = '') {
			global $wpdb;
			
			$values = explode("\n",$list);
			
			if ($rawhost == '') {
				$rawhost = $wpdb->escape($_SERVER['REMOTE_ADDR']);
			}
			
			$host = ip2long($rawhost);
			
			foreach ($values as $item) {
				if (strstr($item ,' - ')) {
					$range = explode('-', $item);
					if($host >= ip2long(trim($range[0])) && $host <= ip2long(trim($range[1]))) {
						return true;
					}
				} else {
					$ipParts = explode('.',$item);
					$i = 0;
					$ipa = '';
					$ipb = '';
					foreach ($ipParts as $part) {
						if (strstr($part, '*')) {
							$ipa .= '0';
							$ipb .= '255';
						} else {
							$ipa .= $part;
							$ipb .= $part;
						}
						if ($i < 3) {
							$ipa .= '.';
							$ipb .= '.';
						}
						$i++;
					}
					if (strcmp($ipa, $ipb) != 0) {
						if($host >= ip2long(trim($ipa)) && $host <= ip2long(trim($ipb))) {
							return true;
						}
					} else {
						if (trim($rawhost) == trim($item)) {
							return true;
						} 
					}
				}
			}
			return false;
		}
		
		function lockout($type, $user = '') {
			global $wpdb;
			
			$options = get_option('bit51_bwps');
			
			$currtime = time();
			
			if ($type == 1) {
				$exptime = $currtime + (60 * $options['ll_banperiod']);
			} else {
				$exptime = $currtime + (60 * $options['id_banperiod']);
			}
			
			if ($type == 1 || $this->checklist($options['id_whitelist']) == false) {
				$wpdb->insert(
					$wpdb->base_prefix . 'bwps_lockouts',
					array(
						'type' => $type,
						'active' => 1,
						'starttime' => $currtime,
						'exptime' => $exptime,
						'host' => $wpdb->escape($_SERVER['REMOTE_ADDR']),
						'user' => $user
					)
				);
			
				if ($options['ll_emailnotify'] ==1 || $options['id_emailnotify'] ==1) {
			
					$toEmail = get_site_option("admin_email");
					$subEmail = get_bloginfo('name') . ' ' . __('Site Lockout Notification', 'better-wp-security');
					$mailHead = 'From: ' . get_bloginfo('name')  . ' <' . $toEmail . '>' . "\r\n\\";
					if ($type == 1) {
						$reason = __('too many login attempts.', $this->hook);
					} else {
						$reason = __('too many attempts to open a file that does not exist.', $this->hook);
					}
				
					if ($user != '') {
						$username = get_user_by('id', $user);
						$who = __('WordPress user', $this->hook) . ', ' . $username->user_login . ', ' . __('at host, ', $this->hook) . $wpdb->escape($_SERVER['REMOTE_ADDR']) . ', ';
					} else {
						$who = __('host', $this->hook) . ', ' . $wpdb->escape($_SERVER['REMOTE_ADDR']) . ', ';
					}
			
					$mesEmail = __("A ", $bwps->this) . $who . __('has been locked out of the WordPress site at', $this->hook) . " " . get_bloginfo('url') . " " . __('until', $this->hook) . " " . date("l, F jS, Y \a\\t g:i:s a e", $exptime) . " " . __('due to ', $this->hook) . $reason . __(' You may login to the site to manually release the lock if necessary.', $this->hook);
				
					$sendMail = wp_mail($toEmail, $subEmail, $mesEmail, $headers);

				}
			}
		}
	
	}

}