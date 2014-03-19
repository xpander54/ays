<?php
/*
Template Name: servicios
*/
?>

<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
		<div class="post" id="post-<?php the_ID(); ?>">
<br>
			<div class="row hidden-xs">

				<div class="col-sm-3 col-sm-offset-1">
					 <img src="<?php bloginfo('template_url');?>/img/servicios/ange.png" alt="AySerrano" class="img-thumbnail">
					
				</div>
				<div class="col-sm-3">
					 <img src="<?php bloginfo('template_url');?>/img/servicios/chapultepec.png" alt="AySerrano" class="img-thumbnail">
					
				</div>
				<div class="col-sm-3">
					 <img src="<?php bloginfo('template_url');?>/img/servicios/cd.png" alt="AySerrano" class="img-thumbnail">
					
				</div>
				

			</div>

			<h2><?php// the_title(); ?></h2>

			<?php //include (TEMPLATEPATH . '/inc/meta.php' ); ?>

			<div class="entry">

				<?php the_content(); ?>

				<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>

			</div>

			<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>

		</div>
		
		<?php // comments_template(); ?>

		<?php endwhile; endif; ?>

<?php// get_sidebar(); ?>

<?php get_footer(); ?>