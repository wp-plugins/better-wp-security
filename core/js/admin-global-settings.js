jQuery( document ).ready( function () {

	jQuery( '#itsec_reset_log_location' ).click( function ( event ) {

		event.preventDefault();

		jQuery( '#itsec_global_log_location' ).val( itsec_global_settings.location );

	} );

} );