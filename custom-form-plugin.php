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


function cfp_form_shortcode()
{
    ob_start();
?>

    <form id="cfp-form">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <textarea name="message" placeholder="Message"></textarea>

        <input type="hidden" name="action" value="cfp_submit_form">
        <?php wp_nonce_field('cfp_nonce_action', 'cfp_nonce'); ?>

        <button type="submit">Submit</button>
    </form>

    <div id="cfp-response"></div>

<?php
    return ob_get_clean();
}

add_shortcode('custom_form', 'cfp_form_shortcode');
