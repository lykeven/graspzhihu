<?php get_header(); ?>

<?php fart_display_slider(); ?>

<div class="clear">
</div>

<div id="main-content-wrapper">
	<div id="main-content">
	<?php if ( have_posts() ) : 
				// starts the loop
				while ( have_posts() ) :

					the_post();

					/*
					 * Include the post format-specific template for the content.
					 */
					get_template_part( 'content', get_post_format() );

				endwhile;
	?>
				<div class="navigation">
					<?php fart_show_pagenavi(); ?>
				</div>  
	<?php else :

				// if no content is loaded, show the 'no found' template
				get_template_part( 'content', 'none' );
			
		  endif; ?>
	</div>

	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>