jQuery( document ).ready( function () {

	set_temp();
	release_temp();

} );

var set_temp = function () {

	//process add to whitelist
	jQuery( '.itsec_temp_whitelist_ajax' ).bind( 'click', function ( event ) {

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

				jQuery( '.itsec_temp_whitelist' ).replaceWith( '<p class="itsec_temp_whitelist submit">' + data.message1 + ', <strong>' + data.ip + '</strong>, ' + data.message2 + ' <strong>' + data.exp + '</strong>.<br /><a href="#" class="itsec_temp_whitelist_release_ajax button-primary">' + data.message3 + '</a></p>' );
				release_temp();

			}
			else {

				jQuery( caller ).replaceWith( '<span class="itsec_temp_whitelist_ajax">fail</span>' );
			}

		} );

	} );

}

var release_temp = function () {

	//process reset whitelist actions
	jQuery( '.itsec_temp_whitelist_release_ajax' ).bind( 'click', function ( event ) {

		event.preventDefault();

		var caller = this;

		var data = {
			action : 'itsec_temp_whitelist_release_ajax',
			nonce  : itsec_temp_whitelist.nonce
		};

		//let user know we're working
		jQuery( caller ).removeClass( 'itsec_tooltip_ajax button-primary' ).addClass( 'button-secondary' ).html( 'Working...' );

		//call the ajax
		jQuery.post( ajaxurl, data, function ( response ) {

			if ( response !== 'error' ) {

				jQuery( '.itsec_temp_whitelist' ).replaceWith( '<p class="itsec_temp_whitelist submit"><a href="#" class="itsec_temp_whitelist_ajax button-primary">' + itsec_temp_whitelist.success + '</a></p>' );
				set_temp();

			}
			else {

				jQuery( caller ).replaceWith( '<span class="itsec_temp_whitelist_ajax">fail</span>' );
			}

		} );

	} );

}