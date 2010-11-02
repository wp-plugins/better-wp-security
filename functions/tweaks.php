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
	
}
	