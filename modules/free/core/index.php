<?php

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_Core_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-core-admin.php' );
	}

	new ITSEC_Core_Admin( $this );

}