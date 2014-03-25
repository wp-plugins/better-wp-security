<?php

if ( ! class_exists( 'ITSEC_Hide_Backend' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-hide-backend.php' );
}

new ITSEC_Hide_Backend();

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_Hide_Backend_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-hide-backend-admin.php' );
	}

	new ITSEC_Hide_Backend_Admin( $this );

}
