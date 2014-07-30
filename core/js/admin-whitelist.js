jQuery( document ).ready( function () {

	//process tooltip actions
	jQuery( '.itsec_temp_whitelist_ajax' ).click( function ( event ) {

		event.preventDefault();

		var caller = this;

		var data = {
			action : 'itsec_temp_whitelist_ajax',
			nonce  : itsec_temp_whitelist.nonce
		};

		//let user know we're working
		jQuery( caller ).removeClass( 'itsec_tooltip_ajax button-primary' ).addClass( 'button-secondary' ).html( 'Working...' );

		//call the ajax
		jQuery.post( ajaxurl, data, function ( response ) {

			if ( response !== 'error' ) {

				data = jQuery.parseJSON( response );

				jQuery( caller ).replaceWith( '<span class="itsec_temp_whitelist_ajax">' + data.message1 + ', <strong>' + data.ip + '</strong>, ' + data.message2 + ' <strong>' + data.exp + '</strong>.</span>' );

			} else {

				jQuery( caller ).replaceWith( '<span class="itsec_temp_whitelist_ajax">fail</span>' );
			}

		} );

	} );

} );