<?php

class ITSEC_Away_Mode {

	private
		$settings,
		$away_file;

	function __construct() {

		global $itsec_globals;

		$this->settings  = get_site_option( 'itsec_away_mode' );
		$this->away_file = $itsec_globals['ithemes_dir'] . '/itsec_away.confg'; //override file

		//Execute away mode functions on admin init
		if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) {
			add_action( 'itsec_admin_init', array( $this, 'execute_away_mode' ) );
		}

	}

	/**
	 * Check if away mode is active
	 *
	 * @param bool $forms [false] Whether the call comes from the same options form
	 * @param      array  @input[NULL] Input of options to check if calling from form
	 *
	 * @return bool true if locked out else false
	 */
	public function check_away( $form = false, $input = null ) {

		global $itsec_globals;

		if ( $form === false ) {

			$test_type  = $this->settings['type'];
			$test_start = $this->settings['start'];
			$test_end   = $this->settings['end'];

		} else {

			$test_type  = $input['type'];
			$test_start = $input['start'];
			$test_end   = $input['end'];

		}

		$transaway = get_site_transient( 'itsec_away' );

		//if transient indicates away go ahead and lock them out
		if ( $form === false && $transaway === true && file_exists( $this->away_file ) ) {

			return true;

		} else { //check manually

			$current_time = $itsec_globals['current_time'];
			$remaining    = 0;

			if ( 1 == $test_type ) { //daily

				$test_start -= strtotime( date( 'Y-m-d', $test_start ) );
				$test_end -= strtotime( date( 'Y-m-d', $test_end ) );
				$day_seconds = $current_time - strtotime( date( 'Y-m-d' ) );

				if ( $test_start < $test_end ) { //same day

					if ( ( $test_start <= $day_seconds ) && ( $test_end >= $day_seconds ) ) {
						$remaining = $test_end - $day_seconds;
					}

				} else { //overnight

					if ( ( $test_start < $day_seconds ) || ( $test_end > $day_seconds ) ) {
						$remaining = $test_end + 86400 - $day_seconds;
					}

				}

			} else if ( ( $test_start <= $current_time ) && ( $test_end >= $current_time ) ) { //one time

				$remaining = $test_end - $current_time;

			}

			if ( $remaining > 0 && ( $form === true || ( $this->settings['enabled'] === true && @file_exists( $this->away_file ) ) ) ) { //if away mode is enabled continue

				if ( $form === false ) {

					if ( get_site_transient( 'itsec_away' ) === true ) {
						delete_site_transient( 'itsec_away' );
					}

					set_site_transient( 'itsec_away', true, $remaining );

				}

				return true; //time restriction is current

			}

		}

		return false; //they are allowed to log in

	}

	/**
	 * Execute away mode functionality
	 *
	 * @return void
	 */
	public function execute_away_mode() {

		//execute lockout if applicable
		if ( $this->check_away() ) {

			wp_redirect( get_option( 'siteurl' ) );
			wp_clear_auth_cookie();

		}

	}

}
