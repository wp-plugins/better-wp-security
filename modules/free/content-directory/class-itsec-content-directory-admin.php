<?php

/**
 * Change wp-content directory admin (priveledged) copy and processing
 *
 * Sets up all administrative functions for the change wp-content feature
 * including fields, sanitation and all other privileged functions.
 *
 * @since   4.0.0
 *
 * @package iThemes_Security
 */
class ITSEC_Content_Directory_Admin {

	/**
	 * The module's saved options
	 *
	 * @since  4.0.0
	 * @access private
	 * @var array
	 */
	private $settings;

	/**
	 * The core plugin class utilized in order to set up admin and other screens
	 *
	 * @since  4.0.0
	 * @access private
	 * @var ITSEC_Core
	 */
	private $core;

	/**
	 * The absolute web patch to the module's files
	 *
	 * @since  4.0.0
	 * @access private
	 * @var string
	 */
	private $module_path;

	/**
	 * Setup the module's administrative functionality
	 *
	 * Loads the file change detection module's priviledged functionality including
	 * changing the folder itself.
	 *
	 * @since 4.0.0
	 *
	 * @param ITSEC_Core $core The core plugin instance
	 *
	 * @return void
	 */
	function run( $core ) {

		$this->core        = $core;
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );
		$this->settings    = false;

		//Set that the module has completed its task if wp-content has already been renamed.
		if ( false === strpos( WP_CONTENT_DIR, 'wp-content' ) || false === strpos( WP_CONTENT_URL, 'wp-content' ) ) {
			$this->settings = true;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); //enqueue scripts for admin page
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'itsec_add_admin_meta_boxes' ) ); //add meta boxes to admin page
		add_filter( 'itsec_add_dashboard_status', array( $this, 'itsec_add_dashboard_status' ) ); //add information for plugin status
		add_filter( 'itsec_tracking_vars', array( $this, 'itsec_tracking_vars' ) ); //Usage information tracked via Google Analytics (opt-in)

		if ( ! empty( $_POST ) ) {
			add_action( 'itsec_admin_init', array( $this, 'itsec_admin_init' ) ); //Process the directory change if a form has been submitted
		}

	}

	/**
	 * Add Files Admin Javascript
	 *
	 * Enqueues files used in the admin area for the content directory module
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && false !== strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_advanced' ) ) {

			wp_register_script( 'itsec_content_directory_js', $this->module_path . 'js/admin-content_directory.js', array( 'jquery' ), $itsec_globals['plugin_build'] );
			wp_enqueue_script( 'itsec_content_directory_js' );

		}

	}

	/**
	 * Build wp-config.php rules
	 *
	 * Sets the array of wp-config.php rules that will either need to be replaced or added to later.
	 *
	 * @since  4.0.0
	 *
	 * @access private
	 *
	 * @param  array $input options to build rules from
	 *
	 * @return array         rules to write
	 */
	private function build_wpconfig_rules( $input = null ) {

		//Get the rules from the database if input wasn't sent
		if ( null === $input ) {
			return array();
		}

		$rules_array = array();

		$new_dir = trailingslashit( ABSPATH ) . $input;

		$rules[] = array(
			'type' => 'add', 'search_text' => '//Do not delete these. Doing so WILL break your site.',
			'rule' => "//Do not delete these. Doing so WILL break your site.",
		);

		$rules[] = array(
			'type' => 'add', 'search_text' => 'WP_CONTENT_URL',
			'rule' => "define( 'WP_CONTENT_URL', '" . trailingslashit( get_option( 'siteurl' ) ) . $input . "' );",
		);

		$rules[] = array(
			'type' => 'add', 'search_text' => 'WP_CONTENT_DIR',
			'rule' => "define( 'WP_CONTENT_DIR', '" . $new_dir . "' );",
		);

		$rules_array[] = array( 'type' => 'wpconfig', 'name' => 'Content Directory', 'rules' => $rules, );

		return $rules_array;

	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * Adds the module's meta settings box to the advanced page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function itsec_add_admin_meta_boxes() {

		if ( false === $this->settings ) { //we don't show anything if the feature has already been implemented

			add_meta_box(
				'content_directory_options',
				__( 'Change Content Directory', 'it-l10n-better-wp-security' ),
				array( $this, 'metabox_advanced_settings' ),
				'security_page_toplevel_page_itsec_advanced',
				'advanced',
				'core'
			);

		}

	}

	/**
	 * Sets the status in the plugin dashboard
	 *
	 * Sets a low priority item for the module's functionality in the plugin
	 * dashboard.
	 *
	 * @since 4.0.0
	 *
	 * @param array $statuses array of existing plugin dashboard statuses
	 *
	 * @return array statuses
	 */
	public function itsec_add_dashboard_status( $statuses ) {

		if ( true === $this->settings ) {

			$status_array = 'safe-low';
			$status       = array(
				'text' => __( 'You have renamed the wp-content directory of your site.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_enable_content_dir', 'advanced' => true,
			);

		} else {

			$status_array = 'low';
			$status       = array(
				'text' => __( 'You should rename the wp-content directory of your site.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_enable_content_dir', 'advanced' => true,
			);

		}

		array_push( $statuses[ $status_array ], $status );

		return $statuses;

	}

	/**
	 * Execute admin initializations
	 *
	 * Processes the changing of the wp-content folder and saves the new folder name to the
	 * database.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function itsec_admin_init() {

		if ( false === $this->settings && isset( $_POST['itsec_enable_content_dir'] ) && 'true' == $_POST['itsec_enable_content_dir'] ) {

			if ( ! wp_verify_nonce( $_POST['wp_nonce'], 'ITSEC_admin_save' ) ) {
				die( __( 'Security check', 'it-l10n-better-wp-security' ) );
			}

			$this->process_directory(); //process the directory change

		}

	}

	/**
	 * Adds fields that will be tracked for Google Analytics
	 *
	 * Allows the tracking of when the content directory has been changed (although
	 * not the new name of the directory) via our Google Analytics tracking
	 * system.
	 *
	 * @since 4.0.0
	 *
	 * @param array $vars tracking vars
	 *
	 * @return array tracking vars
	 */
	public function itsec_tracking_vars( $vars ) {

		$vars['content_directory'] = array(
			'enabled' => '0:b',
		);

		return $vars;

	}

	/**
	 * Render the settings metabox
	 *
	 * Displays the contents of the module's settings metabox on the "Advanced"
	 * page with all module options.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function metabox_advanced_settings() {

		global $itsec_globals;

		if ( false === $this->settings ) {

			$content = '<p>' . __( 'By default, WordPress puts all your content (including images, plugins, themes, uploads and more) in a directory called "wp-content." This default folder name makes it easy for attackers to scan for files with security vulnerabilities on your WordPress installation because they know where the vulnerable files are located. Moving the "wp-content" folder can make it more difficult for an attacker to find problems with your site, as scans of your site\'s file system will not produce any results.', 'it-l10n-better-wp-security' ) . '</p>';
			$content .= '<p>' . __( 'This tool will not allow further changes to your wp-content folder once it has been renamed in order to avoid accidentally breaking the site later. Uninstalling this plugin will not revert the changes made by this feature.', 'it-l10n-better-wp-security' ) . '</p>';
			$content .= '<p>' . __( 'Changing the name of the wp-content directory may in fact break plugins and themes that have "hard-coded" it into their design rather than calling it dynamically.', 'it-l10n-better-wp-security' ) . '</p>';
			$content .= sprintf( '<div class="itsec-warning-message"><span>%s: </span><a href="?page=toplevel_page_itsec_backups">%s</a> %s</div>', __( 'WARNING', 'it-l10n-better-wp-security' ), __( 'Backup your database', 'it-l10n-better-wp-security' ), __( 'before using this tool.', 'it-l10n-better-wp-security' ) );
			$content .= '<div class="itsec-warning-message">' . __( 'Please note: Changing the name of your wp-content directory on a site that already has images and other content referencing it will break your site. For this reason, we highly recommend you only try this technique on a fresh WordPress install.', 'it-l10n-better-wp-security' ) . '</div>';

		} else {

			if ( isset( $_POST['itsec_one_time_save'] ) ) {

				$dir_name = sanitize_file_name( $_POST['name'] );

			} else {

				$dir_name = substr( WP_CONTENT_DIR, strrpos( WP_CONTENT_DIR, '/' ) + 1 );
			}

			$content = '<p>' . __( 'Congratulations! You have already renamed your "wp-content" directory.', 'it-l10n-better-wp-security' ) . '</p>';
			$content .= '<p>' . __( 'Your current content directory is: ', 'it-l10n-better-wp-security' );
			$content .= '<strong>' . $dir_name . '</strong></p>';
			$content .= '<p>' . __( 'No further actions are available on this page.', 'it-l10n-better-wp-security' ) . '</p>';

		}

		echo $content;

		if ( isset( $itsec_globals['settings']['write_files'] ) && true === $itsec_globals['settings']['write_files'] ) {

			if ( false === $this->settings ) { //only show form if user the content directory hasn't already been changed
				?>

				<form method="post" action="?page=toplevel_page_itsec_advanced&settings-updated=true" class="itsec-form">

					<?php wp_nonce_field( 'ITSEC_admin_save', 'wp_nonce' ); ?>

					<table class="form-table">
						<tr valign="top">
							<th scope="row" class="settinglabel">
								<label for="itsec_enable_content_dir"><?php _e( 'Enable Change Directory Name', 'it-l10n-better-wp-security' ); ?></label>
							</th>
							<td class="settingfield">
								<?php //username field ?>
								<input type="checkbox" id="itsec_enable_content_dir" name="itsec_enable_content_dir" value="true"/>

								<p class="description"><?php _e( 'Check this box to enable content directory renaming.', 'it-l10n-better-wp-security' ); ?></p>
							</td>
						</tr>
						<tr valign="top" id="content_directory_name_field">
							<th scope="row" class="settinglabel">
								<label for="itsec_content_name"><?php _e( 'Directory Name', 'it-l10n-better-wp-security' ); ?></label>
							</th>
							<td class="settingfield">
								<?php //username field ?>
								<input id="itsec_content_name" name="name" type="text" value="wp-content"/>

								<p class="description"><?php _e( 'Enter a new directory name to replace "wp-content." You may need to log in again after performing this operation.', 'it-l10n-better-wp-security' ); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e( 'Change Content Directory', 'it-l10n-better-wp-security' ); ?>"/>
					</p>
				</form>

			<?php

			}

		} else {

			printf(
				'<p>%s <a href="?page=toplevel_page_itsec_settings">%s</a> %s',
				__( 'You must allow this plugin to write to the wp-config.php file on the', 'it-l10n-better-wp-security' ),
				__( 'Settings', 'it-l10n-better-wp-security' ),
				__( 'page to use this feature.', 'it-l10n-better-wp-security' )
			);

		}

	}

	/**
	 * Processes the change of wp-content
	 *
	 * Processes the changing of the wp-content directory including physically
	 * renaming the directory, adding the new information to wp-config.php and
	 * making sure the submitted directory name is valid.
	 *
	 * @since  4.0.0
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function process_directory() {

		global $itsec_files;

		//suppress error messages due to timing
		error_reporting( 0 );
		@ini_set( 'display_errors', 0 );

		$dir_name      = sanitize_file_name( $_POST['name'] );
		$old_directory = '';
		$new_directory = '';

		if ( 2 >= strlen( $dir_name ) ) { //make sure the directory name is at least 2 characters

			$type    = 'error';
			$message = __( 'Please choose a directory name that is greater than 2 characters in length.', 'it-l10n-better-wp-security' );

			add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

		} elseif ( 'wp-content' === $dir_name ) { //they must pick something new or we're not going to process

			$type    = 'error';
			$message = __( 'You have not chosen a new name for wp-content. Nothing was saved.', 'it-l10n-better-wp-security' );

			add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

		} else { //process the name change

			$rules = $this->build_wpconfig_rules( $dir_name );

			$itsec_files->set_wpconfig( $rules );

			$configs = $itsec_files->save_wpconfig();

			if ( is_array( $configs ) ) {

				if ( $configs['success'] === false ) {

					$type    = 'error';
					$message = $configs['text'];

					add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

				}

				$old_directory = WP_CONTENT_DIR;
				$new_directory = trailingslashit( ABSPATH ) . $dir_name;

				$renamed = rename( $old_directory, $new_directory );

				if ( ! $renamed ) {

					$type    = 'error';
					$message = __( 'Unable to rename the wp-content folder. Operation cancelled.', 'it-l10n-better-wp-security' );

					add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

				}

			} else {

				add_site_option( 'itsec_manual_update', true );

			}

		}

		$this->settings = true; //this tells the form field that all went well.

		$backup = get_site_option( 'itsec_backup' );

		if ( false !== $backup && isset( $backup['location'] ) ) {

			$backup['location'] = str_replace( $old_directory, $new_directory, $backup['location'] );
			update_site_option( 'itsec_backup', $backup );

		}

		$global = get_site_option( 'itsec_global' );

		if ( false !== $global && ( isset( $global['log_location'] ) || isset( $global['nginx_file'] ) ) ) {

			if ( isset( $global['log_location'] ) ) {
				$global['log_location'] = str_replace( $old_directory, $new_directory, $global['log_location'] );
			}

			if ( isset( $global['nginx_file'] ) ) {
				$global['nginx_file'] = str_replace( $old_directory, $new_directory, $global['nginx_file'] );
			}

			update_site_option( 'itsec_global', $global );

		}

		if ( is_multisite() ) { //put the error messages in the right place if multisite or not

			if ( isset( $type ) ) {

				$error_handler = new WP_Error();

				$error_handler->add( $type, $message );

				$this->core->show_network_admin_notice( $error_handler );

			} else {

				$this->core->show_network_admin_notice( false );

			}

			$this->settings = true;

		}

	}

}