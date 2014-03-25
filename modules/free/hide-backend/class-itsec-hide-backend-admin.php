<?php

class ITSEC_Hide_Backend_Admin {

	private
		$settings,
		$core,
		$module_path;

	function __construct( $core ) {

		if ( is_admin() ) {

			$this->initialize( $core );

		}

	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * @return void
	 */
	public function add_admin_meta_boxes() {

		$id    = 'hide_backend_options';
		$title = __( 'Hide Login Area', 'ithemes-security' );

		add_meta_box(
			$id,
			$title,
			array( $this, 'metabox_hide_backend_settings' ),
			'security_page_toplevel_page_itsec_settings',
			'advanced',
			'core'
		);

		$this->core->add_toc_item(
		           array(
			           'id'    => $id,
			           'title' => $title,
		           )
		);

	}

	/**
	 * Add Away mode Javascript
	 *
	 * @return void
	 */
	public function admin_script() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_settings' ) !== false ) {

			wp_enqueue_script( 'itsec_hide_backend_js', $this->module_path . 'js/admin-hide-backend.js', 'jquery', $itsec_globals['plugin_build'] );

		}

	}

	/**
	 * Build rewrite rules
	 *
	 * @param  array $input options to build rules from
	 *
	 * @return array         rules to write
	 */
	public static function build_rewrite_rules( $rules_array, $input = null ) {

		$server_type = ITSEC_Lib::get_server(); //Get the server type to build the right rules

		if ( $server_type == 'nginx' ) {

			//Get the rules from the database if input wasn't sent
			if ( $input === null ) {
				$input = get_site_option( 'itsec_hide_backend' );
			}

			$rules = ''; //initialize all rules to blank string

			//don't add any rules if the module hasn't been enabled
			if ( $input['enabled'] == true ) {

				$rules .= "\t# " . __( 'Rules to hide the dashboard', 'ithemes-security' ) . PHP_EOL . "\trewrite ^" . $input['slug'] . "/?$ /wp-login.php?\$query_string break;" . PHP_EOL;

				if ( $input['register'] != 'wp-register.php' ) {
					$rules .= "\trewrite ^" . $input['register'] . "/?$ " . $input['slug'] . "?action=register break;" . PHP_EOL;
				}

			}

			if ( strlen( $rules ) > 0 ) {
				$rules = explode( PHP_EOL, $rules );
			} else {
				$rules = false;
			}

			//create a proper array for writing
			$rules_array[] = array( 'type' => 'htaccess', 'priority' => 9, 'name' => 'Hide Backend', 'rules' => $rules, );

		}

		return $rules_array;

	}

	/**
	 * Sets the status in the plugin dashboard
	 *
	 * @since 4.0
	 *
	 * @return array array of statuses
	 */
	public function dashboard_status( $statuses ) {

		if ( $this->settings['enabled'] === true ) {

			$status_array = 'safe-medium';
			$status       = array( 'text' => __( 'Your WordPress Dashboard is hidden.', 'ithemes-security' ), 'link' => '#itsec_hide_backend_enabled', );

		} else {

			$status_array = 'medium';
			$status       = array( 'text' => __( 'Your WordPress Dashboard is using the default addresses. This can make a brute force attack much easier.', 'ithemes-security' ), 'link' => '#itsec_hide_backend_enabled', );

		}

		array_push( $statuses[$status_array], $status );

		return $statuses;

	}

	/**
	 * Empty callback function
	 */
	public function empty_callback_function() {
	}

	/**
	 * echos Hide Backend  Enabled Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function hide_backend_enabled() {

		if ( ( get_option( 'permalink_structure' ) == '' || get_option( 'permalink_structure' ) == false ) && ! is_multisite() ) {

			$adminurl = is_multisite() ? admin_url() . 'network/' : admin_url();

			$content = sprintf( '<p class="noPermalinks">%s <a href="%soptions-permalink.php">%s</a> %s</p>', __( 'You must turn on', 'ithemes-security' ), $adminurl, __( 'WordPress permalinks', 'ithemes-security' ), __( 'to use this feature.', 'ithemes-security' ) );

		} else {

			if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) {
				$enabled = 1;
			} else {
				$enabled = 0;
			}

			$content = '<input type="checkbox" id="itsec_hide_backend_enabled" name="itsec_hide_backend[enabled]" value="1" ' . checked( 1, $enabled, false ) . '/>';
			$content .= '<label for="itsec_hide_backend_enabled"> ' . __( 'Enable the hide backend feature.', 'ithemes-security' ) . '</label>';

		}

		echo $content;

	}

	/**
	 * echos Hide Backend Slug  Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function hide_backend_slug() {

		if ( ( get_option( 'permalink_structure' ) == '' || get_option( 'permalink_structure' ) == false ) && ! is_multisite() ) {

			$content = '';

		} else {

			$content = '<input name="itsec_hide_backend[slug]" id="itsec_hide_backend_strong_passwords_slug" value="' . sanitize_title( $this->settings['slug'] ) . '" type="text"><br />';
			$content .= '<label for="itsec_hide_backend_strong_passwords_slug">' . __( 'Login URL:', 'ithemes-security' ) . trailingslashit( get_option( 'siteurl' ) ) . '<span style="color: #4AA02C">' . sanitize_title( $this->settings['slug'] ) . '</span></label>';
			$content .= '<p class="description">' . __( 'The login url slug cannot be "login," "admin," "dashboard," or "wp-login.php" as these are use by default in WordPress.', 'ithemes-security' ) . '</p>';

		}

		echo $content;

	}

	/**
	 * echos Register Slug  Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function hide_backend_register() {

		if ( ( get_option( 'permalink_structure' ) == '' || get_option( 'permalink_structure' ) == false ) && ! is_multisite() ) {

			$content = '';

		} else {

			$content = '<input name="itsec_hide_backend[register]" id="itsec_hide_backend_strong_passwords_register" value="' . ( $this->settings['register'] !== 'wp-register.php' ? sanitize_title( $this->settings['register'] ) : 'wp-register.php' ) . '" type="text"><br />';
			$content .= '<label for="itsec_hide_backend_strong_passwords_register">' . __( 'Registration URL:', 'ithemes-security' ) . trailingslashit( get_option( 'siteurl' ) ) . '<span style="color: #4AA02C">' . sanitize_title( $this->settings['register'] ) . '</span></label>';

		}

		echo $content;

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
	private function initialize( $core ) {

		$this->core        = $core;
		$this->settings    = get_site_option( 'itsec_hide_backend' );
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		add_filter( 'itsec_file_rules', array( $this, 'build_rewrite_rules' ) );

		add_filter( 'itsec_tooltip_modules', array( $this, 'register_tooltip' ) ); //register tooltip action
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) ); //add meta boxes to admin page
		add_action( 'itsec_admin_init', array( $this, 'initialize_admin' ) ); //initialize admin area
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) ); //enqueue scripts for admin page
		add_filter( 'itsec_add_dashboard_status', array( $this, 'dashboard_status' ) ); //add information for plugin status
		add_filter( 'itsec_tracking_vars', array( $this, 'tracking_vars' ) );

		//manually save options on multisite
		if ( is_multisite() ) {
			add_action( 'itsec_admin_init', array( $this, 'save_network_options' ) ); //save multisite options
		}

	}

	/**
	 * Execute admin initializations
	 *
	 * @return void
	 */
	public function initialize_admin() {

		//Add Settings sections
		add_settings_section(
			'hide_backend-enabled',
			__( 'Hide Login and Admin', 'ithemes-security' ),
			array( $this, 'empty_callback_function' ),
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_section(
			'hide_backend-settings',
			__( 'Hide Login and Admin', 'ithemes-security' ),
			array( $this, 'empty_callback_function' ),
			'security_page_toplevel_page_itsec_settings'
		);

		//Hide Backend Fields
		add_settings_field(
			'itsec_hide_backend[enabled]',
			__( 'Hide Backend', 'ithemes-security' ),
			array( $this, 'hide_backend_enabled' ),
			'security_page_toplevel_page_itsec_settings',
			'hide_backend-enabled'
		);

		add_settings_field(
			'itsec_hide_backend[slug]',
			__( 'Login Slug', 'ithemes-security' ),
			array( $this, 'hide_backend_slug' ),
			'security_page_toplevel_page_itsec_settings',
			'hide_backend-settings'
		);

		if ( get_site_option( 'users_can_register' ) ) {

			add_settings_field(
				'itsec_hide_backend[register]',
				__( 'Register Slug', 'ithemes-security' ),
				array( $this, 'hide_backend_register' ),
				'security_page_toplevel_page_itsec_settings',
				'hide_backend-settings'
			);

		}

		//Register the settings field for the entire module
		register_setting(
			'security_page_toplevel_page_itsec_settings',
			'itsec_hide_backend',
			array( $this, 'sanitize_module_input' )
		);

	}

	/**
	 * Render the settings metabox
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_hide_backend_settings() {

		echo '<p>' . __( 'Hides the login page (wp-login.php, wp-admin, admin and login) making it harder to find by automated attacks and making it easier for users unfamiliar with the WordPress platform.', 'ithemes-security' ) . '</p>';

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'hide_backend-enabled', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'hide_backend-settings', false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save Changes', 'ithemes-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

	}

	/**
	 * Register backups for tooltips
	 *
	 * @param  array $tooltip_modules array of tooltip modules
	 *
	 * @return array                   array of tooltip modules
	 */
	public function register_tooltip( $tooltip_modules ) {

		if ( get_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP' ) || ( isset( $this->settings['show-tooltip'] ) && $this->settings['show-tooltip'] === true ) ) {

			$tooltip_modules['hide-backend'] = array(
				'priority'  => 0,
				'class'     => 'itsec_tooltip_hide_backend',
				'heading'   => __( 'Review Hide Backend Settings', 'ithemes-security' ),
				'text'      => __( 'The hide backend system has been rewritten. You must re-activate the feature to continue using the feature.', 'ithemes-security' ),
				'link_text' => __( 'Review Settings', 'ithemes-security' ),
				'link'      => '?page=toplevel_page_itsec_settings#itsec_hide_backend_enabled',
				'success'   => '',
				'failure'   => '',
			);

		}

		return $tooltip_modules;

	}

	/**
	 * Sanitize and validate input
	 *
	 * @param  Array $input array of input fields
	 *
	 * @return Array         Sanitized array
	 */
	public function sanitize_module_input( $input ) {

		//Process hide backend settings
		$input['enabled'] = ( isset( $input['enabled'] ) && intval( $input['enabled'] == 1 ) ? true : false );
		$input['show-tooltip'] = ( isset( $this->settings['show-tooltip'] ) ? $this->settings['show-tooltip'] : false );

		if ( isset( $input['slug'] ) ) {
			$input['slug'] = sanitize_title( $input['slug'] );
		} else {
			$input['slug'] = 'wplogin';
		}

		if ( isset( $input['register'] ) && $input['register'] !== 'wp-register.php' ) {
			$input['register'] = sanitize_title( $input['register'] );
		} else {
			$input['register'] = 'wp-register.php';
		}

		$forbidden_slugs = array( 'admin', 'login', 'wp-login.php', 'dashboard', 'wp-admin', '' );

		if ( in_array( trim( $input['slug'] ), $forbidden_slugs ) && $input['enabled'] === true ) {

			$invalid_login_slug = true;

			$type    = 'error';
			$message = __( 'Invalid hide login slug used. The login url slug cannot be "login," "admin," "dashboard," or "wp-login.php" ob "" (blank) as these are use by default in WordPress.', 'ithemes-security' );

			add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

		} else {

			$invalid_login_slug = false;

			add_rewrite_rule( $input['slug'] . '/?$', 'wp-login.php', 'top' );

		}

		if ( $input['register'] != 'wp-register.php' && $input['enabled'] === true ) {
			add_rewrite_rule( $input['register'] . '/?$', $input['slug'] . '?action=register', 'top' ); //Login rewrite rule
		}

		if ( $invalid_login_slug === false ) {

			$config_file = ITSEC_Lib::get_htaccess();

			if ( ITSEC_Lib::get_server() == 'nginx' ) {

				if (
					! isset( $type ) &&
					(
						$input['slug'] !== $this->settings['slug'] ||
						$input['register'] !== $this->settings['register'] ||
						$input['enabled'] !== $this->settings['enabled']
					)

				) {

					add_site_option( 'itsec_rewrites_changed', true );

				}

			}

			//Make sure we can write to the file
			$perms = substr( sprintf( '%o', @fileperms( $config_file ) ), - 4 );

			@chmod( $config_file, 0644 );

			flush_rewrite_rules();

			//reset file permissions if we changed them
			if ( $perms == '0444' ) {
				@chmod( $config_file, 0444 );
			}

		}

		if ( is_multisite() ) {

			if ( isset( $type ) ) {

				$error_handler = new WP_Error();

				$error_handler->add( $type, $message );

				$this->core->show_network_admin_notice( $error_handler );

			} else {

				$this->core->show_network_admin_notice( false );

			}

			$this->settings = $input;

		}

		return $input;

	}

	/**
	 * Prepare and save options in network settings
	 *
	 * @return void
	 */
	public function save_network_options() {

		if ( isset( $_POST['itsec_hide_backend'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'security_page_toplevel_page_itsec_settings-options' ) ) {
				die( __( 'Security error!', 'ithemes-security' ) );
			}

			update_site_option( 'itsec_hide_backend', $_POST['itsec_hide_backend'] ); //we must manually save network options

		}

	}

	/**
	 * Adds fields that will be tracked for Google Analytics
	 *
	 * @since 4.0
	 *
	 * @param array $vars tracking vars
	 *
	 * @return array tracking vars
	 */
	public function tracking_vars( $vars ) {

		$vars['itsec_hide_backend'] = array(
			'enabled' => '0:b',
		);

		return $vars;

	}

}