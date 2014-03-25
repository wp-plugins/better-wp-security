<?php 
/*
	Plugin Name: iThemes Security
	Plugin URI: http://ithemes.com/security
	Description: Protect your WordPress site by hiding vital areas of your site, protecting access to important files, preventing brute-force login attempts, detecting attack attempts and more.
	Version: 0.0.371
	Text Domain: ithemes_security
	Domain Path: /languages
	Author: iThemes.com
	Author URI: http://ithemes.com
	License: GPLv2
	Copyright 2014  iThemes  (email : info@ithemes.com)
*/

require( dirname( __FILE__ ) . '/lib/icon-fonts/load.php' ); //Loads iThemes fonts

require( dirname( __FILE__ ) . '/lib/one-version/index.php' ); //Only have one version of the plugin

require_once( dirname( __FILE__ ) .  '/core/class-itsec-core.php' );
new ITSEC_Core( __FILE__, __( 'iThemes Security', 'ithemes-security' ) );
