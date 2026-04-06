<?php
/*
Plugin Name: Custom Form Plugin
Description: Simple Custom Form with AJAX
Version: 1.0
Author: Ami Dalwadi
*/

if (!defined('ABSPATH')) {
    exit;
}

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


function cfp_enqueue_scripts()
{
    wp_enqueue_script('cfp-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);

    wp_localize_script('cfp-script', 'cfp_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'cfp_enqueue_scripts');



function cfp_handle_form()
{

    if (!isset($_POST['cfp_nonce']) || !wp_verify_nonce($_POST['cfp_nonce'], 'cfp_nonce_action')) {
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

    $wpdb->insert($table_name, array(
        'name' => $name,
        'email' => $email,
        'message' => $message
    ));

    echo "Form submitted successfully!";
    wp_die();
}

add_action('wp_ajax_cfp_submit_form', 'cfp_handle_form');
add_action('wp_ajax_nopriv_cfp_submit_form', 'cfp_handle_form');

function cfp_admin_menu()
{
    add_menu_page(
        'Form Entries',
        'Form Entries',
        'manage_options',
        'cfp-entries',
        'cfp_admin_page'
    );
}
add_action('admin_menu', 'cfp_admin_menu');

function cfp_admin_page()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'cfp_entries';
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    echo "<h2>Form Entries</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr>";

    foreach ($results as $row) {
        echo "<tr>
            <td><?php echo esc_html($row->id); ?></td>
            <td><?php echo esc_html($row->name); ?></td>
            <td><?php echo esc_html($row->email); ?></td>
            <td><?php echo esc_html($row->message); ?></td>
            <td><?php echo esc_html($row->created_at); ?></td>
        </tr>";
    }

    echo "</table>";
}
