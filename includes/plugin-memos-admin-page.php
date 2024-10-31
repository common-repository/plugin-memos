<?php
add_action( 'admin_menu', 'plugin_memos_admin_page', 30 );

// Add the admin page to the settings-menu
function plugin_memos_admin_page() {
	add_options_page(
		'Plugin Memos ' . __( 'Status Settings', 'plugin-memos' ),
		'Plugin Memos ' . __( 'Status Settings', 'plugin-memos' ),
		'manage_options',
		'plugin_memos_settings',
		'render_plugin_memos_admin_page'
	);
}


/**
 * Display the admin page were you can create or edit your stati
 *
 */
function render_plugin_memos_admin_page() {
	$arr_errors = array();
	$str_print_error = '';

	if ( isset( $_POST['plugin_memos_status']['plugin_memos_status_submit'] )
		|| isset( $_POST['plugin_memos_status']['plugin_memos_delete'] )
		|| isset( $_POST['plugin_memos_status']['plugin_memos_status_submit_new'] ) ) {

		$arr_errors = plugin_memos_save_stati_in_db( $_POST['plugin_memos_status'] );

	}

	$arr_stati = plugin_memos_get_stati();

	// if there are no errors we flush the plugin_memos_status info.
	// This way after a new status is saved, the fields will be empty again
	if ( 0 == count( $arr_errors ) ) {
		unset( $_POST['plugin_memos_status'] );
	} else {
		foreach ( $arr_errors as $error ) {
			$str_print_error .= '<p style="color: red;">' . $error . '</p>';
		}
	}

	$str_stati_form = plugin_memos_stati_form( $arr_stati, $_POST['plugin_memos_status'] );

?>
	<div>
		<h1><?php echo 'Plugin Memos ' . __( 'Status Settings', 'plugin-memos' );?></h1>
		<div>
<?php
		echo $str_print_error;
?>
		</div>
		<?php echo $str_stati_form;?>
	</div>
<?php
}