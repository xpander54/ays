<?php
/*
Plugin Name: Group File Manager
Plugin URI: "http://www.whereyoursolutionis.com/group-files
Description: Plugin to manage files for your groups. You can create and manage groups by creating a new group and adding users or adding a group policy ( or capability ) to a role. Includes custom icons, categories, notes for files, and more.
Author: Innovative Solutions
Version: 1.2
Author URI: http://www.whereyoursolutionis.com/author/scriptonite/
*/    
 
require(ABSPATH . 'wp-content/plugins/group-file-manager/includes/key.php');
require(ABSPATH . 'wp-content/plugins/group-file-manager/includes/functions.php');
require(ABSPATH . 'wp-content/plugins/group-file-manager/includes/settings.php');
//require(ABSPATH . 'wp-content/plugins/group-file-manager/widget.php');

//add language packs
add_action( 'init', 'groupfiles_langs' );


function groupfiles_langs() { 

load_plugin_textdomain( 'groupfiles', false, 'group-file-manager/includes/lang/' );

} 

  
		
	  		 
 
//activation functions
register_activation_hook(__FILE__,'GroupfileInstaller'); 
 

//Load Scripts
 add_action('wp_enqueue_scripts', 'LoadGFjava');
 add_action('admin_enqueue_scripts', 'LoadGFjava');

 			
//Load Dashboard Widget
add_action('wp_dashboard_setup', 'groupfiles_dashboard');
 

//check for file to download
add_action('admin_init','CheckForFile');
add_action('init','CheckForFile');

//Debugging
//add_action('activated_plugin','save_error');
?>