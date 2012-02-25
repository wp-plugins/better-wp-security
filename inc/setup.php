<?php

if ( ! class_exists( 'bwps_setup' ) ) {

	class bwps_setup extends bit51_bwps {

		function __construct( $case = false ) {
	
			if ( ! $case ) {
				die( 'error' );
			}

			switch($case) {
				case 'activate': //active plugin
					$this->activate_execute();
					break;

				case 'deactivate': //deactivate plugin
					$this->deactivate_execute();
					break;

				case 'uninstall': //uninstall plugin
					$this->uninstall_execute();
					break;
			}
		}

		function on_activate() {
			new bwps_setup( 'activate' );
		}

		function on_deactivate() {
	
			$devel = true; //set to true to uninstall for development
		
			if ( $devel ) {
				$case = 'uninstall';
			} else {
				$case = 'deactivate';
			}

			new bwps_setup( $case );
		}

		function on_uninstall() {
			if ( __FILE__ != WP_UNINSTALL_PLUGIN ) { //verify they actually clicked uninstall
				return;
			}

			new bwps_setup( 'uninstall' );
		}

		function activate_execute() {
		
			if ( is_multisite() && ! strpos( $_SERVER['REQUEST_URI'], 'wp-admin/network/plugins.php' ) ) {
			
				die ( __( '<strong>ERROR</strong>: You must activate this plugin from the network dashboard.', $bwps->hook ) );	
			
			}
			
			global $wpdb;
			
			$this->default_settings(); //verify and set default options
			
			$options = get_option( $this->plugindata );
			
			//Set up log table
			$tables = "CREATE TABLE `" . $wpdb->base_prefix . "bwps_log` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`type` int(1) NOT NULL,
				`timestamp` int(10) NOT NULL,
				`host` varchar(20),
				`user` bigint(20),
				`url` varchar(255),
				`referrer` varchar(255),
				PRIMARY KEY (`id`)
				);";
			
			//set up lockout table	
			$tables .= "CREATE TABLE `" . $wpdb->base_prefix . "bwps_lockouts` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`type` int(1) NOT NULL,
				`active` int(1) NOT NULL,
				`starttime` int(10) NOT NULL,
				`exptime` int(10) NOT NULL,
				`host` varchar(20),
				`user` bigint(20),
				PRIMARY KEY (`id`)
				);";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $tables );
			
			//update if version numbers don't match
			if ( isset( $options['version'] ) && $options['version'] != $this->pluginversion ) {
				$this->update_execute();
			}
			
			$options['version'] = $this->pluginversion; //set new version number
			
			//remove no support nag if it's been more than six months
			if ( ! isset( $options['activatestamp'] ) || $options['activatestamp'] < ( time() - 15552000 ) ) {
			
				if ( isset( $options['no-nag'] ) ) {
					unset( $options['no-nag'] );
				}
				
				//set activate timestamp to today (they'll be notified again in a month)
				$options['activatestamp'] = time();
			}
			
			update_option( $this->plugindata, $options ); //save new plugin data
		}

		function update_execute() {
		
		}

		function deactivate_execute() {
		
			if ( wp_next_scheduled( 'bwps_backup' ) ) {
				wp_clear_scheduled_hook( 'bwps_backup' );
			}
			
		}

		function uninstall_execute() {
			global $wpdb;
			
			$this->deactivate_execute();
			
			$wpdb->query( "DROP TABLE `" . $wpdb->base_prefix . "bwps_lockouts`;" );
			$wpdb->query( "DROP TABLE `" . $wpdb->base_prefix . "bwps_log`;" );
			
			//remove all settings
			foreach( $this->settings as $settings ) {
			
				foreach ( $settings as $setting => $option ) {
					delete_option( $setting );
				}
				
			}
			
			//delete plugin information (version, etc)
			delete_option($this->plugindata);
			
		}
		
	}
	
}
