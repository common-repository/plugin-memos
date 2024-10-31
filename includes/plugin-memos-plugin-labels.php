<?php
/**
 * Print the labels and the form to edit the labels for a plugin
 */

add_action( 'after_plugin_row', 'plugin_memos_print_label', 1, 1 );

/**
 * Adds an extra column for the Edit button
 *
 * @param string $str_plugin_name:	name/file-name of a plugin
 */
function plugin_memos_print_label( $str_plugin_name ) {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$arr_labels = plugin_memos_get_lables_by_plugin_name( $str_plugin_name );

	if ( ! empty( $arr_labels ) ) {
		$str_labels_html = plugin_memos_get_labels_html( $arr_labels, $str_plugin_name );

		echo $str_labels_html;

	}
}


// dont show the update-banner for the plugins where upgrade_forbidden = 1
add_filter( 'site_transient_update_plugins', 'plugin_memos_remove_update_banner', 10, 1 );

/**
 * Deletes the update-infos of the plugins where "upgrade_forbidden" = 1
 *
 * @param object $obj_update_info:	Infos to all the plugin updates WP finds
 */
function plugin_memos_remove_update_banner( $obj_update_info ) {
	$arr_plugin_names = plugin_memos_get_plugin_names_by_option( 'upgrade_forbidden' );

	foreach ( $arr_plugin_names as $plugin_name ) {
		unset( $obj_update_info->response[ $plugin_name ] );
	}

	return $obj_update_info;
}

// dont show the delete-link for the plugins where delete_forbidden = 1
add_filter( 'plugin_action_links', 'plugin_memos_remove_delete_link', 10, 2 );

/**
 * Deletes the delete-links from the plugins
 *
 * @param array $arr_actions:	array with the action-links
 * @param string $str_plugin_file:	plugin file-name
 */
function plugin_memos_remove_delete_link( $arr_actions, $str_plugin_file ) {
	$arr_plugin_names = plugin_memos_get_plugin_names_by_option( 'delete_forbidden' );

	if ( in_array( $str_plugin_file, $arr_plugin_names ) ) {
		unset( $arr_actions['delete'] );
	}

	return $arr_actions;
}

// dont show the deactivate-link for the plugins where deactivate_forbidden = 1
add_filter( 'plugin_action_links', 'plugin_memos_remove_deactivate_link', 10, 2 );

/**
 * Deletes the deactivate-links from the plugins
 *
 * @param array $arr_actions:	array with the action-links
 * @param string $str_plugin_file:	plugin file-name
 */
function plugin_memos_remove_deactivate_link( $arr_actions, $str_plugin_file ) {
	$arr_plugin_names = plugin_memos_get_plugin_names_by_option( 'deactivate_forbidden' );

	if ( in_array( $str_plugin_file, $arr_plugin_names ) ) {
		unset( $arr_actions['deactivate'] );
	}

	return $arr_actions;
}

add_filter( 'manage_plugins_columns', 'plugin_memos_label_edit_button_column' );

/**
 * Adds an extra column for the Edit button
 *
 * @param array $columns:	the columns of the plugin list
 */
function plugin_memos_label_edit_button_column( $arr_columns ) {

	$arr_columns['Plugin Memos'] = 'Plugin Memos';

	return $arr_columns;
}


/**
 * prints the edit button and the modal box to change the labels for each plugin
 *
 * @param string $str_column:	the column name
 * @param string $plugin_file_name:	name/file_name of the plugin
 */
function plugin_memos_label_edit_button( $str_column, $plugin_file_name ) {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	// Splitting the plugin_file_name because we can only use the first part (see further down)
	$arr_plugin_file = explode( '/', $plugin_file_name );
	$str_file_name_short = str_replace( '.php', '', $arr_plugin_file[0] ) ;

	if ( 'Plugin Memos' == $str_column ) {
		$str_edit_form = plugin_memos_edit_labels_form( $plugin_file_name, $str_file_name_short );
?>
		<input type="button" id="plugin_memos_edit" data-plugin-name="<?php echo $str_file_name_short?>" 
		class="plugin-memos-edit-button" name="plugin_memos_edit" value="<?php echo __( 'Edit Labels', 'plugin-memos' );?>"/>
		
		<!-- Example plugin-name: verowa-subscriptions/verowa-subscriptions.php.
		The '.' in the id doesnt work in jQuery. So we only take the "verowa-subscriptions" part as unique identifier-->
		<div class="plugin-memos-edit-labels-box" id="plugin-memos-edit-labels-box-<?php echo $str_file_name_short?>">
			<div class="plugin-memos-labels-form-div" id="plugin-memos-labels-form-div-<?php echo $str_file_name_short?>">
				<input type="hidden" name="plugin_memos_plugin_name" value="<?php echo $plugin_file_name;?>">
				<?php echo $str_edit_form;?>
			</div>
		</div>
<?php
	}
}

add_action( 'manage_plugins_custom_column' , 'plugin_memos_label_edit_button', 1, 2 );