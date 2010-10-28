<?php
if (!class_exists('BWPS_general')) {
	class BWPS_general {
	
		function __construct() {
			global $opts;
			
			if ($opts['general_removeGenerator'] == 1) {
				remove_action('wp_head', 'wp_generator'); //remove generator tag
			}

			if ($opts['general_removeLoginMessages'] == 1) {
				add_filter('login_errors', create_function('$a', "return null;")); //hide login errors
			}

			if ($opts['general_randomVersion'] == 1) {
				$this->randomVersion();
			}
		
		}
		
		function randomVersion() {
			global $wp_version, $ver;

			$newVersion = rand(100,500);

			if (!is_admin()) {
				$wp_version = $newVersion;
			}
		}
	
	}
}