<?php

if ( ! class_exists( 'ITSEC_SSL' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-ssl.php' );
}

$ssl = new ITSEC_SSL();

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_SSL_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-ssl-admin.php' );
	}

	new ITSEC_SSL_Admin( $this, $ssl );

}
