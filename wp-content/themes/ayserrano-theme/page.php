<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="main">
			
			<div class="post" id="post-<?php the_ID(); ?>">

				<h2><?php// the_title(); ?></h2>

				<?php// include (TEMPLATEPATH . '/inc/meta.php' ); ?>

				
				
					<div class="entry">

							

						<?php the_content(); ?>

						<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>

					
				

						<?php  edit_post_link('Edit this entry.', '<p>', '</p>'); ?>

					
						<?php // comments_template(); ?>

					</div>

					<?php endwhile; endif; ?>

			</div>

		</div>
<?php //get_sidebar(); ?>

<?php get_footer(); ?>