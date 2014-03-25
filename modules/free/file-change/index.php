<?php

if ( ! class_exists( 'ITSEC_File_Change' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-file-change.php' );
}

$file_change = new ITSEC_File_Change();

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_File_Change_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-file-change-admin.php' );
	}

	new ITSEC_File_Change_Admin( $this, $file_change );

}
