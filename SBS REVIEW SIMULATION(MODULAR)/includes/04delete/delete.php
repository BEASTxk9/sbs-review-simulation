<?php
function delete_reviews($id)
{
    // ____________________________________________________________________________
    // connect to database.
    global $wpdb;
    // check connection
    if (!$wpdb) {
        $wpdb->show_errors();
    }

    // ____________________________________________________________________________
    // Table name
    $table_name = $wpdb->prefix . 'reviews';
    // SQL query to delete the row with the specified ID
    $wpdb->delete($table_name, array('id' => $id));

}

// Check if the delete button is clicked and perform the deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete(reviews)' && isset($_GET['id'])) {
    delete_reviews($_GET['id']);

    // Redirect to the student details page after deletion
    header('location:' . site_url() . '/reviews/');
    exit;
}

function delete_team($id)
{
    // Connect to the database
    global $wpdb;
    // Check connection
    if (!$wpdb) {
        $wpdb->show_errors();
    }

    // Table name
    $table_name = $wpdb->prefix . 'team';
    // SQL query to delete the row with the specified ID
    $wpdb->delete($table_name, array('id' => $id));
}


    // Check if the delete button is clicked and perform the deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete(team)' && isset($_GET['id'])) {
        delete_team($_GET['id']);

        // Redirect to the teams table page after deletion
        header('location:' . site_url() . '/teams/');
        exit;
    }


?>