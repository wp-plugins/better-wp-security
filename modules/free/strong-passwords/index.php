<?php

if ( ! class_exists( 'ITSEC_Strong_Passwords' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-strong-passwords.php' );
}

new ITSEC_Strong_Passwords();

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_Strong_Passwords_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-strong-passwords-admin.php' );
	}

	new ITSEC_Strong_Passwords_Admin( $this );

}

