<?php
add_action( 'wp_ajax_plugin_memos_save_labels', 'plugin_memos_save_labels');

/**
 * The ajax-handler where we save the labels in the DB
 *
 */
function plugin_memos_save_labels() {
	global $wpdb;

	// check the nonce sent by the ajax call
	check_ajax_referer( 'plugin_memos_save_labels' );

	$arr_form_data = array();

	// first cut the serialized data into strings like "field_name=value"
	$arr_raw_data = explode( '&', urldecode( $_POST['data'] ) );

	// fill the form data in an array: [field_name] => value
	foreach ( $arr_raw_data as $single_formfield ) {
		$arr_name_value = array();
		$arr_name_value = explode( '=', $single_formfield );

		$arr_form_data[$arr_name_value[0]] = $arr_name_value[1];
	}

	// set all the variables
	$arr_stati = plugin_memos_get_stati();
	$str_labels_table_name = $wpdb->prefix . 'plugin_memos_labels';
	$str_plugin_name = trim( $arr_form_data['plugin_memos_plugin_name'] );
	$arr_row_formats = array( '%d', '%s', '%s' );
	$arr_where_format = array( '%d' );

	foreach ( $arr_stati as $single_status ) {
		$int_label_id = intval( $arr_form_data['label_id_' . $single_status['status_id']] );
		$int_status_id = isset( $arr_form_data['show_in_plugin_' . $single_status['status_id']] ) ?
			intval( $arr_form_data['show_in_plugin_' . $single_status['status_id']] )  : 0;
		$str_label_text = isset( $arr_form_data['label_text_' . $single_status['status_id']] ) ?
			trim( $arr_form_data['label_text_' . $single_status['status_id']] ) : '';

		// check the form input
		$str_error = plugin_memos_check_label_form(
			$single_status['status_title'],
			$int_label_id,
			$int_status_id,
			$str_label_text,
			$arr_stati
		);

		// We only save if there are no errors with the label
		if ( 0 == strlen( $str_error ) ) {
			if ( 0 == $int_label_id && 0 != $int_status_id ) {
				// insert new label
				$wpdb->insert(
					$str_labels_table_name,
					array(
						'status_id' => $int_status_id,
						'plugin_name' => $str_plugin_name,
						'label_text' => $str_label_text
					),
					$arr_row_formats
				);
			} else if ( $int_label_id > 0 && 0 != $int_status_id ) {
				// update existing label
				$wpdb->update(
					$str_labels_table_name,
					array(
						'status_id' => $int_status_id,
						'plugin_name' => $str_plugin_name,
						'label_text' => $str_label_text
					),
					array(
						'label_id' => $int_label_id
					),
					$arr_row_formats,
					$arr_where_format
				);
			} else if ( $int_label_id > 0 && 0 == $int_status_id ) {
				$wpdb->delete(
					$str_labels_table_name,
					array(
						'label_id' => $int_label_id
					),
					$arr_where_format
				);
			}

			// older plug in Version has create multiple entries per plug in and status
			plugin_memos_clean_up_database( $str_plugin_name, $int_status_id );

		} else {
			echo $str_error;
		}
	}

	wp_die();
}