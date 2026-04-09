<?php
/*
Plugin Name: Custom Form Plugin REST API Plugin
Description: Simple Custom Form submitted with AJAX using REST API VERSION [custom-form-restapi]
Version: 1.0
Author: Ami Dalwadi
*/

// (security)
if (!defined('ABSPATH')) exit;

// Define constant
define('CFP_PATH_NEW', plugin_dir_path(__FILE__));
define('CFP_URL_NEW', plugin_dir_url(__FILE__));

/* =============================
   INCLUDE FILES
============================= */

require_once CFP_PATH_NEW . 'includes/db-table.php';
require_once CFP_PATH_NEW . 'includes/shortcode.php';

/* =============================
   ACTIVATION HOOK (IMPORTANT)
============================= */

register_activation_hook(__FILE__, 'cfp_create_table_new');

/* =============================
   ENQUEUE SCRIPTS
============================= */

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('cfp-js-new', CFP_URL_NEW . 'assets/js/script.js', [], null, true);

    wp_localize_script('cfp-js-new', 'cfp_rest', [
        //'ajax_url' => admin_url('admin-ajax.php'),
        'rest_url' => rest_url('cfp/v1/save'),
        // 'nonce'    => wp_create_nonce('cfp_nonce')
        'nonce'    => wp_create_nonce('wp_rest')
    ]);
});


// Register REST Route
add_action('rest_api_init', function () {
    register_rest_route('cfp/v1', '/save', [
        'methods'  => 'POST',
        'callback' => 'cfp_save_rest',
        'permission_callback' => '__return_true'
    ]);
});


// REST Callback
function cfp_save_rest($request)
{
    global $wpdb;

    $params = $request->get_json_params();

    $name = sanitize_text_field($params['name']);

    $wpdb->insert($wpdb->prefix . 'cfp_entries', [
        'name' => $name,
        'email' => sanitize_email($params['email']),
        'message' => sanitize_textarea_field($params['message'])
    ]);

    return [
        'status' => 'success',
        'message' => 'Saved via REST API'
    ];
}
