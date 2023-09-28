<?php
// Register the shortcode
add_shortcode('teams', 'teams_table');

// Shortcode function
function teams_table()
{
    // ____________________________________________________________________________
    // Connect to the database.
    global $wpdb;

    // Check connection
    if (!$wpdb) {
        $wpdb->show_errors();
    }

    // ____________________________________________________________________________
    // Set table name that is being called
    $table_name = $wpdb->prefix . 'team';

    // SQL query to retrieve unique team names from the table
    $unique_teams = $wpdb->get_col("SELECT DISTINCT team FROM $table_name");

    // SQL query to retrieve unique user names from the table
    $unique_users = $wpdb->get_col("SELECT DISTINCT username FROM $table_name");

    // ____________________________________________________________________________
    // HTML DISPLAY

    // External links
    $output = '
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    ';
    ?>
    <!-- custom css -->
    <style scoped>
        .teams-table-container{
            overflow: scroll;
            max-height: 50vh;
            box-shadow: 0px 5px 26px rgba(0, 0, 0, 0.1);
        }

        tr:nth-child(even) {
            background-color: rgba(255, 183, 2, 0.212);
        }

        .teams-table-container::-webkit-scrollbar {
            display: contents;
            width: 10px;
         } /* Entrie scrollbar*/

         #delete-btn{
    background-color: rgba(255, 0, 0, 0.37);
    color: white;
    text-decoration: none;
    padding: 5px;
}
    </style>
    <?php

    if (empty($unique_teams)) {
        $output .= '<p>There is no data to display.</p>';
    } else {
        $output .= '
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <!-- Select filter for teams -->
                    <label for="team_filter">Filter by Team:</label>
                    <select id="team_filter" name="team_filter" onchange="filterTable()">
                        <option value="">All Teams</option>';
        // Populate dropdown options with unique team names
        foreach ($unique_teams as $team) {
            $output .= '<option value="' . $team . '">' . $team . '</option>';
        }

        $output .= '
                    </select>
                </div>


<div class="col-sm-12 col-md-6">
<!-- Select filter for user names -->
<label for="user_filter">Filter by User:</label>
<select id="user_filter" name="user_filter" onchange="filterTable()">
    <option value="">All Users</option>';

// Populate dropdown options with unique user names
foreach ($unique_users as $user) {
$output .= '<option value="' . $user . '">' . $user . '</option>';
}

$output .= '
</select>
</div>
            </div>
        </div>
        ';

        $output .= '
        <div class="teams-table-container">
        <table class="table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>username</th>
                    <th>team</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

        $row_id = 1;

        // SQL query to retrieve all data from the table
        $data = $wpdb->get_results("SELECT * FROM $table_name");
        // for each data item in the table
        foreach ($data as $i) {
            $output .= '<tr>';
            $output .= '<td>' .  $row_id . '</td>';
            $output .= '<td>' . $i->username . '</td>';
            $output .= '<td>' . $i->team . '</td>';

            $output .= '<td>   <!-- DELETE BUTTON -->
            <a id="delete-btn" href="#" onclick="confirmDelete(' . $i->id . ')">Delete</a>
            </td>';

            $row_id++;

            $output .= '</tr>';
        }

        $output .= '
            </tbody>
        </table>
        </div>
        ';

        $output .= '
        <script>
        function filterTable() {
            var teamInput, userInput, teamFilter, userFilter, table, tr, tdTeam, tdUser, i, txtValueTeam, txtValueUser;
            teamInput = document.getElementById("team_filter");
            userInput = document.getElementById("user_filter");
            teamFilter = teamInput.value.toUpperCase();
            userFilter = userInput.value.toUpperCase();
            table = document.querySelector(".table-bordered");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                tdTeam = tr[i].getElementsByTagName("td")[2]; // Column index of "team" (zero-based)
                tdUser = tr[i].getElementsByTagName("td")[1]; // Column index of "username" (zero-based)
                if (tdTeam && tdUser) {
                    txtValueTeam = tdTeam.textContent || tdTeam.innerText;
                    txtValueUser = tdUser.textContent || tdUser.innerText;
                    if ((txtValueTeam.toUpperCase().indexOf(teamFilter) > -1) && (txtValueUser.toUpperCase().indexOf(userFilter) > -1)) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        function confirmDelete(teamId) {
            if (confirm("Are you sure you want to delete this user?")) {
                // If the user confirms, redirect to the delete_reviews.php file with the necessary parameters
                window.location.href = "delete.php?action=delete(team)&id=" + teamId;
            } else {
                // If the user cancels, do nothing
            }
        }
        </script>
        ';

                $output .= '
        <!-- bootstrap js -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        ';
    }

    // ____________________________________________________________________________
    // Return the table html
    return $output;
}
?>
