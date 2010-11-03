<?php
class BWPS_tweaks extends BWPS {
	
	function __construct() {
		
		$opts = $this->getOptions();
			
		if ($opts['tweaks_removeGenerator'] == 1) {
			remove_action('wp_head', 'wp_generator'); //remove generator tag
		}

		if ($opts['tweaks_removeLoginMessages'] == 1) {
			add_filter('login_errors', create_function('$a', "return null;")); //hide login errors
		}

		if ($opts['tweaks_randomVersion'] == 1) {
			$this->randomVersion();
		}
		
		if ($opts['tweaks_themeUpdates'] == 1) {
			add_action('init', array(&$this, 'themeupdates'), 1);
		}
		
		if ($opts['tweaks_pluginUpdates'] == 1) {
			add_action('init', array(&$this, 'pluginupdates'), 1);
		}
		
		if ($opts['tweaks_coreUpdates'] == 1) {
			add_action('init', array(&$this, 'coreupdates'), 1);
		}
		
		if ($opts['tweaks_removewlm'] == 1) {
			remove_action('wp_head', 'wlwmanifest_link');
		}
		
		if ($opts['tweaks_removersd'] == 1) {
			remove_action('wp_head', 'rsd_link');
		}
		
		if ($opts['tweaks_strongpass'] == 1) {
			add_action( 'user_profile_update_errors',  array(&$this, 'strongpass'), 0, 3 ); 
		}
		
		chmod(trailingslashit(ABSPATH) . "wp-config.php", 0755);
		
		if ($opts['tweaks_longurls'] == 1) {
			if (strlen($_SERVER['REQUEST_URI']) > 255 ||
				strpos($_SERVER['REQUEST_URI'], "eval(") ||
				strpos($_SERVER['REQUEST_URI'], "CONCAT") ||
				strpos($_SERVER['REQUEST_URI'], "UNION+SELECT") ||
				strpos($_SERVER['REQUEST_URI'], "base64")) {
				@header("HTTP/1.1 414 Request-URI Too Long");
				@header("Status: 414 Request-URI Too Long");
				@header("Connection: Close");
				@exit;
			}
		}	
	}
		
	function randomVersion() {
		global $wp_version, $ver;

		$newVersion = rand(100,500);

		if (!is_admin()) {
			$wp_version = $newVersion;
		}
	}
	
	function pluginupdates() {
		if (!is_super_admin()) {
			remove_action( 'load-update-core.php', 'wp_update_plugins' );
			add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
			wp_clear_scheduled_hook( 'wp_update_plugins' );
		}
	}

	function themeupdates() {
		if (!is_super_admin()) {
			remove_action( 'load-update-core.php', 'wp_update_themes' );
			add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );
			wp_clear_scheduled_hook( 'wp_update_themes' );
		}
	}
	
	function coreupdates() {
		if (!is_super_admin()) {
			remove_action('admin_notices', 'update_nag', 3);
			add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );
			wp_clear_scheduled_hook( 'wp_version_check' );
		}
	}
	
	function strongpass( $errors ) {  
		$opts = $this->getOptions();
		
		$minRole = $opts['tweaks_strongpassrole'];
	
		$availableRoles = array(
			"administrator" => "8",
			"editor" => "5",
			"author" => "2",
			"contributor" => "1",
			"subscriber" => "0"
		);
		
		$rollists = array(
			"administrator" => array("subscriber", "author", "contributor","editor"),
			"editor" =>  array("subscriber", "author", "contributor"),
			"author" =>  array("subscriber", "contributor"),
			"contributor" =>  array("subscriber"),
			"subscriber" => array()
		);
		
		$enforce = true;  
		$args = func_get_args();  
		$userID = $args[2]->ID;  
		if ( $userID ) {  
			$userInfo = get_userdata( $userID );  
			if ( $userInfo->user_level < $availableRoles[$minRole] ) {  
				$enforce = false;  
			}  
		} else {  
			if ( in_array( $_POST["role"],  $rollists[$minRole]) ) {  
				$enforce = false;  
			}  
		}  
		if ( $enforce && !$errors->get_error_data("pass") && $_POST["pass1"] && $this->pwordstrength( $_POST["pass1"], $_POST["user_login"] ) != 4 ) {  
			$errors->add( 'pass', __( '<strong>ERROR</strong>: You MUST Choose a password that rates at least <em>Strong</em> on the meter. Your setting have NOT been saved.' ) );  
		}  
		return $errors;  
	}  
 
	function pwordstrength( $i, $f ) {  
		$h = 1; $e = 2; $b = 3; $a = 4; $d = 0; $g = null; $c = null;  
		if ( strlen( $i ) < 4 )  
			return $h;  
		if ( strtolower( $i ) == strtolower( $f ) )  
			return $e;  
		if ( preg_match( "/[0-9]/", $i ) )  
			$d += 10;  
		if ( preg_match( "/[a-z]/", $i ) )  
			$d += 26;  
		if ( preg_match( "/[A-Z]/", $i ) )  
			$d += 26;  
		if ( preg_match( "/[^a-zA-Z0-9]/", $i ) )  
			$d += 31;  
		$g = log( pow( $d, strlen( $i ) ) );  
		$c = $g / log( 2 );  
		if ( $c < 40 )  
			return $e;  
		if ( $c < 56 )  
			return $b;  
		return $a;  
	}
}
	