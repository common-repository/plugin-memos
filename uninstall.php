<?php
/**
 * This file handles the uninstall procces of plugin memos
 */

// this constant is definded by wordpress when you click on the "delete" link for a plugin
// we only execute this file if the constant is set by wordpress
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Always delete the lables table first because of the foreign key
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}plugin_memos_labels`;" );
$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}plugin_memos_stati`;" );
