<?php

if ( ! class_exists( 'bwps_admin_common' ) ) {

	abstract class bwps_admin_common extends bit51_bwps {
	
		function __construct() {
					
			if ( is_admin() || (is_multisite() && is_network_admin() ) ) {
			
				//add scripts and css
				add_action( 'admin_print_scripts', array( &$this, 'config_page_scripts' ) );
				add_action( 'admin_print_styles', array( &$this, 'config_page_styles' ) );
			
				if ( is_multisite() ) { 
					add_action( 'network_admin_menu', array( &$this, 'register_settings_page' ) ); 
				} else {
					add_action( 'admin_menu',  array( &$this, 'register_settings_page' ) );
				}
			
				//add settings
				add_action( 'admin_init', array( &$this, 'register_settings' ) );
			
				//add action link
				add_filter( 'plugin_action_links', array( &$this, 'add_action_link' ), 10, 2 );
			
				//add donation reminder
				add_action( 'admin_init', array( &$this, 'ask' ) );	
			
				if (isset( $_POST['bwps_page']) ) {
					add_action( 'admin_init', array( &$this, 'form_dispatcher' ) );
				}
				
				add_action( 'admin_init', array( &$this, 'awaycheck' ) );
			}
			
			add_action( 'init', array( &$this, 'backup_scheduler' ) );
						
		}
		
		function awaycheck() {
		
			global $bwps;
			
			if( $bwps->checkaway() ) {
				wp_redirect( get_option( 'siteurl' ) );
			}
			
		}
		
		function backup_scheduler() {
		
			add_action( 'bwps_backup', array( &$this, 'db_backup' ) );
			
			$options = get_option( $this->primarysettings );
			
			if ( $options['backup_enabled'] == 1 ) {
			
				if ( ! wp_next_scheduled( 'bwps_backup' ) ) {
					wp_schedule_event( time(), $options['backup_int'], 'bwps_backup' );
				}
				
			} else {
			
				if ( wp_next_scheduled( 'bwps_backup' ) ) {
					wp_clear_scheduled_hook( 'bwps_backup' );
				}
				
			}
			
		}
		
		function db_backup() {
		
			global $wpdb;
			$this->errorHandler = '';
			
			$backuppath = BWPS_PP . 'lib/phpmysqlautobackup/backups/';
			
			$options = get_option( $this->primarysettings );
			
			@require( BWPS_PP . 'lib/phpmysqlautobackup/run.php' );
			
			$wpdb->query( 'DROP TABLE `phpmysqlautobackup`;' );
			$wpdb->query( 'DROP TABLE `phpmysqlautobackup_log`;' );
			
		}
		
		function getConfig() {
		
			if ( file_exists( trailingslashit( ABSPATH ) . 'wp-config.php' ) ) {
			
				return trailingslashit( ABSPATH ) . 'wp-config.php';
				
			} else {
			
				return trailingslashit( dirname( ABSPATH ) ) . 'wp-config.php';
				
			}
			
		}
		
		function hidebe_genKey() {	
		
			$chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			srand( ( double ) microtime() * 1000000 );
			$pass = '' ;	
				
			for ( $i = 0; $i <= 20; $i++ ) {
			
				$num = rand() % 33;
				$tmp = substr( $chars, $num, 1 );
				$pass = $pass . $tmp;
				
			}
			
			return $pass;	
			
		}
		
		function removehtaccess( $section = 'Better WP Security' ) {
		
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
						if ( $n + 1 < count( $markerdata ) ) //make sure to add newline to appropriate lines
							fwrite( $f, "{$markerline}\n" );
						else
							fwrite( $f, "{$markerline}" );
					}
					
					if ( strpos( $markerline, '# END ' . $section ) !== false ) { //see if we're at the end of the section
						$state = true;
					}
					
				}
				
				fclose( $f );
				
				@chmod( $htaccess, 0444 );
				
				return 1;
				
			}
		
			return 0; //return false if we can't write the file
			
		}
		
		function topdomain( $address ) {
		
			preg_match( "/^(http:\/\/)?([^\/]+)/i", $address, $matches );
			$host = $matches[2];
			preg_match( "/[^\.\/]+\.[^\.\/]+$/", $host, $matches );
			$newAddress =  "(.*)" . $matches[0] ;
			
			return $newAddress;
			
		}
		
		function user_exists( $username ) {
		
			global $wpdb;
			
			//return false if username is null
			if ( $username == '' ) {
				return false;
			}
			
			//queary the user table to see if the user is there
			$user = $wpdb->get_var( "SELECT user_login FROM `" . $wpdb->users . "` WHERE user_login='" . sanitize_text_field( $username) . "';" );
			
			if ( $user == $username ) {
				return true;
			} else {
				return false;
			}
			
		}	
		
		function writehtaccess() {
		
			global $wp_rewrite;
			
			if ( $this->removehtaccess() == -1 ) {
			
				return -1; //we can't write to the file
			
			}
			
			$options = get_option( $this->primarysettings );
			
			$htaccess = ABSPATH . '.htaccess';
			
			$siteurl = explode( '/', get_option( 'siteurl' ) );
			
			if ( isset ( $siteurl[3] ) ) {
			
				$dir = '/' . $siteurl[3] . '/';
				
			} else {
			
				$dir = '/';
			
			}
			
			$rules = '';
			
			if ( $options['hb_enabled'] == 1 ) {
				
				//get the slugs
				$login = $options['hb_login'];
				$admin = $options['hb_admin'];
				$register = $options['hb_register'];
						
				//generate the key
				$key = $options['hb_key'];
				
				//get the domain without subdomain
				$reDomain = $this->topdomain( get_option( 'siteurl' ) );
		
				//hide wordpress backend
				$rules .= "RewriteRule ^" . $login . " " . $dir . "wp-login.php?" . $key . " [R,L]\n" .
					"RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in_.*$\n" .
					"RewriteRule ^" . $admin . "$ " . $dir . "wp-login.php?" . $key . "&redirect_to=" . $dir . "wp-admin/ [R,L]\n" .
					"RewriteRule ^" . $admin . "$ " . $dir . "wp-admin/?" . $key . " [R,L]\n" .
					"RewriteRule ^" . $register . "$ " . $dir . "wp-login.php?" . $key . "&action=register [R,L]\n" .
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
					"RewriteRule ^.*wp-admin/?|^.*wp-login\.php not_found [L]\n" .
					"\n" .
					"RewriteCond %{QUERY_STRING} ^loggedout=true\n" .
					"RewriteRule ^.*$ " . $dir . "wp-login.php?" . $key . " [R,L]\n";
					
			}
			
			@chmod( $htaccess, 0644 );
			
			$ht = explode( "\n", implode( '', file( $htaccess ) ) ); //parse each line of file into array
			
			$bwpsrules = explode( "\n", $rules );
			
			if ( $rules == '' ) {
			
				$open = array();
				$close = array();
				
			} else {
			
				$open = array(
					"# BEGIN Better WP Security\n",
					"RewriteEngine On\n"
				);
				$close = array("# END Better WP Security\n");
				
			}
			
			$contents = array_merge( $open, $bwpsrules, $close, $ht );
				 
			if ( ! $f = @fopen( $htaccess, 'w+' ) ) {
				
				return -1; //we can't write to the file
				
			}
			
			foreach ( $contents as $insertline ) {
			
				fwrite( $f, "{$insertline}\n" );
				
			}
				
			fclose( $f );
			
			//@chmod( $htaccess, 0444 );
			
			return 1;
		
		}
		
	}	
	
}
