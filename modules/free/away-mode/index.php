<?php

if ( ! class_exists( 'ITSEC_Away_Mode' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-away-mode.php' );
}

$away_mode = new ITSEC_Away_Mode();

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_Away_Mode_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-away-mode-admin.php' );
	}

	new ITSEC_Away_Mode_Admin( $this, $away_mode );

}
