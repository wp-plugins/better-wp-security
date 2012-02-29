<?php 

require_once( plugin_dir_path( __FILE__ ) . 'common.php' );
require_once( plugin_dir_path( __FILE__ ) . 'content.php' );
require_once( plugin_dir_path( __FILE__ ) . 'process.php' );

if ( ! class_exists( 'bwps_admin_construct' ) ) {

	class bwps_admin_construct extends bit51_bwps {

		/**
		 * Sets admin configuration
		 *
		 **/
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
	
	}
	
}

new bwps_admin_construct();
new bwps_admin_content();
new bwps_admin_process();
