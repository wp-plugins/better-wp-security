<?php
if ( ! function_exists( 'auth_redirect' ) ) {

	function auth_redirect() {

		if ( ! is_user_logged_in() ) {

			$settings = get_site_option( 'itsec_hide_backend' );

			if ( isset( $settings['theme_compat'] ) && $settings['theme_compat'] === true ) {

				wp_redirect( ITSEC_Lib::get_home_root() . sanitize_title( isset( $settings['theme_compat_slug'] ) ? $settings['theme_compat_slug'] : 'not_found' ), 301 );

				exit;

			} else {

				add_action( 'wp', array( 'ITSEC_Lib', 'set_404' ) );

			}

		} else {
			return;
		}

	}

}
