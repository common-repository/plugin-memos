<?php

/**
 * Gets all the labels for the specified plugin from the DB
 *
 * @param string $str_plugin_name:	plugin name of the plugin to get the labels
 */
function plugin_memos_get_lables_by_plugin_name( $str_plugin_name ) {
	global $wpdb;

	$arr_labels_from_db = array();
	$arr_labels = array();

	$query_get_labels = 'SELECT * FROM `' . $wpdb->prefix . 'plugin_memos_labels`' .
		'WHERE `plugin_name` = "'. $str_plugin_name . '";';

	$arr_labels_from_db = $wpdb->get_results( $query_get_labels, ARRAY_A ) ?? [];

	foreach ( $arr_labels_from_db as $single_label ) {
		$arr_labels[$single_label['status_id']][] = $single_label;
	}

	return $arr_labels;
}



/**
 * Returns all the stati in DB
 * @return array|null|object
 */
function plugin_memos_get_stati() {
	global $wpdb;

	$arr_stati = array();

	$arr_stati = $wpdb->get_results( 'SELECT * FROM `' . $wpdb->prefix . 'plugin_memos_stati`;', ARRAY_A );

	return $arr_stati;
}



/**
 * Returns a string with the HTML code for the labels
 *
 * @param array $arr_labels:	label-infos from the DB
 * @param string $str_plugin_name:	plugin name of the plugin to get the labels
 *
 * @return string
 */
function plugin_memos_get_labels_html( $arr_labels, $str_plugin_name ) {
	global $wpdb;

	$arr_stati = plugin_memos_get_stati();

	$arr_active_plugins = get_option( 'active_plugins' );

	$str_tr_class = in_array( $str_plugin_name, $arr_active_plugins ) ? 'plugin-update-tr active' : 'plugin-update-tr';

	$str_label_html = '<tr class="' . $str_tr_class . '"><td class="plugin-update colspanchange" colspan="5">';

	// assemble the labels
	foreach ( $arr_stati as $single_status ) {
		if ( ! empty( $arr_labels[$single_status['status_id']] ) ) {
			foreach ( $arr_labels[$single_status['status_id']] as $single_label ) {
				$str_background_color = plugin_memos_label_background( $single_status['status_color'] );
				$str_label_text = strlen( $single_label['label_text'] ) > 0 ?
					':&nbsp;' . $single_label['label_text'] : '';

				$str_label_html .= '<div data-plugin-memos="' . $single_label['plugin_name'] . '" ' .
					'style="margin:5px 20px 15px 40px;border-left:3.5px solid #' . $single_status['status_color'] .
					';' . $str_background_color . ';padding:2px 12px">' .
					'<p>' . $single_status['status_title'] . $str_label_text . '</p>' .
					'</div>';
			}
		}
	}

	$str_label_html .= '</td></tr>';

	return $str_label_html;
}



/**
 * Sets the background-color for the labels in the plugin-list
 *
 * @param string $str_hexcode:	hexcode of the color from db
 */
function plugin_memos_label_background( $str_hexcode ) {
	$str_background_style = '';

	$split_hex_color = str_split( $str_hexcode, 2 );

	// with hexdec() we can convert the hex values into decimal values
	$int_red = hexdec( $split_hex_color[0] );
	$int_green = hexdec( $split_hex_color[1] );
	$int_blue = hexdec( $split_hex_color[2] );

	// with rgba we can change the opacity of the background, without also making the text more transparent
	$str_background_style = 'background-color: rgba(' . $int_red . ',' . $int_green . ',' . $int_blue . ',0.3);';

	return $str_background_style;
}



/**
 * Returns a HTML-String of the label-form
 *
 * @param string $str_plugin_name:	plugin name of the edited plugin
 * @param string $str_short_plugin_name:	shorted plugin name of the edited plugin
 */
function plugin_memos_edit_labels_form( $str_plugin_name, $str_short_plugin_name ) {
	$str_edit_box = '';

	$arr_labels = plugin_memos_get_lables_by_plugin_name( $str_plugin_name );

	$arr_stati = plugin_memos_get_stati();

	$str_edit_box = '<div class="plugin-memos-edit-labels-box-content">' .
		'<span class="plugin-memos-close">&times;</span>' .
		'<h2>' . __( 'Edit labels', 'plugin-memos' ) . '</h2>' .
		'<div class="plugin-memos-error-display" id="plugin-memos-error-box-' . $str_short_plugin_name . '"></div>' .
		'<table class="plugin-memos-edit-table">';

	// assemble the label-form
	foreach ( $arr_stati as $single_status ) {
		$str_background_color = plugin_memos_label_background( $single_status['status_color'] );
		$str_label_text = '';
		$str_label_checked = '';
		$int_label_id = 0;

		$str_options_notice = '';

		// display a notice for the options and what they do
		if ( 1 == $single_status['delete_forbidden'] ) {
			$str_options_notice .= '<p>' . __( '"delete" disabled', 'plugin-memos' ) . '</p>';
		}

		if ( 1 == $single_status['deactivate_forbidden'] ) {
			$str_options_notice .= '<p>' . __( '"deactivate" disabled', 'plugin-memos' ) . '</p>';
		}

		if ( 1 == $single_status['upgrade_forbidden'] ) {
			$str_options_notice .= '<p>' . __( 'updates disabled', 'plugin-memos' ) . '</p>';
		}

		if ( ! empty( $arr_labels[$single_status['status_id']] ) ) {
			$str_label_text = strlen( $arr_labels[$single_status['status_id']][0]['label_text'] ) > 0 ?
				$arr_labels[$single_status['status_id']][0]['label_text'] : '';

			$int_label_id = $arr_labels[$single_status['status_id']][0]['label_id'];

			$str_label_checked = 'checked';
		}

		$str_edit_box .= '<tr>' .
			'<td><input type="hidden" name="label_id_' . $single_status['status_id'] . '" value="' . $int_label_id . '"/>' .
			'<input type="checkbox" name="show_in_plugin_' . $single_status['status_id'] . '" ' .
			'value="' . $single_status['status_id'] . '" ' . $str_label_checked . '/></td>' .
			'<td><div style="border-left:3.5px solid #' . $single_status['status_color'] . ';' .
			$str_background_color . ';padding:2px 12px;">' .
			'<span class="plugin-memo-edit-items">' .
			$single_status['status_title'] . '</span></div><div>' . $str_options_notice . '</div></td>' .
			'<td><span class="plugin-memo-edit-items"><textarea class="plugin-memos-label-text" name="label_text_' .
			$single_status['status_id'] . '">' . $str_label_text . '</textarea></span></td></tr>';
	}

	// the save button
	$str_edit_box .= '</table>' .
		'<div class="plugin-memos-save-labels">' .
		'<input type="button" class="plugin-memos-save-labels-button" id="plugin_memos_save_labels_button" ' .
		'data-name-for-save="' . $str_short_plugin_name . '" value="' .
		__( 'save', 'plugin-memos' ) . '"/></div>' .
		'</div>';

	return $str_edit_box;
}



/**
 * Checks the label-form for input errors
 *
 * @param string $str_status_name:	name of the status for this label
 * @param int $int_label_id:	label id from the form (0 if the label is new)
 * @param int $int_status_id:	status id from the form
 * @param string $str_label_text:	label text from the form
 * @param array $arr_stati:	Array with all the stati from the DB
 */
function plugin_memos_check_label_form( $str_status_name, $int_label_id, $int_status_id, $str_label_text, $arr_stati ) {
	$str_error = '';
	$int_label_text_len = strlen( $str_label_text );

	if ( $int_status_id == 0 && $int_label_text_len > 0 ) {
		$str_error .= $str_status_name . ': ' .
			__( 'You can only write a lable text if you also check the box.', 'plugin-memos' ) . '<br/>';
	} else if ( $int_label_text_len > 500 ) {
		$str_error .= $str_status_name . ': ' . __( 'Label text must be less than 500 characters (Now: ' .
			$int_label_text_len . ').', 'plugin-memos' ) . '<br/>';
	}

	return $str_error;
}



/**
 * Returns the plugin names of all labels with the choosen option (delete_forbidden, deactivate_forbidden, upgrade_forbidden)
 *
 * @param string $str_option: status option in DB delete_forbidden || deactivate_forbidden || upgrade_forbidden
 */
function plugin_memos_get_plugin_names_by_option ( $str_option ) {
	global $wpdb;

	$arr_status_ids = array();
	$arr_plugin_names = array();

	$query_get_status_id = 'SELECT `status_id` FROM `' . $wpdb->prefix . 'plugin_memos_stati`' .
		' WHERE `' . $str_option . '` = 1;';

	$arr_status_ids_from_db = $wpdb->get_results( $query_get_status_id, ARRAY_A );

	foreach ( $arr_status_ids_from_db as $single_status_id ) {
		$arr_status_ids[] = $single_status_id['status_id'];
	}

	$query_get_plugin_names = 'SELECT `plugin_name` FROM `' . $wpdb->prefix . 'plugin_memos_labels`' .
		' WHERE `status_id` IN (' . implode( ', ', $arr_status_ids ) . ');';

	$arr_plugin_names_from_db = $wpdb->get_results( $query_get_plugin_names, ARRAY_A );

	foreach ( $arr_plugin_names_from_db as $single_plugin_name ) {
		$arr_plugin_names[] = $single_plugin_name['plugin_name'];
	}

	return $arr_plugin_names;
}



/**
 * assembles the form to edit or create a new status
 *
 * @param array $arr_stati:	all stati from the db
 * @param array $arr_post:	post infos
 */
function plugin_memos_stati_form( $arr_stati, $arr_post ) {
	$str_html = '';

	$str_html .= '<div class="plugin-memos-flex-parent">';

	// the fields for a new status
	$str_new_delete_checked = isset( $_POST['plugin_memos_status']['plugin_memos_delete_forbidden_new'] ) ?
		'checked' : '';
	$str_new_deactivate_checked = isset( $_POST['plugin_memos_status']['plugin_memos_deactivate_forbidden_new'] ) ?
		'checked' : '';
	$str_new_upgrade_checked = isset( $_POST['plugin_memos_status']['plugin_memos_upgrade_forbidden_new'] ) ?
		'checked' : '';

	$str_html .= '<h2>' . __( 'New status', 'plugin-memos' ) . '</h2>' .
		'<form action="options-general.php?page=plugin_memos_settings" method="post">' .
		'<div style="border-bottom: 1px solid black;margin-bottom:50px;"><div class="plugin-memos-flex-child"><div class="plugin-memos-status-title">' .
		'<input style="width:100%;" type="text" name="plugin_memos_status[plugin_memos_status_title_new]" ' .
		'value="' . $_POST['plugin_memos_status']['plugin_memos_status_title_new'] . '"/></div>' .
		'<div class="plugin-memos-new-status-color"><input type="text" class="plugin-memos-color-picker" ' .
		'name="plugin_memos_status[plugin_memos_status_color_new]" ' .
		'value="' . $_POST['plugin_memos_status']['plugin_memos_status_color_new'] . '"/></div>' .
		'<div class="plugin-memos-new-status"><button class="plugin-memos-save-status" ' .
		'name="plugin_memos_status[plugin_memos_status_submit_new]" value="0">' .
		__( 'save', 'plugin-memos' ) . '</button></div></div>' .
		'<h4>' . __( 'Disable options', 'plugin-memos' ) . '</h4>' .
		'<div class="plugin-memos-disable-options-parent">' .
		'<div class="plugin-memos-disable-options-child">' .
		'<label>' . __( 'Disable deactivate', 'plugin-memos' ) . '&nbsp;' .
		'<input type="checkbox" ' . $str_new_deactivate_checked . ' ' .
		'name="plugin_memos_status[plugin_memos_deactivate_forbidden_new]"/></label></div>' .
		'<div class="plugin-memos-disable-options-child"><label>' . __( 'Disable delete', 'plugin-memos' ) . '&nbsp;' .
		'<input type="checkbox" ' . $str_new_delete_checked . ' ' .
		'name="plugin_memos_status[plugin_memos_delete_forbidden_new]"/></label></div>' .
		'<div class="plugin-memos-disable-options-child"><label>' . __( 'Disable update', 'plugin-memos' ) . '&nbsp;' .
		'<input type="checkbox" ' . $str_new_upgrade_checked . ' ' .
		'name="plugin_memos_status[plugin_memos_upgrade_forbidden_new]" /></label></div></div>' .
		'</div></form>';

	foreach ( $arr_stati as $single_status ) {
		// We display the value from $_POST if there is one (e.g. if something new was sent but there was an error)
		$str_delete_forbidden = isset( $arr_post['plugin_memos_delete_forbidden_' . $single_status['status_id']] ) ?
			$arr_post['plugin_memos_delete_forbidden_' . $single_status['status_id']] : $single_status['delete_forbidden'];
		$str_delete_checked = $str_delete_forbidden == 1 ? 'checked' : '';

		$str_deactivate_forbidden = isset( $arr_post['plugin_memos_deactivate_forbidden_' . $single_status['status_id']] ) ?
			$arr_post['plugin_memos_deactivate_forbidden_' . $single_status['status_id']] : $single_status['deactivate_forbidden'];
		$str_deactivate_checked = $str_deactivate_forbidden == 1 ? 'checked' : '';

		$str_upgrade_forbidden = isset( $arr_post['plugin_memos_upgrade_forbidden_' . $single_status['status_id']] ) ?
			$arr_post['plugin_memos_upgrade_forbidden_' . $single_status['status_id']] : $single_status['upgrade_forbidden'];
		$str_upgrade_checked = $str_upgrade_forbidden == 1 ? 'checked' : '';

		// Same as with the options, we display the value from $_POST if there is one.
		$str_status_color = isset( $arr_post['plugin_memos_status_color_' . $single_status['status_id']] ) ?
			$arr_post['plugin_memos_status_color_' . $single_status['status_id']] : '#' . $single_status['status_color'];

		$str_status_title = isset( $arr_post['plugin_memos_status_title_' . $single_status['status_id']] ) ?
			$arr_post['plugin_memos_status_title_' . $single_status['status_id']] : $single_status['status_title'];

		// The html of the stati-form
		$str_html .= '<form action="options-general.php?page=plugin_memos_settings" method="post">' .
			'<div><div class="plugin-memos-flex-child"><div class="plugin-memos-status-title">' .
			'<input style="width:100%;" id="plugin-memos-status_title-' . $single_status['status_id'] . '" ' .
			'disabled type="text" name="plugin_memos_status[plugin_memos_status_title_' .
			$single_status['status_id'] . ']" value="' . $str_status_title . '"/></div>' .
			'<div class="plugin-memos-status-color" id="plugin-memos-color_picker-' .
			$single_status['status_id'] . '"><input type="text" class="plugin-memos-color-picker" ' .
			'name="plugin_memos_status[plugin_memos_status_color_' . $single_status['status_id'] . ']" ' .
			'value="' . $str_status_color . '"/></div>' .
			'<div class="plugin-memos-stati-edit"><button class="plugin-memos-stati-edit-button" value="' .
			$single_status['status_id'] . '">' . __( 'Edit', 'plugin-memos' ) . '</button></div></div>' .
			'<div style="display: none;" id="plugin-memos-status-options-title-' . $single_status['status_id'] . '">' .
			'<h4>' . __( 'Disable options', 'plugin-memos' ) . '</h4></div>' .
			'<div style="display:none;" class="plugin-memos-disable-options-parent" id="plugin-memos-status-options-' .
			$single_status['status_id'] . '">' .
			'<div class="plugin-memos-disable-options-child">' .
			'<label>' .
			'<input type="checkbox" name="plugin_memos_status[plugin_memos_deactivate_forbidden_' .
			$single_status['status_id'] . ']" value="1" ' . $str_deactivate_checked . '/>' .
			__( 'Disable deactivate', 'plugin-memos' ) . '&nbsp;</label></div>' .
			'<div class="plugin-memos-disable-options-child"><label>' .
			'<input type="checkbox" name="plugin_memos_status[plugin_memos_delete_forbidden_' .
			$single_status['status_id'] . ']" value="1" ' . $str_delete_checked . '/>' .
			__( 'Disable delete', 'plugin-memos' ) . '&nbsp;</label></div>' .
			'<div class="plugin-memos-disable-options-child"><label>' .
			'<input type="checkbox" name="plugin_memos_status[plugin_memos_upgrade_forbidden_' .
			$single_status['status_id'] . ']" value="1" ' . $str_upgrade_checked . '/>' .
			__( 'Disable update', 'plugin-memos' ) . '&nbsp;</label></div></div>' .
			'</div><div class="plugin-memos-delete-status" id="plugin-memos-delete-status-' .
			$single_status['status_id'] . '"><button class="plugin-memos-save-status" ' .
			'name="plugin_memos_status[plugin_memos_status_submit]" value="' . $single_status['status_id'] . '">' .
			__( 'save', 'plugin-memos' ) . '</button><button class="plugin-memos-delete-status-button" onclick="return confirm(\'' .
			__( 'Do you really want to delete this status? All labels belonging to it will be deleted as well.',
			'plugin-memos' ) . '\');" name="plugin_memos_status[plugin_memos_delete]" value="' .
			$single_status['status_id'] . '">' . __( 'delete', 'plugin-memos' ) . '</button></div></form>';
	}

	$str_html .= '</div>';

	return $str_html;
}



/**
 * check the input from the stati-form
 *
 * @param string $str_status_title: status_title from the form
 * @param string $str_status_color: status_color from the form
 */
function plugin_memos_check_status_field( $str_status_title, $str_status_color ) {
	// taking away the "#" because ctype_xdigit counts it as an error but the WP color picker automatically adds it.
	$str_status_color = str_replace( '#', '', $str_status_color );

	$arr_error = array();
	$int_len_status_title = strlen( $str_status_title );
	$int_len_status_color = strlen( $str_status_color );

	// The title is not allowed to be empty or over 255 characters long
	if ( $int_len_status_title == 0 ) {
		$arr_error[] = __( 'Please enter a title for the status.', 'plugin-memos' );
	} else if ( $int_len_status_title > 255 ) {
		$arr_error[] = __( 'The title cannot be longer than 255 characters (length: ', 'plugin-memos' ) .
			$int_len_status_title . ').';
	}

	// the color value is not allowed to be empty or over 255 characters long as well
	if ( $int_len_status_color == 0 ) {
		$arr_error[] = __( 'Please pick a color for the status.', 'plugin-memos' );
	} else if ( $int_len_status_color > 255 ) {
		$arr_error[] = __( 'The color cannot be longer than 255 characters (length: ', 'plugin-memos' ) .
			$int_len_status_color . ').';
	// check if all the digits are hexadecimal (a-f, 0-6)
	} else if ( false == ctype_xdigit( $str_status_color ) ) {
		$arr_error[] = __( 'Please enter a correct hexadecimal number as color', 'plugin-memos' );
	}

	return $arr_error;
}



/**
 * save the infos from the stati in the db
 *
 * @param array $arr_post: $_POST data from the form
 */
function plugin_memos_save_stati_in_db( $arr_post ) {
	global $wpdb;

	$arr_errors = array();

	$str_stati_table_name = $wpdb->prefix . 'plugin_memos_stati';
	$arr_row_formats = array( '%s', '%s', '%d', '%d', '%d' );
	$arr_where_format = array( '%d' );

	// if the delete button was pressed
	if ( isset( $arr_post['plugin_memos_delete'] ) ) {
		$int_delete_id = intval( $arr_post['plugin_memos_delete'] );

		plugin_memos_delete_status( $int_delete_id );

	// if an existing status was changed
	} else if ( isset( $arr_post['plugin_memos_status_submit'] ) ) {
		$int_status_id = intval( $arr_post['plugin_memos_status_submit'] );

		$str_status_title = $arr_post['plugin_memos_status_title_' . $int_status_id];
		$str_status_color = $arr_post['plugin_memos_status_color_' . $int_status_id];
		$int_delete_forbidden = isset( $arr_post['plugin_memos_delete_forbidden_' . $int_status_id] ) ? 1 : 0;
		$int_deactivate_forbidden = isset( $arr_post['plugin_memos_deactivate_forbidden_' . $int_status_id] ) ? 1 : 0;
		$int_upgrade_forbidden = isset( $arr_post['plugin_memos_upgrade_forbidden_' . $int_status_id] ) ? 1 : 0;

		// we check for errors
		$arr_errors = plugin_memos_check_status_field( $str_status_title, $str_status_color );

		if ( count( $arr_errors ) == 0 ) {
			$wpdb->update(
				$str_stati_table_name,
				array(
					'status_title' => $str_status_title,
					'status_color' => substr( $str_status_color, 1, 6 ),
					'deactivate_forbidden' => $int_deactivate_forbidden,
					'delete_forbidden' => $int_delete_forbidden,
					'upgrade_forbidden' => $int_upgrade_forbidden
				),
				array(
					'status_id' => $int_status_id
				),
				$arr_row_formats,
				$arr_where_format
			);
		}
	// We insert the status if its a new one
	} else if ( isset( $arr_post['plugin_memos_status_submit_new'] ) ) {
		$str_new_title = $arr_post['plugin_memos_status_title_new'];
		$str_new_color = $arr_post['plugin_memos_status_color_new'];
		$int_new_delete_forbidden = isset( $arr_post['plugin_memos_delete_forbidden_new'] ) ? 1 : 0;
		$int_new_deactivate_forbidden = isset( $arr_post['plugin_memos_deactivate_forbidden_new'] ) ? 1 : 0;
		$int_new_upgrade_forbidden = isset( $arr_post['plugin_memos_upgrade_forbidden_new'] ) ? 1 : 0;

		// check for errors
		$arr_errors = plugin_memos_check_status_field( $str_new_title, $str_new_color );

		if ( 0 == count( $arr_errors ) ) {
			$wpdb->insert(
				$str_stati_table_name,
				array(
					'status_title' => $str_new_title,
					'status_color' => substr( $str_new_color, 1, 6 ),
					'deactivate_forbidden' => $int_new_deactivate_forbidden,
					'delete_forbidden' => $int_new_delete_forbidden,
					'upgrade_forbidden' => $int_new_upgrade_forbidden
				),
				$arr_row_formats
			);
		}
	}

	return $arr_errors;
}



/**
 * deletes the status and all the labels belonging to it
 *
 * @param int $int_status_id: status_id of the status to be deleted
 */
function plugin_memos_delete_status( $int_status_id ) {
	global $wpdb;

	$arr_where = array( 'status_id' => $int_status_id );
	$arr_where_format = array( '%d' );

	// First delete the labels
	$wpdb->delete(
		$wpdb->prefix . 'plugin_memos_labels',
		$arr_where,
		$arr_where_format
	);

	// delete the status after the table
	$wpdb->delete(
		$wpdb->prefix . 'plugin_memos_stati',
		$arr_where,
		$arr_where_format
	);
}




function plugin_memos_clean_up_database( $str_plugin_name, $int_status_id ) {
	global $wpdb;
	if( strlen($str_plugin_name) > 0 && $int_status_id > 0 ) {
		$query = 'SELECT `label_id` FROM `' . $wpdb->prefix . 'plugin_memos_labels` ' .
			'WHERE `plugin_name` = "' . $str_plugin_name . '" and status_id = ' . $int_status_id .
			' ORDER By `label_id`;';
		$arr_records_per_plugin = $wpdb->get_results($query, ARRAY_A);
		$int_record_count = count($arr_records_per_plugin);
		// If it has more than 1 entry, the older ones are deleted
		if ( $int_record_count > 1 ) {
			for ( $i = 0; $i < ($int_record_count - 1); $i++ ) {
				$int_label_id = $arr_records_per_plugin[$i]['label_id'];
				$wpdb->delete(
					$wpdb->prefix . 'plugin_memos_labels',
					array(
						'label_id' => $int_label_id
					),
					array( '%d' )
				);
			}
		}
	}
}