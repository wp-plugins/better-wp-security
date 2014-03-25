<?php

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_Content_Directory_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-content-directory-admin.php' );
	}

	new ITSEC_Content_Directory_Admin( $this );

}
