<?php

class ITSEC_Core_Admin {

	function __construct() {

		if ( is_admin() ) {

			$this->initialize();

		}

	}

	/**
	 * Add meta boxes to primary options pages.
	 *
	 * @since 4.0
	 *
	 * @param array $available_pages array of available page_hooks
	 */
	public function add_admin_meta_boxes( $available_pages ) {

		foreach ( $available_pages as $page ) {

			add_meta_box(
				'itsec_security_updates',
				__( 'Download Our WordPress Security Pocket Guide', 'LION' ),
				array( $this, 'metabox_security_updates' ),
				$page,
				'priority_side',
				'core'
			);

			add_meta_box(
				'itsec_need_help',
				__( 'Need Help Securing Your Site?', 'LION' ),
				array( $this, 'metabox_need_help' ),
				$page,
				'side',
				'core'
			);

			if ( ! class_exists( 'backupbuddy_api0' ) ) {
				add_meta_box(
					'itsec_get_backup',
					__( 'Complete Your Security Strategy With BackupBuddy', 'LION' ),
					array( $this, 'metabox_get_backupbuddy' ),
					$page,
					'side',
					'core'
				);
			}

		}

		add_meta_box(
			'itsec_get_started',
			__( 'Getting Started', 'LION' ),
			array( $this, 'metabox_get_started' ),
			'toplevel_page_itsec',
			'normal',
			'core'
		);

	}

	/**
	 * Adds links to the plugin row meta
	 *
	 * @since 4.0
	 *
	 * @param array $meta Existing meta
	 *
	 * @return array
	 */
	public function add_plugin_meta_links( $meta ) {

		$meta[] = '<a href="http://ithemes.com/security/ithemes-security-professional-setup" target="_blank">' . __( 'Get Pro Setup', 'LION' ) . '</a>';
		$meta[] = '<a href="http://ithemes.com/security" target="_blank">' . __( 'Get Support', 'LION' ) . '</a>';

		return $meta;
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
		add_filter( 'itsec_meta_links', array( $this, 'add_plugin_meta_links' ) );

		//Process support plugin nag
		add_action( 'itsec_admin_init', array( $this, 'setup_nag' ) );

	}

	/**
	 * Display the Get BackupBuddy metabox
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_get_backupbuddy() {

		echo '<p style="text-align: center;"><img src="' . plugins_url( 'img/backupbuddy-logo.png', __FILE__ ) . '" alt="BackupBuddy"></p>';
		echo '<p>' . __( 'BackupBuddy is the complete backup, restore and migration solution for your WordPress site. Schedule automated backups, store your backups safely off-site and restore your site quickly & easily.', 'LION' ) . '</p>';
		echo '<a href="http://ithemes.com/better-backups" class="button-secondary" target="_blank">' . __( 'Get BackupBuddy', 'LION' ) . '</a>';

	}

	/**
	 * Display the metabox for getting started
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_get_started() {

		echo '<div class="itsec_getting_started">';
		echo '<div class="column">';
		echo '<h2>' . __( 'Watch the Walk-Through Video', 'LION' ) . '</h2>';
		echo '<a class="itsec-video-link" href="#" data-video-id="itsec_video"><img src="' . plugins_url( 'img/video.png', __FILE__ ) . '" /></a>';
		echo sprintf( '<p class="itsec-video-description">%s <a href="http://ithem.es/6y" target="_blank">%s</a> %s </p>', __( 'In this short video, we walk through', 'LION' ), __( 'how to get started securing your site', 'LION' ), __( 'with iThemes Security.', 'LION' ) );
		echo '<p class="itsec_video"><iframe src="//player.vimeo.com/video/89142424?title=0&amp;byline=0&amp;portrait=0" width="853" height="480" frameborder="0" ></iframe></p>';

		echo '</div>';


		echo '<div class="column two">';
		echo '<h2>' . __( 'Website Security is a complicated subject, but we have experts that can help.', 'LION' ) . '</h2>';
		echo '<p>' . __( 'Get added peace of mind with professional support from our expert team and pro features to take your site security to the next level with iThemes Security Pro.', 'LION' ) . '</p>';
		echo '<p><a class="button-primary" href="http://www.ithemes.com/security" target="_blank">' . __( 'Get Support and Pro Features', 'LION' ) . '</a></p>';
		echo '</div>';
		echo '</div>';

	}

	/**
	 * Display the Need Help metabox
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_need_help() {

		echo '<p>' . __( 'Be sure your site has been properly secured by having one of our security experts tailor iThemes Security settings to the specific needs of this site.', 'LION' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://ithemes.com/security/ithemes-security-professional-setup" target="_blank">' . __( 'Have an expert secure my site', 'LION' ) . '</a></p>';
		echo '<p>' . __( 'Get added peace of mind with professional support from our expert team and pro features with iThemes Security Pro.', 'LION' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://www.ithemes.com/security" target="_blank">' . __( 'Get iThemes Security Pro', 'LION' ) . '</a></p>';

	}

	/**
	 * Display the Security Updates signup metabox.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_security_updates() {

		ob_start();
		?>

		<div id="mc_embed_signup">
			<form
				action="http://ithemes.us2.list-manage.com/subscribe/post?u=7acf83c7a47b32c740ad94a4e&amp;id=5176bfed9e"
				method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate"
				target="_blank" novalidate>
				<div style="text-align: center;">
					<img src="<?php echo plugins_url( 'img/security-ebook.png', __FILE__ ) ?>" width="145"
					     height="187" alt="WordPress Security - A Pocket Guide">
				</div>
				<p><?php _e( 'Get tips for securing your site + the latest WordPress security updates, news and releases from iThemes.', 'better-wp-security' ); ?></p>

				<div id="mce-responses" class="clear">
					<div class="response" id="mce-error-response" style="display:none"></div>
					<div class="response" id="mce-success-response" style="display:none"></div>
				</div>
				<label for="mce-EMAIL"
				       style="display: block;margin-bottom: 3px;"><?php _e( 'Email Address', 'better-wp-security' ); ?></label>
				<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL"
				       placeholder="email@domain.com">
				<br/><br/>
				<input type="submit" value="<?php _e( 'Subscribe', 'better-wp-security' ); ?>" name="subscribe"
				       id="mc-embedded-subscribe" class="button button-secondary">
			</form>
		</div>

		<?php
		ob_end_flush();

	}

	/**
	 * Display (and hide) setup nag.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function setup_nag() {

		global $blog_id, $itsec_globals;

		if ( is_multisite() && ( $blog_id != 1 || ! current_user_can( 'manage_network_options' ) ) ) { //only display to network admin if in multisite
			return;
		}

		$options = $itsec_globals['data'];

		//display the notifcation if they haven't turned it off
		if ( ( ! isset( $options['setup_completed'] ) || $options['setup_completed'] === false ) ) {

			if ( ! function_exists( 'ithemes_plugin_setup_notice' ) ) {

				function ithemes_plugin_setup_notice() {

					global $itsec_globals;

					echo '<div class="updated" id="itsec_setup_notice"><span class="it-icon-itsec"></span>'
					     . $itsec_globals['plugin_name'] . ' ' . __( 'is almost ready.', 'LION' ) . '<a href="#" class="itsec-notice-button" onclick="document.location.href=\'?itsec_setup=yes&_wpnonce=' . wp_create_nonce( 'itsec-nag' ) . '\';">' . __( 'Secure Your Site Now', 'LION' ) . '</a><a target="_blank" href="http://ithemes.com/ithemes-security-4-is-here" class="itsec-notice-button">' . __( "See what's new in 4.0", 'LION' ) . '</a><a href="#" class="itsec-notice-hide" onclick="document.location.href=\'?itsec_setup=no&_wpnonce=' . wp_create_nonce( 'itsec-nag' ) . '\';">&times;</a>
						</div>';

				}

			}

			if ( is_multisite() ) {
				add_action( 'network_admin_notices', 'ithemes_plugin_setup_notice' ); //register notification
			} else {
				add_action( 'admin_notices', 'ithemes_plugin_setup_notice' ); //register notification
			}

		}

		//if they've clicked a button hide the notice
		if ( isset( $_GET['itsec_setup'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'itsec-nag' ) ) {

			$options = $itsec_globals['data'];

			$options['setup_completed'] = true;

			update_site_option( 'itsec_data', $options );

			if ( is_multisite() ) {
				remove_action( 'network_admin_notices', 'ithemes_plugin_setup_notice' );
			} else {
				remove_action( 'admin_notices', 'ithemes_plugin_setup_notice' );
			}

			if ( sanitize_text_field( $_GET['itsec_setup'] ) == 'no' && isset( $_SERVER['HTTP_REFERER'] ) ) {

				wp_redirect( $_SERVER['HTTP_REFERER'], '302' );

			} else {

				wp_redirect( 'admin.php?page=itsec', '302' );

			}

		}

	}

}