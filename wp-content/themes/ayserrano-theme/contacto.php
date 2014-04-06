<?php
/*
Template Name: contacto
*/
?>

<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
		<div class="post" id="post-<?php the_ID(); ?>">


			<!-- <h2><?php// the_title(); ?></h2> -->

			<?php //include (TEMPLATEPATH . '/inc/meta.php' ); ?>

			<div class="entry entry-contacto">

				<?php the_content(); ?>

				<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>
				
				<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3763.7207777499348!2d-99.18005610000002!3d19.381239299999972!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1ff82de36fe1f%3A0x2ec2684777712961!2sHolbein+118!5e0!3m2!1ses-419!2s!4v1395195963721" width="100%" height="450" frameborder="0" style="border:0"></iframe>
			

			</div>

			<div class="top-btn-div">
				
				<a class='top-btn' title='regresar' href="#body1">
	  				^
	  			</a>
			
			</div>

			<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>



		</div>
		
		<?php // comments_template(); ?>

		<?php endwhile; endif; ?>

		

<?php// get_sidebar(); ?>

<?php get_footer(); ?>