<?php
/**
 * Plugin Name: Plugin Memos
 * Description: Plugin Memos will add a button to each plugin in the plugin-list where you can add labels with comments to the plugin.
 * Author: Nicolas Rickenbacher, Picture-Planet GmbH
 * Author URI: https://www.picture-planet.ch
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: plugin-memos
 * Domain Path: /languages
 *
 */
include( 'includes/functions/plugin-memos-functions.php' );
include( 'includes/plugin-memos-plugin-labels.php' );
include( 'includes/plugin-memos-save-labels.php' );
include( 'includes/plugin-memos-admin-page.php' );


register_activation_hook( __FILE__, 'plugin_memos_activate' );

add_action( 'init', 'plugin_memos_init' );
add_action( 'admin_enqueue_scripts', 'add_plugin_memos_scripts' );


/**
 * Load all the scripts and styles for the plugin
 * 
 */
function add_plugin_memos_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'plugin-memos', plugins_url( 'js/plugin-memos.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
	wp_enqueue_style( 'plugin-memos', plugins_url( 'css/plugin-memos-style.css', __FILE__ ) );
	
	// Add wp color picker styles     
	wp_enqueue_style( 'wp-color-picker' );      
	// enqueue the js-file were we initiate the color picker 
	wp_enqueue_script(
		'plugin-memos-color-picker',
		plugins_url( 'js/plugin-memos-settings.js', __FILE__ ),
		array( 'wp-color-picker' ),
		false,
		true
	);

	// We set the url and the nonce for the ajax call in the global object plugin_memos_ajax_obj
	wp_localize_script(
		'plugin-memos',
		'plugin_memos_ajax_obj',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'plugin_memos_save_labels' ),
		)
	);

}


/**
 * plugin init
 * 
 */
function plugin_memos_init() {

	load_plugin_textdomain( 'plugin-memos' );
}


/**
 * Creates the tables and fills in content when the plugin is activated
 * 
 */
function plugin_memos_activate() {
	global $wpdb;

	$str_charset = $wpdb->get_charset_collate();
	$str_stati_table_name =  $wpdb->prefix . 'plugin_memos_stati';

	$create_tables_query = 'CREATE TABLE `' . $str_stati_table_name . '` (' . 
		' `status_id` INT NOT NULL AUTO_INCREMENT,' . 
		' `status_title` VARCHAR(255) NOT NULL,' . 
		' `status_color` VARCHAR(255) NOT NULL,' . 
		' `deactivate_forbidden` TINYINT NOT NULL DEFAULT 0,' . 
		' `delete_forbidden` TINYINT NOT NULL DEFAULT 0,' . 
		' `upgrade_forbidden` TINYINT NOT NULL DEFAULT 0,' . 
		' PRIMARY KEY (`status_id`)) ' . $str_charset . ';';

	$create_tables_query .= 'CREATE TABLE `' . $wpdb->prefix . 'plugin_memos_labels` (' . 
		' `label_id` INT NOT NULL AUTO_INCREMENT,' . 
		' `status_id` INT NOT NULL,' . 
		' `plugin_name` VARCHAR(255) NOT NULL,' . 
		' `label_text` TEXT(500) NULL,' . 
		' PRIMARY KEY (`label_id`),' . 
		' INDEX `status_id_idx` (`status_id` ASC),' . 
		' CONSTRAINT `status_id`' . 
		' FOREIGN KEY (`status_id`)' . 
		' REFERENCES `' . $str_stati_table_name . '` (`status_id`)' . 
		' ON DELETE NO ACTION' . 
		' ON UPDATE NO ACTION)  ' . $str_charset . ';';

	// In order for dbDelta() to work we need to require upgrad.php from wordpress itself
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	// dbDelta() checks if the tables already exist and only adds them if not
	dbDelta( $create_tables_query );

	$status_count = $wpdb->get_var( 'SELECT COUNT(*) FROM `' . $str_stati_table_name . '`;' );

	// We only add the default stati if there are no others in the table already. 
	// so we dont get the same stati multiple times if the user deactivates and activates the plugin
	if ( intval( $status_count ) == 0 ) {

		$arr_row_formats = array( '%s', '%s', '%d', '%d','%d' );
	
		$wpdb->insert(
			$str_stati_table_name,
			array(
				'status_title' => __( 'Required for other plugins', 'plugin-memos' ),
				'status_color' => 'ff0000',
				'deactivate_forbidden' => 1,
				'delete_forbidden' => 1,
				'upgrade_forbidden' => 0
			),
			$arr_row_formats
		);
	
		$wpdb->insert(
			$str_stati_table_name,
			array(
				'status_title' => __( 'Required for theme', 'plugin-memos' ),
				'status_color' => '660066',
				'deactivate_forbidden' => 1,
				'delete_forbidden' => 1,
				'upgrade_forbidden' => 0
			),
			$arr_row_formats
		);
	
		$wpdb->insert(
			$str_stati_table_name,
			array(
				'status_title' => __( 'Code manually altered', 'plugin-memos' ),
				'status_color' => 'ff6600',
				'deactivate_forbidden' => 0,
				'delete_forbidden' => 1,
				'upgrade_forbidden' => 1
			),
			$arr_row_formats
		);
	
		$wpdb->insert(
			$str_stati_table_name,
			array(
				'status_title' => __( 'Database maintenance', 'plugin-memos' ),
				'status_color' => '808080',
				'deactivate_forbidden' => 1,
				'delete_forbidden' => 1,
				'upgrade_forbidden' => 0
			),
			$arr_row_formats
		);
	}
}