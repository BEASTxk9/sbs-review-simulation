<?php
/**
 * SBS REVIEW SIMULATION
 *
 * @package   SBS REVIEW SIMULATION(modular)
 * @author    Shane Stevens.
 * @copyright Stellenbosch Business School | @2023
 *
 * @wordpress-plugin 
 * Plugin Name: SBS REVIEW SIMULATION(modular)
 * Description: This is a custom plugin...idk what else to add here :)
 * Version: 1.0
 * Author: Shane Stevens.
 * License: Free
 */

// _________________________________________
// IMPORT ALL FILES HERE !IMPORTANT HAS TO BE ONTOP OF THE PAGE BEFORE ANY OTHER CODE IS ADDED
// eg.  require_once plugin_dir_path(__FILE__) . './file.php';

// 1CREATE
require_once plugin_dir_path(__FILE__) . './includes/01create/join_team_page.php';
require_once plugin_dir_path(__FILE__) . './includes/01create/questions_page.php';

// 2READ
require_once plugin_dir_path(__FILE__) . './includes/02read/reviews_table.php';
require_once plugin_dir_path(__FILE__) . './includes/02read/teams_table.php';
require_once plugin_dir_path(__FILE__) . './includes/02read/teams_page.php';
require_once plugin_dir_path(__FILE__) . './includes/02read/thank_you.php';

// 3UPDATE
require_once plugin_dir_path(__FILE__) . './includes/03update/wrong_team.php';

// 4DELETE
require_once plugin_dir_path(__FILE__) . './includes/04delete/delete.php';




// _________________________________________
// CREATE DATABASE TABLES ON ACTIVATING PLUGIN
function create_table_on_activate()
{
    // connect to WordPress database
    global $wpdb;

    // set table names
    $team = $wpdb->prefix . 'team'; // The table name is wp_team
    $reviews = $wpdb->prefix . 'reviews'; // The table name is wp_team


    $charset_collate = $wpdb->get_charset_collate();

    // mysql create tables query
    $sql = "CREATE TABLE $team (
                id INT(10) PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(100) NOT NULL,
                team VARCHAR(255) NOT NULL
            ) $charset_collate;";

    $sql .= "CREATE TABLE $reviews (
                id INT(10) PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(100) NOT NULL,
                team VARCHAR(255) NOT NULL,
                reviewing VARCHAR(255) NOT NULL,
                q_1 INT(10) NOT NULL,
                q_2 INT(10) NOT NULL,
                q_3 INT(10) NOT NULL,
                q_4 INT(10) NOT NULL,
                q_5 INT(10) NOT NULL
            ) $charset_collate;";


    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $result = dbDelta($sql);
    if (is_wp_error($result)) {
        echo 'There was an error creating the tables';
        return;
    }
}

register_activation_hook(__FILE__, 'create_table_on_activate');







// _________________________________________
// (!IMPORTANT DO NOT TOUCH)  CREATE PAGE FUNCTION  (!IMPORTANT DO NOT TOUCH)
function create_page($title_of_the_page, $content, $parent_id = NULL)
{
	$objPage = get_page_by_title($title_of_the_page, 'OBJECT', 'page');
	if (!empty($objPage)) {
		echo "Page already exists:" . $title_of_the_page . "<br/>";
		return $objPage->ID;
	}
	$page_id = wp_insert_post(
		array(
			'comment_status' => 'close',
			'ping_status' => 'close',
			'post_author' => 1,
			'post_title' => ucwords($title_of_the_page),
			'post_name' => strtolower(str_replace(' ', '-', trim($title_of_the_page))),
			'post_status' => 'publish',
			'post_content' => $content,
			'post_type' => 'page',
			'post_parent' => $parent_id //'id_of_the_parent_page_if_it_available'
		)
	);
	echo "Created page_id=" . $page_id . " for page '" . $title_of_the_page . "'<br/>";
	return $page_id;
}




// _________________________________________
// ACTIVATE PLUGIN
function on_activating_your_plugin()
{
    // _________________________________________
	//  CREATE WP PAGES AUTOMATICALLY ANLONG WITH SHORT CODE TO DISPLAY THE CONTENT
	// eg.  create_page('page-name', '[short-code]');
    // _________________________________________
    
    // 1CREATE
    create_page('join', '[join]');
    create_page('questions', '[questions]');

    // 2READ
    create_page('reviews', '[reviews]');
    create_page('teams', '[teams]');
    create_page('teamspage', '[teamspage]');
    create_page('thanks', '[thanks]');
    
    // 3UPDATE
    create_page('changeteam', '[changeteam]');

}
register_activation_hook(__FILE__, 'on_activating_your_plugin');




// _________________________________________
// DEACTIVATE PLUGIN
function on_deactivating_your_plugin()
{
    // _________________________________________
	//  DELETE WP PAGES AUTOMATICALLY ANLONG WITH SHORT CODE TO DISPLAY THE CONTENT
	// eg. 	
    // $page_name = get_page_by_path('page_name');
	// wp_delete_post($page_name->ID, true);
    // _________________________________________

    // 1CREATE
    $join = get_page_by_path('join');
	wp_delete_post($join->ID, true);
    $questions = get_page_by_path('questions');
	wp_delete_post($questions->ID, true);

    // 2READ
    $reviews = get_page_by_path('reviews');
	wp_delete_post($reviews->ID, true);
    $teams = get_page_by_path('teams');
	wp_delete_post($teams->ID, true);
    $teamspage = get_page_by_path('teamspage');
	wp_delete_post($teamspage->ID, true);
    $thanks = get_page_by_path('thanks');
	wp_delete_post($thanks->ID, true);
 

    // 3UPDATE
    $changeteam = get_page_by_path('changeteam');
	wp_delete_post($changeteam->ID, true);

}
register_deactivation_hook(__FILE__, 'on_deactivating_your_plugin');

?>