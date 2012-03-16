<?php

if ( ! class_exists( 'bwps_backup' ) ) {

	class bwps_backup extends bit51_bwps {
		
		/**
		 * Schedules database backups
		 *
		 **/
		function __construct() {
		
			add_action( 'bwps_backup', array( &$this, 'execute_backup' ) );
						
			$options = get_option( $this->primarysettings );
			
			if ( $options['backup_enabled'] == 1 ) {
			
				$nextbackup = $options['backup_next']; //get next schedule
				
				if ( $nextbackup == '' || $nextbackup < time() ) {
					
					switch ( $options['backup_int'] ) { //schedule backup at appropriate time
						case 'hourly':
							$next = 60 * 60;
							break;
						case 'twicedaily':
							$next = 60 * 60 * 12;
							break;
						case 'daily':
							$next = 60 * 60 * 24;
							break;
					}
					
					$options['backup_next'] = ( time() + $next );
					
					update_option( $this->primarysettings, $options );
					
					$this->execute_backup(); //execute backup
					
				}
				
			}
			
		}
		
		/**
		 * Executes database backup
		 *
		 */
		function execute_backup() {
			global $wpdb;
				
			@ini_set( 'auto_detect_line_endings', true );
				
			$options = get_option( $this->primarysettings );
				
			//get all of the tables
			$tables = $wpdb->get_results( 'SHOW TABLES', ARRAY_N );
				
			$return = '';
				
			//cycle through each table
			foreach($tables as $table) {
				
				$result = $wpdb->get_results( 'SELECT * FROM `' . $table[0] . '`;', ARRAY_N );
				$num_fields = sizeof( $wpdb->get_results( 'DESCRIBE `' . $table[0] . '`;' ) );
					
				$return.= 'DROP TABLE IF EXISTS `' . $table[0] . '`;';
				$row2 = $wpdb->get_row( 'SHOW CREATE TABLE `' . $table[0] . '`;', ARRAY_N );
				$return.= PHP_EOL . PHP_EOL . $row2[1] . ";" . PHP_EOL . PHP_EOL;
					
				foreach( $result as $row ) {
						
					$return .= 'INSERT INTO `' . $table[0] . '` VALUES(';
							
					for( $j=0; $j < $num_fields; $j++ ) {
							
						$row[$j] = addslashes( $row[$j] );
						$row[$j] = ereg_replace( PHP_EOL, "\n", $row[$j] );
								
						if ( isset( $row[$j] ) ) { 
							$return .= '"' . $row[$j] . '"' ; 
						} else { 
							$return.= '""'; 
						}
								
						if ( $j < ( $num_fields - 1 ) ) { 
							$return .= ','; 
						}
								
					}
							
					$return .= ");" . PHP_EOL;
							
				}
						
				$return .= PHP_EOL . PHP_EOL;
						
			}
					
			$return .= PHP_EOL . PHP_EOL;
				
			//save file
			$file = 'database-backup-' . time();
			$handle = @fopen( BWPS_PP . '/backups/' . $file . '.sql', 'w+' );
			fwrite( $handle, $return );
			@fclose( $handle );
		
			//zip the file
			if ( class_exists( 'ZipArchive' ) ) {
				
				$zip = new ZipArchive();
				$archive = $zip->open(BWPS_PP . '/backups/' . $file . '.zip', ZipArchive::CREATE);
				$zip->addFile(BWPS_PP . '/backups/' . $file . '.sql', $file . '.sql' );
				$zip->close();
			
				//delete .sql and keep zip
				unlink(BWPS_PP . '/backups/' . $file . '.sql');
				$fileext = '.zip';
				
			} else {
			
				$fileext = '.sql';
					
			}
				
			if ( $options['backup_email'] == 1 ) {
				
				$to = get_option( 'admin_email' );
				$headers = 'From: ' . get_option( 'blogname' ) . ' <' . $to . '>' . PHP_EOL;
				$subject = __( 'Site Database Backup', $this->hook ) . ' ' . date( 'l, F jS, Y \a\\t g:i a', strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s',time() ) ) ) );
				$attachment = array( BWPS_PP . '/backups/' . $file . $fileext );
				$message = __( 'Attached is the backup file for the database powering', $this->hook ) . ' ' . get_option( 'siteurl' ) . __( ' taken', $this->hook ) . ' ' . date( 'l, F jS, Y \a\\t g:i a', strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s',time() ) ) ) );
				
				wp_mail( $to, $subject, $message, $headers, $attachment );
					
				$files = scandir( BWPS_PP . '/backups/', 1 );
					
				foreach ( $files as $file ) {
					if ( strstr( $file, 'database-backup' ) ) {
						unlink ( BWPS_PP . '/backups/' . $file );
					}
				}
			
			}
				
			//delete extra files
			if ( $options['backups_to_retain'] != 0 ) {
				$files = scandir( BWPS_PP . '/backups/', 1 );
				
				$count = 0;
				
				foreach ( $files as $file ) {
					if ( strstr( $file, 'database-backup' ) ) {
						if ( $count >= $options['backups_to_retain'] ) {
							unlink ( BWPS_PP . '/backups/' . $file );
						}
						$count++;
					}	
				}
			}
				
			$options['backup_last'] = time();
				
			update_option( $this->primarysettings, $options );
				
		}
	
	}

}