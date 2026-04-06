<?php

function cfp_handle_form()
{

    if (
        !isset($_POST['cfp_nonce']) ||
        !wp_verify_nonce($_POST['cfp_nonce'], 'cfp_nonce_action')
    ) {
        wp_die('Security check failed');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'cfp_entries';

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);

    if (empty($name) || empty($email)) {
        echo "Required fields missing!";
        wp_die();
    }

    $wpdb->insert(
        $table_name,
        array(
            'name' => $name,
            'email' => $email,
            'message' => $message
        ),
        array('%s', '%s', '%s')
    );

    echo "Form submitted successfully!";
    wp_die();
}

// AJAX hooks
add_action('wp_ajax_cfp_submit_form', 'cfp_handle_form');
add_action('wp_ajax_nopriv_cfp_submit_form', 'cfp_handle_form');
