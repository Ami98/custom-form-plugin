<?php
/*
Plugin Name: Custom Form Plugin
Description: Simple Custom Form with AJAX
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
require_once CFP_PATH . 'includes/admin-page.php';

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
        'cfp-script',
        CFP_URL . 'assets/js/script.js',
        array('jquery'),
        null,
        true
    );

    wp_localize_script('cfp-script', 'cfp_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'cfp_enqueue_scripts');
