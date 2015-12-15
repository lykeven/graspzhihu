<?php
/**
 * The template used for displaying page content
 *
 * @package WordPress
 * @subpackage fArt
 * @author tishonator
 * @since fArt 1.0.0
 *
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<h1 class="entry-title">
		<?php echo get_the_title(); ?>
	</h1><!-- .entry-title -->

	<div class="page-content">
		<?php fart_the_content_single(); ?>
	</div><!-- .page-content -->
	
	<div class="page-after-content">
		
		<?php if ( ! post_password_required() ) : ?>

			<?php if ('open' == $post->comment_status) : ?>

					<span class="comments-icon">
						<?php comments_popup_link(__( 'No Comments', 'fart' ), __( '1 Comment', 'fart' ), __( '% Comments', 'fart' ), '', __( 'Comments are closed.', 'fart' )); ?>
					</span><!-- .comments-icon -->

			<?php endif; ?>
				
			<?php edit_post_link( __( 'Edit', 'fart' ), '<span class="edit-icon">', '</span>' ); ?>

		<?php endif; ?>

	</div><!-- .page-after-content -->
	
</article><!-- #post-## -->
