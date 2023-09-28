<?php
// Function to fetch and display the answers data in a table
function thanks() {
    global $wpdb;

$output = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You!</title> ';
    ?>
    <style scoped>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            align-content: center;
        }
        h1 {
            color: goldenrod;
            text-decoration: 0px 0px 10px rgba(0, 0, 0, 0.31);
        }
        #questions-link{
    color: #561c32;
        font-weight: bold;
 }   
    </style>
    <?php 
    $output .='
</head>
<body>
    <h1>Thank You for Your Submission!</h1>
    <p>Your answers have been successfully submitted.</p>
    <p><a id="questions-link" href="/questions">Click here to do another review.</a></p>
    <!-- Add any additional content or styling as needed -->
</body>
</html>
';

return $output; 
}

add_shortcode('thanks', 'thanks');
?>