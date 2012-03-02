<?php

if ( ! class_exists( 'bwps_admin_content' ) ) {

	class bwps_admin_content extends bwps_admin_common {
	
		function __construct() {
		
			if ( is_multisite() ) { 
				add_action( 'network_admin_menu', array( &$this, 'register_settings_page' ) ); 
			} else {
				add_action( 'admin_menu',  array( &$this, 'register_settings_page' ) );
			}

			//add settings
			add_action( 'admin_init', array( &$this, 'register_settings' ) );
		
		}
	
		/**
		 * Registers all WordPress admin menu items
		 *
		 **/
		function register_settings_page() {
		
			add_menu_page(
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Dashboard', $this->hook ),
				__( 'Security', $this->hook ),
				$this->accesslvl,
				$this->hook,
				array( &$this, 'admin_dashboard' ),
				BWPS_PU . 'images/padlock.png'
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Change Admin User', $this->hook ),
				__( 'Admin User', $this->hook ),
				$this->accesslvl,
				$this->hook . '-adminuser',
				array( &$this, 'admin_adminuser' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Away Mode', $this->hook ),
				__( 'Away Mode', $this->hook ),
				$this->accesslvl,
				$this->hook . '-awaymode',
				array( &$this, 'admin_awaymode' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Ban Users', $this->hook ),
				__( 'Ban Users', $this->hook ),
				$this->accesslvl,
				$this->hook . '-banusers',
				array( &$this, 'admin_banusers' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Change Content Directory', $this->hook ),
				__( 'Content Directory', $this->hook ),
				$this->accesslvl,
				$this->hook . '-contentdirectory',
				array( &$this, 'admin_contentdirectory' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Backup WordPress Database', $this->hook ),
				__( 'Database Backup', $this->hook ),
				$this->accesslvl,
				$this->hook . '-databasebackup',
				array( &$this, 'admin_databasebackup' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Change Database Prefix', $this->hook ),
				__( 'Database Prefix', $this->hook ),
				$this->accesslvl,
				$this->hook . '-databaseprefix',
				array( &$this, 'admin_databaseprefix' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Hide Backend', $this->hook ),
				__( 'Hide Backend', $this->hook ),
				$this->accesslvl,
				$this->hook . '-hidebackend',
				array( &$this, 'admin_hidebackend' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Intrusion Detection', $this->hook ),
				__( 'Intrusion Detection', $this->hook ),
				$this->accesslvl,
				$this->hook . '-intrusiondetection',
				array( &$this, 'admin_intrusiondetection' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Limit Login Attempts', $this->hook ),
				__( 'Login Limits', $this->hook ),
				$this->accesslvl,
				$this->hook . '-loginlimits',
				array( &$this, 'admin_loginlimits' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'WordPress System Tweaks', $this->hook ),
				__( 'System Tweaks', $this->hook ),
				$this->accesslvl,
				$this->hook . '-systemtweaks',
				array( &$this, 'admin_systemtweaks' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'View Logs', $this->hook ),
				__( 'View Logs', $this->hook ),
				$this->accesslvl,
				$this->hook . '-logs',
				array( &$this, 'admin_logs' )
			);
			
			//Make the dashboard the first submenu item and the item to appear when clicking the parent.
			global $submenu;
			if ( isset( $submenu[$this->hook] ) ) {
			
				$submenu[$this->hook][0][0] = __( 'Dashboard', $this->hook );
				
			}
			
		}	
		
		/**
		 * Registers content blocks for dashboard page
		 *
		 **/
		function admin_dashboard() {
			
			$options = get_option( $this->primarysettings );
			
			if ( $options['initial_backup'] == 1 ) { //they've backed up their database or ignored the warning
			
				$this->admin_page( $this->pluginname . ' - ' . __( 'Change Admin User', $this->hook ),
					array(
						array( __( 'One-Click Protection', $this->hook ), 'dashboard_content_2' ), //One-click protection
						array( __( 'System Status', $this->hook ), 'dashboard_content_3' ), //Better WP Security System Status
						array( __( 'Rewrite Rules', $this->hook ), 'dashboard_content_4' ), //Better WP Security Rewrite Rules
						array( __( 'System Information', $this->hook ), 'dashboard_content_5' ) //Generic System Information
					)
				);
			
			} else { //if they haven't backed up their database or ignored the warning
			
				$this->admin_page( $this->pluginname . ' - ' . __( 'Change Admin User', $this->hook ),
					array(
						array( __( 'Welcome!', $this->hook ), 'dashboard_content_1' ), //Try to force the user to back up their site before doing anything else
					)
				);
			
			}
			
		}
		
		/**
		 * Registers content blocks for change admin user page
		 *
		 **/
		function admin_adminuser() {
			$this->admin_page( $this->pluginname . ' - ' . __( 'Change Admin User', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'adminuser_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Change The Admin User', $this->hook ), 'adminuser_content_2' ) //adminuser options
				)
			);
		}
		
		/**
		 * Registers content blocks for away mode page
		 *
		 **/
		function admin_awaymode() {
			$this->admin_page( $this->pluginname . ' - ' . __( 'Administor Away Mode', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'awaymode_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Away Mode Options', $this->hook ), 'awaymode_content_2' ), //awaymode options
					array( __( 'Away Mode Rules', $this->hook ), 'awaymode_content_3' )
				)
			);
		}
		
		/**
		 * Registers content blocks for ban hosts page
		 *
		 **/
		function admin_banusers() {
			$this->admin_page( $this->pluginname . ' - ' . __( 'Ban Users', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'banusers_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Banned Users Configuration', $this->hook ), 'banusers_content_2' ) //banusers options
				)
			);
		}
		
		/**
		 * Registers content blocks for content directory page
		 *
		 **/
		function admin_contentdirectory() {
			$this->admin_page( $this->pluginname . ' - ' . __( 'Change wp-content Directory', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'contentdirectory_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Change The wp-content Directory', $this->hook ), 'contentdirectory_content_2' ) //contentdirectory options
				)
			);
		}
		
		/**
		 * Registers content blocks for database backup page
		 *
		 **/
		function admin_databasebackup() {
			$this->admin_page( $this->pluginname . ' - ' . __( 'Backup WordPress Database', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'databasebackup_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Backup Your WordPress Database', $this->hook ), 'databasebackup_content_2' ), //backup switch
					array( __( 'Schedule Automated Backups', $this->hook ), 'databasebackup_content_3' ), //scheduled backup options
					array( __( 'Download Backups', $this->hook ), 'databasebackup_content_4' ) //where to find downloads
				)
			);
		}
		
		/**
		 * Registers content blocks for database prefix page
		 *
		 **/
		function admin_databaseprefix() {
			$this->admin_page( $this->pluginname . ' - ' . __( 'Change Database Prefix', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'databaseprefix_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Change The Database Prefix', $this->hook ), 'databaseprefix_content_2' ) //databaseprefix options
				)
			);
		}
		
		/**
		 * Registers content blocks for hide backend page
		 *
		 **/
		function admin_hidebackend() {
			$this->admin_page( $this->pluginname . ' - ' . __( 'Hide WordPress Backend', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'hidebackend_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Hide Backend Options', $this->hook ), 'hidebackend_content_2' ), //hidebackend options
					array( __( 'Secret Key', $this->hook ), 'hidebackend_content_3' ) //hidebackend secret key information 
				)
			);
		}
		
		/**
		 * Registers content blocks for intrusion detection page
		 *
		 **/
		function admin_intrusiondetection() {
			$this->admin_page( $this->pluginname . ' - ' . __( 'Intrusion Detection', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'intrusiondetection_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Intrusion Detection', $this->hook ), 'intrusiondetection_content_2' ) //intrusiondetection options
				)
			);
		}
		
		/**
		 * Registers content blocks for login limits page
		 *
		 **/
		function admin_loginlimits() {
			$this->admin_page( $this->pluginname . ' - ' . __( 'Limit Login Attempts', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'loginlimits_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Limit Login Attempts', $this->hook ), 'loginlimits_content_2' ) //loginlimit options
				)
			);
		}
		
		/**
		 * Registers content blocks for system tweaks page
		 *
		 **/
		function admin_systemtweaks() {
			$this->admin_page( $this->pluginname . ' - ' . __( 'Various Security Tweaks', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'systemtweaks_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Server Tweaks', $this->hook ), 'systemtweaks_content_2' ), //systemtweaks htaccess (or other rewrite) options
					array( __( 'Other Tweaks', $this->hook ), 'systemtweaks_content_3' ) //systemtweaks other options
					
				)
			);
		}
		
		/**
		 * Registers content blocks for view logs page
		 *
		 **/
		function admin_logs() {
			$this->admin_page( $this->pluginname . ' - ' . __( 'Better WP Security Logs', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'logs_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Clean Database', $this->hook ), 'logs_content_2' ), //Clean Database
					array( __( 'Current Lockouts', $this->hook ), 'logs_content_3' ), //Current Lockouts log
					array( __( '404 Errors', $this->hook ), 'logs_content_4' ) //404 Errors
				)
			);
		}
		
		/**
		 * Dashboard intro prior to first backup
		 *
		 **/
		function dashboard_content_1() {
			?>
			<p><?php _e( 'Welcome to Better WP Security!', $this->hook ); ?></p>
			<p><?php echo __( 'Before we begin it is extremely important that you make a backup of your database. This will make sure you can get your site back to the way it is right now should something go wrong. Click the button below to make a backup which will be emailed to the website administrator at ', $this->hook ) . '<strong>' . get_option( 'admin_email' ) . '</strong>'; ?></p>
			<form method="post" action="">
				<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
				<input type="hidden" name="bwps_page" value="dashboard_1" />
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Create Database Backup', $this->hook ) ?>" /></p>			
			</form>
			<form method="post" action="">
				<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
				<input type="hidden" name="bwps_page" value="dashboard_2" />
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'No, thanks. I already have a backup', $this->hook ) ?>" /></p>			
			</form>
			<?php
		}
		
		/**
		 * One-click mode
		 *
		 * Information and form to turn on basic security with 1-click
		 *
		 **/
		function dashboard_content_2() {
			$options = get_option( $this->primarysettings );
			if ( $options['backup_enabled'] == 1 && $options['ll_enabled'] == 1 && $options['id_enabled'] == 1 && $options['st_ht_files'] == 1 && $options['st_ht_browsing'] == 1 && $options['st_generator'] == 1 && $options['st_manifest'] == 1 && $options['st_themenot'] == 1 && $options['st_pluginnot'] == 1 && $options['st_corenot'] == 1 && $options['st_enablepassword'] == 1 && $options['st_loginerror'] == 1 && $options['st_ht_request'] == 1 ) {
			?>
			<p><?php _e( 'Congratulations. Your site is secure from basic attacks. Please review the status items below and turn on as many remaining items as you safely can. Full descriptions for each option in this plugin can be found in the corresponding option page for that item.', $this->hook ); ?></p>
			<?php } else { ?>
				<form method="post" action="">
					<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
					<input type="hidden" name="bwps_page" value="dashboard_3" />
					<p><?php _e( 'The button below will turn on all the basic features of Better WP Security which will help automatically protect your site from potential attacks. Please note that it will NOT automatically activate any features which may interfere with other plugins, themes, or content on your site. As such, not all the items in the status will turn green by using the "Secure My Site From Basic Attacks" button. The idea is to activate basic features in one-click so you don\'t have to worry about it.', $this->hook ); ?></p>
					<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Secure My Site From Basic Attacks', $this->hook ) ?>" /></p>			
				</form>
			<?php } ?>
			<?php
		}
		
		/**
		 * Better WP Security System Status
		 *
		 **/
		function dashboard_content_3() {
			global $wpdb;
			
			$options = get_option( $this->primarysettings );
			?>
			<ol>
				<li>
					<?php 
						$isOn = $options['st_enablepassword'];
						$role = $options['st_passrole']; 
					?>
					<?php if ( $isOn == 1 && $role == 'subscriber' ) { ?>
						<span style="color: green;"><?php _e( 'You are enforcing strong passwords for all users.', $this-> hook ); ?></span>
					<?php } elseif ( $isOn == 1 ) { ?>
						<span style="color: orange;"><?php _e( 'You are enforcing strong passwords, but not for all users.', $this-> hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this-> hook ); ?></a></span>					
					<?php } else { ?>
						<span style="color: red;"><?php _e( 'You are not enforcing strong passwords.', $this-> hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this-> hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php $hcount = intval( $options['st_manifest'] ) + intval( $options['st_generator'] ) + intval( $options['st_edituri'] ); ?>
					<?php if ( $hcount == 3 ) { ?>
						<span style="color: green;"><?php _e( 'Your Wordpress header is revealing as little information as possible.', $this-> hook ); ?></span>
					<?php } elseif ( $hcount > 0 ) { ?>
						<span style="color: orange;"><?php _e( 'Your Wordpress header is still revealing some information to users.', $this-> hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this-> hook ); ?></a></span>					
					<?php } else { ?>
						<span style="color: red;"><?php _e( 'Your Wordpress header is showing too much information to users.', $this-> hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this-> hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php $hcount = intval( $options['st_themenot'] ) + intval( $options['st_pluginnot'] ) + intval( $options['st_corenot'] ); ?>
					<?php if ( $hcount == 3 ) { ?>
						<span style="color: green;"><?php _e( 'Non-administrators cannot see available updates.', $this-> hook ); ?></span>
					<?php } elseif ( $hcount > 0 ) { ?>
						<span style="color: orange;"><?php _e( 'Non-administrators can see some updates.', $this-> hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this-> hook ); ?></a></span>					
					<?php } else { ?>
						<span style="color: red;"><?php _e( 'Non-administrators can see all updates.', $this-> hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this-> hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php $adminUser = $wpdb->get_var( "SELECT user_login FROM `" . $wpdb->users . "` WHERE user_login='admin';" ); ?>
					<?php if ( $adminUser =="admin" ) { ?>
						<span style="color: red;"><?php _e( 'The <em>admin</em> user still exists.', $this-> hook ); ?> <a href="admin.php?page=better_wp_security-adminuser"><?php _e( 'Click here to rename admin.', $this-> hook ); ?></a></span>
					<?php } else { ?>
						<span style="color: green;"><?php _e( 'The <em>admin</em> user has been removed.', $this-> hook ); ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if ( $wpdb->base_prefix == 'wp_' ) { ?>
						<span style="color: red;"><?php _e( 'Your table prefix should not be ', $this->hook ); ?><em>wp_</em>. <a href="admin.php?page=better_wp_security-databaseprefix"><?php _e( 'Click here to rename it.', $this->hook ); ?></a></span>
					<?php } else { ?>
						<span style="color: green;"><?php echo __( 'Your table prefix is', $this->hook ) . ' ' . $wpdb->base_prefix; ?></span>
					<?php } ?>
				</li>
				<li>
					<?php if ( $options['backup_enabled'] == 1 ) { ?>
						<span style="color: green;"><?php _e( 'You have scheduled regular backups of your WordPress database.', $this->hook ); ?></span>
					<?php } else { ?>
						<span style="color: orange;"><?php _e( 'You are not scheduling regular backups of your WordPress database.', $this->hook ); ?> <a href="admin.php?page=better_wp_security-databasebackup"><?php _e( 'Click here to fix.', $this->hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php if ( $options['am_enabled'] == 1 ) { ?>
						<span style="color: green;"><?php _e( 'Your Wordpress admin area is not available when you will not be needing it.', $this->hook ); ?>. </span>
					<?php } else { ?>
						<span style="color: orange;"><?php _e( 'Your Wordpress admin area is available 24/7. Do you really update 24 hours a day?', $this->hook ); ?> <a href="admin.php?page=better_wp_security-awaymode"><?php _e( 'Click here to fix.', $this->hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php if ( $options['ll_enabled'] == 1 ) { ?>
						<span style="color: green;"><?php _e( 'Your login area is protected from brute force attacks.', $this->hook ); ?></span>
					<?php } else { ?>
						<span style="color: red;"><?php _e( 'Your login area is not protected from brute force attacks.', $this->hook ); ?> <a href="admin.php?page=better_wp_security-loginlimits"><?php _e( 'Click here to fix.', $this->hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php if ( $options['hb_enabled'] == 1 ) { ?>
						<span style="color: green;"><?php _e( 'Your Wordpress admin area is hidden.', $this->hook ); ?></span>
					<?php } else { ?>
						<span style="color: blue;"><?php _e( 'Your Wordpress admin area is not hidden.', $this->hook ); ?> <a href="admin.php?page=better_wp_security-hidebackend"><?php _e( 'Click here to fix.', $this->hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php $hcount = intval( $options['st_ht_files'] ) + intval( $options['st_ht_browsing'] ) + intval( $options['st_ht_request'] ) + intval( $options['st_ht_query'] ); ?>
					<?php if ( $hcount == 4 ) { ?>
						<span style="color: green;"><?php _e( 'Your .htaccess file is fully secured.', $this-> hook ); ?></span>
					<?php } elseif ( $hcount > 0 ) { ?>
						<span style="color: blue;"><?php _e( 'Your .htaccess file is partially secured.', $this-> hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this-> hook ); ?></a></span>					
					<?php } else { ?>
						<span style="color: red;"><?php _e( 'Your .htaccess file is NOT secured.', $this-> hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this-> hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php if ( $options['id_enabled'] == 1 ) { ?>
						<span style="color: green;"><?php _e( 'Your installation is actively blocking attackers trying to scan your site for vulnerabilities.', $this->hook ); ?></span>
					<?php } else { ?>
						<span style="color: red;"><?php _e( 'Your installation is not actively blocking attackers trying to scan your site for vulnerabilities.', $this->hook ); ?> <a href="admin.php?page=better_wp_security-intrusiondetection"><?php _e( 'Click here to fix.', $this->hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php if ( $options['st_longurl'] == 1 ) { ?>
						<span style="color: green;"><?php _e( 'Your installation does not accept long URLs.', $this->hook ); ?></span>
					<?php } else { ?>
						<span style="color: blue;"><?php _e( 'Your installation accepts long (over 255 character) URLS. This can lead to vulnerabilities.', $this->hook ); ?> <a href="admin.php?page=better_wp_security-contentdirectory"><?php _e( 'Click here to fix.', $this->hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php if ( $options['st_fileedit'] == 1 ) { ?>
						<span style="color: green;"><?php _e( 'You are not allowing users to edit theme and plugin files from the Wordpress backend.', $this->hook ); ?></span>
					<?php } else { ?>
						<span style="color: blue;"><?php _e( 'You are allowing users to edit theme and plugin files from the Wordpress backend.', $this->hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this->hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php if ( $options['st_randomversion'] == 1 ) { ?>
						<span style="color: green;"><?php _e( 'Version information is obscured to all non admin users.', $this->hook ); ?></span>
					<?php } else { ?>
						<span style="color: blue;"><?php _e( 'Users may still be able to get version information from various plugins and themes.', $this->hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this->hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php if ( ! strstr( WP_CONTENT_DIR, 'wp-content' ) || ! strstr( WP_CONTENT_URL, 'wp-content' ) ) { ?>
						<span style="color: green;"><?php _e( 'You have renamed the wp-content directory of your site.', $this->hook ); ?></span>
					<?php } else { ?>
						<span style="color: blue;"><?php _e( 'You should rename the wp-content directory of your site.', $this->hook ); ?> <a href="admin.php?page=better_wp_security-contentdirectory"><?php _e( 'Click here to do so.', $this->hook ); ?></a></span>
					<?php } ?>
				</li>
				<li>
					<?php if ( FORCE_SSL_LOGIN === true && FORCE_SSL_ADMIN === true ) { ?>
						<span style="color: green;"><?php _e( 'You are requiring a secure connection for logins and the admin area.', $this-> hook ); ?></span>
					<?php } elseif ( FORCE_SSL_LOGIN === true || FORCE_SSL_ADMIN === true ) { ?>
						<span style="color: blue;"><?php _e( 'You are requiring a secure connection for logins or the admin area but not both.', $this-> hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this-> hook ); ?></a></span>	
					<?php } else { ?>
						<span style="color: blue;"><?php _e( 'You are not requiring a secure connection for logins or for the admin area.', $this-> hook ); ?> <a href="admin.php?page=better_wp_security-systemtweaks"><?php _e( 'Click here to fix.', $this-> hook ); ?></a></span>
					<?php } ?>
				</li>
			</ol>
			<hr />
			<ul>
				<li><span style="color: green;"><?php _e( 'Items in green are fully secured. Good Job!', $this->hook ); ?></span></li>
				<li><span style="color: orange;"><?php _e( 'Items in orange are partially secured. Turn on more options to fully secure these areas.', $this->hook ); ?></span></li>
				<li><span style="color: red;"><?php _e( 'Items in red are not secured. You should secure these items immediately', $this->hook ); ?></span></li>
				<li><span style="color: blue;"><?php _e( 'Items in blue are not fully secured but may conflict with other themes, plugins, or the other operation of your site. Secure them on if you can but if you cannot do not worry about them.', $this->hook ); ?></span></li>
			</ul>
			<?php
		}
		
		/**
		 * Rewrite rules
		 *
		 * Rewrite rules generated by better wp security
		 *
		 **/
		function dashboard_content_4() {
			
			$rules = $this->getrules();
			
			if ( $rules == '') {
				?>
				<p><?php _e( 'No rules have been generated. Turn on more features to see rewrite rules.', $this->hook ); ?></p>
				<?php
			} else {
				?>
				<style type="text/css">
					code {
						 overflow-x: auto; /* Use horizontal scroller if needed; for Firefox 2, not needed in Firefox 3 */
						 overflow-y: hidden;
						 background-color: transparent;
						 white-space: pre-wrap; /* css-3 */
						 white-space: -moz-pre-wrap !important; /* Mozilla, since 1999 */
						 white-space: -pre-wrap; /* Opera 4-6 */
						 white-space: -o-pre-wrap; /* Opera 7 */
						 /* width: 99%; */
						 word-wrap: break-word; /* Internet Explorer 5.5+ */
						 
					}
				</style>
				<?php echo highlight_string( $rules, true ); ?>
				<?php
			}
			
		}
		
		/**
		 * General System Information
		 *
		 **/
		function dashboard_content_5() {
			global $wpdb;
			$options = get_option( $this->primarysettings );
			?>
			<ul>
				<li>
					<h4><?php _e( 'User Information', $this->hook ); ?></h4>
					<ul>
						<li><?php _e( 'Public IP Address', $this->hook ); ?>: <strong><a target="_blank" title="<?php _e( 'Get more information on this address', $this->hook ); ?>" href="http://whois.domaintools.com/<?php echo $_SERVER['REMOTE_ADDR']; ?>"><?php echo $_SERVER['REMOTE_ADDR']; ?></a></strong></li>
						<li><?php _e( 'User Agent', $this->hook ); ?>: <strong><?php echo $_SERVER['HTTP_USER_AGENT']; ?></strong></li>
					</ul>
				</li>
				
				<li>
					<h4><?php _e( 'File System Information', $this->hook ); ?></h4>
					<ul>
						<li><?php _e( 'Website Root Folder', $this->hook ); ?>: <strong><?php echo get_site_url(); ?></strong></li>
						<li><?php _e( 'Document Root Path', $this->hook ); ?>: <strong><?php echo $_SERVER['DOCUMENT_ROOT']; ?></strong></li>
						<?php 
							$htaccess = ABSPATH . '.htaccess';
							@chmod( $htaccess, 0644 );
							
							if ( $f = fopen( $htaccess, 'a' ) ) { 
							
								fclose( $f );
								$copen = '';
								$cclose = '';
								$htaw = __( 'Yes', $this->hook ); 
								
							} else {
							
								$copen = '<font color="red">';
								$cclose = '</font>';
								$htaw = __( 'No. Better WP Security will be severely limited in it\'s ability to secure your site', $this->hook ); 
								
							}
							
							@chmod( $htaccess, 0444 );
						?>
						<li><?php _e( '.htaccess File is Writable', $this->hook ); ?>: <strong><?php echo $copen . $htaw . $cclose; ?></strong></li>
						<?php 
							$conffile = $this->getConfig();
							@chmod( $conffile, 0644 );
							
							if ( $f = fopen( $conffile, 'a' ) ) { 
							
								fclose( $f );
								$copen = '';
								$cclose = '';
								$wconf = __( 'Yes', $this->hook ); 
								
							} else {
							
								$copen = '<font color="red">';
								$cclose = '</font>';
								$wconf = __( 'No. Better WP Security will be severely limited in it\'s ability to secure your site', $this->hook ); 
								
							}
							
							@chmod( $conffile, 0444 );
						?>
						<li><?php _e( 'wp-config.php File is Writable', $this->hook ); ?>: <strong><?php echo $copen . $wconf . $cclose; ?></strong></li>
					</ul>
				</li>
			
				<li>
					<h4><?php _e( 'Database Information', $this->hook ); ?></h4>
					<ul>
						<li><?php _e( 'MySQL Database Version', $this->hook ); ?>: <?php $sqlversion = $wpdb->get_var( "SELECT VERSION() AS version" ); ?><strong><?php echo $sqlversion; ?></strong></li>
						<li><?php _e( 'MySQL Client Version', $this->hook ); ?>: <strong><?php echo mysql_get_client_info(); ?></strong></li>
						<li><?php _e( 'Database Host', $this->hook ); ?>: <strong><?php echo DB_HOST; ?></strong></li>
						<li><?php _e( 'Database Name', $this->hook ); ?>: <strong><?php echo DB_NAME; ?></strong></li>
						<li><?php _e( 'Database User', $this->hook ); ?>: <strong><?php echo DB_USER; ?></strong></li>
						<?php $mysqlinfo = $wpdb->get_results( "SHOW VARIABLES LIKE 'sql_mode'" );
							if ( is_array( $mysqlinfo ) ) $sql_mode = $mysqlinfo[0]->Value;
							if ( empty( $sql_mode ) ) $sql_mode = __( 'Not Set', $this->hook );
							else $sql_mode = __( 'Off', $this->hook );
						?>
						<li><?php _e( 'SQL Mode', $this->hook ); ?>: <strong><?php echo $sql_mode; ?></strong></li>
					</ul>
				</li>
				
				<li>
					<h4><?php _e( 'Server Information', $this->hook ); ?></h4>
					<ul>
						<li><?php _e( 'Server / Website IP Address', $this->hook ); ?>: <strong><a target="_blank" title="<?php _e( 'Get more information on this address', $this->hook ); ?>" href="http://whois.domaintools.com/<?php echo $_SERVER['SERVER_ADDR']; ?>"><?php echo $_SERVER['SERVER_ADDR']; ?></a></strong></li>
							<li><?php _e( 'Server Type', $this->hook ); ?>: <strong><?php echo $_SERVER['SERVER_SOFTWARE']; ?></strong></li>
							<li><?php _e( 'Operating System', $this->hook ); ?>: <strong><?php echo PHP_OS; ?></strong></li>
							<li><?php _e( 'Browser Compression Supported', $this->hook ); ?>: <strong><?php echo $_SERVER['HTTP_ACCEPT_ENCODING']; ?></strong></li>
					</ul>
				</li>
				
				<li>
					<h4><?php _e( 'PHP Information', $this->hook ); ?></h4>
					<ul>
						<li><?php _e( 'PHP Version', $this->hook ); ?>: <strong><?php echo PHP_VERSION; ?></strong></li>
						<li><?php _e( 'PHP Memory Usage', $this->hook ); ?>: <strong><?php echo round(memory_get_usage() / 1024 / 1024, 2) . __( ' MB', $this->hook ); ?></strong> </li>
						<?php 
							if ( ini_get( 'memory_limit' ) ) {
								$memory_limit = ini_get( 'memory_limit' ); 
							} else {
								$memory_limit = __( 'N/A', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Memory Limit', $this->hook ); ?>: <strong><?php echo $memory_limit; ?></strong></li>
						<?php 
							if ( ini_get( 'upload_max_filesize' ) ) {
								$upload_max = ini_get( 'upload_max_filesize' );
							} else 	{
								$upload_max = __( 'N/A', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Max Upload Size', $this->hook ); ?>: <strong><?php echo $upload_max; ?></strong></li>
						<?php 
							if ( ini_get( 'post_max_size' ) ) {
								$post_max = ini_get( 'post_max_size' );
							} else {
								$post_max = __( 'N/A', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Max Post Size', $this->hook ); ?>: <strong><?php echo $post_max; ?></strong></li>
						<?php 
							if ( ini_get( 'safe_mode' ) ) {
								$safe_mode = __( 'On', $this->hook );
							} else {
								$safe_mode = __( 'Off', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Safe Mode', $this->hook ); ?>: <strong><?php echo $safe_mode; ?></strong></li>
						<?php 
							if (ini_get( 'allow_url_fopen' ) ) {
								$allow_url_fopen = __( 'On', $this->hook );
							} else {
								$allow_url_fopen = __( 'Off', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Allow URL fopen', $this->hook ); ?>: <strong><?php echo $allow_url_fopen; ?></strong></li>
						<?php 
							if (ini_get( 'allow_url_include' ) ) {
								$allow_url_include = __( 'On', $this->hook );
							} else {
								$allow_url_include = __( 'Off', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Allow URL Include' ); ?>: <strong><?php echo $allow_url_include; ?></strong></li>
							<?php 
							if (ini_get( 'display_errors' ) ) {
								$display_errors = __( 'On', $this->hook );
							} else {
								$display_errors = __( 'Off', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Display Errors', $this->hook ); ?>: <strong><?php echo $display_errors; ?></strong></li>
						<?php 
							if (ini_get( 'display_startup_errors' ) ) {
								$display_startup_errors = __( 'On', $this->hook );
							} else {
								$display_startup_errors = __( 'Off', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Display Startup Errors', $this->hook ); ?>: <strong><?php echo $display_startup_errors; ?></strong></li>
						<?php 
							if (ini_get( 'expose_php' ) ) {
								$expose_php = __( 'On', $this->hook );
							} else {
								$expose_php = __( 'Off', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Expose PHP', $this->hook ); ?>: <strong><?php echo $expose_php; ?></strong></li>
						<?php 
							if (ini_get( 'register_globals' ) ) {
								$register_globals = __( 'On', $this->hook );
							} else {
								$register_globals = __( 'Off', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Register Globals', $this->hook ); ?>: <strong><?php echo $register_globals; ?></strong></li>
						<?php 
							if (ini_get( 'max_execution_time' ) ) {
								$max_execute = ini_get( 'max_execution_time' );
							} else {
								$max_execute = __( 'N/A', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Max Script Execution Time' ); ?>: <strong><?php echo $max_execute; ?> <?php _e( 'Seconds' ); ?></strong></li>
						<?php 
							if (ini_get( 'magic_quotes_gpc' ) ) {
								$magic_quotes_gpc = __( 'On', $this->hook );
							} else {
								$magic_quotes_gpc = __( 'Off', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Magic Quotes GPC', $this->hook ); ?>: <strong><?php echo $magic_quotes_gpc; ?></strong></li>
						<?php 
							if (ini_get( 'open_basedir' ) ) {
								$open_basedir = __( 'On', $this->hook );
							} else {
								$open_basedir = __( 'Off', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP open_basedir', $this->hook ); ?>: <strong><?php echo $open_basedir; ?></strong></li>
						<?php 
							if (is_callable( 'xml_parser_create' ) ) {
								$xml = __( 'Yes', $this->hook );
							} else {
								$xml = __( 'No', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP XML Support', $this->hook ); ?>: <strong><?php echo $xml; ?></strong></li>
						<?php 
							if (is_callable( 'iptcparse' ) ) {
								$iptc = __( 'Yes', $this->hook );
							} else {
								$iptc = __( 'No', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP IPTC Support', $this->hook ); ?>: <strong><?php echo $iptc; ?></strong></li>
						<?php 
							if (is_callable( 'exif_read_data' ) ) {
								$exif = __( 'Yes', $this->hook ). " ( V" . substr(phpversion( 'exif' ),0,4) . ")" ;
							} else {
								$exif = __( 'No', $this->hook ); 
							}
						?>
						<li><?php _e( 'PHP Exif Support', $this->hook ); ?>: <strong><?php echo $exif; ?></strong></li>
					</ul>
				</li>
				
				<li>
					<h4><?php _e( 'Wordpress Configuration', $this->hook ); ?></h4>
					<ul>
						<?php
							if ( is_multisite() ) { 
								$multSite = __( 'Multisite is enabled', $this->hook );
							} else {
								$multSite = __( 'Multisite is NOT enabled', $this->hook );
							}
							?>
							<li><?php _e( '	Multisite', $this->hook );?>: <strong><?php echo $multSite; ?></strong></li>
						<?php
							if ( get_option( 'permalink_structure' ) != '' ) { 
								$copen = '';
								$cclose = '';
								$permalink_structure = __( 'Enabled', $this->hook ); 
							} else {
								$copen = '<font color="red">';
								$cclose = '</font>';
								$permalink_structure = __( 'WARNING! Permalinks are NOT Enabled. Permalinks MUST be enabled for Better WP Security to function correctly', $this->hook ); 
							}
						?>
						<li><?php _e( 'WP Permalink Structure', $this->hook ); ?>: <strong> <?php echo $copen . $permalink_structure . $cclose; ?></strong></li>
						<li><?php _e( 'Wp-config Location', $this->hook );?>: <strong><?php echo $this->getConfig(); ?></strong></li>
					</ul>
				</li>
				<li>
					<h4><?php _e( 'Better WP Security variables', $this->hook ); ?></h4>
					<ul>
						<?php 
							if ( $options['hb_key'] == '' ) {
								$hbkey = __( 'Not Yet Available. Enable Hide Backend mode to generate key.', $this->hook );
							} else {
								$hbkey = $options['hb_key'];
							}
						?>
						<li><?php _e( 'Hide Backend Key', $this->hook );?>: <strong><?php echo $hbkey; ?></strong></li>
						<?php $options = get_option( $this->plugindata ); ?>
						<li><?php _e( 'Better WP Security Version', $this->hook );?>: <strong><?php echo $options['version']; ?></strong></li>
					</ul>
				</li>
			</ul>
			<?php
		}
	
		/**
		 * Intro content for change admin user page
		 *
		 **/
		function adminuser_content_1() {
			?>
			<p><?php _e( 'By default WordPress initially creates a username with the username of "admin." This is insecure as this user has full rights to your WordPress system and a potential hacker already knows that it is there. All an attacker would need to do at that point is guess the password. Changing this username will force a potential attacker to have to guess both your username and your password which makes some attacks significantly more difficult.', $this->hook ); ?></p>
			<p><?php _e( 'Note that this function will only work if you chose a username other than "admin" when installing WordPress.', $this->hook ); ?></p>
			<?php
		}
		
		/**
		 * Options form for change andmin user page
		 *
		 **/
		function adminuser_content_2() {
			if ( $this->user_exists( 'admin' ) ) { //only show form if user 'admin' exists
				?>
				<form method="post" action="">
					<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
					<input type="hidden" name="bwps_page" value="adminuser_1" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for "newuser"><?php _e( 'Enter Username', $this->hook ); ?></label>
							</th>
							<td>
								<?php //username field ?>
								<input id="newuser" name="newuser" type="text" />
								<p><?php _e( 'Enter a new username to replace "admin." Please note that if you are logged in as admin you will have to log in again.', $this->hook ); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
				</form>
				<?php
			} else { //if their is no admin user display a note 
				?>
					<p><?php _e( 'Congratulations! You do not have a user named "admin" in your WordPress installation. No further action is available on this page.', $this->hook ); ?></p>
				<?
			}
		}
		
		/**
		 * Intro content for away mode page
		 *
		 **/
		function awaymode_content_1() {
			?>
			<p><?php _e( 'As many of us update our sites on a general schedule it is not always necessary to permit site access all of the time. The options below will disable the backend of the site for the specified period. This could also be useful to disable site access based on a schedule for classroom or other reasons.', $this->hook ); ?></p>
			<p><?php _e( 'Please note that according to your', $this->hook ); ?> <a href="options-general.php"><?php _e( 'Wordpress timezone settings', $this->hook ); ?></a> <?php _e( 'your local time is', $this->hook ); ?> <strong><em><?php echo date( 'l, F jS, Y \a\\t g:i a', strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s',time() ) ) ) ); ?></em></strong>. <?php _e( 'If this is incorrect please correct it on the', $this->hook ); ?> <a href="options-general.php"><?php _e( 'Wordpress general settings page', $this->hook ); ?></a> <?php _e( 'by setting the appropriate time zone. Failure to do so may result in unintended lockouts.', $this->hook ); ?></p>
			<?php
		}
		
		/**
		 * Options form for away mode page
		 *
		 **/
		function awaymode_content_2() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="awaymode_1" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
			<?php 
				//get saved options
				$cDate = strtotime( date( 'n/j/y 12:00 \a\m', time() ) );
				$sTime = $options['am_starttime'];
				$eTime = $options['am_endtime'];
				$sDate = $options['am_startdate'];
				$eDate = $options['am_enddate'];
				$shdisplay = date( 'g', $sTime );
				$sidisplay = date( 'i', $sTime );
				$ssdisplay = date( 'a', $sTime );
				$ehdisplay = date( 'g', $eTime );
				$eidisplay = date( 'i', $eTime );
				$esdisplay = date( 'a', $eTime );
				
				if ( $options['am_enabled'] == 1 && $eDate > $cDate ) {	
				
					$smdisplay = date( 'n', $sDate );
					$sddisplay = date( 'j', $sDate );
					$sydisplay = date( 'Y', $sDate );
					
					$emdisplay = date( 'n', $eDate );
					$eddisplay = date( 'j', $eDate );
					$eydisplay = date( 'Y', $eDate );
					
				} else {
				
					$sDate = strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', time() + 86400) ) );
					$eDate = strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', time() + ( 86400 * 2 ) ) ) );
					$smdisplay = date( 'n', $sDate );
					$sddisplay = date( 'j', $sDate );
					$sydisplay = date( 'Y', $sDate );
					
					$emdisplay = date( 'n', $eDate );
					$eddisplay = date( 'j', $eDate );
					$eydisplay = date( 'Y', $eDate );
					
				}
			?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "am_enabled"><?php _e( 'Enable Away Mode', $this->hook ); ?></label>
						</th>
						<td>
							<input id="am_enabled" name="am_enabled" type="checkbox" value="1" <?php checked( '1', $options['am_enabled'] ); ?> />
							<p><?php _e( 'Check this box to enable away mode.', $this->hook ); ?></p>
						</td>
					</tr>	
					<tr valign="top">
						<th scope="row">
							<label for="am_type"><?php _e( 'Type of Restriction', $this->hook ); ?></label>
						</th>
						<td>
							<label><input name="am_type" id="am_type" value="1" <?php checked( '1', $options['am_type'] ); ?> type="radio" /> <?php _e( 'Daily', $this->hook ); ?></label>
							<label><input name="am_type" value="0" <?php checked( '0', $options['am_type'] ); ?> type="radio" /> <?php _e( 'One Time', $this->hook ); ?></label>
							<p><?php _e( 'Selecting <em>"One Time"</em> will lock out the backend of your site from the start date and time to the end date and time. Selecting <em>"Daily"</em> will ignore the start and and dates and will disable your site backend from the start time to the end time.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="am_startdate"><?php _e( 'Start Date', $this->hook ); ?></label>
						</th>
						<td>
							<select name="am_startmonth" id="am_startdate">
								<?php
									for ( $i = 1; $i <= 12; $i++ ) { //determine default
										if ( $smdisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . date( 'F', strtotime( $i . '/1/' . date( 'Y', time() ) ) ) . '</option>';
									}
								?>
							</select> 
							<select name="am_startday">
								<?php
									for ( $i = 1; $i <= 31; $i++ ) { //determine default
										if ( $sddisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . date( 'jS', strtotime( '1/' . $i . '/' . date( 'Y', time() ) ) ) . '</option>';
									}
								?>
							</select>, 
							<select name="am_startyear">
								<?php
									for ( $i = date( 'Y', time() ); $i < ( date( 'Y', time() ) + 2 ); $i++ ) { //determine default
										if ( $sydisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
									}
								?>
							</select>
							<p><?php _e( 'Select the date at which access to the backend of this site will be disabled. Note that if <em>"Daily"</em> mode is selected this field will be ignored and access will be banned every day at the specified time.', $this->hook ); ?>
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="am_enddate"><?php _e( 'End Date', $this->hook ); ?></label>
						</th>
						<td>
							<select name="am_endmonth" id="am_enddate">
								<?php
									for ( $i = 1; $i <= 12; $i++ ) { //determine default
										if ( $emdisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . date( 'F', strtotime( $i . '/1/' . date( 'Y', time() ) ) ) . '</option>';
									}
								?>
							</select> 
							<select name="am_endday">
								<?php
									for ( $i = 1; $i <= 31; $i++ ) { //determine default
										if ( $eddisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . date( 'jS', strtotime( '1/' . $i . '/' . date( 'Y', time() ) ) ) . '</option>';
									}
								?>
							</select>, 
							<select name="am_endyear">
								<?php
									for ( $i = date( 'Y', time() ); $i < ( date( 'Y', time() ) + 2 ); $i++ ) { //determine default
										if ( $eydisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
									}
								?>
							</select>
							<p><?php _e( 'Select the date at which access to the backend of this site will be re-enabled. Note that if <em>"Daily"</em> mode is selected this field will be ignored and access will be banned every day at the specified time.', $this->hook ); ?>
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="am_starttime"><?php _e( 'Start Time', $this->hook ); ?></label>
						</th>
						<td>
							<select name="am_starthour" id="am_starttime">
								<?php
									for ( $i = 1; $i <= 12; $i++ ) { //determine default
										if ( $shdisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
									}
								?>
							</select> : 
							<select name="am_startmin">
								<?php
									for ( $i = 0; $i < 60; $i++ ) { //determine default
										if ( $sidisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										if ( $i < 10 ) {
											$val = "0" . $i;
										} else {
											$val = $i;
										}
										echo '<option value="' . $val . '"' . $selected . '>' . $val . '</option>';
									}
								?>
							</select> 
							<select name="am_starthalf">											
								<option value="am"<?php if ( $ssdisplay == 'am' ) echo ' selected'; ?>>am</option>
								<option value="pm"<?php if ( $ssdisplay == 'pm' ) echo ' selected'; ?>>pm</option>
							</select>
							<p><?php _e( 'Select the time at which access to the backend of this site will be disabled. Note that if <em>"Daily"</em> mode is selected access will be banned every day at the specified time.', $this->hook ); ?>
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="am_endtime"><?php _e( 'End Time', $this->hook ); ?></label>
						</th>
						<td>
							<select name="am_endhour" id="am_endtime">
								<?php
									for ( $i = 1; $i <= 12; $i++ ) {//determine default
										if ( $ehdisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
									}
								?>
							</select> : 
							<select name="am_endmin">
								<?php
									for ( $i = 0; $i < 60; $i++ ) { //determine default
										if ( $eidisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										if ( $i < 10 ) {
											$val = "0" . $i;
										} else {
											$val = $i;
										}
										echo '<option value="' . $val . '"' . $selected . '>' . $val . '</option>';
									}
								?>
							</select> 
							<select name="am_endhalf">											
								<option value="am"<?php if ( $esdisplay == 'am' ) echo ' selected'; ?>>am</option>
								<option value="pm"<?php if ( $esdisplay == 'pm' ) echo ' selected'; ?>>pm</option>
							</select>
							<p><?php _e( 'Select the time at which access to the backend of this site will be re-enabled. Note that if <em>"Daily"</em> mode is selected access will be banned every day at the specified time.', $this->hook ); ?>
							</p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		/**
		 * Selection summary block for away mode page
		 *
		 **/
		function awaymode_content_3() {

			$options = get_option( $this->primarysettings ); //use settings fields 
			
			//format times for display
			if ( $options['am_type'] == 1 ) {
			
				$freq = ' <strong><em>' . __( 'every day' ) . '</em></strong>';
				$stime = '<strong><em>' . date( 'g:i a', $options['am_starttime'] ) . '</em></strong>';
				$etime = '<strong><em>' . date( 'g:i a', $options['am_endtime'] ) . '</em></strong>';
				
			} else {
			
				$freq = '';
				$stime = '<strong><em>' . date( 'l, F jS, Y', $options['am_startdate'] ) . __( ' at ', $this->hook ) . date( 'g:i a', $options['am_starttime'] ) . '</em></strong>';
				$etime = '<strong><em>' . date( 'l, F jS, Y', $options['am_enddate'] ) . __( ' at ', $this->hook ) . date( 'g:i a', $options['am_endtime'] ) . '</em></strong>';
				
			}
			
			if ( $options['am_enabled'] == 1 ) {
				?>
				<p style="font-size: 150%; text-align: center;"><?php _e( 'The backend (administrative section) of this site will be unavailable', $this->hook ); ?><?php echo $freq; ?> <?php _e( 'from', $this->hook ); ?> <?php echo $stime; ?> <?php _e( 'until', $this->hook ); ?> <?php echo $etime; ?>.</p>
				<?php } else { ?>
					<p><?php _e( 'Away mode is currently diabled', $this->hook ); ?></p>
				<?php
			}	
		}
		
		/**
		 * Intro block for ban hosts page
		 *
		 **/
		function banusers_content_1() {
			?>
			<p><?php _e( 'This feature allows you to ban hosts and user agents from your site completely using individual or groups of IP addresses as well as user agents without having to manage any configuration of your server. Any IP or user agent found in the lists below will not be allowed any access to your site.', $this->hook ); ?></p>
			<p><?php _e( 'Please note banning ip address ranges works using the WordPress database and PHP in order to keep it simple to use. That said, it is not nearly as effecient as banning hosts via your server configuration. I recommend keeping the list here short or using it only for temporary bans to avoid performance issues. This applies only to ip address ranges as individual ip addresses and user agents are added directly to your site\'s .htaccess file or NGINX rewrite rules.', $this->hook ); ?></p>
			<?php
		}
		
		/**
		 * Options form for ban hosts page
		 *
		 **/
		function banusers_content_2() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="banusers_1" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "bu_enabled"><?php _e( 'Enable Banned Users', $this->hook ); ?></label>
						</th>
						<td>
							<input id="bu_enabled" name="bu_enabled" type="checkbox" value="1" <?php checked( '1', $options['bu_enabled'] ); ?> />
							<p><?php _e( 'Check this box to enable the banned users feature.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "bu_banrange"><?php _e( 'Ban Hosts', $this->hook ); ?></label>
						</th>
						<td>
							<textarea id="bu_banrange" rows="10" cols="50" name="bu_banrange"><?php echo isset( $_POST['bu_banrange'] ) ? $_POST['bu_banrange'] : $options['bu_banrange'] . "\n" . $options['bu_individual']; ?></textarea>
							<p><?php _e( 'Use the guidelines below to enter hosts that will not be allowed access to your site. Note you cannot ban yourself.', $this->hook ); ?></p>
							<ul><em>
								<li><?php _e( 'You may ban users by individual IP address or IP address range.', $this->hook ); ?></li>
								<li><?php _e( 'Individual IP addesses must be in IPV4 standard format (i.e. ###.###.###.###). Wildcards (*) are allowed to specify a range of ip addresses.', $this->hook ); ?></li>
								<li><?php _e( 'IP Address ranges may also be specified using the format ###.###.###.### - ###.###.###.###. Wildcards cannot be used in addresses specified like this.', $this->hook ); ?></li>
								<li><a href="http://ip-lookup.net/domain-lookup.php" target="_blank"><?php _e( 'Lookup IP Address.', $this->hook ); ?></a></li>
								<li><?php _e( 'Enter only 1 IP address or 1 IP address range per line.', $this->hook ); ?></li>
							</em></ul>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "bu_banrange"><?php _e( 'Ban User Agents', $this->hook ); ?></label>
						</th>
						<td>
							<textarea id="bu_banrange" rows="10" cols="50" name="bu_banagent"><?php echo isset( $_POST['bu_banrange'] ) ? $_POST['bu_banagent'] : $options['bu_banagent']; ?></textarea>
							<p><?php _e( 'Use the guidelines below to enter user agents that will not be allowed access to your site.', $this->hook ); ?></p>
							<ul><em>
								<li><?php _e( 'Enter only 1 user agent per line.', $this->hook ); ?></li>
							</em></ul>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		/**
		 * Intro block for change content directory page 
		 *
		 **/
		function contentdirectory_content_1() {
			?>
			<p><?php _e( 'By default WordPress puts all your content including images, plugins, themes, uploads, and more in a directory called "wp-content". This makes it easy to scan for vulnerable files on your WordPress installation as an attacker already knows where the vulnerable files will be at. As there are many plugins and themes with security vulnerabilities moving this folder can make it harder for an attacker to find problems with your site as scans of your site\'s file system will not produce any results.', $this->hook ); ?></p>
			<p><?php _e( 'Please note that changing the name of your wp-content directory on a site that already has images and other content referencing it will break your site. For that reason I highly recommend you do not try this on anything but a fresh WordPress install. In addition, this tool will not allow further changes to your wp-content folder once it has already been renamed in order to avoid accidently breaking a site later on. This includes uninstalling this plugin which will not revert the changes made by this page.', $this->hook ); ?></p>
			<p><?php _e( 'Finally, changing the name of the wp-content directory may in fact break plugins and themes that have "hard-coded" it into their design rather than call it dynamically.', $this->hook ); ?></p>
			<p style="text-align: center; font-size: 130%; font-weight: bold; color: #ff0000;"><?php _e( 'WARNING: BACKUP YOUR WORDPRESS INSTALLATION BEFORE USING THIS TOOL!', $this->hook ); ?></p>
			<p style="text-align: center; font-size: 130%; font-weight: bold; color: #ff0000;"><?php _e( 'RENAMING YOUR wp-content WILL BREAK LINKS ON A SITE WITH EXISTING CONTENT.', $this->hook ); ?></p>
			<?php
		}
		
		/**
		 * Options form for change content directory page
		 *
		 **/
		function contentdirectory_content_2() {
			if ( ! isset( $_POST['bwps_page'] ) && strpos( WP_CONTENT_DIR, 'wp-content' ) ) { //only show form if user the content directory hasn't already been changed
				?>
				<form method="post" action="">
					<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
					<input type="hidden" name="bwps_page" value="contentdirectory_1" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for "dirname"><?php _e( 'Directory Name', $this->hook ); ?></label>
							</th>
							<td>
								<?php //username field ?>
								<input id="dirname" name="dirname" type="text" value="wp-content" />
								<p><?php _e( 'Enter a new directory name to replace "wp-content." You may need to log in again after performing this operation.', $this->hook ); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
				</form>
				<?php
			} else { //if their is no admin user display a note 
				if ( isset( $_POST['bwps_page'] ) ) {
					$dirname = $_POST['dirname'];
				} else {
					$dirname = substr( WP_CONTENT_DIR, strrpos( WP_CONTENT_DIR, '/' ) + 1 );
				}
				?>
					<p><?php _e( 'Congratulations! You have already renamed your "wp-content" directory.', $this->hook ); ?></p>
					<p><?php _e( 'Your current content directory is: ', $this->hook ); ?><strong><?php echo $dirname ?></strong></p>
					<p><?php _e( 'No further actions are available on this page.', $this->hook ); ?></p>
				<?
			}
		}
		
		/**
		 * Intro block for database backup page
		 *
		 **/
		function databasebackup_content_1() {
			?>
			<p><?php _e( 'While this plugin goes a long way to helping secure your website nothing can give you a 100% guarantee that your site won\'t be the victim of an attack. When something goes wrong one of the easiest ways of getting your site back is to restore the database from a backup and replace the files with fresh ones. Use the button below to create a full backup of your database for this purpose. You can also schedule automated backups and download or delete previous backups.', $this->hook ); ?></p>
			<?php		
		}
		
		/**
		 * Spot backup form for database backup page
		 *
		 **/
		function databasebackup_content_2() {
			?>
			<form method="post" action="">
				<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
				<input type="hidden" name="bwps_page" value="databasebackup_1" />
				<p><?php _e( 'Press the button below to create a backup of your WordPress database. If you have "Send Backups By Email" selected in automated backups you will receive an email containing the backup file.', $this->hook ); ?></p>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Create Database Backup', $this->hook ) ?>" /></p>			
			</form>
			<?php
		}	
		
		/**
		 * Options form for database backup page
		 *
		 **/
		function databasebackup_content_3() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="databasebackup_2" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "backup_enabled"><?php _e( 'Enable Scheduled Backups', $this->hook ); ?></label>
						</th>
						<td>
							<input id="backup_enabled" name="backup_enabled" type="checkbox" value="1" <?php checked( '1', $options['backup_enabled'] ); ?> />
							<p><?php _e( 'Check this box to enable scheduled backups which will be emailed to the address below.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "backup_int"><?php _e( 'Backup Interval', $this->hook ); ?></label>
						</th>
						<td>
							<select id="backup_int" name="backup_int">
								<option value="hourly" <?php selected( $options['backup_int'], 'hourly' ); ?>>Hourly</option>
								<option value="twicedaily" <?php selected( $options['backup_int'], 'twicedaily' ); ?>>Twice Daily</option>
								<option value="daily" <?php selected( $options['backup_int'], 'daily' ); ?>>Daily</option>
							</select>
							<p><?php _e( 'Select the frequency of automated backups.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "backup_email"><?php _e( 'Send Backups by Email', $this->hook ); ?></label>
						</th>
						<td>
							<input id="backup_email" name="backup_email" type="checkbox" value="1" <?php checked( '1', $options['backup_email'] ); ?> />
							<p><?php _e( 'Email backups to the current site admin.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "backups_to_retain"><?php _e( 'Backups to Keep', $this->hook ); ?></label>
						</th>
						<td>
							<input id="backups_to_retain" name="backups_to_retain" type="text" value="<?php echo $options['backups_to_retain']; ?>" />
							<p><?php _e( 'Number of backup files to retain. Enter 0 to keep all files.', $this->hook ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		/**
		 * Backup location information for database backup page
		 *
		 **/
		function databasebackup_content_4() {
			$options = get_option( $this->primarysettings );
			if ( $options['backup_email'] == 1 ) { //emailing so let them know
				?>
				<p><?php echo __( 'Database backups are NOT saved to the server and instead will be emailed to', $this->hook ) . ' <strong>' . get_option( 'admin_email' ) . '</strong>. ' . __( 'To change this unset "Send Backups by Email" in the "Scheduled Automated Backups" section above.', $this->hook ); ?></p>
				<?php
			} else { //saving to disk so let them know where
				?>
				<p><?php _e( 'Please note that for security backups are not available for direct download. You will need to go to ', $this->hook ); ?></p>
				<p><strong><em><?php echo BWPS_PP . 'lib/phpmysqlautobackup/backups'; ?></em></strong></p>
				<p><?php _e( ' via FTP or SSH to download the files. This is because there is too much sensative information in the backup files and you do not want anyone just stumbling upon them.', $this->hook ); ?></p>
				<?php
			}
		}
		
		/**
		 * Intro box for change database prefix page
		 *
		 **/
		function databaseprefix_content_1() {
			?>
			<p><?php _e( 'By default WordPress assigns the prefix "wp_" to all the tables in the database where your content, users, and objects live. For potential attackers this means it is easier to write scripts that can target WordPress databases as all the important table names for 95% or so of sites are already known. Changing this makes it more difficult for tools that are trying to take advantage of vulnerabilites in other places to affect the database of your site.', $this->hook ); ?></p>
			<p><?php _e( 'Please note that the use of this tool requires quite a bit of system memory which my be more than some hosts can handle. If you back your database up you can\'t do any permanent damage but without a proper backup you risk breaking your site and having to perform a rather difficult fix.', $this->hook ); ?></p>
			<p style="text-align: center; font-size: 130%; font-weight: bold; color: blue;"><?php _e( 'WARNING: <a href="?page=better_wp_security-databasebackup">BACKUP YOUR DATABASE</a> BEFORE USING THIS TOOL!', $this->hook ); ?></p>
			<?php
		}
		
		/**
		 * Options form for change database prefix page
		 *
		 **/
		function databaseprefix_content_2() {
			global $wpdb;
			?>
			<?php if ( $wpdb->base_prefix == 'wp_' ) { //using default table prefix ?>
				<p><strong><?php _e( 'Your database is using the default table prefix', $this->hook ); ?> <em>wp_</em>. <?php _e( 'You should change this.', $this->hook ); ?></strong></p>
			<?php } else { ?>
				<p><?php _e( 'Your current database table prefix is', $this->hook ); ?> <strong><em><?php echo $wpdb->base_prefix; ?></em></strong></p>
			<?php } ?>
			<form method="post" action="">
				<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
				<input type="hidden" name="bwps_page" value="databaseprefix_1" />
				<p><?php _e( 'Press the button below to generate a random database prefix value and update all of your tables accordingly.', $this->hook ); ?></p>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Change Database Table Prefix', $this->hook ) ?>" /></p>			
			</form>
			<?php
		}
		
		/**
		 * Intro block for hide backend page
		 *
		 **/
		function hidebackend_content_1() {
			?>
			<p><?php _e( 'The "hide backend" feature changes the URL from which you can access your WordPress backend thereby further obscuring your site to potential attackers.', $this->hook); ?></p>
			<p><?php _e( 'This feature will need to modify your site\'s .htaccess file if you use the Apache webserver or, if you use NGINX you will need to add the rules manually to your virtualhost configuration. In both cases it requires permalinks to be turned on in your settings to function.', $this->hook); ?></p>
			<?php
		}
		
		/**
		 * Options form for hide backend page
		 *
		 **/
		function hidebackend_content_2() {
			?>
			<?php if ( get_option( 'permalink_structure' ) == '' && ! is_multisite() ) { //don't display form if permalinks are off ?>
				<p><?php echo __( 'You must turn on', $this->hook ) . ' <a href="/wp-admin/options-permalink.php">' . __( 'WordPress permalinks', $this->hook ) . '</a> ' . __( 'to use this feature.', $this->hook ); ?></p>
			<?php } else { ?>
				<form method="post" action="">
				<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
				<input type="hidden" name="bwps_page" value="hidebackend_1" />
				<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for "hb_enabled"><?php _e( 'Enable Hide Backend', $this->hook ); ?></label>
							</th>
							<td>
								<input id="hb_enabled" name="hb_enabled" type="checkbox" value="1" <?php checked( '1', $options['hb_enabled'] ); ?> />
								<p><?php _e( 'Check this box to enable the hide backend.', $this->hook ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="hb_login"><?php _e( 'Login Slug', $this->hook ); ?></label>
							</th>
							<td>
								<input name="hb_login" id="hb_login" value="<?php echo $options['hb_login']; ?>" type="text"><br />
								<em><span style="color: #666666;"><strong><?php _e( 'Login URL:', $this->hook ); ?></strong> <?php echo trailingslashit( get_option( 'siteurl' ) ); ?></span><span style="color: #4AA02C"><?php echo $options['hb_login']; ?></span></em>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="hb_register"><?php _e( 'Register Slug', $this->hook ); ?></label>
							</th>
							<td>
								<input name="hb_register" id="hb_register" value="<?php echo $options['hb_register']; ?>" type="text"><br />
								<em><span style="color: #666666;"><strong><?php _e( 'Register URL:', $this->hook ); ?></strong> <?php echo trailingslashit( get_option( 'siteurl' ) ); ?></span><span style="color: #4AA02C"><?php echo $options['hb_register']; ?></span></em>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="hb_admin"><?php _e( 'Admin Slug', $this->hook ); ?></label>
							</th>
							<td>
								<input name="hb_admin" id="hb_admin" value="<?php echo $options['hb_admin']; ?>" type="text"><br />
								<em><span style="color: #666666;"><strong><?php _e( 'Admin URL:', $this->hook ); ?></strong> <?php echo trailingslashit( get_option( 'siteurl' ) ); ?></span><span style="color: #4AA02C"><?php echo 	$options['hb_admin']; ?></span></em>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
				</form>
			<?php } ?>
			<?php
		}
		
		/**
		 * Key information for hide backend page
		 *
		 **/
		function hidebackend_content_3() {
			$options = get_option( $this->primarysettings );
			?>
			<p><?php _e( 'Keep this key in a safe place. You can use it to manually fix plugins that link to wp-login.php. Once turning on this feature and plugins linking to wp-login.php will fail without adding ?[the key]& after wp-login.php. 99% of users will not need this key. The only place you would ever use it is to fix a bad login link in the code of a plugin or theme.', $this->hook ); ?></p>
			<p style="font-weight: bold; text-align: center;"><?php echo $options['hb_key']; ?></p>
			<?php
		}
		
		/**
		 * Intro form for intrusion detection page
		 *
		 **/
		function intrusiondetection_content_1() {
			?>
			<p><?php _e( 'Currently intrusion detection looks only at a user who is hitting a large number of non-existent pages, that is they are getting a large number of 404 errors. It assumes that a user who hits a lot of 404 errors in a short period of time is scanning for something (presumably a vulnerability) and locks them out accordingly (you can set the thresholds for this below). This also gives the added benefit of helping you find hidden problems causing 404 errors on unseen parts of your site as all errors will be logged in the "View Logs" page. You can set threshholds for this feature below.', $this->hook ); ?></p>
			<?php
		}
		
		/**
		 * Options form for intrusion detection page
		 *
		 **/
		function intrusiondetection_content_2() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="intrusiondetection_1" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "id_enabled"><?php _e( 'Enable Instrusion Detection', $this->hook ); ?></label>
						</th>
						<td>
							<input id="id_enabled" name="id_enabled" type="checkbox" value="1" <?php checked( '1', $options['id_enabled'] ); ?> />
							<p><?php _e( 'Check this box to enable instrustion detection.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "id_emailnotify"><?php _e( 'Email Notifications', $this->hook ); ?></label>
						</th>
						<td>
							<input id="id_emailnotify" name="id_emailnotify" type="checkbox" value="1" <?php checked( '1', $options['id_emailnotify'] ); ?> />
							<p><?php _e( 'Enabling this feature will trigger an email to be sent to the website administrator whenever a host is locked out of the system.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "id_checkinterval"><?php _e( 'Check Period', $this->hook ); ?></label>
						</th>
						<td>
							<input id="id_checkinterval" name="id_checkinterval" type="text" value="<?php echo $options['id_checkinterval']; ?>" />
							<p><?php _e( 'The number of minutes in which 404 errors should be remembered. Setting this too long can cause legitimate users to be banned.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "id_threshold"><?php _e( 'Error Threshold', $this->hook ); ?></label>
						</th>
						<td>
							<input id="id_threshold" name="id_threshold" type="text" value="<?php echo $options['id_threshold']; ?>" />
							<p><?php _e( 'The numbers of errors (within the check period timeframe) that will trigger a lockout.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "id_banperiod"><?php _e( 'Lockout Period', $this->hook ); ?></label>
						</th>
						<td>
							<input id="id_banperiod" name="id_banperiod" type="text" value="<?php echo $options['id_banperiod']; ?>" />
							<p><?php _e( 'The number of minutes a host will be banned from the site after triggering a lockout.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "id_whitelist"><?php _e( 'White List', $this->hook ); ?></label>
						</th>
						<td>
							<textarea id="id_whitelist" rows="10" cols="50" name="id_whitelist"><?php echo isset( $_POST['id_whitelist'] ) ? $_POST['id_whitelist'] : $options['id_whitelist']; ?></textarea>
							<p><?php _e( 'Use the guidelines below to enter hosts that will never be locked out due to too many 404 errors. This could be useful for Google, etc.', $this->hook ); ?></p>
							<ul><em>
								<li><?php _e( 'You may whitelist users by individual IP address or IP address range.', $this->hook ); ?></li>
								<li><?php _e( 'Individual IP addesses must be in IPV4 standard format (i.e. ###.###.###.###). Wildcards (*) are allowed to specify a range of ip addresses.', $this->hook ); ?></li>
								<li><?php _e( 'IP Address ranges may also be specified using the format ###.###.###.### - ###.###.###.###. Wildcards cannot be used in addresses specified like this.', $this->hook ); ?></li>
								<li><a href="http://ip-lookup.net/domain-lookup.php" target="_blank"><?php _e( 'Lookup IP Address.', $this->hook ); ?></a></li>
								<li><?php _e( 'Enter only 1 IP address or 1 IP address range per line.', $this->hook ); ?></li>
								<li><?php _e( '404 errors will still be logged for users on the whitelist. Only the lockout will be prevented', $this->hook ); ?></li>
							</em></ul>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		/**
		 * Intro block for login limits page
		 *
		 **/
		function loginlimits_content_1() {
			?>
			<p><?php _e( 'If one had unlimited time and wanted to try an unlimited number of password combimations to get into your site they eventually would, right? This method of attach, known as a brute force attack, is something that WordPress is acutely susceptible by default as the system doesn\t care how many attempts a user makes to login. It will always let you try agin. Enabling login limits will ban the host user from attempting to login again after the specified bad login threshhold has been reached.', $this->hook ); ?></p>
			<?php	
		}
		
		/**
		 * Options form for login limits page
		 *
		 **/
		function loginlimits_content_2() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="loginlimits_1" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "ll_enabled"><?php _e( 'Enable Login Limits', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_enabled" name="ll_enabled" type="checkbox" value="1" <?php checked( '1', $options['ll_enabled'] ); ?> />
							<p><?php _e( 'Check this box to enable login limits on this site.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "ll_maxattemptshost"><?php _e( 'Max Login Attempts Per Host', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_maxattemptshost" name="ll_maxattemptshost" type="text" value="<?php echo $options['ll_maxattemptshost']; ?>" />
							<p><?php _e( 'The number of login attempts a user has before their host or computer is locked out of the system.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "ll_maxattemptsuser"><?php _e( 'Max Login Attempts Per User', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_maxattemptsuser" name="ll_maxattemptsuser" type="text" value="<?php echo $options['ll_maxattemptsuser']; ?>" />
							<p><?php _e( 'The number of login attempts a user has before their username is locked out of the system. Note that this is different from hosts in case an attacker is using multiple computers. In addition, if they are using your login name you could be locked out yourself.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "ll_checkinterval"><?php _e( 'Login Time Period (minutes)', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_checkinterval" name="ll_checkinterval" type="text" value="<?php echo $options['ll_checkinterval']; ?>" />
							<p><?php _e( 'The number of minutes in which bad logins should be remembered.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "ll_banperiod"><?php _e( 'Lockout Time Period (minutes)', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_banperiod" name="ll_banperiod" type="text" value="<?php echo $options['ll_banperiod']; ?>" />
							<p><?php _e( 'The length of time a host or computer will be banned from this site after hitting the limit of bad logins.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "ll_emailnotify"><?php _e( 'Email Notifications', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_emailnotify" name="ll_emailnotify" type="checkbox" value="1" <?php checked( '1', $options['ll_emailnotify'] ); ?> />
							<p><?php _e( 'Enabling this feature will trigger an email to be sent to the website administrator whenever a host or user is locked out of the system.', $this->hook ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		/**
		 * Intro block for view logs page
		 *
		 **/
		function logs_content_1() {
			?>
			<p><?php _e( 'This page contains the logs generated by Better WP Security, current lockouts (which can be cleared here) and a way to cleanup the logs to save space on the server and reduce CPU load. Please note, you must manually clear these logs, they will not do so automatically. I highly recommend you do so regularly to improve performance which can otherwise be slowed if the system has to search through large log-files on a regular basis.', $this->hook ); ?></p>
			<?php
		}
		
		/**
		 * Clear logs form for view logs page
		 *
		 **/
		function logs_content_2() {
			global $wpdb;
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="log_1" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
			<?php //get database record counts
				$countlogin = $wpdb->get_var( "SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "bwps_log` WHERE `timestamp` < " . ( time() - ( $options['ll_checkinterval'] * 60 ) ) . " AND `type` = 1;" );
				$count404 = $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "bwps_log` WHERE `timestamp` < " . (time() - ( $options['id_checkinterval'] * 60 ) ) . " AND `type` = 2;" );
				$countlockout = $wpdb->get_var( "SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "bwps_lockouts` WHERE `exptime` < " . time() . " OR `active` = 0;" );
			 ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Old Data', $this->hook ); ?>
						</th>
						<td>
							<p><?php _e( 'Below is old security data still in your Wordpress database. Data is considered old when the lockout has expired, or been manually cancelled, or when the log entry will no longer be used to generate a lockout.', $this->hook ); ?></p>
							<p><?php _e( 'This data is not automatically deleted so that it may be used for analysis. You may delete this data with the form below. To see the actual data you will need to access your database directly.', $this->hook ); ?></p>
							<p><?php _e( 'Check the box next to the data you would like to clear and then press the "Remove Old Data" button.', $this->hook ); ?></p>
							<ul>
								<li style="list-style: none;"> <input type="checkbox" name="badlogins" id="badlogins" value="1" /> <label for="badlogins"><?php _e( 'Your database contains', $this->hook ); ?> <strong><?php echo $countlogin; ?> <?php _e( 'bad login entries.', $this->hook ); ?></strong></label></li>
								<li style="list-style: none;"> <input type="checkbox" name="404s" id="404s" value="1" /> <label for="404s"><?php _e( 'Your database contains', $this->hook ); ?> <strong><?php echo $count404; ?> <?php _e( '404 errors.', $this->hook ); ?></strong><br />
								<em><?php _e( 'This will clear the 404 log below.', $this->hook ); ?></em></label></li>
								<li style="list-style: none;"> <input type="checkbox" name="lockouts" id="lockouts" value="1" /> <label for="lockouts"><?php _e( 'Your database contains', $this->hook ); ?> <strong><?php echo $countlockout; ?> <?php _e( 'old lockouts.', $this->hook ); ?></strong></label></li>
							</ul>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Remove Data', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		/**
		 * Active lockouts table and form for view logs page
		 *
		 **/
		function logs_content_3() {
			global $wpdb;
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="log_2" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
			<?php //get locked out hosts and users from database
				$hostLocks = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_lockouts` WHERE `active` = 1 AND `exptime` > " . time() . " AND `host` != 0;", ARRAY_A );
				$userLocks = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_lockouts` WHERE `active` = 1 AND `exptime` > " . time() . " AND `user` != 0;", ARRAY_A );
			 ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Locked out hosts', $this->hook ); ?>
						</th>
						<td>
							<?php if ( sizeof( $hostLocks ) > 0 ) { ?>
							<ul>
								<?php foreach ( $hostLocks as $host) { ?>
									<li style="list-style: none;"><input type="checkbox" name="lo_<?php echo $host['id']; ?>" id="lo_<?php echo $host['id']; ?>" value="<?php echo $host['id']; ?>" /> <label for="lo_<?php echo $host['id']; ?>"><strong><?php echo $host['host']; ?></strong> - Expires <em><?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $host['exptime'] ) ); ?></em></label></li>
								<?php } ?>
							</ul>
							<?php } else { //no host is locked out ?>
								<p><?php _e( 'Currently no hosts are locked out of this website.', $this->hook ); ?></p>
							<?php } ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Locked out users', $this->hook ); ?>
						</th>
						<td>
							<?php if (sizeof( $userLocks ) > 0 ) { ?>
							<ul>
								<?php foreach ( $userLocks as $user ) { ?>
									<?php $userdata = get_userdata( $user['user'] ); ?>
									<li style="list-style: none;"><input type="checkbox" name="lo_<?php echo $user['id']; ?>" id="lo_<?php echo $user['id']; ?>" value="<?php echo $user['id']; ?>" /> <label for="lo_<?php echo $user['id']; ?>"><strong><?php echo $userdata->user_login; ?></strong> - Expires <em><?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $user['exptime'] ) ); ?></em></label></li>
								<?php } ?>
							</ul>
							<?php } else { //no user is locked out ?>
								<p><?php _e( 'Currently no users are locked out of this website.', $this->hook ); ?></p>
							<?php } ?>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Release Lockout', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		/**
		 * 404 table for view logs page
		 *
		 **/
		function logs_content_4() {
			global $wpdb;
			
			$errors = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_log` WHERE `type` = 2;", ARRAY_A );
			$grouped = array();
			foreach ( $errors as $error ) { //loop through and group 404s
				if ( isset( $grouped[$error['url']] ) ) {
					$grouped[$error['url']]['count'] = $grouped[$error['url']]['count'] + 1;
					$grouped[$error['url']]['last'] = $grouped[$error['url']]['last'] > $error['timestamp'] ? $grouped[$error['url']]['last'] : $error['timestamp'];
				} else {
					$grouped[$error['url']]['count'] = 1;
					$grouped[$error['url']]['last'] = $error['timestamp'];
				} 
			}
			if ( sizeof( $grouped ) > 0 ) {
			?>
			<p><?php _e( 'The following is a list of 404 errors found on your site with the relative url listed first, the number of times the error was encountered in parenthases, and the last time the error was encounterd given last.', $this->hook ); ?></p>
			<?php
				foreach ( $grouped as $url => $data ) {
					?>
					<li><?php echo $url; ?> (<?php echo $data['count']; ?>) <?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $data['last'] ) ); ?></li>
					<?php
				}
			?>
			
			<?php
			} else { //the log is empty
			?>
				<p><?php _e( 'There are currently no 404 errors in the log', $this->hook ); ?></p>
			<?php 
			}
		}
		
		/**
		 * Intro block for system tweaks page
		 *
		 **/
		function systemtweaks_content_1() {
			?>
			<p><?php _e( 'This page contains a number of tweaks that can significantly improve the security of your system.', $this->hook ); ?></p>
			<p><?php _e( 'Rewrite tweaks make use of rewrite rules and, in the case of Apache, will write them to your .htaccess file. If you are however using NGINX you will need to manually copy the rules on the Better WP Security Dashboard and put them in your server configuration.', $this->hook ); ?></p>
			<p><?php _e( 'The other in some cases, make use of editing your wp-config.php file. Those that do can be manually turned off by reverting the changes that file.', $this->hook ); ?></p>
			<p><?php _e( 'Be advsied, some of these tweaks may in fact break other plugins and themes that make use of techniques that are often seen in practice as suspicious. That said, I highly recommend turning these on one-by-one and don\'t worry if you cannot use them all.', $this->hook ); ?></p>
			<?php
		}
		
		/**
		 * Rewrite options for system tweaks page
		 *
		 **/
		function systemtweaks_content_2() {
			?>
			<?php if ( $this->bwpsserver == 'unsupported' ) { //don't diplay options for unsupported server ?> 
				<p><?php _e( 'Your webserver is unsupported. You must use Apache or NGINX to make use of these rules.', $this->hook ); ?></p>
			<?php } else { ?>
				<form method="post" action="">
				<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
				<input type="hidden" name="bwps_page" value="systemtweaks_1" />
				<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for "st_ht_files"><?php _e( 'Protect Files', $this->hook ); ?></label>
							</th>
							<td>
								<input id="st_ht_files" name="st_ht_files" type="checkbox" value="1" <?php checked( '1', $options['st_ht_files'] ); ?> />
								<p><?php _e( 'Prevent public access to readme.html, wp-config.php, install.php, wp-includes, and .htaccess. These files can give away important information on your site and serve no purpose to the public once WordPress has been successfully installed.', $this->hook ); ?></p>
							</td>
						</tr>
						<?php if ( strstr( strtolower( $_SERVER['SERVER_SOFTWARE'] ), 'apache' ) ) { ?>
							<tr valign="top">
								<th scope="row">
									<label for "st_ht_browsing"><?php _e( 'Disable Directory Browsing', $this->hook ); ?></label>
								</th>
								<td>
									<input id="st_ht_browsing" name="st_ht_browsing" type="checkbox" value="1" <?php checked( '1', $options['st_ht_browsing'] ); ?> />
									<p><?php _e( 'Prevents users from seeing a list of files in a directory when no index file is present.', $this->hook ); ?></p>
								</td>
							</tr>
						<?php } ?>
						<tr valign="top">
							<th scope="row">
								<label for "st_ht_request"><?php _e( 'Filter Request Methods', $this->hook ); ?></label>
							</th>
							<td>
								<input id="st_ht_request" name="st_ht_request" type="checkbox" value="1" <?php checked( '1', $options['st_ht_request'] ); ?> />
								<p><?php _e( 'Filter out hits with the trace, delete, or track request methods.', $this->hook ); ?></p>
							</td>
						</tr>
						<tr valign="top" style="border: 1px solid #ffcc00;">
							<th scope="row">
								<label for "st_ht_query"><?php _e( 'Filter Suspicious Query Strings', $this->hook ); ?></label>
							</th>
							<td>
								<input id="st_ht_query" name="st_ht_query" type="checkbox" value="1" <?php checked( '1', $options['st_ht_query'] ); ?> />
								<p><?php _e( 'Filter out suspicious query strings in the URL. These are very often signs of someone trying to gain access to your site but some plugins and themes can also be blocked.', $this->hook ); ?></p>
								<p style="color: #ff0000;font-style: italic;"><?php _e( 'Warning: This feature is known to cause conflicts with some plugins and themes.', $this->hook ); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Rewrite Changes', $this->hook ) ?>" /></p>
				</form>
			<?php } ?>
			<?php
		}
		
		/**
		 * Other tweaks options form for system tweaks page
		 *
	 	 **/
		function systemtweaks_content_3() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="systemtweaks_2" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
				<table class="form-table">
					<tr>
						<td scope="row" colspan="2">
							<h4><?php _e( 'Header Tweaks', $this->hook ); ?></h4>
						</td>
					<tr valign="top">
						<th scope="row">
							<label for "st_generator"><?php _e( 'Remove Wordpress Generator Meta Tag', $this->hook ); ?></label>
						</th>
						<td>
							<input id="st_generator" name="st_generator" type="checkbox" value="1" <?php checked( '1', $options['st_generator'] ); ?> />
							<p><?php _e( 'Removes the <meta name="generator" content="WordPress [version]" /> meta tag from your sites header. This process hides version information from a potential attacker making it more difficult to determine vulnerabilities.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "st_manifest"><?php _e( 'Remove wlwmanifest header', $this->hook ); ?></label>
						</th>
						<td>
							<input id="st_manifest" name="st_manifest" type="checkbox" value="1" <?php checked( '1', $options['st_manifest'] ); ?> />
							<p><?php _e( 'Removes the Windows Live Writer header. This is not needed if you do not use Windows Live Writer.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top" style="border: 1px solid #ffcc00;">
						<th scope="row">
							<label for "st_edituri"><?php _e( 'Remove EditURI header', $this->hook ); ?></label>
						</th>
						<td>
							<input id="st_edituri" name="st_edituri" type="checkbox" value="1" <?php checked( '1', $options['st_edituri'] ); ?> />
							<p><?php _e( 'Removes the RSD (Really Simple Discovery) header. If you don\'t integrate your blog with external XML-RPC services such as Flickr then the "RSD" function is pretty much useless to you.', $this->hook ); ?></p>
							<p style="color: #ff0000;font-style: italic;"><?php _e( 'Warning: This feature is known to cause conflicts with some 3rd party application and services that may want to interact with WordPress.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr>
						<td scope="row" colspan="2">
							<h4><?php _e( 'Dashboard Tweaks', $this->hook ); ?></h4>
						</td>
					<tr valign="top">
						<th scope="row">
							<label for "st_themenot"><?php _e( 'Hide Theme Update Notifications', $this->hook ); ?></label>
						</th>
						<td>
							<input id="st_themenot" name="st_themenot" type="checkbox" value="1" <?php checked( '1', $options['st_themenot'] ); ?> />
							<p><?php _e( 'Hides theme update notifications from users who cannot update themes. Please note that this only makes a difference in multi-site installations.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "st_pluginnot"><?php _e( 'Hide Plugin Update Notifications', $this->hook ); ?></label>
						</th>
						<td>
							<input id="st_pluginnot" name="st_pluginnot" type="checkbox" value="1" <?php checked( '1', $options['st_pluginnot'] ); ?> />
							<p><?php _e( 'Hides plugin update notifications from users who cannot update themes. Please note that this only makes a difference in multi-site installations.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "st_corenot"><?php _e( 'Hide Core Update Notifications', $this->hook ); ?></label>
						</th>
						<td>
							<input id="st_corenot" name="st_corenot" type="checkbox" value="1" <?php checked( '1', $options['st_corenot'] ); ?> />
							<p><?php _e( 'Hides core update notifications from users who cannot update themes. Please note that this only makes a difference in multi-site installations.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr>
						<td scope="row" colspan="2">
							<h4><?php _e( 'Strong Password Tweaks', $this->hook ); ?></h4>
						</td>
					<tr valign="top">
						<th scope="row">
							<label for "st_enablepassword"><?php _e( 'Enable strong password enforcement', $this->hook ); ?></label>
						</th>
						<td>
							<input id="st_enablepassword" name="st_enablepassword" type="checkbox" value="1" <?php checked( '1', $options['st_enablepassword'] ); ?> />
							<p><?php _e( 'Enforce strong passwords for all users with at least the role specified below.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top" style="border: 1px solid #ffcc00;">
						<th scope="row">
							<label for "st_passrole"><?php _e( 'Strong Password Role', $this->hook ); ?></label>
						</th>
						<td>
							<select name="st_passrole" id="st_passrole">
								<option value="administrator" <?php if ($options['st_passrole'] == "administrator") echo "selected"; ?>>Administrator</option>
								<option value="editor" <?php if ($options['st_passrole'] == "editor") echo "selected"; ?>>Editor</option>
								<option value="author" <?php if ($options['st_passrole'] == "author") echo "selected"; ?>>Author</option>
								<option value="contributor" <?php if ($options['st_passrole'] == "contributor") echo "selected"; ?>>Contributor</option>
								<option value="subscriber" <?php if ($options['st_passrole'] == "subscriber") echo "selected"; ?>>Subscriber</option>
							</select>
							<p><?php _e( 'Minimum role at which a user must choose a strong password. For more information on Wordpress roles and capabilities please see', $this->hook ); ?> <a hre="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">http://codex.wordpress.org/Roles_and_Capabilities</a>.</p>
							<p style="color: #ff0000;font-style: italic;"><?php _e( 'Warning: If your site invites public registrations setting the role too low may annoy your members.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr>
						<td scope="row" colspan="2">
							<h4><?php _e( 'Other Tweaks', $this->hook ); ?></h4>
						</td>
					<tr valign="top">
						<th scope="row">
							<label for "st_loginerror"><?php _e( 'Remove Wordpress Login Error Messages', $this->hook ); ?></label>
						</th>
						<td>
							<input id="st_loginerror" name="st_loginerror" type="checkbox" value="1" <?php checked( '1', $options['st_loginerror'] ); ?> />
							<p><?php _e( 'Prevents error messages from being displayed to a user upon a failed login attempt.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top" style="border: 1px solid #ffcc00;">
						<th scope="row">
							<label for "st_randomversion"><?php _e( 'Display random version number to all non-administrative users', $this->hook ); ?></label>
						</th>
						<td>
							<input id="st_randomversion" name="st_randomversion" type="checkbox" value="1" <?php checked( '1', $options['st_randomversion'] ); ?> />
							<p><?php _e( 'Displays a random version number to visitors who are not logged in at all points where version number must be used and removes the version completely from where it can.', $this->hook ); ?></p>
							<p style="color: #ff0000;font-style: italic;"><?php _e( 'Warning: This feature is known to cause conflicts with some plugins and themes.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top" style="border: 1px solid #ffcc00;">
						<th scope="row">
							<label for "st_longurl"><?php _e( 'Prevent long URL strings', $this->hook ); ?></label>
						</th>
						<td>
							<input id="st_longurl" name="st_longurl" type="checkbox" value="1" <?php checked( '1', $options['st_longurl'] ); ?> />
							<p><?php _e( 'Limits the number of characters that can be sent in the URL. Hackers often take advantage of long URLs to try to inject information into your database.', $this->hook ); ?></p>
							<p style="color: #ff0000;font-style: italic;"><?php _e( 'Warning: This feature is known to cause conflicts with some plugins and themes.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top"  style="border: 1px solid #ffcc00;">
						<th scope="row">
							<label for "st_fileedit"><?php _e( 'Turn off file editor in Wordpress Back-end', $this->hook ); ?></label>
						</th>
						<td>
							<input id="st_fileedit" name="st_fileedit" type="checkbox" value="1" <?php checked( '1', $options['st_fileedit'] ); ?> />
							<p><?php _e( 'Disables the file editor for plugins and themes requiring users to have access to the file system to modify files. Once activated you will need to manually edit theme and other files using a tool other than WordPress.', $this->hook ); ?></p>
							<p style="color: #ff0000;font-style: italic;"><?php _e( 'Warning: This feature is known to cause conflicts with some plugins and themes.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr>
						<td scope="row" colspan="2">
							<h4><?php _e( 'SSL Tweaks', $this->hook ); ?></h4>
							<h4 style="color: red; text-align: center; border-bottom: none;">WARNING: You're server MUST support SSL to use this feature. Using this feature without SSL support will cause the backend of your site to become unavailable.</h4>
						</td>
					<?php
					echo '<script language="javascript">';
					echo 'function forcessl() {';
					echo 'alert( "' . __( 'Are you sure you want to enable SSL? If your server does not support SSL you will be locked out of your WordPress admin backend.', $this->hook ) . '" );';
					echo '}';
					echo '</script>';
					?>
					<tr valign="top" style="border: 1px solid #ff0000;">
						<th scope="row">
							<label for "st_forceloginssl"><?php _e( 'Enforce Login SSL', $this->hook ); ?></label>
						</th>
						<td>
							<input onchange="forcessl()" id="st_forceloginssl" name="st_forceloginssl" type="checkbox" value="1" <?php checked( '1', $options['st_forceloginssl'] ); ?> />
							<p><?php _e( 'Forces all logins to be served only over a secure SSL connection.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top"  style="border: 1px solid #ff0000;">
						<th scope="row">
							<label for "st_forceadminssl"><?php _e( 'Enforce Admin SSL', $this->hook ); ?></label>
						</th>
						<td>
							<input onchange="forcessl()" id="st_forceadminssl" name="st_forceadminssl" type="checkbox" value="1" <?php checked( '1', $options['st_forceadminssl'] ); ?> />
							<p><?php _e( 'Forces all of the WordPress backend to be served only over a secure SSL connection.', $this->hook ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
	
	}

}
