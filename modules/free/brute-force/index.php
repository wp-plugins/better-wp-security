<?php

if ( ! class_exists( 'ITSEC_Brute_Force' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-brute-force.php' );
}

$brute_force = new ITSEC_Brute_Force();

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_Brute_Force_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-brute-force-admin.php' );
	}

	new ITSEC_Brute_Force_Admin( $this, $brute_force );

}
