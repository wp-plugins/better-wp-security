<?php

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_Admin_User_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-admin-user-admin.php' );
	}

	new ITSEC_Admin_User_Admin( $this );

}
