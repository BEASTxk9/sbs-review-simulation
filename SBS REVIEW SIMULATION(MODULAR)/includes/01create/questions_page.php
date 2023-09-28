<?php
// register shortcode
add_shortcode('questions', 'questions_page');
// create function
function questions_page()
{
    // ____________________________________________________________________________    
    // connect to database.
    global $wpdb;
    // check connection
    if (is_null($wpdb)) {
        $wpdb->show_errors();
    }

    // ____________________________________________________________________________    
    // Set table names that are being called
    $table_name = $wpdb->prefix . 'reviews';
    $teams_table_name = $wpdb->prefix . 'team';

    // Get the current user's information
    $current_user = wp_get_current_user();
    $username = $current_user->display_name;

    // Get the current user's team from the teams table
    $team = $wpdb->get_var($wpdb->prepare("SELECT team FROM $teams_table_name WHERE username = %s", $username));

    if (isset($_POST['submit'])) {

        if ($team) {
            // id is automatically set
            $ratings = array();
            for ($i = 1; $i <= 5; $i++) { // Updated for the five questions
                // Validate each question's rating (you can add more validation as needed)
                $rating = isset($_POST['q_' . $i]) ? intval($_POST['q_' . $i]) : 0;
                $ratings["q_$i"] = $rating;
            }

            // Extract ratings from the $ratings array
            $q_1 = $ratings['q_1'];
            $q_2 = $ratings['q_2'];
            $q_3 = $ratings['q_3'];
            $q_4 = $ratings['q_4'];
            $q_5 = $ratings['q_5'];

            // Get the selected user from the dropdown
            $selected_user = $_POST['selected_user'];

            // mysql add query (add a comma after "reviewing" in the column list)
            $sql = "INSERT INTO $table_name (username, team, reviewing, q_1, q_2, q_3, q_4, q_5) 
                    VALUES ('$username', '$team', '$selected_user', '$q_1', '$q_2', '$q_3', '$q_4', '$q_5')";

            $result = $wpdb->query($sql);

            // if successful redirect
            if ($result) {
                $redirect_url = site_url('/thanks/');
                ?>
                <script>
                    window.location.href = "<?php echo $redirect_url; ?>";
                </script>
                <?php
                exit;
            }
        } else {
            wp_die($wpdb->last_error);
        }
    }

    // ____________________________________________________________________________
    // Fetch users in the current user's team who are not already in the reviews table
    $team_users = $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT t.username
         FROM $teams_table_name AS t
         LEFT JOIN $table_name AS r ON t.username = r.reviewing
         WHERE t.team = %s AND (r.reviewing IS NULL OR r.reviewing <> %s) AND t.username <> %s", // Exclude the current user
        $team, $username, $username // Adding $username in the prepare method
    ));

    // Fetch users who are already in the reviews table as reviewers
    $reviewing_users = $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT reviewing
         FROM $table_name
         WHERE username = %s",
        $username
    ));

    // Remove the users who are already in the reviews table as reviewers from the team_users array
    $team_users = array_diff($team_users, $reviewing_users);

    // ____________________________________________________________________________
    // HTML DISPLAY

    // external links
    $output = '
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    ';
    ?>
    <!-- custom css -->
    <style scoped>

       *{
        font-family: Roboto mono;
       }

       label, input, select, ::placeholder{
        font-weight: 500;
       }
       select{
        border: 1px solid black;
       }
       select:focus{
        border: 1px solid goldenrod;
       }


        /* Add custom CSS styles for star ratings */
        input[type="radio"].star {
            display: none; /* Hide the default radio button */
        }

        label.star {
            font-size: 2rem;
            color: black;
            margin-left: 5px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        label.star:before {
            content: '\2606'; /* Unicode character for empty star */
        }

        input[type="radio"].star:checked ~ label.star:before {
            content: '\2605'; /* Unicode character for filled star */
            color: #FFD700; /* Yellow color for filled star */
        }

        /* Reverse the order of the stars */
        .star-rating-container {
            display: flex;
            flex-direction: row-reverse;
            justify-content: start;
            align-items: start;
        }

        #submit_answers_btn{
            background-color: transparent;
            width: 100%;
            color: black;
        }

        #join_team_link{
            color: #561c32;
        }

        #questions-team-link{
            display: flex;
            justify-content: center;
            align-items: center;
            align-content: center;
            text-align: center;
        }

        @media only screen and (max-width: 425px){
            label.star {
            font-size: 1rem;
        }
}
        
    </style>

    <?php


if (!$team) {
    return '
    <div id="questions-team-link" class="container">
    <div class="row justify-content-center text-center">
    <div class="col-sm-12">
    <p>Please join a team in order to give a rating.</p>
    <p><a id="join_team_link" href="/join">Click here to join a team.</a></p>
    </div>
    </div>
    </div>
    ';
}else{

    $output .= '

    <div id="question-container" class="container">
        <div class="row">
            <div class="col-sm-12">

                <form method="POST" action="">
    ';

    // Select field for users in the current user's team
    $output .= '
    <label for="selected_user">Select a team mate to review:</label><br>
    <select name="selected_user" id="selected_user" required>
    <option value="">Select here</option>
    ';
    foreach ($team_users as $user) {
        $output .= '<option value="' . $user . '">' . $user . '</option>';
    }
    $output .= '</select><br><br>';


    // q_1
    $output .= '
    <label for="q_1">1. Contributed to fair share of work. (10)</label><br>
    <div class="star-rating-container" required>
    ';
    for ($rating = 10; $rating >= 1; $rating--) {
        $output .= '
        <input type="radio" name="q_1" value="' . $rating . '" class="star" id="q_1' . $rating . '">
        <label class="star" for="q_1' . $rating . '"></label>
        ';
    }
    $output .= '
    </div>
    ';
    // q_2
    $output .= '
    <label for="q_2">2. Contributed to successful outcomes of simulation. (10)</label><br>
    <div class="star-rating-container" required>
    ';
    for ($rating = 10; $rating >= 1; $rating--) {
        $output .= '
        <input type="radio" name="q_2" value="' . $rating . '" class="star" id="q_2' . $rating . '">
        <label class="star" for="q_2' . $rating . '"></label>
        ';
    }
    $output .= '
    </div>
    ';
    // q_3
    $output .= '
    <label for="q_3">3. Delivered high quality work. (10)</label><br>
    <div class="star-rating-container" required>
    ';
    for ($rating = 10; $rating >= 1; $rating--) {
        $output .= '
        <input type="radio" name="q_3" value="' . $rating . '" class="star" id="q_3' . $rating . '">
        <label class="star" for="q_3' . $rating . '"></label>
        ';
    }
    $output .= '
    </div>
    ';
    // q_4
    $output .= '
    <label for="q_4">4. Attended team meetings/sessions/work. (10)</label><br>
    <div class="star-rating-container" required>
    ';
    for ($rating = 10; $rating >= 1; $rating--) {
        $output .= '
        <input type="radio" name="q_4" value="' . $rating . '" class="star" id="q_4' . $rating . '">
        <label class="star" for="q_4' . $rating . '"></label>
        ';
    }
    $output .= '
    </div>
    ';
    // q_5
    $output .= '
    <label for="q_5">5. Worked well with other team members. (10)</label><br>
    <div class="star-rating-container" required>
    ';
    for ($rating = 10; $rating >= 1; $rating--) {
        $output .= '
        <input type="radio" name="q_5" value="' . $rating . '" class="star" id="q_5' . $rating . '">
        <label class="star" for="q_5' . $rating . '"></label>
        ';
    }
    $output .= '
    </div>
    ';

    $output .= '
    <!-- submit -->
    <button type="submit" name="submit" value="Submit" id="submit_answers_btn">
    <span class="box" >
        SUBMIT!
    </span>
</button>
</form>

</div>
</div>
</div>
    ';


    ?>
    
    <!-- submit btn css -->
<style scoped>
    .box {
  width: 100%;
  height: auto;
  float: left;
  transition: .5s linear ease-in-out;
  position: relative;
  display: block;
  overflow: hidden;
  padding: 15px;
  text-align: center;
  background: transparent !important;
  text-transform: uppercase;
  font-weight: 900;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.61);
}

.box:before {
  position: absolute;
  content: '';
  left: 0;
  bottom: 0;
  height: 4px;
  width: 100%;
  border-bottom: 4px solid transparent;
  border-left: 4px solid transparent;
  box-sizing: border-box;
  transform: translateX(100%);
}

.box:after {
  position: absolute;
  content: '';
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  border-top: 4px solid transparent;
  border-right: 4px solid transparent;
  box-sizing: border-box;
  transform: translateX(-100%);
}

.box:hover {
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
}

.box:hover:before {
  border-color: #262626;
  height: 100%;
  transform: translateX(0);
  transition: .5s transform linear, .5s height linear .5s ;
}

.box:hover:after {
  border-color: #262626;
  height: 100%;
  transform: translateX(0);
  transition: .5s transform linear, .5s height linear .5s;
}

button {
  color: black;
  text-decoration: none;
  cursor: pointer;
  outline: none;
  border: none;
  background: transparent;
}
</style>

    <?php

    $output .= '
<!-- bootstrap js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
';

    // Return the create item form in html
    return $output;
}
}
?>
