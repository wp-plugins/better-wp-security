<?php

if ( ! class_exists( 'bwps_filecheck' ) ) {

	class bwps_filecheck extends bit51_bwps {
	
		function __construct() {
		
			global $bwpsoptions;
			
			if ( $bwpsoptions['id_fileenabled'] == 1 && ( $bwpsoptions['id_filechecktime'] == '' || $bwpsoptions['id_filechecktime'] < ( time() - 86400 ) ) ) {
			
				$this->execute_filecheck();
				
				$bwpsoptions['id_filechecktime'] = time();
				
				update_option( $this->primarysettings, $bwpsoptions );
			
			}
			
			add_action( 'admin_init', array( &$this, 'warning' ) );
		
		}
		
		function checkFile ( $file ) {
		
			global $bwpsoptions;
			
			$list = $bwpsoptions['id_specialfile'];
			
			$flag = false;
			$isDir = false;
			
			if ( trim( $list ) != '' ) {
			
				$list = explode( "\n", $list );
				
			} else {
			
				return true;
				
			}
			
			foreach ( $list as $item ) {
			
				if ( is_dir( ABSPATH . $file ) ) {
				
					$isDir = true;
				
					$pathinfo = pathinfo( trim( $file ) );
						
					if ( strcmp( $pathinfo['dirname'], trim( $item ) ) == 0 ) {
					
						$flag = true;
								
					}
				
				} else {
				
					if ( strpos( $item , '.' ) === 0) { //a file extension
					
						if ( strcmp( '.' . trim( end ( explode( '.' , $file ) ) ), trim( $item ) ) == 0 ) {
					 	
							$flag = true;
					 	
						 }
				
					} else { //a file
				
						if ( strcmp( trim( $item ), trim( end ( explode( '/' , $file ) ) ) ) == 0 ){
					
							$flag = true;
						
						}
				
					}
					
				}
				
			}
			
			if ( $bwpsoptions['id_fileincex'] == 1 ) {
			
				if ( $flag == true ) {
					return false;
				} else {
					return true;
				}
			
			} elseif ( $isDir == true ) {
				
				if ( $flag == true ) {
					return false;
				} else {
					return true;
				}
				
			} else {		
			
				return $flag;
				
			}
		
		}
		
		function execute_filecheck( $auto = true ) {
		
			global $wpdb, $bwpsoptions;
			
			$logItems = maybe_unserialize( get_option( 'bwps_file_log' ) );
			
			if ( $logItems === false ) {
			
				$logItems = array();
			
			} 
			
			$currItems = $this->scanfiles();
			
			$added = array_diff_assoc( $currItems, $logItems );
			$removed = array_diff_assoc( $logItems, $currItems );
			$compcurrent = array_diff_key( $currItems, $added );
			$complog = array_diff_key( $logItems, $removed ); 
			$changed = array();
			
			foreach ( $compcurrent as $currfile => $currattr) {
			
				if ( array_key_exists( $currfile, $complog ) ) {
				
					if ( strcmp( $currattr['mod_date'], $complog[$currfile]['mod_date'] ) != 0 || strcmp( $currattr['hash'], $complog[$currfile]['hash'] ) != 0 ) {
						$changed[$currfile]['hash'] = $currattr['hash'];
						$changed[$currfile]['mod_date'] = $currattr['mod_date'];
					}
				
				}
			
			}
			
			$addcount = sizeof( $added );
			$removecount = sizeof( $removed );
			$changecount = sizeof( $changed );
			
			$combined = array(
				'added' => $added,
				'removed' => $removed,
				'changed' => $changed
			);
			
			update_option( 'bwps_file_log', serialize( $currItems ) );
			
			if ( $addcount != 0 || $removecount != 0 || $changecount != 0 ) {
			
				//log to database
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
			
				update_option( 'bwps_intrusion_warning', 1 );
				
				if ( $bwpsoptions['id_fileemailnotify'] == 1 ) {
					$this->fileemail();
				}
				
			}
		
		}
		
		function fileemail() {
		
			$to = get_option( 'admin_email' );
			$headers = 'From: ' . get_option( 'blogname' ) . ' <' . $to . '>' . PHP_EOL;
			$subject = __( 'WordPress File Change Warning', $this->hook ) . ' ' . date( 'l, F jS, Y \a\\t g:i a', strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s',time() ) ) ) );

			$message = '<p>' . __('<p>A file (or files) on your site at ', $this->hook ) . ' ' . get_option( 'siteurl' ) . __( ' have been changed. Please review the report below to verify changes are not the result of a compromise.', $this->hook ) . '</p>';
			$message .= $this->getdetails();
			
			add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
			wp_mail( $to, $subject, $message, $headers );
		
		}
		
		function getdetails( $id = '' ) {
		
			global $wpdb;
			
			if ( $id == '' ) {
			
				$maxtime = $wpdb->get_results( "SELECT  id, MAX(timestamp) FROM `" . $wpdb->base_prefix . "bwps_log` WHERE type=3;", ARRAY_A );
				
				$reportid = $maxtime[0]['id'];
				
			} else {
			
				$reportid = $id;
			
			}
		
			$changes = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_log` WHERE id=" . absint( $reportid ) . " ORDER BY timestamp DESC;", ARRAY_A );
		
			$data = maybe_unserialize( $changes[0]['data'] );
			
			$added = $data['added'];
			$removed = $data['removed'];
			$changed = $data['changed'];			
			
			$report = __( 'Scan Time:', $this->hook ) . ' ' . date( 'l, F jS g:i a e', $changes[0]['timestamp'] ) . "<br />" . PHP_EOL;
			$report .= __( 'Files Added:', $this->hook ) . ' ' . sizeof( $added ) . "<br />" . PHP_EOL;
			$report .= __( 'Files Deleted:', $this->hook ) . ' ' . sizeof( $added ) . "<br />" . PHP_EOL;
			$report .= __( 'Files Modified:', $this->hook ) . ' ' . sizeof( $added ) . "<br />" . PHP_EOL;
			
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
				$report .= '<td>' . date( 'n/j/y H:i:s', $attr['mod_date'] ) . '</td>' . PHP_EOL;
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
				$report .= '<td>' . date( 'n/j/y H:i:s', $attr['mod_date'] ) . '</td>' . PHP_EOL;
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
				$report .= '<td>' . date( 'n/j/y H:i:s', $attr['mod_date'] ) . '</td>' . PHP_EOL;
				$report .= '<td>' . $attr['hash'] . '</td>' . PHP_EOL;
				$report .= '</tr>' . PHP_EOL;
			}
			$report .= '</table>' . PHP_EOL;
			
			return $report;
		
		
		}
		
		function scanfiles( $path = '' ) {
			
			global $bwpsoptions;

            $data = array();

			if ( $dirHandle = @opendir( ABSPATH . $path ) ) {
			
				while ( ( $item = readdir( $dirHandle ) ) !== false ) { // loop through dirs
					
					if ( $item != '.' && $item != '..' ) {

						$relname = $path . $item;
                        
						$absname = ABSPATH . $relname;
						
						if ( $this->checkFile( $relname ) == true ) {
						
							if ( filetype( $absname ) == 'dir' ) {
							
								$data = array_merge( $data, $this->scanfiles( $relname . '/' ) );
								
							} else {
							
								$data[$relname] = array();
								$data[$relname]['mod_date'] = filemtime( $absname );
								$data[$relname]['hash'] = md5_file( $absname );
							
							}
						
						}
						
					}
					
				}   
				
				@closedir( $dirHandle );  
                        
			} 
			
			return $data; // return the files we found in this dir
			
		}
		
		function warning() {
		
			if ( get_option( 'bwps_intrusion_warning' ) == 1 ) {
			
				if ( ! function_exists( 'bit51_plugin_donate_notice' ) ) {
			
					function bit51_plugin_donate_notice(){
				
						global $plugname;
						global $plughook;
						global $plugopts;
					
					    echo '<div class="error">
				       <p>' . __( 'It looks like you\'ve been enjoying', $plughook ) . ' ' . $plugname . ' ' . __( 'for at least 30 days. Would you consider a small donation to help support continued development of the plugin?', $plughook ) . '</p> <p><input type="button" class="button " value="' . __( 'View Logs', $plughook ) . '" onclick="document.location.href=\'?bit51_view_logs=yes&_wpnonce=' .  wp_create_nonce('bit51-nag') . '\';">  <input type="button" class="button " value="' . __('Dismiss Warning', $plughook) . '" onclick="document.location.href=\'' . admin_url() . 'admin.php?bit51_dismiss_warning=yes&_wpnonce=' .  wp_create_nonce( 'bit51-nag' ) . '\';"></p>
					    </div>';
				    
					}
				
				}
				
				add_action( 'admin_notices', 'bit51_plugin_donate_notice' ); //register notification
				
			}
			
			//if they've clicked a button hide the notice
			if ( ( isset( $_GET['bit51_view_logs'] ) || isset( $_GET['bit51_dismiss_warning'] ) ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bit51-nag' ) ) {
			
				delete_option( 'bwps_intrusion_warning' );
				
				if ( isset( $_GET['bit51_dismiss_warning'] ) ) {				
					wp_redirect( $_SERVER['HTTP_REFERER'], 302 );
				}
				
				if ( isset( $_GET['bit51_view_logs'] ) ) {
					wp_redirect( admin_url() . 'admin.php?page=better_wp_security-logs#file-change', 302 );
				}
				
			}
		
		}
	
	}

}