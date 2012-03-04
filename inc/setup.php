<?php

require_once( plugin_dir_path( __FILE__ ) . 'admin/common.php' );

if ( ! class_exists( 'bwps_setup' ) ) {

	class bwps_setup extends bwps_admin_common {

		/**
		 * Establish setup object
		 *
		 * Establishes set object and calls appropriate execution function
		 *
		 * @param bool $case[optional] Appropriate execution module to call
		 *
		 **/
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
		
		/**
		 * Public function to activate
		 *
		 **/
		function on_activate() {
			new bwps_setup( 'activate' );
		}

		/**
		 * Public function to deactivate
		 *
		 **/
		function on_deactivate() {
	
			$devel = true; //set to true to uninstall for development
		
			if ( $devel ) {
				$case = 'uninstall';
			} else {
				$case = 'deactivate';
			}

			new bwps_setup( $case );
		}

		/**
		 * Public function to uninstall
		 *
		 **/
		function on_uninstall() {
		
			if ( __FILE__ != WP_UNINSTALL_PLUGIN ) { //verify they actually clicked uninstall
				return;
			}

			new bwps_setup( 'uninstall' );
			
		}
		
		/**
		 * Activate execution
		 *
		 **/
		function activate_execute() {
		
			//if this is multisite make sure they're network activating or die
			if ( is_multisite() && ! strpos( $_SERVER['REQUEST_URI'], 'wp-admin/network/plugins.php' ) ) {
			
				die ( __( '<strong>ERROR</strong>: You must activate this plugin from the network dashboard.', $bwps->hook ) );	
			
			}
			
			global $wpdb;
			
			$this->default_settings(); //verify and set default options
			
			$options = get_option( $this->plugindata );
			
			$options['version'] = $this->pluginversion; //set new version number
			
			//remove no support nag if it's been more than six months
			if ( ! isset( $options['activatestamp'] ) || $options['activatestamp'] < ( time() - 15552000 ) ) {
			
				if ( isset( $options['no-nag'] ) ) {
					unset( $options['no-nag'] );
				}
				
				//set activate timestamp to today (they'll be notified again in a month)
				$options['activatestamp'] = time();
			}
			
			//save plugin data
			update_option( $this->plugindata, $options ); //save new plugin data
			
			//update if version numbers don't match
			if ( ( isset( $options['version'] ) && $options['version'] != $this->pluginversion ) || get_option( 'BWPS_options' ) != false ) {
				$this->update_execute();
			}
			
			//get plugin settings
			$options = get_option( $this->primarysettings );
			
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
			
			//get contents of wp-config.php
			$lines = explode( "\n", implode( '', file( $this->getconfig() ) ) ); //parse each line of file into array
			
			//set default options for wp-config stuff
			foreach ($lines as $line) {
			
				if ( strstr( $line, 'DISALLOW_FILE_EDIT' ) && strstr( $line, 'true' ) ) {
					
					$options['st_fileedit'] = 1;
					
				}
				
				if ( strstr( $line, 'FORCE_SSL_LOGIN' ) && strstr( $line, 'true' ) ) {
				
					$option['st_forceloginssl'] = 1;
					
				}
				
				if ( strstr( $line, 'FORCE_SSL_ADMIN' ) && strstr( $line, 'true' ) ) {
				
					$option['st_forceadminssl'] = 1;
					
				}
				
			}
			
			update_option( $this->primarysettings, $options ); //save new options data
			
			if ( strstr( strtolower( $_SERVER['SERVER_SOFTWARE'] ), 'apache' ) ) { //if they're using apache write to .htaccess
			
				$this->writehtaccess();
			
			}
			
			$this->writewpconfig(); //write appropriate options to wp-config.php
			
		}

		/**
		 * Update execution
		 *
		 **/
		function update_execute() {
			global $wpdb;
		
			if ( get_option( 'BWPS_options' ) != false ) {
			
				$oldoptions = get_option( 'BWPS_options' );
				$options = get_option( $this->primarysettings );
				
				$options['am_enabled'] = $oldoptions['away_enable'];
				$options['am_type'] = $oldoptions['away_mode'];
				$options['am_startdate'] = $oldoptions['away_start'];
				$options['am_starttime'] = $oldoptions['away_start'];
				$options['am_enddate'] = $oldoptions['away_end'];
				$options['am_endtime'] = $oldoptions['away_end'];
				$options['st_generator'] = $oldoptions['tweaks_removeGenerator'];
				$options['st_loginerror'] = $oldoptions['tweaks_removeLoginMessages'];
				$options['st_randomversion'] = $oldoptions['tweaks_randomVersion'];
				$options['st_themenot'] = $oldoptions['tweaks_themeUpdates'];
				$options['st_pluginnot'] = $oldoptions['tweaks_pluginUpdates'];
				$options['st_corenot'] = $oldoptions['tweaks_coreUpdates'];
				$options['st_manifest'] = $oldoptions['tweaks_removewlm'];
				$options['st_edituri'] = $oldoptions['tweaks_removersd'];
				$options['st_longurl'] = $oldoptions['tweaks_longurls'];
				$options['st_enablepassword'] = $oldoptions['tweaks_strongpass'];
				$options['st_passrole'] = $oldoptions['tweaks_strongpassrole'];
				$options['st_ht_files'] = $oldoptions['htaccess_protectht'];
				$options['st_ht_browsing'] = $oldoptions['htaccess_dirbrowse'];
				$options['st_ht_request'] = $oldoptions['htaccess_request'];
				$options['st_ht_query'] = $oldoptions['htaccess_qstring'];
				$options['hb_enabled'] = $oldoptions['hidebe_enable'];
				$options['hb_login'] = $oldoptions['hidebe_login_slug'];
				$options['hb_admin'] = $oldoptions['hidebe_admin_slug'];
				$options['hb_register'] = $oldoptions['hidebe_register_slug'];
				$options['hb_key'] = $oldoptions['hidebe_key'];
				$options['ll_enabled'] = $oldoptions['ll_enable'];
				$options['ll_maxattemptshost'] = $oldoptions['ll_maxattemptshost'];
				$options['ll_maxattemptsuser'] = $oldoptions['ll_maxattemptsuser'];
				$options['ll_checkinterval'] = $oldoptions['ll_checkinterval'];
				$options['ll_banperiod'] = $oldoptions['ll_banperiod'];
				$options['ll_emailnotify'] = $oldoptions['ll_emailnotify'];
				$options['id_enabled'] = $oldoptions['idetect_d404enable'];
				$options['id_emailnotify'] = $oldoptions['idetect_emailnotify'];
				$options['id_checkinterval'] = ( $oldoptions['idetect_checkint'] / 60 );
				$options['id_threshold'] = $oldoptions['idetect_locount'];
				$options['id_banperiod'] = ( $oldoptions['idetect_lolength'] / 60 );
				$options['id_whitelist'] = $oldoptions['idetect_whitelist'];
				$options['bu_enabled'] = $oldoptions['banvisits_enable'];
				
				$ips = array();
				$ranges = array();
				
				$items = explode ("\n", $oldoptions['banvisits_banlist'] );
				
				foreach ( $items as $item ) {
					
					if ( strstr( $item, '-' ) {
					
						$r = explode( '-', $item )
					
						if ( ip2long( trim( str_replace( '*', '0', $r[0] ) ) ) != false && ip2long( trim( str_replace( '*', '0', $r[1] ) ) ) != false ) {
						
							$ranges[] = $item;
						
						}
					
					} elseif ( strstr( $item, '*' ) {
					
						if ( ip2long( trim( str_replace( '*', '0', $item ) ) ) != false ) {
						
							$ranges[] = $item;
						
						}
					
					} else {
					
						if ( ip2long( trim( $item ) ) != false ) {
						
							$ips[] = $item;
						
						}
						
					}
					
				}
				
				$options['bu_banrange'] = implode( "\n", $ranges );
				$options['bu_individual'] = implode( "\n", $ips );
				
				update_option( $this->primarysettings, $options ); //save new options data
				
				delete_option( 'BWPS_Login_Slug' );
				delete_option( 'BWPS_options' );
				delete_option( 'BWPS_versions' );
				
				$wpdb->query( "DROP TABLE IF EXISTS `" . $wpdb->base_prefix . "BWPS_d404`;" );
				$wpdb->query( "DROP TABLE `" . $wpdb->base_prefix . "BWPS_ll`;" );
				$wpdb->query( "DROP TABLE `" . $wpdb->base_prefix . "BWPS_lockouts`;" );
				
				$this->deletehtaccess('Better WP Security Protect htaccess');
				$this->deletehtaccess('Better WP Security Hide Backend');
				$this->deletehtaccess('Better WP Security Ban IPs');
			
			}
		
		}
		
		/**
		 * Deactivate execution
		 *
		 **/
		function deactivate_execute() {
		
			if ( wp_next_scheduled( 'bwps_backup' ) ) {
				wp_clear_scheduled_hook( 'bwps_backup' );
			}
			
			//delete options from files
			$this->deletewpconfig();
			$this->deletehtaccess();
			
			if ( function_exists( 'apc_store' ) ) { 
				apc_clear_cache(); //Let's clear APC (if it exists) when big stuff is saved.
			}
			
		}
		
		/**
		 * Uninstall execution
		 *
		 **/
		function uninstall_execute() {
			global $wpdb;
			
			$this->deactivate_execute(); //execute deactivation functions
			
			//drop database tables
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
			
			if ( function_exists( 'apc_store' ) ) { 
				apc_clear_cache(); //Let's clear APC (if it exists) when big stuff is saved.
			}
			
		}
		
	}
	
}
