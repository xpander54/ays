<aside class='asesores_list'>
    <br>
    <?php query_posts('p=59'); if(have_posts()) : the_post(); ?>
     <?php the_content(); ?>
    <?php endif; ?>

    <?php //if (function_exists('dynamic_sidebar') && dynamic_sidebar('Sidebar Widgets')) : else : ?>
    
        <!-- All this stuff in here only shows up if you DON'T have any widgets active in this zone -->

    	<?php get_search_form(); ?>
    
    	<?php //wp_list_pages('title_li=<h2>Pages</h2>' ); ?>

        <h2>Login</h2>
        <ul>
            <?php wp_register(); ?>
            <li><?php wp_loginout(); ?></li>
            <?php wp_meta(); ?>
        </ul>
    
    	<h2>Archivo</h2>
    	<ul>
    		<?php wp_get_archives('type=monthly'); ?>
    	</ul>
        
        <h2>Categorias</h2>
        <ul>
    	   <?php wp_list_categories('show_count=1&title_li='); ?>
        </ul>
        
    	<?php wp_list_bookmarks(); ?>
    
    	
    	
    	<!-- <h2>Subscribe</h2>
        <ul>
            <li><a href="<?php bloginfo('rss2_url'); ?>">Entries (RSS)</a></li>
            <li><a href="<?php bloginfo('comments_rss2_url'); ?>">Comments (RSS)</a></li>
        </ul> -->
	
	<?php//endif; ?>

</aside>