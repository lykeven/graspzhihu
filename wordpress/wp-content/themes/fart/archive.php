<?php
/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @package WordPress
 * @subpackage fArt
 * @author tishonator
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @since fArt 1.0.0
 *
 */

 get_header(); ?>

<div id="main-content-wrapper">
	<div id="main-content">
		<?php
			/**
			 * we use get_the_archive_title() to get the title of the archive of 
			 * a category (taxonomy), tag (term), author, custom post type, post format, date, etc.
			 */
			$title = get_the_archive_title();
		?>
	
		<div id="info-title">
			<?php echo $title; ?>
		</div><!-- #infoTxt -->
	
		<?php if ( have_posts() ) : ?>

				<?php
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

				</div><!-- .navigation --> 
		<?php else :

				// If no content, include the "No posts found" template.
				get_template_part( 'content', 'none' );
			
			  endif; ?>
	</div><!-- #main-content -->

	<?php get_sidebar(); ?>
	
</div><!-- #main-content-wrapper -->

<?php get_footer(); ?>