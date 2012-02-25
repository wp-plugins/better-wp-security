<?php

if ( ! class_exists( 'bwps_admin_process' ) ) {

	class bwps_admin_process extends bwps_admin_common {
	
		function form_dispatcher() {
			//verify nonce
			if ( ! wp_verify_nonce( $_POST['wp_nonce'], 'BWPS_admin_save' ) ) {
				die( 'Security error!' );
			}
			
			switch ( $_POST['bwps_page'] ) {
				case 'adminuser_1':
					$this->adminuser_process_1();
					break;
				case 'awaymode_1':
					$this->awaymode_process_1();
					break;
				case 'banhosts_1':
					$this->banhosts_process_1();
					break;
				case 'contentdirectory_1':
					$this->contentdirectory_process_1();
					break;
				case 'databasebackup_1':
					$this->databasebackup_process_1();
					break;
				case 'databasebackup_2':
					$this->databasebackup_process_2();
					break;
				case 'databaseprefix_1':
					$this->databaseprefix_process_1();
					break;
				case 'hidebackend_1':
					$this->hidebackend_process_1();
					break;
				case 'intrusiondetection_1':
					$this->intrusiondetection_process_1();
					break;
				case 'loginlimits_1':
					$this->loginlimits_process_1();
					break;
				case 'log_1':
					$this->log_process_1();
					break;
				case 'log_2':
					$this->log_process_2();
					break;
				case 'systemtweaks_1':
					$this->systemtweaks_process_1();
					break;
				case 'systemtweaks_2':
					$this->systemtweaks_process_2();
					break;
			}
		}
		
		function adminuser_process_1() {
		
			global $wpdb;
			$errorHandler = __( 'Successfully Changed admin Username. If you are logged in as admin you will have to log in again before continuing.', $this->hook );
			
			//sanitize the username
			$newuser = wp_strip_all_tags( $_POST['newuser'] );
			
			if ( strlen( $newuser ) < 1 ) { //if the field was left blank set an error message
			
				$errorHandler = new WP_Error();
				$errorHandler->add( '2', $newuser . __( 'You must enter a valid username. Please try again', $this->hook ) );
				
			} else {	
			
				if ( validate_username( $newuser ) ) { //make sure username is valid
				
					if ( $this->user_exists( $newuser ) ) { //if the user already exists set an error
					
						if ( ! is_wp_error( $errorHandler ) ) {
							$errorHandler = new WP_Error();
						}
								
						$errorHandler->add( '2', $newuser . __( ' already exists. Please try again', $this->hook ) );
								
					} else {
								
						//query main user table
						$wpdb->query( "UPDATE `" . $wpdb->users . "` SET user_login = '" . $newuser . "' WHERE user_login='admin'" );
						
						if ( is_multisite() ) { //process sitemeta if we're in a multi-site situation
						
							$oldAdmins = $wpdb->get_var( "SELECT meta_value FROM `" . $wpdb->sitemeta . "` WHERE meta_key='site_admins'" );
							$newAdmins = str_replace( '5:"admin"', strlen( $newuser) . ':"' . $newuser . '"', $oldAdmins );
							$wpdb->query( "UPDATE `" . $wpdb->sitemeta . "` SET meta_value = '" . $newAdmins . "' WHERE meta_key='site_admins'" );
							
						}
						
					}
					
				} else {
				
					if ( ! is_wp_error( $errorHandler ) ) { //set an error for invalid username
						$errorHandler = new WP_Error();
					}
				
					$errorHandler->add( '2', $newuser . __( ' is not a valid username. Please try again', $this->hook ) );
				}
			}
			
			$this-> showmessages( $errorHandler ); //finally show messages
			
		}
		
		function awaymode_process_1() {
		
			$errorHandler = __( 'Settings Saved', $this->hook );
			
			$options = get_option( $this->primarysettings );
			
			$options['am_enabled'] = ( $_POST['am_enabled'] == 1 ? 1 : 0 );
			$options['am_type'] = ( $_POST['am_type'] == 1 ? 1 : 0 );
			
			$startDate = $_POST['am_startmonth'] . "/" . $_POST['am_startday'] . "/" . $_POST['am_startyear'];
			$endDate = $_POST['am_endmonth'] . "/" . $_POST['am_endday'] . "/" . $_POST['am_endyear'];
			
			if ( $endDate <= $startDate ) {
			
				if ( ! is_wp_error( $errorHandler ) ) {
					$errorHandler = new WP_Error();
				}
						
				$errorHandler->add( '2', __( 'The ending date must be after the current date.', $this->hook ) );
			}
			
			$startTime = $_POST['am_starthour'] . ':' . $_POST['am_startmin'] . ' ' . $_POST['am_starthalf'];
			$endTime = $_POST['am_endhour'] . ':' . $_POST['am_endmin'] . ' ' . $_POST['am_endhalf'];
			
			
			$options['am_startdate'] = strtotime( $startDate . ' 12:01 am' );
			$options['am_enddate'] = strtotime( $endDate . ' 12:01 am' );
			$options['am_starttime'] = strtotime( '1/1/1970 ' . $startTime );
			$options['am_endtime'] = strtotime( '1/1/1970 ' . $endTime );
			
			if ( ! is_wp_error( $errorHandler ) ) {
				update_option( $this->primarysettings, $options );
			}
						
			$this-> showmessages( $errorHandler );
			
		}
		
		function banhosts_process_1() {
		
			global $bwps; 
			
			$errorHandler = __( 'Settings Saved', $this->hook );
			
			$options = get_option( $this->primarysettings );
			
			$options['bh_enabled'] = ( $_POST['bh_enabled'] == 1 ? 1 : 0 );
			
			$banlist = explode("\n", $_POST['bh_banlist'] );
			$banitems = array();
			
			if(!empty( $banlist ) ) {
			
				foreach( $banlist as $item ) {
				
					if ( strlen( $item) > 0 ) {
					
						if ( strstr( $item,' - ' ) ) {
						
							$range = explode( '-', $item );
							$start = trim( $range[0] );
							$end = trim( $range[1] );
							
							if ( ip2long( $end ) == false ) {
							
								if ( ! is_wp_error( $errorHandler ) ) {
									$errorHandler = new WP_Error();
								}
								
								$errorHandler->add( '1', __( $item . ' contains an invalid ip (' . $end . ').', $this->hook ) );
								
							}
							
							if ( ip2long( $start ) == false ) {
							
								if ( ! is_wp_error( $errorHandler ) ) {
									$errorHandler = new WP_Error();
								}
								
								$errorHandler->add( '1', __( $item . ' contains an invalid ip (' . $start . ').', $this->hook ) );
								
							} else {
							
								$banitems[] = trim( $item );
								
							}	
								
						} else {
						
							$ipParts = explode( '.',$item );
							$isIP = 0;
							
							foreach ( $ipParts as $part ) {
							
								if ( ( is_numeric( trim( $part ) ) && trim( $part ) <= 255 && trim( $part ) >= 0 ) || trim( $part ) == '*' ) {
									$isIP++;
								}
								
							}
							
							if( $isIP == 4 ) {
							
								if ( ip2long( trim( str_replace( '*', '0', $item ) ) ) == false ) {
								
									if ( ! is_wp_error( $errorHandler ) ) {
										$errorHandler = new WP_Error();
									}
									
									$errorHandler->add( '1', __( $item . ' is not a valid ip.', $this->hook ) );
									
								} else {
								
									$banitems[] = trim( $item );
									
								}
								
							} else {
							
								if ( ! is_wp_error( $errorHandler ) ) {
									$errorHandler = new WP_Error();
								}
								
								$errorHandler->add( '1', __( $item . ' is note a valid ip.', $this->hook ) );
								
							}
							
						}
						
					}
					
				}
				
			}
			
			$options['bh_banlist'] = implode( "\n",$banitems );
			
			if ( $bwps->checklist( $options['bh_banlist'] ) ) {
			
				if (!is_wp_error( $errorHandler) ) {
					$errorHandler = new WP_Error();
				}
				
				$errorHandler->add( '1', __( 'You cannot ban yourself. Please try again.', $this->hook ) );
				
			}
			
			if (!is_wp_error( $errorHandler ) ) {
				update_option( $this->primarysettings, $options );
			}
						
			$this-> showmessages( $errorHandler );
		}
		
		function contentdirectory_process_1() {
		
			global $wpdb;
			$errorHandler = __( 'Settings Saved', $this->hook );
			
			$oldDir = WP_CONTENT_DIR;
			$newDir = trailingslashit( ABSPATH ) . $wpdb->escape( $_POST['dirname'] );
			
			$renamed = rename( $oldDir, $newDir );
			
			if ( ! $renamed ) {
			
				if ( ! is_wp_error( $errorHandler ) ) {
					$errorHandler = new WP_Error();
				}
						
				$errorHandler->add( '2', $newuser . __( 'Unable to rename the wp-content folder. Operation cancelled.', $this->hook ) );
				
				die( 'Old Dir = ' . $oldDir . ', New Dir = ' . $newDir );
				
			}
			
			$wpconfig = $this->getConfig(); //get the path for the config file
					
			@chmod( $wpconfig, 0644 ); //make sure the config file is writable
					
			$handle = @fopen( $wpconfig, 'r+' ); //open for reading
					
			if ( $handle && $renamed ) {
			
				$scanText = "/* That's all, stop editing! Happy blogging. */";
				$altScan = "/* Stop editing */";
				$newText = "define( 'WP_CONTENT_DIR', '" . $newDir . "' );\r\ndefine( 'WP_CONTENT_URL', '" . trailingslashit( get_option( 'siteurl' ) ) . $wpdb->escape( $_POST['dirname'] ) . "' );\r\n\r\n/* That's all, stop editing! Happy blogging. */\r\n";
					
				//read each line into an array
				while ( $lines[] = fgets( $handle, 4096 ) ) {}
						
				fclose( $handle ); //close reader
						
				$handle = @fopen( $wpconfig, 'w+' ); //open writer
						
				foreach ( $lines as $line ) { //process each line
						
					if ( strstr( $line, 'WP_CONTENT_DIR' ) || strstr( $line, 'WP_CONTENT_URL' ) ) {
					
						$line = str_replace( $line, '', $line );

					}

					if (strstr( $line, $scanText ) ) {
					
						$line = str_replace( $scanText, $newText, $line );
					
					} else if ( strstr( $line, $altScan ) ) {
					
						$line = str_replace( $altScan, $newText, $line );
					
					}
							
					fwrite( $handle, $line ); //write the line
							
				}
						
				fclose( $handle ); //close the config file
						
				@chmod( $wpconfig, 0444 ); //make sure the config file is no longer writable
						
			}
			
			$this-> showmessages( $errorHandler ); //finally show messages
			
		}
		
		function databasebackup_process_1() {
		
			$errorHandler = __( 'Database Backup Completed.', $this->hook );
			
			$this->db_backup();
			
			$this->showmessages( $errorHandler );		
			
		}
		
		function databasebackup_process_2() {
			$errorHandler = __( 'Settings Saved', $this->hook );
			
			$options = get_option( $this->primarysettings ); //load the options
			
			$options['backup_email'] = ( $_POST['backup_email'] == 1 ? 1 : 0 );
			$options['backup_enabled'] = ( $_POST['backup_enabled'] == 1 ? 1 : 0 );
			$options['backups_to_retain'] = absint( $_POST['backups_to_retain'] );
			$options['backup_int'] = $_POST['backup_int'];
						
			update_option( $this->primarysettings, $options );
			
			if ( $options['backup_email'] == 1 ) {
			
				$backuppath = BWPS_PP . 'lib/phpmysqlautobackup/backups';
				$files = scandir( $backuppath );
			
				foreach ( $files as $file ) {
					unlink( $backuppath . '/' . $file );			
				}
			}
			
			if ( wp_next_scheduled( 'bwps_backup' ) ) {
				wp_clear_scheduled_hook( 'bwps_backup' );
			}
			
			$this-> showmessages( $errorHandler );
			
		}
		
		function databaseprefix_process_1() {
			global $wpdb;
			$errorHandler = __( 'Database Prefix Changed', $this->hook );	
	
			$checkPrefix = true;//Assume the first prefix we generate is unique
			
			while ( $checkPrefix ) {
			
				$avail = 'abcdefghijklmnopqrstuvwxyz0123456789';
				
				$newPrefix = $avail[rand( 0, 25 )];
				
				$prelength = rand( 4, 9 );
				
				for ( $i = 0; $i < $prelength; $i++ ) {
					$newPrefix .= $avail[rand( 0, 35 )];
				}
				
				$newPrefix .= '_';
						
				$checkPrefix = $wpdb->get_results( 'SHOW TABLES LIKE "' . $newPrefix . '%";', ARRAY_N ); //if there are no tables with that prefix in the database set checkPrefix to false
					
			}
					
			$tables = $wpdb->get_results( 'SHOW TABLES LIKE "' . $wpdb->base_prefix . '%"', ARRAY_N ); //retrieve a list of all tables in the DB
					
			//Rename each table
			foreach ( $tables as $table ) {
					
				$table = substr( $table[0], strlen( $wpdb->base_prefix ), strlen( $table[0] ) ); //Get the table name without the old prefix
		
				//rename the table and generate an error if there is a problem
				if ( $wpdb->query( 'RENAME TABLE `' . $wpdb->base_prefix . $table . '` TO `' . $newPrefix . $table . '`;' ) === false ) {
		
					if ( ! is_wp_error( $errorHandler ) ) { //set an error for invalid username
						$errorHandler = new WP_Error();
					}
		
					$errorHandler->add( '2', __( 'Error: Could not rename table ', $this->hook ) . $wpdb->base_prefix . __( '. You may have to rename the table manually.', $this->hook ) );	
						
				}
						
			}
					
			$upOpts = true; //assume we've successfully updated all options to start
					
			if ( is_multisite() ) {
						
				$blogs = $wpdb->get_col( "SELECT blog_id FROM `" . $wpdb->blogs . "` WHERE public = '1' AND archived = '0' AND mature = '0' AND spam = '0' ORDER BY blog_id DESC" ); //get list of blog id's
					
				if (is_array( $blogs) ) { //make sure there are other blogs to update
						
					//update each blog's user_roles option
					foreach ( $blogs as $blog ) {
							
						$results = $wpdb->query( 'UPDATE `' . $newPrefix . $blog . '_options` SET option_name = "' . $newPrefix . $blog . '_user_roles" WHERE option_name = "' . $wpdb->base_prefix . $blog . '_user_roles" LIMIT 1;' );
								
						if ( $results === false ) { //if there's an error upOpts should equal false
							$upOpts = false;
						}
								
					}
							
				}
						
			}
					
			$upOpts = $wpdb->query( 'UPDATE `' . $newPrefix . 'options` SET option_name = "' . $newPrefix . 'user_roles" WHERE option_name = "' . $wpdb->base_prefix . 'user_roles" LIMIT 1;' ); //update options table and set flag to false if there's an error
										
			if ( $upOpts === false ) { //set an error
		
				if (!is_wp_error( $errorHandler ) ) {
					$errorHandler = new WP_Error();
				}
							
				$errorHandler->add( '2', __( 'Could not update prefix refences in options tables.', $this->hook ) );
						
			}
										
			$rows = $wpdb->get_results( 'SELECT * FROM `' . $newPrefix . 'usermeta`' ); //get all rows in usermeta
										
			//update all prefixes in usermeta
			foreach ( $rows as $row ) {
					
				if ( substr( $row->meta_key, 0, strlen( $wpdb->base_prefix ) ) == $wpdb->base_prefix ) {
						
					$pos = $newPrefix . substr( $row->meta_key, strlen( $wpdb->base_prefix ), strlen( $row->meta_key ) );
							
					$result = $wpdb->query( 'UPDATE `' . $newPrefix . 'usermeta` SET meta_key="' . $pos . '" WHERE meta_key= "' . $row->meta_key . '" LIMIT 1;' );
							
					if ( $result == false ) {
								
						if ( ! is_wp_error( $errorHandler ) ) {
							$errorHandler = new WP_Error();
						}
										
						$errorHandler->add( '2', __( 'Could not update prefix refences in usermeta table.', $this->hook ) );
								
					}
							
				}
						
			}
					
			$wpconfig = $this->getConfig(); //get the path for the config file
					
			@chmod( $wpconfig, 0644 ); //make sure the config file is writable
					
			$handle = @fopen( $wpconfig, "r+" ); //open for reading
					
			if ( $handle ) {
					
				//read each line into an array
				while ( $lines[] = fgets( $handle, 4096 ) ){}
						
				fclose( $handle ); //close reader
						
				$handle = @fopen( $wpconfig, "w+" ); //open writer
						
				foreach ( $lines as $line ) { //process each line
						
					//if the prefix is in the line
					if (strpos( $line, 'table_prefix' ) ) {
							
						$line = str_replace( $wpdb->base_prefix, $newPrefix, $line );
								
					}
							
					fwrite( $handle, $line ); //write the line
							
				}
						
				fclose( $handle ); //close the config file
						
				@chmod( $wpconfig, 0444 ); //make sure the config file is no longer writable
						
				$wpdb->base_prefix = $newPrefix; //update the prefix
						
			}
					
			$this-> showmessages( $errorHandler ); //finally show messages
			
			remove_action( 'admin_notices', 'site_admin_notice' );
			remove_action( 'network_admin_notices', 'site_admin_notice' );
					
		}	
		
		function hidebackend_process_1() {
		
			$errorHandler = __( 'Settings Saved', $this->hook );
			
			$options = get_option( $this->primarysettings ); //load the options
			
			if ( get_option( 'permalink_structure' ) == '' && ! is_multisite() ) {
			
				if ( ! is_wp_error( $errorHandler ) ) {
					$errorHandler = new WP_Error();
				}
								
				$errorHandler->add( '2', __( 'You must enable permalinks in your WordPress settings for this feature to work.', $this->hook ) );
			
			}
			
			$options['hb_enabled'] = ( $_POST['hb_enabled'] == 1 ? 1 : 0 );
			$options['hb_login'] = sanitize_title( esc_html__( $_POST['hb_login'] ) );
			$options['hb_admin'] = sanitize_title( esc_html__( $_POST['hb_admin'] ) );
			$options['hb_register'] = sanitize_title( esc_html__( $_POST['hb_register'] ) );
			
			if ( $options['hb_key'] == '' ) {
				$options['hb_key'] = $this->hidebe_genKey();
			}
			
			if ( ! is_wp_error( $errorHandler ) ) {
			
				update_option( $this->primarysettings, $options );
				
				if ($this->bwpsserver == 'apache') {
				
					$this->writehtaccess();
					
				} else {
				
					$errorHandler = __( 'Settings Saved. You will have to manually add rewrite rules to your NGINX configuration. See the Better WP Security Dashboard for a list of the rewrite rules you will need.', $this->hook );
				
				}
				
			}
						
			$this-> showmessages( $errorHandler );
		
		}
		
		function intrusiondetection_process_1() {
		
			$errorHandler = __( 'Settings Saved', $this->hook );
			
			$options = get_option( $this->primarysettings );
			
			$options['id_enabled'] = ( $_POST['id_enabled'] == 1 ? 1 : 0 );
			$options['id_emailnotify'] = ( $_POST['id_emailnotify'] == 1 ? 1 : 0 );
			$options['id_checkinterval'] = absint( $_POST['id_checkinterval'] );
			$options['id_banperiod'] = absint( $_POST['id_banperiod'] );
			$options['id_threshold'] = absint( $_POST['id_threshold'] );
			
			if ( $options['id_banperiod'] == 0 ) {
			
				if (!is_wp_error( $errorHandler ) ) {
					$errorHandler = new WP_Error();
				}
						
				$errorHandler->add( '2', __( 'Lockout time period needs to be aan integer greater than 0.', $this->hook ) );
				
			}
			
			if ( $options['id_checkinterval'] == 0 ) {
			
				if ( ! is_wp_error( $errorHandler ) ) {
					$errorHandler = new WP_Error();
				}
						
				$errorHandler->add( '2', __( 'Login time period needs to be aan integer greater than 0.', $this->hook ) );
				
			}
			
			if ( $options['id_threshold'] == 0 ) {
			
				if ( ! is_wp_error( $errorHandler ) ) {
					$errorHandler = new WP_Error();
				}
						
				$errorHandler->add( '2', __('The error threshold needs to be aan integer greater than 0.', $this->hook ) );
				
			}
			
			$whiteList = explode( "\n", $_POST['id_whitelist'] );
			$whiteitems = array();
			
			if(!empty( $whiteList ) ) {
			
				foreach( $whiteList as $item ) {
				
					if ( strlen( $item ) > 0) {
					
						if ( strstr( $item,' - ' ) ) {
						
							$range = explode( '-', $item );
							$start = trim( $range[0] );
							$end = trim( $range[1] );
							
							if ( ip2long( $end ) == false ) {
							
								if ( ! is_wp_error( $errorHandler ) ) {
									$errorHandler = new WP_Error();
								}
								
								$errorHandler->add( '1', __( $item . ' contains an invalid ip (' . $end . ').', $this->hook ) );
								
							}
							
							if ( ip2long( $start ) == false ) {
							
								if ( ! is_wp_error( $errorHandler ) ) {
									$errorHandler = new WP_Error();
								}
								
								$errorHandler->add( '1', __( $item . ' contains an invalid ip (' . $start . ').', $this->hook ) );
								
							} else {
							
								$whiteitems[] = trim( $item );
								
							}	
								
						} else {
						
							$ipParts = explode( '.',$item );
							$isIP = 0;
							
							foreach ( $ipParts as $part ) {
							
								if ( ( is_numeric( trim( $part ) ) && trim( $part ) <= 255 && trim( $part ) >= 0 ) || trim( $part ) == '*' ) {
									$isIP++;
								}
								
							}
							
							if( $isIP == 4 ) {
							
								if ( ip2long( trim( str_replace( '*', '0', $item ) ) ) == false ) {
								
									if ( ! is_wp_error( $errorHandler ) ) {
										$errorHandler = new WP_Error();
									}
									
									$errorHandler->add( '1', __( $item . ' is not a valid ip.', $this->hook ) );
									
								} else {
								
									$whiteitems[] = trim( $item );
									
								}
								
							} else {
							
	    						if ( ! is_wp_error( $errorHandler ) ) {
									$errorHandler = new WP_Error();
								}
								
								$errorHandler->add( '1', __( $item . ' is note a valid ip.', $this->hook ) );
								
							}
							
						}
						
					}
					
				}
				
			}
			
			$options['id_whitelist'] = implode( "\n",$whiteitems );
			
			if ( ! is_wp_error( $errorHandler ) ) {
				update_option( $this->primarysettings, $options );
			}
						
			$this-> showmessages( $errorHandler );
		
		}
		
		function loginlimits_process_1() {
		
			$errorHandler = __( 'Settings Saved', $this->hook );
			
			$options = get_option( $this->primarysettings ); //load the options
			
			$options['ll_enabled'] = ( $_POST['ll_enabled'] == 1 ? 1 : 0 );
			$options['ll_emailnotify'] = ( $_POST['ll_emailnotify'] == 1 ? 1 : 0 );
			$options['ll_maxattemptshost'] = absint( $_POST['ll_maxattemptshost'] );
			$options['ll_maxattemptsuser'] = absint( $_POST['ll_maxattemptsuser'] );
			$options['ll_checkinterval'] = absint( $_POST['ll_checkinterval'] );
			$options['ll_banperiod'] = absint( $_POST['ll_banperiod'] );
			
			if ( $options['ll_banperiod'] == 0 ) {
			
				if ( ! is_wp_error( $errorHandler ) ) {
					$errorHandler = new WP_Error();
				}
						
				$errorHandler->add( '2', __( 'Lockout time period needs to be aan integer greater than 0.', $this->hook ) );
				
			}
			
			if ( $options['ll_checkinterval'] == 0 ) {
			
				if ( ! is_wp_error( $errorHandler ) ) {
					$errorHandler = new WP_Error();
				}
						
				$errorHandler->add( '2', __( 'Login time period needs to be aan integer greater than 0.', $this->hook ) );
				
			}
			
			if ( $options['ll_maxattemptshost'] == 0 ) {
			
				if ( ! is_wp_error( $errorHandler ) ) {
					$errorHandler = new WP_Error();
				}
						
				$errorHandler->add( '2', __( 'Max login attempts per host needs to be aan integer greater than 0.', $this->hook ) );
				
			}
			
			if ( $options['ll_maxattemptsuser'] == 0 ) {
			
				if ( ! is_wp_error( $errorHandler ) ) {
					$errorHandler = new WP_Error();
				}
						
				$errorHandler->add( '2', __( 'Max login attempts per user needs to be aan integer greater than 0.', $this->hook ) );
				
			}
			
			
			if ( ! is_wp_error( $errorHandler ) ) {
				update_option( $this->primarysettings, $options );
			}
						
			$this-> showmessages( $errorHandler );
			
		}
		
		function log_process_1() {
		
			global $wpdb;
			
			$errorHandler = __( 'The selected records have been cleared.', $this->hook );
			
			$options = get_option( $this->primarysettings ); //load the options
			
			if ( $_POST['badlogins'] == 1 ) {
				$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "bwps_log` WHERE `timestamp` < " . ( time() - ( $options['ll_checkinterval'] * 60 ) ) . " AND `type` = 1;" );
			}
			
			if ( $_POST['404s'] == 1 ) {
				$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "bwps_log` WHERE `timestamp` < " . ( time() - ( $options['id_checkinterval'] * 60 ) ) . " AND `type` = 2;" );
			}
			
			if ( $_POST['lockouts'] == 1 ) {
				$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "bwps_lockouts` WHERE `exptime` < " . time() . " OR `active` = 0;" );
			}
			
			if ( ! is_wp_error( $errorHandler ) ) {
				update_option( $this->primarysettings, $options );
			}
						
			$this-> showmessages( $errorHandler );
		}
		
		function log_process_2() {
			global $wpdb;
			
			$errorHandler = __( 'The selected lockouts have been cleared.', $this->hook );
			
			foreach ( $_POST as $key => $value ) {
			
				if ( strstr( $key, "lo_" ) ) {
				
					$wpdb->update(
						$wpdb->base_prefix . 'bwps_lockouts',
						array(
							'active' => 0
						),
						array(
							'id' => $value
						)
					);
					
				}
				
			}
			
			$this-> showmessages( $errorHandler );
			
		}
		
		function systemtweaks_process_1() {
		
			$errorHandler = __( 'Settings Saved', $this->hook );
			
			$options = get_option( $this->primarysettings ); //load the options
			
			$options['st_ht_files'] = ( $_POST['st_ht_files'] == 1 ? 1 : 0 );
			$options['st_ht_browsing'] = ( $_POST['st_ht_browsing'] == 1 ? 1 : 0 );
			$options['st_ht_request'] = ( $_POST['st_ht_request'] == 1 ? 1 : 0 );
			$options['st_ht_query'] = ( $_POST['st_ht_query'] == 1 ? 1 : 0 );
			
			if ( ! is_wp_error( $errorHandler ) ) {
			
				update_option( $this->primarysettings, $options );
				
				if ($this->bwpsserver == 'apache') {
				
					$this->writehtaccess();
					
				} else {
				
					$errorHandler = __( 'Settings Saved. You will have to manually add rewrite rules to your NGINX configuration. See the Better WP Security Dashboard for a list of the rewrite rules you will need.', $this->hook );
				
				}
				
			}
						
			$this-> showmessages( $errorHandler );
			
		}
		
		function systemtweaks_process_2() {
		
			$errorHandler = __( 'Settings Saved', $this->hook );
			
			$options = get_option( $this->primarysettings ); //load the options
			
			$options['st_generator'] = ( $_POST['st_generator'] == 1 ? 1 : 0 );
			$options['st_manifest'] = ( $_POST['st_manifest'] == 1 ? 1 : 0 );
			$options['st_edituri'] = ( $_POST['st_edituri'] == 1 ? 1 : 0 );
			$options['st_themenot'] = ( $_POST['st_themenot'] == 1 ? 1 : 0 );
			$options['st_pluginnot'] = ( $_POST['st_pluginnot'] == 1 ? 1 : 0 );
			$options['st_corenot'] = ( $_POST['st_corenot'] == 1 ? 1 : 0 );
			$options['st_enablepassword'] = ( $_POST['st_enablepassword'] == 1 ? 1 : 0 );
			if ( ctype_alpha( wp_strip_all_tags( $_POST['st_passrole'] ) ) ) {
				$options['st_passrole'] = wp_strip_all_tags( $_POST['st_passrole'] );
			}
			$options['st_loginerror'] = ( $_POST['st_loginerror'] == 1 ? 1 : 0 );
			$options['st_randomversion'] = ( $_POST['st_randomversion'] == 1 ? 1 : 0 );
			$options['st_longurl'] = ( $_POST['st_longurl'] == 1 ? 1 : 0 );
			$options['st_fileedit'] = ( $_POST['st_fileedit'] == 1 ? 1 : 0 );
			$options['st_forceloginssl'] = ( $_POST['st_forceloginssl'] == 1 ? 1 : 0 );
			$options['st_forceadminssl'] = ( $_POST['st_forceadminssl'] == 1 ? 1 : 0 );
			
			if ( ! is_wp_error( $errorHandler ) ) {
			
				update_option( $this->primarysettings, $options );
				$this->writewpconfig();
				
			}
						
			$this-> showmessages( $errorHandler );
		
		}
	
	}

}
