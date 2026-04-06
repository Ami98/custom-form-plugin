<?php

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
            <td>{$row->id}</td>
            <td>" . esc_html($row->name) . "</td>
            <td>" . esc_html($row->email) . "</td>
            <td>" . esc_html($row->message) . "</td>
            <td>{$row->created_at}</td>
        </tr>";
    }

    echo "</table>";
}
