<?php

if ( ! class_exists( 'ITSEC_Away_Mode_Setup' ) ) {

	class ITSEC_Away_Mode_Setup {

		private
			$defaults;

		public function __construct() {

			global $itsec_setup_action;

			$this->defaults = array(
				'enabled' => false,
				'type'    => false,
				'start'   => 1,
				'end'     => 1,
			);

			if ( isset( $itsec_setup_action ) ) {

				switch ( $itsec_setup_action ) {

					case 'activate':
						$this->execute_activate();
						break;
					case 'upgrade':
						$this->execute_upgrade();
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
		 * @return void
		 */
		public function execute_activate() {

			$options = get_site_option( 'itsec_away_mode' );

			if ( $options === false ) {

				add_site_option( 'itsec_away_mode', $this->defaults );

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

			delete_site_option( 'itsec_away_mode' );
			delete_site_transient( 'itsec_away' );
			delete_site_transient( 'itsec_away_mode' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade() {

			global $itsec_old_version;

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options, $itsec_globals;

				$current_options = get_site_option( 'itsec_away_mode' );

				if ( $current_options === false ) {
					$current_options = $this->defaults;
				}

				$current_options['enabled'] = isset( $itsec_bwps_options['am_enabled'] ) && $itsec_bwps_options['am_enabled'] == 1 ? true : false;
				$current_options['type']    = isset( $itsec_bwps_options['am_type'] ) && $itsec_bwps_options['am_type'] == 1 ? 1 : 2;

				if ( isset( $current_options['am_startdate'] ) && isset( $current_options['am_starttime'] ) ) {
					$current_options['start'] = $current_options['am_startdate'] + $current_options['am_starttime'];

				}

				if ( isset( $current_options['am_enddate'] ) && isset( $current_options['am_endtime'] ) ) {
					$current_options['end'] = $current_options['am_enddate'] + $current_options['am_endtime'];
				}

				update_site_option( 'itsec_away_mode', $current_options );

				$away_file = $itsec_globals['ithemes_dir'] . '/itsec_away.confg'; //override file

				if ( $current_options['enabled'] === true && ! file_exists( $away_file ) ) {

					@file_put_contents( $away_file, 'true' );

				} else {

					@unlink( $away_file );

				}

			}

		}

	}

}

new ITSEC_Away_Mode_Setup();