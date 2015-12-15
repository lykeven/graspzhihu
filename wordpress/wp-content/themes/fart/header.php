<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "body-content-wrapper" div.
 *
 * @package WordPress
 * @subpackage fArt
 * @author tishonator
 * @since fArt 1.0.0
 *
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo('charset'); ?>" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div id="body-content-wrapper">
			
			<header id="header-main-fixed">

				<div id="header-content-wrapper">

					<div id="header-top">
						<?php fart_display_social_sites(); ?>
					</div>
						
						<div class="clear"></div>
				
					<div id="header-logo">
						<?php fart_show_website_logo_image_or_title(); ?>
					</div><!-- #header-logo -->
					
					<nav id="navmain">
					
						<?php wp_nav_menu( array( 'theme_location' => 'primary',
												  'fallback_cb'    => 'wp_page_menu',
												  
												  ) ); ?>
					</nav><!-- #navmain -->
					
					<div class="clear">
					</div><!-- .clear -->
					
				</div><!-- #header-content-wrapper -->
				
			</header><!-- #header-main-fixed -->
			
			<div id="header-spacer">
				&nbsp;
			</div><!-- #header-spacer -->
