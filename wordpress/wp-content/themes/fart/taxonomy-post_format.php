<?php
/**
 * The template file listing taxonomy post formats
 *
 * A Post Format is a piece of meta information that can be used by 
 * a theme to customize its presentation of a post. The Post Formats 
 * feature provides a standardized list of formats that are available 
 * to all themes that support the feature. Themes are not required to 
 * support every format on the list. New formats cannot be introduced by
 * themes or even plugins. 
 *
 * @package WordPress
 * @subpackage fArt
 * @author tishonator
 * @since fArt 1.0.0
 * @link https://codex.wordpress.org/Post_Formats
 *
 */

 get_header(); ?>

<div id="main-content-wrapper">

	<div id="main-content">

		<h1 class="page-title">
			<?php
				if ( is_tax( 'post_format', 'post-format-aside' ) ) :
					_e( 'Asides', 'fart' );

				elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
					_e( 'Images', 'fart' );

				elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
					_e( 'Videos', 'fart' );

				elseif ( is_tax( 'post_format', 'post-format-audio' ) ) :
					_e( 'Audio', 'fart' );

				elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
					_e( 'Quotes', 'fart' );

				elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
					_e( 'Links', 'fart' );

				elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) :
					_e( 'Galleries', 'fart' );

				else :
					_e( 'Archives', 'fart' );

				endif;
			?>
		</h1><!-- .page-title -->

	<?php if ( have_posts() ) : ?>
	
				<?php
				// starts the loop
				while ( have_posts() ) :

					the_post();

					/*
					 * Include the post format-specific template for the content.
					 */
					get_template_part( 'content', get_post_format() );

				endwhile; // end of have_posts()
	?>
				<div class="navigation">
				
					<?php 	global $wp_query;

							$big = 999999999; // need an unlikely integer
				  
							echo paginate_links( array (
												'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
												'format' => '?paged=%#%',
												'current' => max( 1, get_query_var('paged') ),
												'total' => $wp_query->max_num_pages,
												'prev_next' => false,
											) ); ?>
							
				</div><!-- .navigation -->
	<?php else :

				// if no content is loaded, show the 'no found' template
				get_template_part( 'content', 'none' );
			
		  endif; // end of have_posts()
		  ?>

	</div><!-- #main-content -->

</div><!-- #main-content-wrapper -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>