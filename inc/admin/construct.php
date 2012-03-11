<?php 

require_once( plugin_dir_path( __FILE__ ) . 'common.php' );
require_once( plugin_dir_path( __FILE__ ) . 'content.php' );
require_once( plugin_dir_path( __FILE__ ) . 'process.php' );

if ( ! class_exists( 'bwps_admin_construct' ) ) {

	class bwps_admin_construct extends bwps_admin_common {

		/**
		 * Sets admin configuration
		 *
		 **/
		function __construct() {
			
			//add scripts and css
			add_action( 'admin_print_scripts', array( &$this, 'config_page_scripts' ) );
			add_action( 'admin_print_styles', array( &$this, 'config_page_styles' ) );
	
			//add action link
			add_filter( 'plugin_action_links', array( &$this, 'add_action_link' ), 10, 2 );
	
			//add donation reminder
			add_action( 'admin_init', array( &$this, 'ask' ) );	
		
			add_action( 'admin_init', array( &$this, 'awaycheck' ) );
			
			add_action( 'init', array( &$this, 'backup' ) );
				
		}
	
	}
	
}

new bwps_admin_construct();
new bwps_admin_content();
new bwps_admin_process();
