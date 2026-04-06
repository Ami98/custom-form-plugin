<?php
/*
Plugin Name: Custom Form Plugin
Description: Simple Custom Form with AJAX
Version: 1.0
Author: Ami Dalwadi
*/

function cfp_create_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'cfp_entries';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100),
        email VARCHAR(100),
        message TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'cfp_create_table');
