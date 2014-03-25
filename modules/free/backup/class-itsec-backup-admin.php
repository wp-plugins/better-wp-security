<?php

class ITSEC_Backup_Admin {

	private
		$core,
		$module,
		$module_path,
		$settings;

	function __construct( $core, $module ) {

		if ( is_admin() ) {

			$this->initialize( $core, $module );

		}

	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function add_admin_meta_boxes() {

		if ( ! class_exists( 'backupbuddy_api0' ) ) {

			add_meta_box(
				'backup_description',
				__( 'Description', 'ithemes-security' ),
				array( $this, 'add_module_intro' ),
				'security_page_toplevel_page_itsec_backups',
				'normal',
				'core'
			);

			add_meta_box(
				'backup_one_time',
				__( 'Make a Database Backup', 'ithemes-security' ),
				array( $this, 'metabox_one_time' ),
				'security_page_toplevel_page_itsec_backups',
				'advanced',
				'core'
			);

			$id    = 'backup_options';
			$title = __( 'Database Backups', 'ithemes-security' );

			add_meta_box(
				$id,
				$title,
				array( $this, 'metabox_advanced_settings' ),
				'security_page_toplevel_page_itsec_settings',
				'advanced',
				'core'
			);

			if ( ! class_exists( 'backupbuddy_api0' ) ) {
				add_meta_box(
					'backupbuddy_info',
					__( 'Take the Next Steps in Security with BackupBuddy', 'ithemes-security' ),
					array( $this, 'backupbuddy_metabox' ),
					'security_page_toplevel_page_itsec_backups',
					'advanced',
					'core'
				);
			}

			$this->core->add_toc_item(
			           array(
				           'id'    => $id,
				           'title' => $title,
			           )
			);

		}

	}

	/**
	 * Build and echo the away mode description
	 *
	 * @return void
	 */
	public function add_module_intro() {

		$content = '<p>' . __( 'One of the best ways to protect yourself from an attack is to have access to a database backup of your site. If something goes wrong, you can get your site back by restoring the database from a backup and replacing the files with fresh ones. Use the button below to create a backup of your database for this purpose. You can also schedule automated backups and download or delete previous backups.', 'ithemes-security' ) . '</p>';

		echo $content;

	}

	/**
	 * Add Backup CSS and JS.
	 *
	 * Enqueue and registers scripts and stylesheets for Backup admin page.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function admin_script() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_settings' ) !== false ) {

			wp_enqueue_script( 'itsec_backup_js', $this->module_path . 'js/admin-backup.js', 'jquery', $itsec_globals['plugin_build'] );
			wp_enqueue_script( 'jquery_multiselect', $this->module_path . 'js/jquery.multi-select.js', 'jquery', $itsec_globals['plugin_build'] );

			wp_register_style( 'itsec_ms_styles', $this->module_path . 'css/multi-select.css' ); //add multi-select css
			wp_enqueue_style( 'itsec_ms_styles' );

			wp_register_style( 'itsec_backup_styles', $this->module_path . 'css/admin-backup.css' ); //add multi-select css
			wp_enqueue_style( 'itsec_backup_styles' );

			wp_localize_script( 'itsec_backup_js', 'exclude_text', array( 'available' => __( 'Tables for Backup', 'ithemes-security' ), 'excluded' => __( 'Excluded Tables', 'ithemes-security' ) ) );

		}

		if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_backups' ) !== false ) {
			wp_register_style( 'itsec_backup_styles', $this->module_path . 'css/admin-backup.css' ); //add multi-select css
			wp_enqueue_style( 'itsec_backup_styles' );
		}

	}

	/**
	 * echos all sites Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function all_sites() {

		if ( isset( $this->settings['all_sites'] ) && $this->settings['all_sites'] === true ) {
			$all_sites = 1;
		} else {
			$all_sites = 0;
		}

		$content = '<input type="checkbox" id="itsec_backup_all_sites" name="itsec_backup[all_sites]" value="1" ' . checked( 1, $all_sites, false ) . '/>';
		$content .= '<label for="itsec_backup_all_sites"> ' . __( 'Checking this box will have the backup script backup all tables in your database, even if they are not part of this WordPress site.', 'ithemes-security' ) . '</label>';

		echo $content;

	}

	/**
	 * Sets the status in the plugin dashboard
	 *
	 * @since 4.0
	 *
	 * @return array statuses
	 */
	public function dashboard_status( $statuses ) {


		if ( class_exists( 'backupbuddy_api0' ) && sizeof( backupbuddy_api0::getSchedules() ) >= 1 ) {

			$status_array = 'safe-medium';
			$status = array( 'text' => __( 'Your site is performing scheduled database and file backups.', 'ithemes-security' ), 'link' => '?page=pb_backupbuddy_scheduling', );

		} elseif ( class_exists( 'backupbuddy_api0' ) ) {

			$status_array = 'medium';
			$status = array( 'text' => __( 'BackupBuddy is installed but backups do not appear to have been scheduled. Please schedule backups', 'ithemes-security' ), 'link' => '?page=pb_backupbuddy_scheduling', );

		} elseif ( $this->settings['enabled'] === true ) {

			$status_array = 'medium';
			$status       = array( 'text' => __( 'Your site is performing scheduled database backups but is not backing up files. Consider purchasing or scheduling BackupBuddy to protect your investment.', 'ithemes-security' ), 'link' => 'http://ithemes.com/better-backups', );

		} else {

			$status_array = 'high';
			$status       = array( 'text' => __( 'Your site is not performing any scheduled database backups.', 'ithemes-security' ), 'link' => '#itsec_backup_enabled', );

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
	 * echos Enable Backups Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function enabled() {

		if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) {
			$enabled = 1;
		} else {
			$enabled = 0;
		}

		$content = '<input type="checkbox" id="itsec_backup_enabled" name="itsec_backup[enabled]" value="1" ' . checked( 1, $enabled, false ) . '/>';
		$content .= '<label for="itsec_backup_enabled"> ' . __( 'Enable Scheduled Database Backups', 'ithemes-security' ) . '</label>';

		echo $content;

	}

	/**
	 * echos Exclude tables Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function exclude() {

		global $wpdb;

		$ignored_tables = array(
			'commentmeta',
			'comments',
			'links',
			'options',
			'postmeta',
			'posts',
			'term_relationships',
			'term_taxonomy',
			'terms',
			'usermeta',
			'users'
		);

		//get all of the tables
		if ( isset( $this->settings['all_sites'] ) && $this->settings['all_sites'] === true ) {
			$tables = $wpdb->get_results( 'SHOW TABLES', ARRAY_N ); //retrieve a list of all tables in the DB
		} else {
			$tables = $wpdb->get_results( 'SHOW TABLES LIKE "' . $wpdb->base_prefix . '%"', ARRAY_N ); //retrieve a list of all tables for this WordPress installation
		}

		$content = '<label for="itsec_backup_exclude"> ' . __( 'Tables with data that does not need to be backed up', 'ithemes-security' ) . '</label>';
		$content .= '<select multiple="multiple" name="itsec_backup[exclude][]" id="itsec_backup_exclude">';

		foreach ( $tables as $table ) {

			$short_table = substr( $table[0], strlen( $wpdb->prefix ) );

			if ( in_array( $short_table, $ignored_tables ) === false ) {

				if ( isset( $this->settings['exclude'] ) && in_array( $short_table, $this->settings['exclude'] ) ) {
					$selected = ' selected';
				} else {
					$selected = '';
				}

				$content .= '<option value="' . $short_table . '"' . $selected . '>' . $table[0] . '</option>';

			}

		}

		$content .= '</select>';
		$content .= '<p class="description"> ' . __( 'Some plugins can create log files in your database. While these logs might be handy for some functions, they can also take up a lot of space and, in some cases, even make backing up your database almost impossible. Select log tables above to exclude their data from the backup. Note: The table itself will be backed up, but not the data in the table.', 'ithemes-security' ) . '</p>';

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
	private function initialize( $core, $module ) {

		$this->core        = $core;
		$this->module      = $module;
		$this->settings    = get_site_option( 'itsec_backup' );
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		add_filter( 'itsec_tooltip_modules', array( $this, 'register_tooltip' ) ); //register tooltip action
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) ); //add meta boxes to admin page
		add_action( 'itsec_admin_init', array( $this, 'initialize_admin' ) ); //initialize admin area
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) ); //enqueue scripts for admin page
		add_filter( 'itsec_add_dashboard_status', array( $this, 'dashboard_status' ) ); //add information for plugin status
		add_filter( 'itsec_tracking_vars', array( $this, 'tracking_vars' ) );

		if ( isset( $_POST['itsec_backup'] ) && $_POST['itsec_backup'] == 'one_time_backup' ) {
			add_action( 'itsec_admin_init', array( $this, 'one_time_backup' ) );
		}

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
			'backup-settings-2',
			__( 'Configure Database Backups', 'ithemes-security' ),
			array( $this, 'empty_callback_function' ),
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_section(
			'backup-enabled',
			__( 'Enable Database Backups', 'ithemes-security' ),
			array( $this, 'empty_callback_function' ),
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_section(
			'backup-settings',
			__( 'Backup Schedule Settings', 'ithemes-security' ),
			array( $this, 'empty_callback_function' ),
			'security_page_toplevel_page_itsec_settings'
		);

		//404 Detection Fields
		add_settings_field(
			'itsec_backup[enabled]',
			__( 'Schedule Database Backups', 'ithemes-security' ),
			array( $this, 'enabled' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-enabled'
		);

		add_settings_field(
			'itsec_backup[interval]',
			__( 'Backup Interval', 'ithemes-security' ),
			array( $this, 'interval' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings'
		);

		add_settings_field(
			'itsec_backup[all_sites]',
			__( 'Backup Full Database', 'ithemes-security' ),
			array( $this, 'all_sites' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings-2'
		);

		add_settings_field(
			'itsec_backup[method]',
			__( 'Backup Method', 'ithemes-security' ),
			array( $this, 'method' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings-2'
		);

		add_settings_field(
			'itsec_backup[location]',
			__( 'Backup Location', 'ithemes-security' ),
			array( $this, 'location' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings-2'
		);

		add_settings_field(
			'itsec_backup[zip]',
			__( 'Compress Backup Files', 'ithemes-security' ),
			array( $this, 'zip' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings-2'
		);

		add_settings_field(
			'itsec_backup[exclude]',
			__( 'Exclude Tables', 'ithemes-security' ),
			array( $this, 'exclude' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings-2'
		);

		//Register the settings field for the entire module
		register_setting(
			'security_page_toplevel_page_itsec_settings',
			'itsec_backup',
			array( $this, 'sanitize_module_input' )
		);

	}

	/**
	 * echos Backup Interval Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function interval() {

		if ( isset( $this->settings['interval'] ) ) {
			$interval = absint( $this->settings['interval'] );
		} else {
			$interval = 3;
		}

		$content = '<input class="small-text" name="itsec_backup[interval]" id="itsec_backup_interval" value="' . $interval . '" type="text"> ';
		$content .= '<label for="itsec_backup_interval"> ' . __( 'Days', 'ithemes-security' ) . '</label>';
		$content .= '<p class="description"> ' . __( 'The number of days between database backups.', 'ithemes-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Backup Location Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function location() {

		global $itsec_globals;

		if ( isset( $this->settings['location'] ) ) {
			$location = sanitize_text_field( $this->settings['location'] );
		} else {
			$location = $itsec_globals['ithemes_backup_dir'];
		}

		$content = '<input class="large-text" name="itsec_backup[location]" id="itsec_backup_location" value="' . $location . '" type="text">';
		$content .= '<label for="itsec_backup_location"> ' . __( 'The path on your machine where backup files should be stored.', 'ithemes-security' ) . '</label>';
		$content .= '<p class="description"> ' . __( 'This path must be writable by your website. For added security, it is recommended you do not include it in your website root folder.', 'ithemes-security' ) . '</p>';

		echo $content;

	}

	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function metabox_advanced_settings() {

		echo '<p>' . __( 'One of the best ways to protect yourself from an attack is to have access to a database backup of your site. If something goes wrong, you can get your site back by restoring the database from a backup and replacing the files with fresh ones. Use the button below to create a backup of your database for this purpose. You can also schedule automated backups and download or delete previous backups.', 'ithemes-security' ) . '</p>';

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'backup-settings-2', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'backup-enabled', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'backup-settings', false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save Changes', 'ithemes-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

	}

	/**
	 * Display the form for one-time database backups
	 *
	 * @return void
	 */
	public function metabox_one_time() {

		$content = '<form method="post" action="">';
		$content .= wp_nonce_field( 'itsec_do_backup', 'wp_nonce' );
		$content .= '<input type="hidden" name="itsec_backup" value="one_time_backup" />';
		$content .= '<p>' . __( 'Press the button below to create a backup of your WordPress database. If you have "Send Backups By Email" selected in automated backups you will receive an email containing the backup file.', 'ithemes-security' ) . '</p>';
		$content .= '<p class="submit"><input type="submit" class="button-primary" value="' . __( 'Create Database Backup', 'ithemes_security' ) . '" /></p>';
		$content .= '<p><a href="?page=toplevel_page_itsec_settings#itsec_backup_all_sites">' . __( 'Adjust Backup Settings', 'ithemes-security' ) . '</a>';
		$content .= '</form>';

		echo $content;
	}

	/**
	 * Display the BackupBuddy information metabox
	 *
	 * @return void
	 */
	public function backupbuddy_metabox() {

		$content = '<p>' . __( 'A database backup is just a simple start. BackupBuddy goes one step further to provide complete backups of all your site files (including image and media files, themes, plugins, widgets and settings) - which aren\'t included in a database backup. With BackupBuddy you can customize backup schedules, send your backup files  safely off-site to remote storage destinations, restore your site quickly & easily and even move your whole site to a new host or domain.', 'ithemes-security' ) . '</p>';
		$content .= '<h4>' . __( '5 Reasons You Need a Complete Backup Strategy', 'ithemes-security' ) . '</h4>';
		$content .= '<ol>';
		$content .= '<li><strong>' . __( 'Database backups aren\'t enough.', 'ithemes-security' ) . '</strong> ' . __( 'You need complete backups of your entire site (including images and media files, themes, plugins, widgets and settings).', 'ithemes-security' ) . '</li>';
		$content .= '<li><strong>' . __( 'Backup files should be protected.', 'ithemes-security' ) . '</strong> ' . __( 'Send and store them safely off-site to a secure remote destination (like email, Dropbox, Amazon S3, etc.)', 'ithemes-security' ) . '</li>';
		$content .= '<li><strong>' . __( 'Backups should be automated and scheduled so you don\'t forget.', 'ithemes-security' ) . '</strong> ' . __( 'Set daily, weekly or monthly backups that automatically send backups off-site.', 'ithemes-security' ) . '</li>';
		$content .= '<li><strong>' . __( 'Restoring your site should be quick and easy.', 'ithemes-security' ) . '</strong> ' . __( 'If you get hacked or your server crashes, you shouldn\'t have to worry about restoring your site. Reliable backups mean nothing gets corrupted or broken during the restore process.', 'ithemes-security' ) . '</li>';
		$content .= '<li><strong>' . __( 'You should own your backup files.', 'ithemes-security' ) . '</strong> ' . __( 'Don\'t just rely on a host or service. It\'s your site, so you should own everything on it.', 'ithemes-security' ) . '</li>';
		$content .= '</ol>';

		$content .= '<p class="bub-cta"><a href="http://ithemes.com/better-backups" target="_blank" class="button-primary" >' . __( 'Learn more about BackupBuddy', 'ithemes-security' ) . '</a></p>';

		echo $content;
	}

	/**
	 * echos method Field
	 *
	 * @param  array $args field arguements
	 *
	 * @return void
	 */
	public function method() {

		if ( isset( $this->settings['method'] ) ) {
			$method = $this->settings['method'];
		} else {
			$method = 0;
		}

		echo '<select id="itsec_backup_method" name="itsec_backup[method]">';

		echo '<option value="0" ' . selected( $method, '0' ) . '>' . __( 'Save Locally and Email', 'ithemes-security' ) . '</option>';
		echo '<option value="1" ' . selected( $method, '1' ) . '>' . __( 'Email Only', 'ithemes-security' ) . '</option>';
		echo '<option value="2" ' . selected( $method, '2' ) . '>' . __( 'Save Locally Only', 'ithemes-security' ) . '</option>';
		echo '</select><br />';
		echo '<label for="itsec_backup_method"> ' . __( 'Backup Save Method', 'ithemes-security' ) . '</label>';
		echo '<p class="description">' . __( 'Select what we should do with your backup file. You can have it emailed to you, saved locally or both.' ) . '</p>';

	}

	/**
	 * Executes one-time backup.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function one_time_backup() {

		if ( ! wp_verify_nonce( $_POST['wp_nonce'], 'itsec_do_backup' ) ) {
			die( __( 'Security error!', 'ithemes-security' ) );
		}

		$this->module->do_backup( true );

	}

	/**
	 * Register backups for tooltips
	 *
	 * @param  array $tooltip_modules array of tooltip modules
	 *
	 * @return array                   array of tooltip modules
	 */
	public function register_tooltip( $tooltip_modules ) {

		$tooltip_modules['backup'] = array(
			'priority'  => 10,
			'class'     => 'itsec_tooltip_backup',
			'heading'   => __( 'Back up your site', 'ithemes-security' ),
			'text'      => __( 'We recommend making a database backup before you get started securing your site.', 'ithemes-security' ),
			'link_text' => __( 'Make a backup', 'ithemes-security' ),
			'callback'  => array( $this, 'tooltip_ajax' ),
			'success'   => __( 'Backup completed. Please check your email or uploads folder.', 'ithemes-security' ),
			'failure'   => __( 'Whoops. Something went wrong. Check the backup page or contact support.', 'ithemes-security' ),
		);

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

		global $itsec_globals;

		$input['enabled']   = ( isset( $input['enabled'] ) && intval( $input['enabled'] == 1 ) ? true : false );
		$input['all_sites'] = ( isset( $input['all_sites'] ) && intval( $input['all_sites'] == 1 ) ? true : false );
		$input['interval']  = isset( $input['interval'] ) ? absint( $input['interval'] ) : 3;
		$input['method']    = isset( $input['method'] ) ? intval( $input['method'] ) : 0;
		$input['location']  = isset( $input['location'] ) ? sanitize_text_field( $input['location'] ) : $itsec_globals['ithemes_backup_dir'];
		$input['last_run']  = isset( $this->settings['last_run'] ) ? $this->settings['last_run'] : 0;

		if ( isset( $input['location'] ) && $input['location'] != $itsec_globals['ithemes_backup_dir'] ) {
			$good_path = ITSEC_Lib::validate_path( $input['location'] );
		} else {
			$good_path = true;
		}

		if ( $good_path !== true ) {

			$type            = 'error';
			$message         = __( 'The file path entered does not appear to be valid. Please ensure it exists and that WordPress can write to it. ', 'ithemes-security' );
			$input['method'] = 2;

			add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

		}

		$input['exclude'] = ( isset( $input['exclude'] ) ? $input['exclude'] : array() );

		$input['zip'] = ( isset( $input['zip'] ) && intval( $input['zip'] == 1 ) ? true : false );

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

		if ( isset( $_POST['itsec_backup'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'security_page_toplevel_page_itsec_settings-options' ) ) {
				die( __( 'Security error!', 'ithemes-security' ) );
			}

			update_site_option( 'itsec_backup', $_POST['itsec_backup'] ); //we must manually save network options

		}

	}

	/**
	 * Performs actions for tooltip function.
	 *
	 * @since 4.0
	 *
	 * return void
	 */
	public function tooltip_ajax() {

		$result = $this->module->do_backup( true );

		if ( $result === true ) {
			die( 'true' );
		} else {
			die( 'false' );
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

		$vars['itsec_backup'] = array(
			'enabled' => '0:b',
			'method'  => '3:s',
			'zip'     => '1:b',
		);

		return $vars;

	}

	/**
	 * echos Zip Backups Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function zip() {

		if ( isset( $this->settings['zip'] ) && $this->settings['zip'] === false ) {
			$zip = 0;
		} else {
			$zip = 1;
		}

		$content = '<input type="checkbox" id="itsec_backup_zip" name="itsec_backup[zip]" value="1" ' . checked( 1, $zip, false ) . '/>';
		$content .= '<label for="itsec_backup_zip"> ' . __( 'Zip Database Backups', 'ithemes-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'You may need to turn this off if you are having problems with backups.', 'ithemes-security' ) . '</p>';
		echo $content;

	}

}