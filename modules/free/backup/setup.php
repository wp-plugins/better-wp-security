<?php

if ( ! class_exists( 'ITSEC_Backup_Setup' ) ) {

	class ITSEC_Backup_Setup {

		private
			$defaults;

		public function __construct() {

			global $itsec_setup_action, $itsec_globals;

			$this->defaults = array(
				'enabled'   => false,
				'interval'  => 3,
				'all_sites' => false,
				'method'    => 1,
				'location'  => $itsec_globals['ithemes_backup_dir'],
				'last_run'  => 0,
				'zip'       => true,
				'exclude'   => array(
					'itsec_log',
					'itsec_temp',
					'itsec_lockouts',
				),
			);

			if ( isset( $itsec_setup_action ) ) {

				switch ( $itsec_setup_action ) {

					case 'activate':
						$this->execute_activate();
						break;
					case 'upgrade':
						$this->execute_activate( true );
						break;
					case 'deactivate':
						$this->execute_deactivate();
						break;
					case 'uninstall':
						$this->execute_uninstall();
						break;

				}

			} else {
				wp_die( 'error' );
			}

		}

		/**
		 * Execute module activation.
		 *
		 * @since 4.0
		 *
		 * @param  boolean $upgrade true if the plugin is updating
		 *
		 * @return void
		 */
		public function execute_activate( $upgrade = false ) {

			$options = get_site_option( 'itsec_backup' );

			if ( $options === false ) {

				add_site_option( 'itsec_backup', $this->defaults );

			}

			if ( $upgrade === true ) {
				$this->execute_upgrade();
			}

		}

		/**
		 * Execute module deactivation
		 *
		 * @return void
		 */
		public function execute_deactivate() {
		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_backup' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade() {

			global $itsec_old_version;

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_backup' );

				$current_options['enabled']  = isset( $itsec_bwps_options['backup_enabled'] ) && $itsec_bwps_options['backup_enabled'] == 1 ? true : false;
				$current_options['interval'] = isset( $itsec_bwps_options['backup_interval'] ) ? intval( $itsec_bwps_options['backup_interval'] ) : 1;

				update_site_option( 'itsec_backup', $current_options );

			}

		}

	}

}

new ITSEC_Backup_Setup();