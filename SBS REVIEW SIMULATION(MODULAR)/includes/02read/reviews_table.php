<?php
// Register the shortcode
add_shortcode('reviews', 'reviews_table');

// Shortcode function
function reviews_table()
{
    // ____________________________________________________________________________
    // Connect to the database.
    global $wpdb;

    // Check connection
    if (!$wpdb) {
        $wpdb->show_errors();
    }

    ?>
    <script>
        function confirmDelete(reviewId) {
            if (confirm("Are you sure you want to delete this review?")) {
                // If the user confirms, redirect to the delete_reviews.php file with the necessary parameters
                window.location.href = "delete.php?action=delete(reviews)&id=" + reviewId;
            } else {
                // If the user cancels, do nothing
            }
        }

        function filterTable() {
            var teamInput, userInput, reviewingInput, teamFilter, userFilter, reviewingFilter, table, tr, tdTeam, tdUser, tdReviewing, i, txtValueTeam, txtValueUser, txtValueReviewing;
            teamInput = document.getElementById("team_filter");
            userInput = document.getElementById("user_filter");
            reviewingInput = document.getElementById("reviewing_filter");
            teamFilter = teamInput.value.toUpperCase();
            userFilter = userInput.value.toUpperCase();
            reviewingFilter = reviewingInput.value.toUpperCase();
            table = document.querySelector(".table-bordered");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                tdTeam = tr[i].getElementsByTagName("td")[2]; // Column index of "team" (zero-based)
                tdUser = tr[i].getElementsByTagName("td")[1]; // Column index of "username" (zero-based)
                tdReviewing = tr[i].getElementsByTagName("td")[3]; // Column index of "reviewing" (zero-based)
                if (tdTeam && tdUser && tdReviewing) {
                    txtValueTeam = tdTeam.textContent || tdTeam.innerText;
                    txtValueUser = tdUser.textContent || tdUser.innerText;
                    txtValueReviewing = tdReviewing.textContent || tdReviewing.innerText;
                    if ((txtValueTeam.toUpperCase().indexOf(teamFilter) > -1) && (txtValueUser.toUpperCase().indexOf(userFilter) > -1) && (txtValueReviewing.toUpperCase().indexOf(reviewingFilter) > -1)) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
    <?php

    // ____________________________________________________________________________
    // Set table name that is being called
    $table_name = $wpdb->prefix . 'reviews';

    // SQL query to retrieve data from the table
    $data = $wpdb->get_results("SELECT * FROM $table_name");

    // SQL query to retrieve unique team names from the table
    $unique_teams = $wpdb->get_col("SELECT DISTINCT team FROM $table_name");

    // SQL query to retrieve unique user names from the table
    $unique_users = $wpdb->get_col("SELECT DISTINCT username FROM $table_name");

    // SQL query to retrieve unique reviewing names from the table
    $unique_reviewing = $wpdb->get_col("SELECT DISTINCT reviewing FROM $table_name");

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
        .review-table-container{
            overflow: scroll;
            max-height: 50vh;
            box-shadow: 0px 5px 26px rgba(0, 0, 0, 0.1);
        }

        .total-score-btn{
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.31);
            width: 100%;
        }

        tr:nth-child(even) {
            background-color: rgba(255, 183, 2, 0.212);
        }

        .offcanvas {
    transition: all 0.3s ease-in-out;
    height: 100%;
    overflow: scroll;
    min-width: 100%;
    border: 1px solid rgba(128, 128, 128, 0.486);
    box-shadow: 0px 0px 10px rgba(128, 128, 128, 0.336);
  }

  @media only screen and (max-width: 1000px){
    .offcanvas{
    padding: 10px;
    }
  }

  #delete-btn{
    background-color: rgba(255, 0, 0, 0.37);
    color: white;
    text-decoration: none;
    padding: 5px;
}
    </style>
    <?php

    if (count($data) === 0) {
        $output .= '<p>There is no data to display.</p>';
    } else {

        $output .= '
        <div class="container">
            <div class="row">

                <div class="col-sm-12 col-md-4">
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

                    <div class="col-sm-12 col-md-4">
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


                    <div class="col-sm-12 col-md-4">
                    <!-- Select filter for reviewing names -->
                    <label for="reviewing_filter">Filter by Reviewing:</label>
                    <select id="reviewing_filter" name="reviewing_filter" onchange="filterTable()">
                        <option value="">All Reviewing</option>';

        // Populate dropdown options with unique reviewing names
        foreach ($unique_reviewing as $reviewing) {
            $output .= '<option value="' . $reviewing . '">' . $reviewing . '</option>';
        }

        $output .= '
                    </select>
                </div>

            </div>
        </div>
        ';

$output .= '
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <button class="btn total-score-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                Total Scores
                </button>
        </div>
    </div>
</div>
';

        $output .= '
        <div class="review-table-container">
        <table class="table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>username</th>
                    <th>team</th>
                    <th>Reviewing</th>
                    <th>Question 1</th>
                    <th>Question 2</th>
                    <th>Question 3</th>
                    <th>Question 4</th>
                    <th>Question 5</th>
                    <th>Total Score(50)</th>
                    <th>Percentage</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

$row_id1 = 1;

        // for each data item in the table
        foreach ($data as $i) {
            $output .= '<tr>';
            $output .= '<td>' . $row_id1 . '</td>';
            $output .= '<td>' . $i->username . '</td>';
            $output .= '<td>' . $i->team . '</td>';
            $output .= '<td>' . $i->reviewing . '</td>';
            $output .= '<td>' . $i->q_1 . '</td>';
            $output .= '<td>' . $i->q_2 . '</td>';
            $output .= '<td>' . $i->q_3 . '</td>';
            $output .= '<td>' . $i->q_4 . '</td>';
            $output .= '<td>' . $i->q_5 . '</td>';
            // Calculate total score out of 50
            $totalScore = $i->q_1 + $i->q_2 + $i->q_3 + $i->q_4 + $i->q_5;
            $output .= '<td>' . $totalScore . '</td>';

            // Calculate percentage
            $percentage = ($totalScore / 50) * 100;
            $output .= '<td>' . $percentage . '%</td>';

            $output .= '<td>   <!-- DELETE BUTTON -->
            <a id="delete-btn" href="#" onclick="confirmDelete(' . $i->id . ')">Delete</a>
            </td>';
            $row_id1++;
            $output .= '</tr>';
        }

        $output .= '
            </tbody>
        </table>
        </div>
        ';


// New array to store user totals and review counts
$userTotals = array();
$userReviewCounts = array();

// for each data item in the table
foreach ($data as $i) {
    // Calculate total score out of 50
    $totalScore = $i->q_1 + $i->q_2 + $i->q_3 + $i->q_4 + $i->q_5;

    // Store the total score for the reviewing person in the array
    $reviewing = $i->reviewing;
    if (!isset($userTotals[$reviewing])) {
        $userTotals[$reviewing] = 0;
        $userReviewCounts[$reviewing] = 0;
    }
    $userTotals[$reviewing] += $totalScore;
    $userReviewCounts[$reviewing]++;
}

        $output .= '
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasExampleLabel">Total Scores</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
        ';



$output .= '
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Total Score</th>
                        <th>Average Score</th>
                    </tr>
                </thead>
                <tbody>';

$row_id2 = 1;

// Display the user totals in the table
foreach ($userTotals as $username => $totalScore) {
    $reviewCount = $userReviewCounts[$username];
    $averageScore = $reviewCount > 0 ? round($totalScore / ($reviewCount * 50) * 100, 2) : 0;
    $output .= '<tr>';
    $output .= '<td>' . $row_id2 . '</td>';
    $output .= '<td>' . $username . '</td>';
    $output .= '<td>' . $totalScore . '/' . ($reviewCount * 50) . '</td>';
    $output .= '<td>' . $averageScore . '%</td>';
    $output .= '</tr>';

    $row_id2++;
}

$output .= '
                </tbody>
            </table>
';

        $output .= '
</div>
</div>
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