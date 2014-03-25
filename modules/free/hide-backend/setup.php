<?php

if ( ! class_exists( 'ITSEC_Hide_Backend_Setup' ) ) {

	class ITSEC_Hide_Backend_Setup {

		private
			$defaults;

		public function __construct() {

			global $itsec_setup_action;

			$this->defaults = array(
				'enabled'  => false,
				'slug'     => 'wplogin',
				'register' => 'wp-register.php',
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

			$options = get_site_option( 'itsec_hide_backend' );

			if ( is_multisite() ) {

				switch_to_blog( 1 );

				$bwps_options = get_option( 'bit51_bwps' );

				restore_current_blog();

			} else {

				$bwps_options = get_option( 'bit51_bwps' );

			}

			if ( $bwps_options !== false && isset( $bwps_options['hb_enabled'] ) && $bwps_options['hb_enabled'] == 1 ) {

				$this->defaults['show-tooltip'] = true;
				define( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP', true );

			} else {

				$this->defaults['show-tooltip'] = false;

			}

			if ( $options === false ) {

				add_site_option( 'itsec_hide_backend', $this->defaults );

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

			delete_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP' );

		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_hide_backend' );

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

				$current_options = get_site_option( 'itsec_hide_backend' );

				$current_options['enabled']  = isset( $itsec_bwps_options['hb_enabled'] ) && $itsec_bwps_options['hb_enabled'] == 1 ? true : false;
				$current_options['register'] = isset( $itsec_bwps_options['hb_register'] ) ? sanitize_text_field( $itsec_bwps_options['hb_register'] ) : 'wp-register.php';

				if ( $current_options['enabled'] === true ) {

					$current_options['show-tooltip'] = true;
					set_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP', true, 600 );

				} else {

					$current_options['show-tooltip'] = false;

				}

				$forbidden_slugs = array( 'admin', 'login', 'wp-login.php', 'dashboard', 'wp-admin', '' );

				if ( isset( $itsec_bwps_options['hb_login'] ) && ! in_array( trim( $itsec_bwps_options['hb_login'] ), $forbidden_slugs ) ) {

					$current_options['slug'] = $itsec_bwps_options['hb_login'];
					set_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP', true, 600 );

				} else {

					$current_options['enabled'] = false;
					set_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP', true, 600 );

				}

				update_site_option( 'itsec_hide_backend', $current_options );
				add_site_option( 'itsec_rewrites_changed', true );

			}

		}

	}

}

new ITSEC_Hide_Backend_Setup();