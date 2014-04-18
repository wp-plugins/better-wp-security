<?php

class ITSEC_Away_Mode {

	private
		$settings,
		$away_file;

	function run() {

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
	 * @since 4.0
	 *
	 * @param bool  $form  [false] Whether the call comes from the same options form
	 * @param array $input [NULL] Input of options to check if calling from form
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

		$current_time  = $itsec_globals['current_time'];
		$has_away_file = @file_exists( $this->away_file );

		if ( 1 == $test_type ) { //daily

			$test_start -= strtotime( date( 'Y-m-d', $test_start ) );
			$test_end -= strtotime( date( 'Y-m-d', $test_end ) );
			$day_seconds = $current_time - strtotime( date( 'Y-m-d', $current_time ) );

			if ( $test_start === $test_end ) {
				return false;
			}

			if ( $test_start < $test_end ) { //same day

				if ( $test_start <= $day_seconds && $test_end >= $day_seconds && ( $form === true || ( $this->settings['enabled'] === true && $has_away_file ) ) ) {
					return true;
				}

			} else { //overnight

				if ( ( $test_start < $day_seconds || $test_end > $day_seconds ) && ( $form === true || ( $this->settings['enabled'] === true && $has_away_file ) ) ) {
					return true;
				}

			}

		} else if ( $test_start !== $test_end && $test_start <= $current_time && $test_end >= $current_time && ( $form === true || ( $this->settings['enabled'] === true && $has_away_file ) ) ) { //one time

			return true;

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
