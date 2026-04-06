<?php

/* =========================
   ADMIN MENU
========================= */
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


/* =========================
   HANDLE ACTIONS (DELETE + UPDATE)
========================= */
function cfp_handle_admin_actions()
{
    if (!current_user_can('manage_options')) return;

    global $wpdb;
    $table_name = $wpdb->prefix . 'cfp_entries';

    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    $id     = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* ===== DELETE ===== */
    if ($action === 'delete' && $id && isset($_GET['_wpnonce'])) {

        if (!wp_verify_nonce($_GET['_wpnonce'], 'cfp_delete_' . $id)) {
            wp_die('Security check failed');
        }

        $wpdb->delete($table_name, ['id' => $id], ['%d']);

        wp_redirect(admin_url('admin.php?page=cfp-entries&msg=deleted'));
        exit;
    }

    /* ===== UPDATE ===== */
    if (isset($_POST['update_entry'], $_POST['_wpnonce'])) {

        $id = intval($_POST['id']);

        if (!wp_verify_nonce($_POST['_wpnonce'], 'cfp_update_' . $id)) {
            wp_die('Security check failed');
        }

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);

        $wpdb->update(
            $table_name,
            [
                'name' => $name,
                'email' => $email,
                'message' => $message
            ],
            ['id' => $id],
            ['%s', '%s', '%s'],
            ['%d']
        );

        wp_redirect(admin_url('admin.php?page=cfp-entries&msg=updated'));
        exit;
    }
}
add_action('admin_init', 'cfp_handle_admin_actions');


/* =========================
   ADMIN PAGE UI
========================= */
function cfp_admin_page()
{
    if (!current_user_can('manage_options')) return;

    global $wpdb;
    $table_name = $wpdb->prefix . 'cfp_entries';

    echo "<div class='wrap'><h2>Form Entries</h2>";

    /* ===== MESSAGE ===== */
    if (isset($_GET['msg'])) {
        if ($_GET['msg'] === 'deleted') {
            echo "<div style='color:red;'>Entry deleted successfully!</div>";
        }
        if ($_GET['msg'] === 'updated') {
            echo "<div style='color:green;'>Entry updated successfully!</div>";
        }
    }

    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    $id     = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* =========================
       EDIT FORM
    ========================== */
    if ($action === 'edit' && $id) {

        $row = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");

        if (!$row) {
            echo "<div>Record not found</div></div>";
            return;
        }
?>

        <h3>Edit Entry</h3>
        <form method="post" style="max-width:400px;">
            <input type="hidden" name="id" value="<?php echo esc_attr($row->id); ?>">

            <?php wp_nonce_field('cfp_update_' . $row->id); ?>

            <p>
                <label>Name</label><br>
                <input type="text" name="name" value="<?php echo esc_attr($row->name); ?>" required style="width:100%;">
            </p>

            <p>
                <label>Email</label><br>
                <input type="email" name="email" value="<?php echo esc_attr($row->email); ?>" required style="width:100%;">
            </p>

            <p>
                <label>Message</label><br>
                <textarea name="message" style="width:100%;"><?php echo esc_textarea($row->message); ?></textarea>
            </p>

            <p>
                <input type="submit" name="update_entry" class="button button-primary" value="Update">
            </p>
        </form>

        </div>

<?php
        return;
    }

    /* =========================
       SEARCH FORM
    ========================== */

    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    echo "<form method='get' style='margin-bottom:15px;'>";
    echo "<input type='hidden' name='page' value='cfp-entries'>";
    echo "<input type='text' name='s' value='" . esc_attr($search) . "' placeholder='Search...'>";
    echo "<input type='submit' value='Search'>";
    echo "</form>";

    /* =========================
       PAGINATION + SEARCH QUERY
    ========================== */

    $per_page = 3;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;

    $where = "WHERE 1=1";

    if (!empty($search)) {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $where .= $wpdb->prepare(
            " AND (name LIKE %s OR email LIKE %s OR message LIKE %s)",
            $like,
            $like,
            $like
        );
    }

    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where");
    $total_pages = ceil($total_items / $per_page);

    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name $where ORDER BY id DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        )
    );

    /* =========================
       TABLE
    ========================== */

    echo "<table border='1' cellpadding='10' style='width:100%; border-collapse:collapse;'>";
    echo "<tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Date</th>
            <th>Action</th>
          </tr>";

    foreach ($results as $row) {

        $edit_url = admin_url('admin.php?page=cfp-entries&action=edit&id=' . $row->id);

        $delete_url = wp_nonce_url(
            admin_url('admin.php?page=cfp-entries&action=delete&id=' . $row->id),
            'cfp_delete_' . $row->id
        );

        echo "<tr>
            <td>{$row->id}</td>
            <td>" . esc_html($row->name) . "</td>
            <td>" . esc_html($row->email) . "</td>
            <td>" . esc_html($row->message) . "</td>
            <td>" . esc_html($row->created_at) . "</td>
            <td>
                <a href='" . esc_url($edit_url) . "'>Edit</a> | 
                <a href='" . esc_url($delete_url) . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>
            </td>
        </tr>";
    }

    echo "</table>";

    /* =========================
       PAGINATION LINKS
    ========================== */

    echo "<div style='margin-top:20px;'>";

    for ($i = 1; $i <= $total_pages; $i++) {

        $page_url = admin_url('admin.php?page=cfp-entries&paged=' . $i . '&s=' . urlencode($search));

        if ($i == $current_page) {
            echo "<strong style='margin:5px;'>$i</strong>";
        } else {
            echo "<a href='" . esc_url($page_url) . "' style='margin:5px;'>$i</a>";
        }
    }

    echo "</div>";

    echo "</div>";
}
