<?php

class ITSEC_Help_Admin {

	function __construct() {

		if ( is_admin() ) {

			$this->initialize();

		}

	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 */
	public function add_admin_meta_boxes() {

		add_meta_box(
			'itsec_help_info',
			__( 'Help', 'ithemes-security' ),
			array( $this, 'add_help_intro' ),
			'security_page_toplevel_page_itsec_help',
			'normal',
			'core'
		);

	}

	/**
	 * Initializes all admin functionality.
	 *
	 * @since 4.0
	 *
	 * @param ITSEC_Core $core The $itsec_core instance
	 *
	 * @return void
	 */
	private function initialize() {

		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) ); //add meta boxes to admin page

	}

	/**
	 * Build and echo the away mode description
	 *
	 * @return void
	 */
	public function add_help_intro() {

		echo '<p>' . __( 'Website security is a complicated subject, but we have experts that can help.', 'ithemes-security' ) . '</p>';

		echo '<p><strong>' . __( 'Support & Pro Features with iThemes Security Pro', 'ithemes-security' ) . '</strong><br />';
		echo  __( 'Get added peace of mind with professional support from our expert team and pro features to take your site security to the next level with iThemes Security Pro.', 'ithemes-security' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://www.ithemes.com/security" target="_blank">' . __( 'Get iThemes Security Pro', 'ithemes-security' ) . '</a></p>';
		echo '<hr>';

		echo '<p><strong>' . __( 'Have a Pro Secure Your Site', 'ithemes-security' ) . '</strong><br />';
		echo  __( 'Be sure your site has been properly secured by having one of our security experts tailor your security settings to the specific needs of your site.', 'ithemes-security' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://ithemes.com/security/ithemes-security-professional-setup" target="_blank">' . __( 'Have an expert secure my site', 'ithemes-security' ) . '</a></p>';
		echo '<hr>';

		echo '<p><strong>' . __( 'Hack Repair', 'ithemes-security' ) . '</strong><br />';
		echo  __( 'Has your site been hacked? Contact one of our recommended hack repair partners to get things back in order.', 'ithemes-security' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://ithemes.com/security/wordpress-hack-repair" target="_blank">' . __( 'Get hack repair', 'ithemes-security' ) . '</a></p>';

	}

}