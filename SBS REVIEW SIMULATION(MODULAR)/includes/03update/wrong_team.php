<?php
// Register shortcode
add_shortcode('changeteam', 'update_team');

// Create function
function update_team()
{
    global $wpdb;

    // Check connection
    if (is_null($wpdb)) {
        $wpdb->show_errors();
    }

    $table_name = $wpdb->prefix . 'team';

    if (isset($_POST['submit'])) {
        // Get the current user's information
        $current_user = wp_get_current_user();
        $username = $current_user->display_name;

        $selected_team = $_POST['team'];
        $team_name = substr($selected_team, 0, -2); // Remove the last two characters (number)
        $team_number = substr($selected_team, -2);  // Get the last two characters (number)
        $team =  $team_name . $team_number;

        // Check if the user is already in a team
        $user_team = $wpdb->get_var($wpdb->prepare("SELECT team FROM $table_name WHERE username = %s", $username));

        if ($user_team && $user_team === $team) {
            // User is already in the selected team, no need to update or insert
            $redirect_url = site_url('/teamspage/');
            ?>
            <script>
                window.location.href = "<?php echo $redirect_url; ?>";
            </script>
            <?php
            exit;
        } elseif ($user_team) {
            // User is already in a team, so we need to update their existing team
            $sql = "UPDATE $table_name
                    SET team = %s
                    WHERE username = %s";

            $result = $wpdb->query($wpdb->prepare($sql, $team, $username));
        } else {
            // User is not in a team, so we need to insert a new row
            $sql = "INSERT INTO $table_name (username, team) 
                    VALUES (%s, %s)";

            $result = $wpdb->query($wpdb->prepare($sql, $username, $team));
        }

        if ($result) {
            $redirect_url = site_url('/teamspage/');
            ?>
            <script>
                window.location.href = "<?php echo $redirect_url; ?>";
            </script>
            <?php
            exit;
        } else {
            wp_die($wpdb->last_error);
        }
    }

    $output = '
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>';

    ?>
    <!-- custom css -->
    <style scoped>
      #teams-list{
        color: #561c32;
        font-weight: bold;
      }

      form label:nth-child(1){
        display: flex;
        justify-content: center;
      }

      #submit_answers_btn{
            background-color: transparent;
            width: 100%;
            color: black;
            margin-top: 5px;
        }

        select{
        border: 1px solid black;
       }
       select:focus{
        border: 1px solid goldenrod;
       }
    </style>
    
    <?php

    $output .= '
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <form method="POST" action="">
                    <!-- select team -->
                    <label for="team">Change team. Please refer to the list to check what team you are in :) <a id="teams-list" href="https://stellenbosch-my.sharepoint.com/:x:/g/personal/shanes_sun_ac_za/ERTXx1dMoZZJuVy8HPnas7kBkS2hXgWEmYVu5wHDUTM3NQ?e=dhyomB" target="_blank">TEAMS LIST<a></label><br>
                    <select id="team" name="team" class="mb-3" required>
                    ';

    // Generate 18 options with team names and option values
    for ($i = 1; $i <= 19; $i++) {
        $team_name = 'Team';
        $team_number = str_pad($i, 2, '0', STR_PAD_LEFT); // Pad the number with leading zeros (e.g., 01, 02, ..., 19)
        $team_full_name = $team_name . ' ' . $team_number;
        $output .= '<option value="' . $team_full_name . '">' . $team_full_name . '</option>';
    }

    $output .= '
                </select><br>
                <!-- submit -->
                <button type="submit" name="submit" value="Submit" id="submit_answers_btn">
                <span class="box" >
                    CHANGE TEAM!
                </span>
            </button>
            </form>
        </div>
    </div>
    </div>';

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

    return $output;
}
?>
