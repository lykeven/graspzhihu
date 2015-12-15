<?php
/**
 * Site Front Page
 *
 * This is a traditional static HTML site model with a fixed front page and
 * content placed in Pages, rarely if ever using posts, categories, or tags. 
 *
 * @package WordPress
 * @subpackage fArt
 * @author tishonator
 * @since fArt 1.0.0
 * @link https://codex.wordpress.org/Creating_a_Static_Front_Page
 *
 */

/**
 * If front page is set to display the blog posts index, include home.php;
 * otherwise, display static front page content
 */
if ( 'posts' == get_option( 'show_on_front' ) ) :

    get_template_part( 'home' );

else :

	get_header();

	fart_display_slider();
?>
	<div class="clear">
	</div><!-- .clear -->

	<div id="main-content-wrapper">
		<div id="main-content-home">
			<?php get_sidebar( 'home' ); ?>
		</div><!-- #main-content -->
	</div><!-- #main-content-wrapper -->

<?php

	get_footer();

endif; ?>
