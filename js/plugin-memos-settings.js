(function( jQuery ) {
	jQuery( function() {
		jQuery( '.plugin-memos-color-picker' ).wpColorPicker();
	});
	
})( jQuery );

jQuery( document ).ready( function () {
	jQuery( '.plugin-memos-stati-edit-button' ).click( function( e ){
		e.preventDefault();
		e.stopPropagation();

		var status_id = jQuery( this ).val();
		var options_title = jQuery( '#plugin-memos-status-options-title-' + status_id );
		var options = jQuery( '#plugin-memos-status-options-' + status_id );
		var delete_status = jQuery( '#plugin-memos-delete-status-' + status_id );
		var status_title = jQuery( '#plugin-memos-status_title-' + status_id );
		var color_picker = jQuery( '#plugin-memos-color_picker-' + status_id );

		// show or hide the options title
		if ( options_title.css( 'display' ) == 'none' ) {
			options_title.css( 'display', 'block' );
		} else {
			options_title.css( 'display', 'none' )
		}

		// show or hide the options block		
		if ( options.css( 'display' ) == 'none' ) {
			options.css( 'display', 'block' );
		} else {
			options.css( 'display', 'none' );
		}

		// show or hide the save/delete buttons
		if ( delete_status.css( 'display' ) == 'none' ) {
			delete_status.css( 'display', 'block' );
		} else {
			delete_status.css( 'display', 'none' );
		}

		// disable or allow the title textfield
		if ( status_title.prop( 'disabled' ) == true ) {
			status_title.removeAttr( 'disabled' );
		} else {
			status_title.attr( 'disabled', true );
		}
		
		// show or hide the color picker
		if ( color_picker.css( 'display' ) == 'none' ) {
			color_picker.css( 'display', 'block' );
		} else {
			color_picker.css( 'display', 'none' );
		}
	});
});