jQuery( document ).ready( function () {
	var label_data = document.querySelectorAll( '[data-plugin-memos]' );

	// iterate over all the lables
	for ( var i in label_data ) if ( label_data.hasOwnProperty( i ) ) {
		var plugin_name = label_data[i].getAttribute( 'data-plugin-memos' );

		// select the plugin row with label
		var plugin_row = document.querySelectorAll( '[data-plugin="' + plugin_name + '"] th, [data-plugin="' + plugin_name + '"] td' );

		var index = 0
		var length = plugin_row.length;

		// change the the box-shadow for all plugins with labels
		for ( ; index < length; index++ ) {
			plugin_row[index].style.boxShadow = "none";
		}
	}

	// The modal box for the labels-form
	jQuery( '.plugin-memos-edit-button' ).click( function (e) {
        e.preventDefault();
        e.stopPropagation();
		
		var plugin_name = jQuery( this ).attr( 'data-plugin-name' );

		// take all the infos and fields from the plugin were the button was pressed
		var selectedPlugin = jQuery( '#plugin-memos-edit-labels-box-' + plugin_name ).html();
		
		if ( jQuery( '.plugin-memos-labels-form-wrap' ).css( 'display' ) == 'none' ) {
			jQuery( '.plugin-memos-labels-form-wrap' ).css( 'display', 'flex' );
		} else {
			jQuery( '.plugin-memos-labels-form-wrap' ).css( 'display', 'none' );			
		}
		
		// put the selected infos into the form
		jQuery( '#plugin-memos-labels-form' ).html( selectedPlugin );

		// post the infos with ajax when the save button was clicked
		jQuery( '.plugin-memos-save-labels-button' ).on( 'click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			jQuery('.plugin-memos-save-labels-button').prop("disabled", true);
	
			var plugin_name = jQuery( this ).attr( 'data-name-for-save' );
			
			jQuery.post( plugin_memos_ajax_obj.ajax_url, { // the url were the call is sent
				_ajax_nonce: plugin_memos_ajax_obj.nonce, // the nonce is used to verify that the call comes from this plugin
				action: 'plugin_memos_save_labels', // the name of the action WP generates
				data: jQuery( '#plugin-memos-labels-form' ).serialize() // serialize the form-data
				},
				function( data ) {
					data = data.trim();
					if ( data == '' ) {
						// if there are no errors we close the box and reload
						jQuery( '.plugin-memos-edit-labels-box' ).css( 'display', 'none' );
						window.location.reload();
					} else {
						// Print the errors if there are none already. If there are we have to remove them first
						if ( jQuery( '.plugin-memos-labels-form-wrap #plugin-memos-error-box-' + plugin_name ).length <= 1 ) {
							jQuery( '.plugin-memos-labels-form-wrap #plugin-memos-error-box-' + plugin_name ).html( '<p style="color:red;">' + data + '</p>');
						} else {
							// first remove the errors from before then print the new ones.
							jQuery( '.plugin-memos-labels-form-wrap #plugin-memos-error-box-' + plugin_name ).remove();
							jQuery( '.plugin-memos-labels-form-wrap #plugin-memos-error-box-' + plugin_name ).append( '<p style="color:red;">' + data + '</p>');
						}
					}

					jQuery( '.plugin-memos-save-labels-button' ).prop( "disabled", false );
			});
			
		});

		// the close button
		jQuery( '.plugin-memos-close' ).click( function (e) {
			e.preventDefault();
			e.stopPropagation();

			// clean the form out when its closed
			jQuery( '#plugin-memos-labels-form' ).html( '' );
			jQuery( '.plugin-memos-labels-form-wrap' ).css( 'display', 'none' );
		});
    });

	// Add the empty form after the plugin-list. 
	// We do this because the plugin-list is one entire form and having forms inside a form is not good
	jQuery( '#bulk-action-form' ).after( '<div class="plugin-memos-labels-form-wrap">' + 
		'<div class="plugin-memos-label-form-flex"><form id="plugin-memos-labels-form"></form></div></div>' );
});
