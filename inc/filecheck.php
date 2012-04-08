<?php

if ( ! class_exists( 'bwps_filecheck' ) ) {

	class bwps_filecheck extends bit51_bwps {
	
		/**
		 * Initialize file checker
		 *
		 * Initializes file checker object, runs file checker on schedule and adds admin warning (if applicable)
		 *
		 **/
		function __construct() {
		
			global $bwpsoptions;
			
			//only exececute if it has been more than 24 hours or the check has never occured and file checking is enabled.
			if ( $bwpsoptions['id_fileenabled'] == 1 && ( $bwpsoptions['id_filechecktime'] == '' || $bwpsoptions['id_filechecktime'] < ( time() - 86400 ) ) ) {
			
				//die( 'Last Time: ' . $bwpsoptions['id_filechecktime'] );
			
				$this->execute_filecheck();
			
			}
			
			//add action for admin warning
			// @todo make network capable
			add_action( 'admin_init', array( &$this, 'warning' ) );
		
		}
		
		/**
		 * Check file list
		 *
		 * Checks if given file should be included in file check based on exclude/include options
		 *
		 * @param string $file path of file to check from site root
		 * @return bool true if file should be checked false if not
		 *
		 **/
		function checkFile ( $file ) {
		
			global $bwpsoptions;
			
			//get file list from last check
			$list = $bwpsoptions['id_specialfile'];
			
			//assume not a directory and not checked
			$flag = false;
			$isDir = false;
			
			//if list is empty return true
			if ( trim( $list ) != '' ) {
			
				$list = explode( "\n", $list );
				
			} else {
			
				//if empty include list we include nothing. If empty exclude list we include everything
				if ( $bwpsoptions['id_fileincex'] == 1 ) {
			
					return true;
					
				} else {
				
					return false;
					
				}
				
			}
			
			//compare file to list
			foreach ( $list as $item ) {
			
				//$file is a directory
				if ( is_dir( ABSPATH . $file ) ) {
				
					$isDir = true;
				
					$pathinfo = pathinfo( trim( $file ) );
						
					if ( strcmp( $pathinfo['dirname'], trim( $item ) ) == 0 ) {
					
						$flag = true;
								
					}
				
				} else { //$file is a file
				
					if ( strpos( $item , '.' ) === 0) { //list item is a file extension
					
						if ( strcmp( '.' . trim( end ( explode( '.' , $file ) ) ), trim( $item ) ) == 0 ) {
					 	
							$flag = true;
					 	
						 }
				
					} else { //list item is a single file
				
						if ( strcmp( trim( $item ), trim( end ( explode( '/' , $file ) ) ) ) == 0 ){
					
							$flag = true;
						
						}
				
					}
					
				}
				
			}
			
			if ( $bwpsoptions['id_fileincex'] == 1 ) {
			
				if ( $flag == true ) { //if exclude reverse
					return false;
				} else {
					return true;
				}
			
			} elseif ( $isDir == true ) { //reverse properly for directories
				
				if ( $flag == true ) {
					return false;
				} else {
					return true;
				}
				
			} else { //return flag 
			
				return $flag;
				
			}
		
		}
		
		/**
		 * Executes filecheck
		 *
		 * Executes file checking for all operations
		 *
		 * @param bool $auto[optional] is this an automatic check
		 *
		 **/
		function execute_filecheck( $auto = true ) {
		
			global $wpdb, $bwpsoptions, $logid;
			
			//get old file list
			if ( is_multisite() ) {
					
				switch_to_blog( 1 );
					
				$logItems = maybe_unserialize( get_option( 'bwps_file_log' ) );
					
				restore_current_blog();
					
			} else {
					
				$logItems = maybe_unserialize( get_option( 'bwps_file_log' ) );
						
			}
			
			//if there are no old files old file list is an empty array
			if ( $logItems === false ) {
			
				$logItems = array();
			
			} 
			
			$currItems = $this->scanfiles(); //scan current files
			
			$added = array_diff_assoc( $currItems, $logItems ); //files added
			$removed = array_diff_assoc( $logItems, $currItems ); //files deleted
			$compcurrent = array_diff_key( $currItems, $added ); //remove all added files from current filelist
			$complog = array_diff_key( $logItems, $removed );  //remove all deleted files from old file list
			$changed = array(); //array of changed files
			
			//compare file hashes and mod dates
			foreach ( $compcurrent as $currfile => $currattr) {
			
				if ( array_key_exists( $currfile, $complog ) ) {
				
					//if attributes differ added to changed files array
					if ( strcmp( $currattr['mod_date'], $complog[$currfile]['mod_date'] ) != 0 || strcmp( $currattr['hash'], $complog[$currfile]['hash'] ) != 0 ) {
						$changed[$currfile]['hash'] = $currattr['hash'];
						$changed[$currfile]['mod_date'] = $currattr['mod_date'];
					}
				
				}
			
			}
			
			//get count of changes
			$addcount = sizeof( $added );
			$removecount = sizeof( $removed );
			$changecount = sizeof( $changed );
			
			//create single array of all changes
			$combined = array(
				'added' => $added,
				'removed' => $removed,
				'changed' => $changed
			);
			
			//save current files to log
			//Get the options
			if ( is_multisite() ) {
					
				switch_to_blog( 1 );
					
				update_option( 'bwps_file_log', serialize( $currItems ) );
					
				restore_current_blog();
					
			} else {
					
				update_option( 'bwps_file_log', serialize( $currItems ) );
						
			}
			
			//log check to database
			$wpdb->insert(
				$wpdb->base_prefix . 'bwps_log',
				array(
					'type' => '3',
					'timestamp' => time(),
					'host' => '',
					'user' => '',
					'url' => '',
					'referrer' => '',
					'data' => serialize( $combined )
				)
			);
			
			$logid = $wpdb->insert_id;
			
			//if not the first check and files have changed warn about changes
			if ( $bwpsoptions['id_filechecktime'] != '' ) {
			
				if ( $addcount != 0 || $removecount != 0 || $changecount != 0 ) {
			
					//Update the right options
					if ( is_multisite() ) {
					
						switch_to_blog( 1 );
					
						update_option( 'bwps_intrusion_warning', 1 );
					
						restore_current_blog();
					
					} else {
					
						update_option( 'bwps_intrusion_warning', 1 );
						
					}
				
					if ( $bwpsoptions['id_fileemailnotify'] == 1 ) {
						$this->fileemail();
					}
				
				}
				
			}
				
			//set latest check time
			$bwpsoptions['id_filechecktime'] = time();
				
			//Update the right options
			if ( is_multisite() ) {
						
				switch_to_blog( 1 );
						
				update_option( $this->primarysettings, $bwpsoptions );
					
				restore_current_blog();
						
			} else {
						
				update_option( $this->primarysettings, $bwpsoptions );
				
			}
		
		}
		
		/**
		 * Email report
		 *
		 * Sends a report to site admin email address if changes have been detected
		 *
		 **/
		function fileemail() {
			global $logid;
		
			//create all headers and subject
			$to = get_option( 'admin_email' );
			$headers = 'From: ' . get_option( 'blogname' ) . ' <' . $to . '>' . PHP_EOL;
			$subject = __( 'WordPress File Change Warning', $this->hook ) . ' ' . date( 'l, F jS, Y \a\\t g:i a e', strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s',time() ) ) ) );

			//create message
			$message = '<p>' . __('<p>A file (or files) on your site at ', $this->hook ) . ' ' . get_option( 'siteurl' ) . __( ' have been changed. Please review the report below to verify changes are not the result of a compromise.', $this->hook ) . '</p>';
			$message .= $this->getdetails( $logid, true ); //get report
			
			add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) ); //send as html
			
			wp_mail( $to, $subject, $message, $headers ); //send message
		
		}
		
		/**
		 * Get Report Details
		 *
		 * Returns details of all changed files found in given report
		 *
		 * @param string $id integer ID of report desired
		 * @param bool $email[optional] is this to be displayed in email
		 * @return string report details
		 *
		 **/
		function getdetails( $id, $email = false ) {
		
			global $wpdb;
			
			//get the change array
			$changes = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_log` WHERE id=" . absint( $id ) . " ORDER BY timestamp DESC;", ARRAY_A );
			
			$data = maybe_unserialize( $changes[0]['data'] );
				
			//seperate array by category
			$added = $data['added'];
			$removed = $data['removed'];
			$changed = $data['changed'];			
			$report = '<strong>' . __( 'Scan Time:', $this->hook ) . '</strong> ' . date( 'l, F jS g:i a e', $changes[0]['timestamp'] ) . "<br />" . PHP_EOL;
			$report .= '<strong>' . __( 'Files Added:', $this->hook ) . '</strong> ' . sizeof( $added ) . "<br />" . PHP_EOL;
			$report .= '<strong>' . __( 'Files Deleted:', $this->hook ) . '</strong> ' . sizeof( $removed ) . "<br />" . PHP_EOL;
			$report .= '<strong>' . __( 'Files Modified:', $this->hook ) . '</strong> ' . sizeof( $changed ) . "<br />" . PHP_EOL;
		
			if ( $email == true ) {
					
				$report .= '<h4>' . __( 'Files Added', $this->hook ) . '</h4>';
				$report .= '<table border="1" style="width: 100%; text-align: center;">' . PHP_EOL;
				$report .= '<tr>' . PHP_EOL;
				$report .= '<th>' . __( 'File', $this->hook ) . '</th>' . PHP_EOL;
				$report .= '<th>' . __( 'Modified', $this->hook ) . '</th>' . PHP_EOL;
				$report .= '<th>' . __( 'File Hash', $this->hook ) . '</th>' . PHP_EOL;
				$report .= '</tr>' . PHP_EOL;
				foreach ( $added as $item => $attr ) { 
					$report .= '<tr>' . PHP_EOL;
					$report .= '<td>' . $item . '</td>' . PHP_EOL;
					$report .= '<td>' . date( 'l F jS, Y \a\t g:i a e', strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', $attr['mod_date'] ) ) ) ) . '</td>' . PHP_EOL;
					$report .= '<td>' . $attr['hash'] . '</td>' . PHP_EOL;
					$report .= '</tr>' . PHP_EOL;
				}
				$report .= '</table>' . PHP_EOL;
			
				$report .= '<h4>' . __( 'Files Deleted', $this->hook ) . '</h4>';
				$report .= '<table border="1" style="width: 100%; text-align: center;">' . PHP_EOL;
				$report .= '<tr>' . PHP_EOL;
				$report .= '<th>' . __( 'File', $this->hook ) . '</th>' . PHP_EOL;
				$report .= '<th>' . __( 'Modified', $this->hook ) . '</th>' . PHP_EOL;
				$report .= '<th>' . __( 'File Hash', $this->hook ) . '</th>' . PHP_EOL;
				$report .= '</tr>' . PHP_EOL;
				foreach ( $removed as $item => $attr ) { 
					$report .= '<tr>' . PHP_EOL;
					$report .= '<td>' . $item . '</td>' . PHP_EOL;
					$report .= '<td>' . date( 'l F jS, Y \a\t g:i a e', strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', $attr['mod_date'] ) ) ) ) . '</td>' . PHP_EOL;
					$report .= '<td>' . $attr['hash'] . '</td>' . PHP_EOL;
					$report .= '</tr>' . PHP_EOL;
				}
				$report .= '</table>' . PHP_EOL;
			
				$report .= '<h4>' . __( 'Files Modified', $this->hook ) . '</h4>';
				$report .= '<table border="1" style="width: 100%; text-align: center;">' . PHP_EOL;
				$report .= '<tr>' . PHP_EOL;
				$report .= '<th>' . __( 'File', $this->hook ) . '</th>' . PHP_EOL;
				$report .= '<th>' . __( 'Modified', $this->hook ) . '</th>' . PHP_EOL;
				$report .= '<th>' . __( 'File Hash', $this->hook ) . '</th>' . PHP_EOL;
				$report .= '</tr>' . PHP_EOL;
				foreach ( $changed as $item => $attr ) { 
					$report .= '<tr>' . PHP_EOL;
					$report .= '<td>' . $item . '</td>' . PHP_EOL;
					$report .= '<td>' . date( 'l F jS, Y \a\t g:i a e', strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', $attr['mod_date'] ) ) ) ) . '</td>' . PHP_EOL;
					$report .= '<td>' . $attr['hash'] . '</td>' . PHP_EOL;
					$report .= '</tr>' . PHP_EOL;
				}
				$report .= '</table>' . PHP_EOL;
			
				return $report;
				
			} else {
			
				echo $report;
			
				$log_details_added_table = new log_details_added_table( $id );
				$log_details_added_table->prepare_items();
				$log_details_added_table->display();
				
				$log_details_removed_table = new log_details_removed_table( $id );
				$log_details_removed_table->prepare_items();
				$log_details_removed_table->display();
				
				$log_details_modified_table = new log_details_modified_table( $id );
				$log_details_modified_table->prepare_items();
				$log_details_modified_table->display();
			
			}
		
		}
		
		/**
		 * Scans all files in a given path
		 * 
		 * Scans all files in a given path and returns an array of filename, mod_date, and file hash
		 *
		 * @param string $path[optional] path to scan, defaults to WordPress root
		 * @return array array of files found and their information
		 *
		 **/
		function scanfiles( $path = '' ) {
			
			global $bwpsoptions;

            $data = array();

			if ( $dirHandle = @opendir( ABSPATH . $path ) ) { //get the directory
			
				while ( ( $item = readdir( $dirHandle ) ) !== false ) { // loop through dirs
					
					if ( $item != '.' && $item != '..' ) { //don't scan parent/etc

						$relname = $path . $item;
                        
						$absname = ABSPATH . $relname;
						
						if ( $this->checkFile( $relname ) == true ) { //make sure the user wants this file scanned
						
							if ( filetype( $absname ) == 'dir' ) { //if directory scan it
							
								$data = array_merge( $data, $this->scanfiles( $relname . '/' ) );
								
							} else { //is file so add to array
							
								$data[$relname] = array();
								$data[$relname]['mod_date'] = filemtime( $absname );
								$data[$relname]['hash'] = md5_file( $absname );
							
							}
						
						}
						
					}
					
				}   
				
				@closedir( $dirHandle ); //close the directory we're working with
                        
			} 
			
			return $data; // return the files we found in this dir
			
		}
		
		/**
		 * Display admin warning
		 *
		 * Displays a warning to adminstrators when file changes have been detected
		 *
		 **/
		function warning() {
		
			global $blog_id; //get the current blog id
			
			if ( is_multisite() && ( $blog_id != 1 || ! current_user_can( 'manage_network_options' ) ) ) { //only display to network admin if in multisite
				return;
			}
		
			//if there is a warning to display
			if ( get_option( 'bwps_intrusion_warning' ) == 1 ) {
			
				if ( ! function_exists( 'bit51_filecheck_warning' ) ) {
			
					function bit51_filecheck_warning(){
				
						global $plugname;
						global $plughook;
						global $plugopts;
						$adminurl = is_multisite() ? admin_url() . 'network/' : admin_url();
					
					    echo '<div class="error">
				       <p>' . __( 'Better WP Security has noticed a change to some files in your WordPress installation. Please review the logs to make sure your system has not been compromised.', $plughook ) . '</p> <p><input type="button" class="button " value="' . __( 'View Logs', $plughook ) . '" onclick="document.location.href=\'?bit51_view_logs=yes&_wpnonce=' .  wp_create_nonce('bit51-nag') . '\';">  <input type="button" class="button " value="' . __('Dismiss Warning', $plughook) . '" onclick="document.location.href=\'' . $adminurl . 'admin.php?bit51_dismiss_warning=yes&_wpnonce=' .  wp_create_nonce( 'bit51-nag' ) . '\';"></p>
					    </div>';
				    
					}
				
				}
				
				//put the warning in the right spot
				if ( is_multisite() ) {
					add_action( 'network_admin_notices', 'bit51_filecheck_warning' ); //register notification
				} else {
					add_action( 'admin_notices', 'bit51_filecheck_warning' ); //register notification
				}
				
			}
			
			//if they've clicked a button hide the notice
			if ( ( isset( $_GET['bit51_view_logs'] ) || isset( $_GET['bit51_dismiss_warning'] ) ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bit51-nag' ) ) {
				
				//Get the options
				if ( is_multisite() ) {
						
					switch_to_blog( 1 );
						
					delete_option( 'bwps_intrusion_warning' );
						
					restore_current_blog();
						
				} else {
						
					delete_option( 'bwps_intrusion_warning' );
							
				}
				
				//take them back to where they started
				if ( isset( $_GET['bit51_dismiss_warning'] ) ) {				
					wp_redirect( $_SERVER['HTTP_REFERER'], 302 );
				}
				
				//take them to the correct logs page
				if ( isset( $_GET['bit51_view_logs'] ) ) {
					if ( is_multisite() ) {
						wp_redirect( admin_url() . 'network/admin.php?page=better_wp_security-logs#file-change', 302 );
					} else {
						wp_redirect( admin_url() . 'admin.php?page=better_wp_security-logs#file-change', 302 );
					}
				}
				
			}
		
		}
	
	}

}