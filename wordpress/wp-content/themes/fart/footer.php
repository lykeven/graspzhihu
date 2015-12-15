<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "body-content-wrapper" div and all content after.
 *
 * @package WordPress
 * @subpackage fArt
 * @author tishonator
 * @since fArt 1.0.0
 *
 */
?>
			<a href="#" class="scrollup"></a>
			<footer id="footer-main">
			
				<div id="footer-content-wrapper">
				
					<?php get_sidebar( 'footer' ); ?>
				
					<div class="clear">
					</div><!-- .clear -->
					
					<div id="copyright">
					
						<p>
						 	<?php fart_show_copyright_text(); ?> <a href="<?php echo esc_url( 'http://tishonator.com/product/fart' ); ?>" title="<?php esc_attr_e( 'fart Theme', 'fart' ); ?>">
							<?php _e('fArt Theme', 'fart'); ?></a> <?php esc_attr_e( 'powered by', 'fart' ); ?> <a href="<?php echo esc_url( 'http://wordpress.org/' ); ?>" title="<?php esc_attr_e( 'WordPress', 'fart' ); ?>">
							<?php _e('WordPress', 'fart'); ?></a>
						</p>
						
					</div><!-- #copyright -->
					
				</div><!-- #footer-content-wrapper -->
				
			</footer><!-- #footer-main -->

		</div><!-- #body-content-wrapper -->
		<?php wp_footer(); ?>
	</body>
</html>