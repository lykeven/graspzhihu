<?php
/**
 * The template for displaying search results pages.
 *
 * @package WordPress
 * @subpackage fArt
 * @author tishonator
 * @since fArt 1.0.0
 *
 */

 get_header(); ?>

<div id="main-content-wrapper">

	<div id="main-content">

		<div id="info-title">
			<?php printf( __( 'You searched for "%s". Here are the results:', 'fart' ),
						get_search_query() );
			?>
		</div><!-- #infoTxt -->

		<?php if ( have_posts() ) :

					// starts the loop
					while ( have_posts() ) :
	
						the_post();
	
						/*
						 * include the post format-specific template for the content.
						 */
						get_template_part( 'content', get_post_format() );
	
					endwhile; // end of have_posts()
					?>
					<div class="navigation">
						<?php fart_show_pagenavi(); ?>
					</div><!-- .navigation -->
					
		<?php else :

				// if no content is loaded, show the 'no found' template
				get_template_part( 'content', 'none' );
			
		      endif; // end of have_posts()
		      ?>

	</div><!-- #main-content -->

	<?php get_sidebar(); ?>

</div><!-- #main-content-wrapper -->

<?php get_footer(); ?>