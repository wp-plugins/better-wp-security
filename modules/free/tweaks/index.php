<?php

if ( ! class_exists( 'ITSEC_Tweaks' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-tweaks.php' );
}

new ITSEC_Tweaks();

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_Tweaks_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-tweaks-admin.php' );
	}

	new ITSEC_Tweaks_Admin( $this );

}

