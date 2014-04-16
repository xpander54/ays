<?php
	
	// Add RSS links to <head> section
	automatic_feed_links();
	
	// Load jQuery
	if ( !is_admin() ) {
	   wp_deregister_script('jquery');
	   wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"), false);
	   wp_enqueue_script('jquery');
       wp_register_script('script', (get_bloginfo('template_directory').'/js/script.js'));
       wp_enqueue_script('script');
       wp_register_script('shadowbox', (get_template_directory_uri() . "/shadowbox/shadowbox.js"), false);
       wp_enqueue_script('shadowbox');

	}

    add_action('admin_bar_menu', 'remove_wp_logo', 999);

    function remove_wp_logo( $wp_admin_bar ) {
        $wp_admin_bar->remove_node('wp-logo');
    }

    
	
	// Clean up the <head>
	function removeHeadLinks() {
    	remove_action('wp_head', 'rsd_link');
    	remove_action('wp_head', 'wlwmanifest_link');
    }
    add_action('init', 'removeHeadLinks');
    remove_action('wp_head', 'wp_generator');
    
	// Declare sidebar widget zone
    if (function_exists('register_sidebar')) {
    	register_sidebar(array(
    		'name' => 'Sidebar Widgets',
    		'id'   => 'sidebar-widgets',
    		'description'   => 'These are widgets for the sidebar.',
    		'before_widget' => '<div id="%1$s" class="widget %2$s">',
    		'after_widget'  => '</div>',
    		'before_title'  => '<h2>',
    		'after_title'   => '</h2>'
    	));
    }




?>