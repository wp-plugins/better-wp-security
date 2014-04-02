<?php

if ( ! class_exists( 'ITSEC_Backup' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-backup.php' );
}

$itsec_backup = new ITSEC_Backup( $this );

if ( is_admin() ) {

	if ( ! class_exists( 'ITSEC_Backup_Admin' ) ) {
		require( dirname( __FILE__ ) . '/class-itsec-backup-admin.php' );
	}

	new ITSEC_Backup_Admin( $this, $itsec_backup );

}
