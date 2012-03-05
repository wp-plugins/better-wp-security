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
	
			$devel = false; //set to true to uninstall for development
		
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
		
			new bwps_setup( 'uninstall' );
			
		}
		
		/**
		 * Activate execution
		 *
		 **/
		function activate_execute() {
			global $wpdb;
		
			//if this is multisite make sure they're network activating or die
			if ( is_multisite() && ! strpos( $_SERVER['REQUEST_URI'], 'wp-admin/network/plugins.php' ) ) {
			
				die ( __( '<strong>ERROR</strong>: You must activate this plugin from the network dashboard.', $bwps->hook ) );	
			
			}			
			
			$options = get_option( $this->plugindata );
					
			$oldversion = $options['version']; //set new version number
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
			if ( $oldversion != $this->pluginversion || get_option( 'BWPS_options' ) != false ) {
				$this->update_execute($oldversion);
			}
			
			$this->default_settings(); //verify and set default options
			
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
		function update_execute($oldversion = '') {
			global $wpdb;
			
			if ( get_option( 'BWPS_options' ) != false ) {
			
				$oldoptions = unserialize( get_option( 'BWPS_options' ) );
				$options = get_option( $this->primarysettings );
				
				$options['am_enabled'] = isset( $oldoptions['away_enable'] ) ? $oldoptions['away_enable'] : '0';
				$options['am_type'] = isset( $oldoptions['away_mode'] ) ? $oldoptions['away_mode'] : '0';
				$options['am_startdate'] = isset( $oldoptions['away_start'] ) ? $oldoptions['away_start'] : '1';
				$options['am_starttime'] = isset( $oldoptions['away_start'] ) ? $oldoptions['away_start'] : '1';
				$options['am_enddate'] = isset( $oldoptions['away_end'] ) ? $oldoptions['away_end'] : '1';
				$options['am_endtime'] = isset( $oldoptions['away_end'] ) ? $oldoptions['away_end'] : '1';
				$options['st_generator'] = isset( $oldoptions['tweaks_removeGenerator'] ) ? $oldoptions['tweaks_removeGenerator'] : '0';
				$options['st_loginerror'] = isset( $oldoptions['tweaks_removeLoginMessages'] ) ? $oldoptions['tweaks_removeLoginMessages'] : '0';
				$options['st_randomversion'] = isset( $oldoptions['tweaks_randomVersion'] ) ? $oldoptions['tweaks_randomVersion'] : '0';
				$options['st_themenot'] = isset( $oldoptions['tweaks_themeUpdates'] ) ? $oldoptions['tweaks_themeUpdates'] : '0';
				$options['st_pluginnot'] = isset( $oldoptions['tweaks_pluginUpdates'] ) ? $oldoptions['tweaks_pluginUpdates'] : '0';
				$options['st_corenot'] = isset( $oldoptions['tweaks_coreUpdates'] ) ? $oldoptions['tweaks_coreUpdates'] : '0';
				$options['st_manifest'] = isset( $oldoptions['tweaks_removewlm'] ) ? $oldoptions['tweaks_removewlm'] : '0';
				$options['st_edituri'] = isset( $oldoptions['tweaks_removersd'] ) ? $oldoptions['tweaks_removersd'] : '0';
				$options['st_longurl'] = isset( $oldoptions['tweaks_longurls'] ) ? $oldoptions['tweaks_longurls'] : '0';
				$options['st_enablepassword'] = isset( $oldoptions['tweaks_strongpass'] ) ? $oldoptions['away_enable'] : '0';
				$options['st_passrole'] = isset( $oldoptions['tweaks_strongpassrole'] ) ? $oldoptions['away_enable'] : '0';
				$options['st_ht_files'] = isset( $oldoptions['htaccess_protectht'] ) ? $oldoptions['away_enable'] : '0';
				$options['st_ht_browsing'] = isset( $oldoptions['htaccess_dirbrowse'] ) ? $oldoptions['away_enable'] : '0';
				$options['st_ht_request'] = isset( $oldoptions['htaccess_request'] ) ? $oldoptions['away_enable'] : '0';
				$options['st_ht_query'] = isset( $oldoptions['htaccess_qstring'] ) ? $oldoptions['away_enable'] : '0';
				$options['hb_enabled'] = isset( $oldoptions['hidebe_enable'] ) ? $oldoptions['hidebe_enable'] : '0';
				$options['hb_login'] = isset( $oldoptions['hidebe_login_slug'] ) ? $oldoptions['hidebe_login_slug'] : 'login';
				$options['hb_admin'] = isset( $oldoptions['hidebe_admin_slug'] ) ? $oldoptions['hidebe_admin_slug'] : 'admin';
				$options['hb_register'] = isset( $oldoptions['hidebe_register_slug'] ) ? $oldoptions['hidebe_register_slug'] : 'register';
				$options['hb_key'] = isset( $oldoptions['hidebe_key'] ) ? $oldoptions['hidebe_key'] : '';
				$options['ll_enabled'] = isset( $oldoptions['ll_enable'] ) ? $oldoptions['ll_enable'] : '0';
				$options['ll_maxattemptshost'] = isset( $oldoptions['ll_maxattemptshost'] ) ? $oldoptions['ll_maxattemptshost'] : '5';
				$options['ll_maxattemptsuser'] = isset( $oldoptions['ll_maxattemptsuser'] ) ? $oldoptions['ll_maxattemptsuser'] : '10';
				$options['ll_checkinterval'] = isset( $oldoptions['ll_checkinterval'] ) ? $oldoptions['ll_checkinterval'] : '5';
				$options['ll_banperiod'] = isset( $oldoptions['ll_banperiod'] ) ? $oldoptions['ll_banperiod'] : '15';
				$options['ll_emailnotify'] = isset( $oldoptions['ll_emailnotify'] ) ? $oldoptions['ll_emailnotify'] : '1';
				$options['id_enabled'] = isset( $oldoptions['idetect_d404enable'] ) ? $oldoptions['idetect_d404enable'] : '0';
				$options['id_emailnotify'] = isset( $oldoptions['idetect_emailnotify'] ) ? $oldoptions['idetect_emailnotify'] : '1';
				$options['id_checkinterval'] = isset( $oldoptions['idetect_checkint'] ) ? ( $oldoptions['idetect_checkint'] / 60 ) : '5';
				$options['id_threshold'] = isset( $oldoptions['idetect_locount'] ) ? $oldoptions['idetect_locount'] : '20';
				$options['id_banperiod'] = isset( $oldoptions['idetect_lolength'] ) ? ( $oldoptions['idetect_lolength'] / 60 ) : '15';
				$options['id_whitelist'] = isset( $oldoptions['idetect_whitelist'] ) ? $oldoptions['idetect_whitelist'] : '0';
				$options['bu_enabled'] = isset( $oldoptions['banvisits_enable'] ) ? $oldoptions['banvisits_enable'] : '0';
				
				
				if ( isset(  $oldoptions['banvisits_banlist'] ) ) {
					$list = array();
				
					$items = explode ("\n", $oldoptions['banvisits_banlist'] );
				
					foreach ( $items as $item ) {
					
						if ( strstr( $item, '*' ) ) {
					
							if ( ip2long( trim( str_replace( '*', '0', $item ) ) ) != false ) {
						
								$list[] = $item;
						
							}
					
						} elseif ( ! strstr( $item, '-' ) ) {
					
							if ( ip2long( trim( $item ) ) != false ) {
						
								$list[] = $item;
						
							}
						
						}
						
					}
				
					$options['bu_banlist'] = implode( "\n", $list );
				
				}
				
				update_option( $this->primarysettings, $options ); //save new options data
				
				delete_option( 'BWPS_Login_Slug' );
				delete_option( 'BWPS_options' );
				delete_option( 'BWPS_versions' );
				
				$wpdb->query( "DROP TABLE IF EXISTS `" . $wpdb->base_prefix . "BWPS_d404`;" );
				$wpdb->query( "DROP TABLE IF EXISTS `" . $wpdb->base_prefix . "BWPS_ll`;" );
				$wpdb->query( "DROP TABLE IF EXISTS `" . $wpdb->base_prefix . "BWPS_lockouts`;" );
				
				$this->deletehtaccess('Better WP Security Protect htaccess');
				$this->deletehtaccess('Better WP Security Hide Backend');
				$this->deletehtaccess('Better WP Security Ban IPs');
			
			} else {
			
				$options = get_option( $this->primarysettings );
				
				if ( str_replace( '.', '', $oldversion ) < 304 ) {
				
					$ranges = explode( "\n", $options['bu_banrange'] );
					$ips = explode( "\n", $options['bu_individual'] );
					
					if ( sizeof( $ranges ) > 0 ) {
					
						for ( $i = 0; $i < sizeof( $ranges ); $i++ ) {
					
							if ( strstr( $ranges[$i], '-' ) ) {
							
								unset( $ranges[$i] );
							
							}
					
						}
						
						$options['bu_banlist'] = implode( "\n", array_merge( $ranges, $ips ) );
						
						update_option( $this->primarysettings, $options ); //save new options data
					
					}
					
				}
			
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
			$wpdb->query( "DROP TABLE IF EXISTS `" . $wpdb->base_prefix . "bwps_lockouts`;" );
			$wpdb->query( "DROP TABLE IF EXISTS `" . $wpdb->base_prefix . "bwps_log`;" );
			
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
