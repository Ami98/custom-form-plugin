<?php
/*
Plugin Name: Custom Form Plugin
Description: Simple Custom Form submitted with AJAX using admin-ajax VERSION [custom_form]
Version: 1.0
Author: Ami Dalwadi
*/

// (security)
if (!defined('ABSPATH')) exit;

// Define constant
define('CFP_PATH', plugin_dir_path(__FILE__));
define('CFP_URL', plugin_dir_url(__FILE__));

/* =============================
   INCLUDE FILES
============================= */

require_once CFP_PATH . 'includes/db-table.php';
require_once CFP_PATH . 'includes/shortcode.php';
require_once CFP_PATH . 'includes/ajax-handler.php';

/* =============================
   ACTIVATION HOOK (IMPORTANT)
============================= */

register_activation_hook(__FILE__, 'cfp_create_table');

/* =============================
   ENQUEUE SCRIPTS
============================= */

function cfp_enqueue_scripts()
{
    wp_enqueue_script(
        'cfp-js',
        CFP_URL . 'assets/js/script.js',
        [],
        null,
        true
    );

    wp_localize_script('cfp-js', 'cfp_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'cfp_enqueue_scripts');
