<?php

if (!class_exists('bwps_secure')) {

	class bwps_secure extends bit51_bwps {
	
		function __construct() {
			
			add_action('init', array(&$this, 'siteinit'));
		
		}
		
		function siteinit() {
			global $current_user;
			
			$options = get_option($this->primarysettings);
			
			if ($options['ll_enabled'] == 1 && $this->checklock($current_user->user_login)) {
				wp_clear_auth_cookie();
				die(__('error', $this->hook));
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
					'type' => 1,
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
			}		
		}
		
		function lockout($type, $user = '') {
			global $wpdb;
			
			$options = get_option('bit51_bwps');
			
			$currtime = time();
			
			$exptime = $currtime + (60 * $options['ll_banperiod']);
			
			$wpdb->insert(
				$wpdb->base_prefix . 'bwps_lockouts',
				array(
					'type' => 1,
					'active' => 1,
					'starttime' => $currtime,
					'exptime' => $exptime,
					'host' => $wpdb->escape($_SERVER['REMOTE_ADDR']),
					'user' => $user
				)
			);
			
			if ($options['ll_emailnotify'] ==1) {
			
				$toEmail = get_site_option("admin_email");
				$subEmail = get_bloginfo('name') . ' ' . __('Site Lockout Notification', 'better-wp-security');
				$mailHead = 'From: ' . get_bloginfo('name')  . ' <' . $toEmail . '>' . "\r\n\\";
				
				if ($user != '') {
					$username = get_user_by('id', $user);
					$who = __('WordPress user', $this->hook) . ', ' . $username->user_login . ', ' . __('at host, ', $this->hook) . $wpdb->escape($_SERVER['REMOTE_ADDR']) . ', ';
				} else {
					$who = __('host', $this->hook) . ', ' . $wpdb->escape($_SERVER['REMOTE_ADDR']) . ', ';
				}
			
				$mesEmail = __("A ", $bwps->this) . $who . __('has been locked out of the WordPress site at', $this->hook) . " " . get_bloginfo('url') . " " . __('until', $this->hook) . " " . date("l, F jS, Y \a\\t g:i:s a e", $exptime) . " " . __('due to too many failed login attempts. You may login to the site to manually release the lock if necessary.', 'better-wp-security');
				
				$sendMail = wp_mail($toEmail, $subEmail, $mesEmail, $headers);

			}
		}
	
	}

}