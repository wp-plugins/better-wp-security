<?php
if ( ! function_exists( 'auth_redirect' ) ) {

	function auth_redirect() {

		if ( ! is_user_logged_in() ) {

			$settings = get_site_option( 'itsec_hide_backend' );

			if ( isset( $settings['theme_compat'] ) && $settings['theme_compat'] === true ) {

				wp_redirect( ITSEC_Lib::get_home_root() . sanitize_title( isset( $this->settings['theme_compat_slug'] ) ? $this->settings['theme_compat_slug'] : 'not_found' ), 301 );

			} else {

				add_action( 'wp', array( 'ITSEC_Lib', 'set_404' ) );

			}

		} else {
			return;
		}

	}

}