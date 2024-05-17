<?php
/*
Plugin Name: Post Link Search
Description: Busca paraules claus a google al obrir un post
Author: Daniel Fernández López  
Version: 1.0
*/

register_activation_hook(__FILE__, 'setup_plugin_db');

define("PL_DIR", plugin_dir_path(__FILE__));
require_once PL_DIR . 'includes/pl-functions.php';
require_once PL_DIR . 'public/pl-functions.php';

function setup_plugin_db() {
    global $wpdb;
    $sql = "CREATE TABLE pl_engines (
                    ID INT AUTO_INCREMENT PRIMARY KEY,
                    engine VARCHAR(255),
                    link VARCHAR(255)
                    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);    
}