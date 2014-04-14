<?php

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_Database_Prefix_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-database-prefix-admin.php' );
	}

	new ITSEC_Database_Prefix_Admin( $this );

}
