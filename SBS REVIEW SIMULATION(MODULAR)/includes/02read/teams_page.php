<?php
// Register the shortcode
add_shortcode('teamspage', 'display_team_users');

// Shortcode function to display all users in the current user's team
function display_team_users()
{
    global $wpdb;

    // Get the current user's information
    $current_user = wp_get_current_user();
    $username = $current_user->display_name;

    // Get the current user's team from the teams table
    $teams_table_name = $wpdb->prefix . 'team';
    $team = $wpdb->get_var($wpdb->prepare("SELECT team FROM $teams_table_name WHERE username = %s", $username));

    // Check if the user is part of a team
    if ($team) {
        // Fetch users in the current user's team
        $team_users = $wpdb->get_col($wpdb->prepare("SELECT username FROM $teams_table_name WHERE team = %s", $team));

?>

<style scoped>
 #wrong-team{
    color: #561c32;
        font-weight: bold;
 }   

.container{
    text-shadow: 0px 0px 10px rgba(0, 0, 0, 0.31);
    font-weight: 500;
    color: black;
}

p{
    padding: -5px;
    margin-top: -5px;
}

#wait{
    color: rgba(104, 103, 103, 0.658);
    text-shadow: none;
}
</style>

<?php

    // External links
    $output = '
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    ';

        // Display the users
        $output .= '
        <div class="container">
    <div class="row">
        <div class="col-sm-6 pb-3">
                <h1>Welcome to ' . esc_html($team) . ' </h1>
        </div>
        <div class="col-sm-6 text-end">
        <p><a id="wrong-team" href="/changeteam">Click here to change teams.</a></p>
        </div>
        <div class="col-sm-12">
        <p>
        <span>Please note only registered team members will display.</span><br>
            <span>See the list of people in your team:</span>
        </p>
            <ol>
        '; // Include the team in the h3 tag
        foreach ($team_users as $user) {
            $output .= '<li>' . esc_html($user) . '</li>';
        }
        $output .= '
        </ol>
        <p id="wait">Please wait for the rest of your team members to register in order to give a reviw.</p>
        </div>
    </div>

</div>
        ';
        
    } else {
        $output = '<p>You are not part of any team.</p>';
    }

    return $output;
}
?>
