<?php

if ( ! class_exists( 'bwps_admin_common' ) ) {

	abstract class bwps_admin_common extends bit51_bwps {
		
		/**
		 * Redirects to homepage if awaymode is active
		 *
		 **/
		function awaycheck() {
		
			global $bwps;
			
			if( $bwps->checkaway() ) {
				wp_redirect( get_option( 'siteurl' ) );
			}
			
		}
		
		/**
		 * Schedules database backups
		 *
		 **/
		function backup_scheduler() {
		
			add_action( 'bwps_backup', array( &$this, 'db_backup' ) );
			
			$options = get_option( $this->primarysettings );
			
			if ( $options['backup_enabled'] == 1 ) {
			
				if ( ! wp_next_scheduled( 'bwps_backup' ) ) {
					wp_schedule_event( time(), $options['backup_int'], 'bwps_backup' );
				}
				
			} else { //no recurring backups
			
				if ( wp_next_scheduled( 'bwps_backup' ) ) {
					wp_clear_scheduled_hook( 'bwps_backup' );
				}
				
			}
			
		}
		
		/**
		 * Executes database backup
		 *
		 */
		function db_backup() {
		
			global $wpdb;
			$this->errorHandler = '';
			
			//find backup library
			$backuppath = BWPS_PP . 'lib/phpmysqlautobackup/backups/';
			
			$options = get_option( $this->primarysettings );
			
			@require( BWPS_PP . 'lib/phpmysqlautobackup/run.php' );
			
			$wpdb->query( 'DROP TABLE `phpmysqlautobackup`;' );
			$wpdb->query( 'DROP TABLE `phpmysqlautobackup_log`;' );
			
		}
		
		/**
		 * Deletes BWPS options from .htaccess
		 *
		 * Deletes all possible BWPS options from .htaccess and cleans for rewrite
		 *
		 * @return int -1 for failure, 1 for success
		 *
		 **/
		function deletehtaccess( $section = 'Better WP Security' ) {
				
			$htaccess = ABSPATH . '.htaccess';
						
			$markerdata = explode( "\n", implode( '', file( $htaccess ) ) ); //parse each line of file into array
		
			if ( $markerdata ) { //as long as there are lines in the file
					
				$state = true;
						
				@chmod( $htaccess, 0644 );
						
				if ( ! $f = @fopen( $htaccess, 'w+' ) ) {
							
					return -1; //we can't write to the file
							
				}
						
				foreach ( $markerdata as $n => $markerline ) { //for each line in the file
						
					if ( strpos( $markerline, '# BEGIN ' . $section ) !== false ) { //if we're at the beginning of the section
						$state = false;
					}
							
					if ( $state == true ) { //as long as we're not in the section keep writing
						if ( $n + 1 < count( $markerdata ) ) {//make sure to add newline to appropriate lines
						
							fwrite( $f, "{$markerline}\n" );
							
						} else {
						
							fwrite( $f, "{$markerline}" );
							
						}
						
					}
							
					if ( strpos( $markerline, '# END ' . $section ) !== false ) { //see if we're at the end of the section
						$state = true;
					}
							
				}
						
				fclose( $f );
						
				@chmod( $htaccess, 0444 );
						
				return 1;
						
			}
				
			return 1; //nothing to write
					
		}
		
		/**
		 * Deletes BWPS options from wp-config
		 *
		 * Deletes all possible BWPS options from wp-config and cleans for rewrite
		 *
		 * @return int -1 for failure, 1 for success
		 *
		 **/
		function deletewpconfig() {
		
			$configfile = $this->getConfig();
							
				$lines = explode( "\n", implode( '', file( $configfile ) ) ); //parse each line of file into array
			
				if ( $lines ) { //as long as there are lines in the file
						
					$state = true;
							
					@chmod( $configfile, 0644 );
							
					if ( ! $f = @fopen( $configfile, 'w+' ) ) {
								
						return -1; //we can't write to the file
								
					}
							
					foreach ( $lines as $line ) { //for each line in the file
						
						if ( ! strstr( $line, 'DISALLOW_FILE_EDIT' ) && ! strstr( $line, 'FORCE_SSL_LOGIN' ) && ! strstr( $line, 'FORCE_SSL_ADMIN' ) ) {
						
							if ( $n + 1 < count( $lines ) ) {//make sure to add newline to appropriate lines
							
								fwrite( $f, "{$line}\n" );
								
							} else {
							
								fwrite( $f, "{$line}" );
								
							}
						
						}
														
					}
							
					fclose( $f );
							
					@chmod( $configfile, 0444 );
							
					return 1;
							
				}
					
				return 1; //nothing to write
				
		}
		
		/**
		 * Gets location of wp-config.php
		 *
		 * Finds and returns path to wp-config.php
		 *
		 * @return string path to wp-config.php
		 *
		 **/
		function getConfig() {
		
			if ( file_exists( trailingslashit( ABSPATH ) . 'wp-config.php' ) ) {
			
				return trailingslashit( ABSPATH ) . 'wp-config.php';
				
			} else {
			
				return trailingslashit( dirname( ABSPATH ) ) . 'wp-config.php';
				
			}
			
		}
		
		/**
		 * Generates rewrite rules
		 *
		 * Generates rewrite rules for use in Apache or NGINX
		 *
		 * @return string|boolean Rewrite rules or false if unsupported server
		 *
		 **/
		function getrules() {
		
			//figure out what server they're using
			if ( strstr( strtolower( $_SERVER['SERVER_SOFTWARE'] ), 'apache' ) ) {
			
				$bwpsserver = 'apache';
				
			} else if ( strstr( strtolower( $_SERVER['SERVER_SOFTWARE'] ), 'nginx' ) ) {
			
				$bwpsserver = 'nginx';
				
			} else { //unsupported server
			
				return false;
			
			}
		
			$options = get_option( $this->primarysettings );
			
			$rules = '';
			
			//remove directory indexing
			if ( $options['st_ht_browsing'] == 1 ) {
			
				if ( $bwpsserver == 'apache' ) {
				
					$rules .= "Options All -Indexes\n\n";
				
				}
				
			}
			
			//ban hosts
			if ( $options['bu_enabled'] == 1 ) {
			
				$hosts = explode( "\n", $options['bu_individual'] );
				
				if ( ! empty( $hosts ) ) {
				
					if ( $bwpsserver == 'apache' ) {
					
						$rules .= "Order allow,deny\n" .
						"Allow from all\n" .
						"Deny from ";
				
						foreach ( $hosts as $host ) {
						
							$rules .= trim( $host ) . ' ';
						
						}
					
						$rules .= "\n\n";
						
					} else {
					
						foreach ( $hosts as $host ) {
						
							$rules .= "\tdeny " . trim( $host ) . "\n";
						
						}
						
						$rules .= "\n";
					
					}
				
				}
			
			}
			
			//lockdown files
			if ( $options['st_ht_files'] == 1 ) {
			
				if ( $bwpsserver == 'apache' ) {
				
					$rules .= 
						"<files .htaccess>\n" .
							"Order allow,deny\n" . 
							"Deny from all\n" .
						"</files>\n\n" .
						"<files readme.html>\n" .
							"Order allow,deny\n" . 
							"Deny from all\n" .
						"</files>\n\n" .
						"<files install.php>\n" .
							"Order allow,deny\n" . 
							"Deny from all\n" .
						"</files>\n\n" .
						"<files wp-config.php>\n" .
							"Order allow,deny\n" . 
							"Deny from all\n" .
						"</files>\n\n";
					
				} else {
				
					$rules .= 
						"\tlocation ~ /\.ht {\n" .
						"\t\tdeny all;\n" .
						"\t}\n\n" .
						"\tlocation ~ wp-config.php {\n" .
						"\t\tdeny all;\n".
						"\t}\n\n" .
						"\tlocation ~ readme.html {\n" .
						"\t\tdeny all;\n" .
						"\t}\n\n" .
						"\tlocation ~ install.php {\n" .
						"\t\tdeny all;\n".
						"\t}\n\n";
				}
				
			}
			
			//start mod_rewrite rules
			if ( $options['st_ht_request'] == 1 || $options['st_ht_query'] == 1 || $options['hb_enabled'] == 1 || ( $options['bu_enabled'] == 1 && strlen(  $options['bu_banagent'] ) > 0 ) ) {
			
				if ( $bwpsserver == 'apache' ) {
				
					$rules .= "<IfModule mod_rewrite.c>\n" . 
						"RewriteEngine On\n\n";
				
				} else {
				
					$rules .= 
						"\tset \$susquery 0;\n" .
						"\tset \$rule_2 0;\n" .
						"\tset \$rule_3 0;\n\n";
				
				}
			
			}
			
			//ban hosts and agents
			if ( $options['bu_enabled'] == 1 && strlen(  $options['bu_banagent'] ) > 0 ) {
				
				$agents = explode( "\n", $options['bu_banagent'] );
				
				if ( ! empty( $agents ) ) {
				
					if ( $bwpsserver == 'apache' ) {
					
						$count = 1;
				
						foreach ( $agents as $agent ) {
							
							$rules .= "RewriteCond %{HTTP_USER_AGENT} ^" . trim( $agent ) . "$";
							
							if ( $count < sizeof( $agents ) ) {
							
								$rules .= " [OR]\n";
								$count++;
							
							} else {
							
								$rules .= "\n";
							
							}
							
						}
					
						$rules .= "RewriteRule ^(.*)$ - [F,L]\n\n";
						
					} else {
					
						$count = 1;
						$alist = '';
						
						foreach ( $agents as $agent ) {
									
							$alist .= trim( $agent );
									
							if ( $count < sizeof( $agents ) ) {
									
								$agents .= "|";
								$count++;
									
							}
									
						}
							
						$rules .= 
							"\tif (\$http_user_agent ~* " . $alist . ") {\n" .
							"\t\treturn 403;\n" .
							"\t}\n\n";
					}
				
				}
			
			}
			
			if ( $options['st_ht_files'] == 1 ) {
			
				if ( $bwpsserver == 'apache' ) {
				
					$rules .= "RewriteRule ^wp-admin/includes/ - [F,L]\n\n" .
						"RewriteRule !^wp-includes/ - [S=3]\n\n" .
						"RewriteRule ^wp-includes/[^/]+\.php$ - [F,L]\n\n" .
						"RewriteRule ^wp-includes/js/tinymce/langs/.+\.php - [F,L]\n\n" .
						"RewriteRule ^wp-includes/theme-compat/ - [F,L]\n\n";
					
				} else {
				
					$rules .= 
						"\trewrite ^wp-includes/(.*).php /not_found last;\n" .
						"\trewrite ^/wp-admin/includes(.*)$ /not_found last;\n\n";
				
				}
				
			}
			
			if ( $options['st_ht_request'] == 1 ) {
			
				if ( $bwpsserver == 'apache' ) {
				
					$rules .= "RewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK) [NC]\n" . 
						"RewriteRule ^(.*)$ - [F,L]\n\n";
				
				} else {
				
					$rules .= 
					"\tif (\$request_method ~* \"^(TRACE|DELETE|TRACK)\"){\n" .
					"\t\treturn 403;\n" .
					"\t}\n\n";
				
				}
				
			}
			
			//filter suspicious queries
			if ( $options['st_ht_query'] == 1 ) {
			
				if ( $bwpsserver == 'apache' ) {
				
					$rules .= "RewriteCond %{QUERY_STRING} \.\.\/ [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} boot\.ini [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} tag\= [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} ftp\:  [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} http\:  [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} https\:  [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|%3D) [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} ^.*(\[|\]|\(|\)|<|>|ê|\"|;|\?|\*|=$).* [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} ^.*(&#x22;|&#x27;|&#x3C;|&#x3E;|&#x5C;|&#x7B;|&#x7C;).* [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} ^.*(%24&x).* [NC,OR]\n" .  
						"RewriteCond %{QUERY_STRING} ^.*(%0|%A|%B|%C|%D|%E|%F|127\.0).* [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} ^.*(globals|encode|localhost|loopback).* [NC,OR]\n" . 
						"RewriteCond %{QUERY_STRING} ^.*(request|select|insert|union|declare).* [NC]\n" . 
						"RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in_.*$\n" .
						"RewriteRule ^(.*)$ - [F,L]\n\n";
				
				} else {
				
					$rules .= 
					
						"\tif (\$args ~* \"\\.\\./\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .										
						"\tif (\$args ~* \"boot.ini\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"tag=\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .										
						"\tif (\$args ~* \"ftp:\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"http:\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"https:\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"(<|%3C).*script.*(>|%3E)\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"mosConfig_[a-zA-Z_]{1,21}(=|%3D)\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"base64_encode\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"(%24&x)\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"(\\[|\\]|\\(|\\)|<|>|ê|\\\"|;|\?|\*|=$)\"){\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"(&#x22;|&#x27;|&#x3C;|&#x3E;|&#x5C;|&#x7B;|&#x7C;|%24&x)\"){\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"(%0|%A|%B|%C|%D|%E|%F|127.0)\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"(globals|encode|localhost|loopback)\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n" .
						"\tif (\$args ~* \"(request|select|insert|union|declare)\") {\n" .
						"\t\tset \$susquery 1;\n" .
						"\t}\n\n";
				
				}
				
			}
			
			if ( $bwpsserver == 'nginx' ) {
			
				$rules .= 
					"\tif (\$http_cookie !~* \"wordpress_logged_in_\" ) {\n" .
					"\t\tset \$susquery \"\${susquery}2\";\n" .
					"\t\tset \$rule_2 1;\n" .
					"\t\tset \$rule_3 1;\n" .
					"\t}\n\n";
			
			}
			
			if ( $options['st_ht_query'] == 1 ) {
			
				if ( $bwpsserver == 'nginx' ) {
			
					$rules .= 
						"\tif (\$susquery = 12) {\n" .
						"\t\treturn 403;\n" .
						"\t}\n\n";
						
				}
				
			}
			
			//hide backend rules	
			if ( $options['hb_enabled'] == 1 ) {
					
				//get the slugs
				$login = $options['hb_login'];
				$admin = $options['hb_admin'];
				$register = $options['hb_register'];
							
				//generate the key
				$key = $options['hb_key'];
					
				//get the domain without subdomain
				$reDomain = $this->topdomain( get_option( 'siteurl' ) );
				
				$siteurl = explode( '/', get_option( 'siteurl' ) );

				if ( isset ( $siteurl[3] ) ) {

					$dir = '/' . $siteurl[3] . '/';
       
				} else {

					$dir = '/';

				}
			
				//hide wordpress backend
				if ( $bwpsserver == 'apache' ) {
					
					$rules .= "RewriteRule ^" . $login . "$ " . $dir . "wp-login.php?" . $key . " [R,L]\n\n" .
						"RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in_.*$\n" .
						"RewriteRule ^" . $admin . "$ " . $dir . "wp-login.php?" . $key . "&redirect_to=" . $dir . "wp-admin/ [R,L]\n\n" .
						"RewriteRule ^" . $admin . "$ " . $dir . "wp-admin/?" . $key . " [R,L]\n\n" .
						"RewriteRule ^" . $register . "$ " . $dir . "wp-login.php?" . $key . "&action=register [R,L]\n\n" .
						"RewriteCond %{HTTP_REFERER} !^" . $reDomain . $dir . "wp-admin \n" .
						"RewriteCond %{HTTP_REFERER} !^" . $reDomain . $dir . "wp-login\.php \n" .
						"RewriteCond %{HTTP_REFERER} !^" . $reDomain . $dir . $login . " \n" .
						"RewriteCond %{HTTP_REFERER} !^" . $reDomain . $dir . $admin . " \n" .
						"RewriteCond %{HTTP_REFERER} !^" . $reDomain . $dir . $register . " \n" .
						"RewriteCond %{QUERY_STRING} !^" . $key . " \n" .
						"RewriteCond %{QUERY_STRING} !^action=logout\n" . 
						"RewriteCond %{QUERY_STRING} !^action=rp\n" . 
						"RewriteCond %{QUERY_STRING} !^action=register\n" .
						"RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in_.*$\n" .
						"RewriteRule ^.*wp-admin/?|^.*wp-login\.php not_found [L]\n\n" .
						"RewriteCond %{QUERY_STRING} ^loggedout=true\n" .
						"RewriteRule ^.*$ " . $dir . "wp-login.php?" . $key . " [R,L]\n";
							
				} else {
					
					$rules .= 
						"\trewrite ^" . $dir . $login . "$ " . $dir . "wp-login.php?" . $key . " redirect;\n\n" .
						"\tif (\$rule_2 = 1) {\n" .
						"\t\trewrite ^" . $dir . $admin . "$ " . $dir . "wp-login.php?" . $key . "&redirect_to=/wp-admin/ redirect;\n" .
						"\t}\n\n" .
						"\tif (\$rule_2 = 0) {\n" .
						"\t\trewrite ^" . $dir . $admin . "$ " . $dir . "wp-admin/?" . $key . " redirect;\n" .
						"\t}\n\n" .
						"\trewrite ^" . $dir . $register . "$ " . $dir . "wp-login.php?" . $key . "&action=register redirect;\n\n" .
						"\tif (\$http_referer !~* wp-admin ) {\n" .
						"\t\tset \$rule_3 \"\${rule_3}1\";\n" .
						"\t}\n\n" .
						"\tif (\$http_referer !~* wp-login.php ) {\n" .
						"\t\tset \$rule_3 \"\${rule_3}1\";\n" .
						"\t}\n\n" .
						"\tif (\$http_referer !~* " . $login . " ) {\n" .
						"\t\tset \$rule_3 \"\${rule_3}1\";\n" .
						"\t}\n\n" .
						"\tif (\$http_referer !~* " . $admin . " ) {\n" .
						"\t\tset \$rule_3 \"\${rule_3}1\";\n" .
						"\t}\n\n" .
						"\tif (\$http_referer !~* " . $register . " ) {\n" .
						"\t\tset \$rule_3 \"\${rule_3}1\";\n" .
						"\t}\n\n" .
						"\tif (\$args !~ \"^action=logout\") {\n" .
						"\t\tset \$rule_3 \"\${rule_3}1\";\n" .
						"\t}\n\n" .
						"\tif (\$args !~ \"^" . $key . "\") {\n" .
						"\t\tset \$rule_3 \"\${rule_3}1\";\n" .
						"\t}\n\n" .
						"\tif (\$args !~ \"^action=rp\") {\n" .
						"\t\tset \$rule_3 \"\${rule_3}1\";\n" .
						"\t}\n\n" .
						"\tif (\$rule_3 = 111111111) {\n" .
						"\t\trewrite ^(.*/)?wp-login.php " . $dir . "not_found last;\n" .
						"\t\trewrite ^" . $dir . "wp-admin(.*)$ " . $dir . "not_found last;\n" .
						"\t}\n\n";
				
				}
				
				//close mod_rewrite
				if ( $options['st_ht_request'] == 1 || $options['st_ht_query'] == 1 || $options['hb_enabled'] == 1 ) {
				
					if ( $bwpsserver == 'apache' ) {
					
						$rules .= "</IfModule>\n";
					
					}
				
				}
	
			}
			
			//add markers if we have rules
			if ( $rules != '' ) {
				$rules = "# BEGIN Better WP Security\n" . $rules . "# END Better WP Security\n";
			}
				
			return $rules;
		
		}
		
		/**
		 * Generates secret key
		 *
		 * Generates secret key for hide backend function
		 *
		 * @return string key
		 *
		 **/
		function hidebe_genKey() {
		
			$size = 20; //length of key
			$chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"; //available characters
			srand( ( double ) microtime() * 1000000 ); //random seed
			$pass = '' ;
				
			for ( $i = 0; $i <= $size; $i++ ) {
			
				$num = rand() % 33;
				$tmp = substr( $chars, $num, 1 );
				$pass = $pass . $tmp;
				
			}
			
			return $pass;	
			
		}
		
		/**
		 * Return primary domain from given url
		 *
		 * Returns primary domsin name (without subdomains) of given URL
		 *
		 * @param string $address address to filter
		 * @return string domain name
		 *
		 **/		
		function topdomain( $address ) {
		
			preg_match( "/^(http:\/\/)?([^\/]+)/i", $address, $matches );
			$host = $matches[2];
			preg_match( "/[^\.\/]+\.[^\.\/]+$/", $host, $matches );
			$newAddress =  "(.*)" . $matches[0] ;
			
			return $newAddress;
			
		}
		
		/**
		 * Checks if user exists
		 *
		 * Checks to see if WordPress user with given username exists
		 *
		 * @param string $username login username of user to check
		 * @return bool true if user exists otherwise false
		 *
		 **/
		function user_exists( $username ) {
		
			global $wpdb;
			
			//return false if username is null
			if ( $username == '' ) {
				return false;
			}
			
			//queary the user table to see if the user is there
			$user = $wpdb->get_var( "SELECT user_login FROM `" . $wpdb->users . "` WHERE user_login='" . sanitize_text_field( $username ) . "';" );
			
			if ( $user == $username ) {
				return true;
			} else {
				return false;
			}
			
		}	
		
		/**
		 * Writes .htaccess options
		 *
		 * Writes various Better WP Security options to the .htaccess file
		 *
		 * @return int Write results -1 for error, 1 for success
		 *
		 **/
		function writehtaccess() {
			
			//clean up old rules first
			if ( $this->deletehtaccess() == -1 ) {
			
				return -1; //we can't write to the file
			
			}
			
			$htaccess = ABSPATH . '.htaccess';
			
			//get the subdirectory if it is installed in one
			$siteurl = explode( '/', get_option( 'siteurl' ) );
			
			if ( isset ( $siteurl[3] ) ) {
			
				$dir = '/' . $siteurl[3] . '/';
				
			} else {
			
				$dir = '/';
			
			}		
						
			@chmod( $htaccess, 0644 );
			
			$ht = explode( "\n", implode( '', file( $htaccess ) ) ); //parse each line of file into array
			
			$rules = $this->getrules();	
			
			$rulesarray = explode( "\n", $rules );
			
			$contents = array_merge( $rulesarray, $ht );
			 
			if ( ! $f = @fopen( $htaccess, 'w+' ) ) {
				
				return -1; //we can't write to the file
				
			}
			
			//write each line to file
			foreach ( $contents as $insertline ) {
			
				fwrite( $f, "{$insertline}\n" );
				
			}
				
			fclose( $f );
			
			@chmod( $htaccess, 0444 );
			
			return 1; //success
		
		}
		
		/**
		 * Writes wp-config.php options
		 *
		 * Writes various Better WP Security options to the wp-config.php file
		 *
		 * @return int Write results -1 for error, 1 for success
		 *
		 **/
		function writewpconfig() {
			
			//clear the old rules first
			if ( $this->deletewpconfig() == -1 ) {
			
				return -1; //we can't write to the file
			
			}
			
			$options = get_option( $this->primarysettings );
			
			$lines = '';
			
			$configfile = $this->getconfig();
			
			@chmod( $configfile, 0644 );
			
			$config = explode( "\n", implode( '', file( $configfile ) ) ); //parse each line of file into array
			
			if ( $options['st_fileedit'] == 1 ) {
			
				$lines .= "define('DISALLOW_FILE_EDIT', true);\n\n";
			
			}
			
			if ( $options['st_forceloginssl'] == 1 ) {
			
				$lines .= "define('FORCE_SSL_LOGIN', true);\n";
			
			}
			
			if ( $options['st_forceadminssl'] == 1 ) {
			
				$lines .= "define('FORCE_SSL_ADMIN', true);\n\n";
			
			}
			
			if ( ! $f = @fopen( $configfile, 'w+' ) ) {
				
				return -1; //we can't write to the file
				
			}
			
			//rewrite each appropriate line
			foreach ($config as $line) {
			
				if ( strstr( $line, "/* That's all, stop editing! Happy blogging. */" ) ) {
				
					$line = $lines . $line; //paste ending 
				
				}
				
				fwrite( $f, "{$line}\n" );
				
			}
			
			fclose( $f );
			
			@chmod( $configfile, 0444 );
			
			return 1; //success
		
		}
			
	}	
	
}
