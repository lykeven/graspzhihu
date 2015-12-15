<?php
/**
 * fArt functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage fArt
 * @author tishonator
 * @since fArt 1.0.0
 *
 */

if ( ! function_exists( 'fart_setup' ) ) :
/**
 * fArt setup.
 *
 * Set up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support post thumbnails.
 *
 */
function fart_setup() {

	load_theme_textdomain( 'fart', get_template_directory() . '/languages' );

	add_theme_support( "title-tag" );

	// add the visual editor to resemble the theme style
	add_editor_style( array( 'css/editor-style.css' ) );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary'   => __( 'primary menu', 'fart' ),
	) );

	// Add wp_enqueue_scripts actions
	add_action( 'wp_enqueue_scripts', 'fart_load_scripts' );

	add_action( 'widgets_init', 'fart_widgets_init' );

	// add Custom background				 
	add_theme_support( 'custom-background', 
				   array ('default-color'  => '#FFFFFF')
				 );

	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 'full', 'full', true );

	if ( ! isset( $content_width ) )
		$content_width = 900;

	add_theme_support( 'automatic-feed-links' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list',
	) );

	// add custom header
	add_theme_support( 'custom-header', array (
					   'default-image'          => '',
					   'random-default'         => false,
					   'width'                  => 0,
					   'height'                 => 0,
					   'flex-height'            => false,
					   'flex-width'             => false,
					   'default-text-color'     => '',
					   'header-text'            => true,
					   'uploads'                => true,
					   'wp-head-callback'       => '',
					   'admin-head-callback'    => '',
					   'admin-preview-callback' => '',
					) );

	// add support for Post Formats.
	add_theme_support( 'post-formats', array (
											'aside',
											'image',
											'video',
											'audio',
											'quote', 
											'link',
											'gallery',
					) );
}
endif; // fart_setup
add_action( 'after_setup_theme', 'fart_setup' );

function fart_post_classes( $classes ) {
	if ( ! post_password_required() && ! is_attachment() && has_post_thumbnail() ) {
		$classes[] = 'has-post-thumbnail';
	}

	return $classes;
}
add_filter( 'post_class', 'fart_post_classes' );

/**
 * the main function to load scripts in the fArt theme
 * if you add a new load of script, style, etc. you can use that function
 * instead of adding a new wp_enqueue_scripts action for it.
 */
function fart_load_scripts() {

	// load main stylesheet.
	wp_enqueue_style( 'fart-style', get_stylesheet_uri(), array( ) );
	
	wp_enqueue_style( 'fart-fonts', fart_fonts_url(), array(), null );
	
	// Load thread comments reply script
	if ( is_singular() ) {
		wp_enqueue_script( 'comment-reply' );
	}
	
	// Load Utilities JS Script
	wp_enqueue_script( 'fart-js', get_template_directory_uri() . '/js/fart.js', array( 'jquery' ) );
	
	wp_enqueue_script( 'fart-jquery-mobile-js', get_template_directory_uri() . '/js/jquery.mobile.customized.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'fart-jquery-easing-js', get_template_directory_uri() . '/js/jquery.easing.1.3.js', array( 'jquery' ) );
	wp_enqueue_script( 'fart-camera-js', get_template_directory_uri() . '/js/camera.min.js', array( 'jquery' ) );
}

/**
 *	Load google font url used in the fArt theme
 */
function fart_fonts_url() {

    $fonts_url = '';
 
    /* Translators: If there are characters in your language that are not
    * supported by PT Sans, translate this to 'off'. Do not translate
    * into your own language.
    */
    $cantarell = _x( 'on', 'Noticia Text font: on or off', 'fart' );

    if ( 'off' !== $cantarell ) {
        $font_families = array();
 
        $font_families[] = 'Noticia Text:400,700';
 
        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),
            'subset' => urlencode( 'latin,latin-ext' ),
        );
 
        $fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
    }
 
    return $fonts_url;
}

function fart_display_social_sites() {

	echo '<ul class="header-social-widget">';

	$socialURL = get_theme_mod('fart_social_facebook', '#');
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow us on Facebook', 'fart') . '" class="facebook16"></a>';
	}

	$socialURL = get_theme_mod('fart_social_google', '#');
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow us on Google+', 'fart') . '" class="google16"></a>';
	}

	$socialURL = get_theme_mod('fart_social_twitter', '#');
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow us on Twitter', 'fart') . '" class="twitter16"></a>';
	}

	$socialURL = get_theme_mod('fart_social_linkedin', '#');
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow us on LinkeIn', 'fart') . '" class="linkedin16"></a>';
	}

	$socialURL = get_theme_mod('fart_social_instagram', '#');
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow us on Instagram', 'fart') . '" class="instagram16"></a>';
	}

	$socialURL = get_theme_mod('fart_social_rss', get_bloginfo( 'rss2_url' ));
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow our RSS Feeds', 'fart') . '" class="rss16"></a>';
	}

	$socialURL = get_theme_mod('fart_social_tumblr', '#');
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow us on Tumblr', 'fart') . '" class="tumblr16"></a>';
	}

	$socialURL = get_theme_mod('fart_social_youtube', '#');
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow us on Youtube', 'fart') . '" class="youtube16"></a>';
	}

	$socialURL = get_theme_mod('fart_social_pinterest', '#');
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow us on Pinterest', 'fart') . '" class="pinterest16"></a>';
	}

	$socialURL = get_theme_mod('fart_social_vk', '#');
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow us on VK', 'fart') . '" class="vk16"></a>';
	}

	$socialURL = get_theme_mod('fart_social_flickr', '#');
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow us on Flickr', 'fart') . '" class="flickr16"></a>';
	}

	$socialURL = get_theme_mod('fart_social_vine', '#');
	if ( !empty($socialURL) ) {

		echo '<li><a href="' . esc_url( $socialURL ) . '" title="' . __('Follow us on Vine', 'fart') . '" class="vine16"></a>';
	}

	echo '</ul>';
}

/**
 * Display website's logo image
 */
function fart_show_website_logo_image_or_title() {

	$logoImage = get_theme_mod('fart_header_logo', null);

	if ( get_header_image() != '' ) {
	
		// Check if the user selected a header Image in the Customizer or the Header Menu
		$logoImgPath = get_header_image();
		$siteTitle = get_bloginfo( 'name' );
		$imageWidth = get_custom_header()->width;
		$imageHeight = get_custom_header()->height;
		
		echo '<a href="'.home_url('/').'" title="'.get_bloginfo('name').'">';
		
		echo "<img src='$logoImgPath' alt='$siteTitle' title='$siteTitle' width='$imageWidth' height='$imageHeight' alt='' />";
		
		echo '</a>';

	} else if ( !empty( $logoImage ) ) {
		 
		echo '<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo('name') ) . '">';
		
		$siteTitle = get_bloginfo( 'name' );
		
		echo "<img src='" . esc_attr( $logoImage ) . "' alt='" . esc_attr( $siteTitle ) . "' title='" . esc_attr( $siteTitle ) . "' />";
	
		echo '</a>';

	} else {
	
		echo '<a href="' . esc_url( home_url('/') ) . '" title="' . esc_attr( get_bloginfo('name') ) . '">';
		
		echo '<h1>'.get_bloginfo('name').'</h1>';
		
		echo '</a>';
		
		echo '<strong>'.get_bloginfo('description').'</strong>';
	}
}

/**
 *	Displays the copyright text.
 */
function fart_show_copyright_text() {

	$footerText = get_theme_mod('fart_footer_copyright', null);

	if ( !empty( $footerText ) ) {

		echo esc_html( $footerText ) . ' | ';		
	}
}

/**
 *	widgets-init action handler. Used to register widgets and register widget areas
 */
function fart_widgets_init() {
	
	// Register Sidebar Widget.
	register_sidebar( array (
						'name'	 		 =>	 __( 'Sidebar Widget Area', 'fart'),
						'id'		 	 =>	 'sidebar-widget-area',
						'description'	 =>  __( 'The sidebar widget area', 'fart'),
						'before_widget'	 =>  '',
						'after_widget'	 =>  '',
						'before_title'	 =>  '<div class="sidebar-before-title"></div><h3 class="sidebar-title">',
						'after_title'	 =>  '</h3><div class="sidebar-after-title"></div>',
					) );
					
	/**
	 * Add Homepage Columns Widget areas
	 */
	register_sidebar( array (
							'name'			 =>  __( 'Homepage Column #1', 'ftourism' ),
							'id' 			 =>  'homepage-column-1-widget-area',
							'description'	 =>  __( 'The Homepage Column #1 widget area', 'fart' ),
							'before_widget'  =>  '',
							'after_widget'	 =>  '',
							'before_title'	 =>  '<h2 class="sidebar-title">',
							'after_title'	 =>  '</h2><div class="sidebar-after-title"></div>',
						) );
						
	register_sidebar( array (
							'name'			 =>  __( 'Homepage Column #2', 'ftourism' ),
							'id' 			 =>  'homepage-column-2-widget-area',
							'description'	 =>  __( 'The Homepage Column #2 widget area', 'fart' ),
							'before_widget'  =>  '',
							'after_widget'	 =>  '',
							'before_title'	 =>  '<h2 class="sidebar-title">',
							'after_title'	 =>  '</h2><div class="sidebar-after-title"></div>',
						) );
						
	register_sidebar( array (
							'name'			 =>  __( 'Homepage Column #3', 'ftourism' ),
							'id' 			 =>  'homepage-column-3-widget-area',
							'description'	 =>  __( 'The Homepage Column #3 widget area', 'fart' ),
							'before_widget'  =>  '',
							'after_widget'	 =>  '',
							'before_title'	 =>  '<h2 class="sidebar-title">',
							'after_title'	 =>  '</h2><div class="sidebar-after-title"></div>',
						) );
	
	register_sidebar( array (
							'name'			 =>  __( 'Homepage Column #4', 'ftourism' ),
							'id' 			 =>  'homepage-column-4-widget-area',
							'description'	 =>  __( 'The Homepage Column #4 widget area', 'fart' ),
							'before_widget'  =>  '',
							'after_widget'	 =>  '',
							'before_title'	 =>  '<h2 class="sidebar-title">',
							'after_title'	 =>  '</h2><div class="sidebar-after-title"></div>',
						) );
	
	// Register Footer Column #1
	register_sidebar( array (
							'name'			 =>  __( 'Footer Column #1', 'fart' ),
							'id' 			 =>  'footer-column-1-widget-area',
							'description'	 =>  __( 'The Footer Column #1 widget area', 'fart' ),
							'before_widget'  =>  '',
							'after_widget'	 =>  '',
							'before_title'	 =>  '<h2 class="footer-title">',
							'after_title'	 =>  '</h2><div class="footer-after-title"></div>',
						) );
	
	// Register Footer Column #2
	register_sidebar( array (
							'name'			 =>  __( 'Footer Column #2', 'fart' ),
							'id' 			 =>  'footer-column-2-widget-area',
							'description'	 =>  __( 'The Footer Column #2 widget area', 'fart' ),
							'before_widget'  =>  '',
							'after_widget'	 =>  '',
							'before_title'	 =>  '<h2 class="footer-title">',
							'after_title'	 =>  '</h2><div class="footer-after-title"></div>',
						) );
	
	// Register Footer Column #3
	register_sidebar( array (
							'name'			 =>  __( 'Footer Column #3', 'fart' ),
							'id' 			 =>  'footer-column-3-widget-area',
							'description'	 =>  __( 'The Footer Column #3 widget area', 'fart' ),
							'before_widget'  =>  '',
							'after_widget'	 =>  '',
							'before_title'	 =>  '<h2 class="footer-title">',
							'after_title'	 =>  '</h2><div class="footer-after-title"></div>',
						) );
}

/**
 * Displays the slider
 */
function fart_display_slider() { ?>
	 
	 <div class="camera_wrap camera_emboss" id="camera_wrap">
		<?php
			// display slides
			for ( $i = 1; $i <= 3; ++$i ) {
			
					$defaultSlideContent = __( '<h2>Lorem ipsum dolor</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><a class="btn" title="Read more" href="#">Read more</a>', 'fart' );
					
					$defaultSlideImage = get_template_directory_uri().'/images/slider/' . $i .'.jpg';

					$slideContent = get_theme_mod( 'fart_slide'.$i.'_content', html_entity_decode( $defaultSlideContent ) );
					$slideImage = get_theme_mod( 'fart_slide'.$i.'_image', $defaultSlideImage );
				?>
					<div data-thumb="<?php echo esc_attr( $slideImage ); ?>" data-src="<?php echo esc_attr( $slideImage ); ?>">
						<div class="camera_caption fadeFromBottom">
							<?php echo $slideContent; ?>
						</div>
					</div>
<?php		} ?>
	</div><!-- #camera_wrap -->
<?php 
}

/**
 *	Displays the page navigation
 */
function fart_show_pagenavi( $p = 2 ) { // pages will be show before and after current page

	if ( is_singular() ) {
		return; // do NOT show in single page
	}
  
	global $wp_query, $paged;
	$max_page = $wp_query->max_num_pages;
	
	if ( $max_page == 1 ) {
		return; // don't show when only one page
	}
  
	if ( empty( $paged ) ) {
		$paged = 1;
	}
  
	// pages
	if ( $paged > $p + 1 ) {
		fart_p_link( 1, __('First', 'fart') );
	}
  
	if ( $paged > $p + 2 ) {
		echo '... ';
	}
  
	for ( $i = $paged - $p; $i <= $paged + $p; ++$i ) { 
		// Middle pages
		if ( $i > 0 && $i <= $max_page ) {
			$i == $paged ? print "<span class='page-numbers current'>{$i}</span> " : fart_p_link($i);
		}
	}
  
	if ( $paged < $max_page - $p - 1 ) {
		echo '... ';
	}
  
	if ( $paged < $max_page - $p ) {
		fart_p_link( $max_page, __('Last', 'fart') );
	}
}

function fart_p_link( $i, $title = '' ) {

	if ( $title == '' ) {
		$title = sprintf( __('Page %s', 'fart'), $i );
	}
	
	echo "<a class='page-numbers' href='", esc_url( get_pagenum_link( $i ) ), "' title='", esc_attr($title), "'>{$i}</a>";
}

/**
 *	Used to load the content for posts and pages.
 */
function fart_the_content() {

	// Display Thumbnails if thumbnail is set for the post
	if ( has_post_thumbnail() ) {
		
		echo '<a href="'. esc_url( get_permalink() ) .'" title="' . esc_attr( get_the_title() ) . '">';
		
		the_post_thumbnail();
		
		echo '</a>';
	}
	the_content( __( 'Read More', 'fart') );
}

/**
 *	Displays the single content.
 */
function fart_the_content_single() {

	// Display Thumbnails if thumbnail is set for the post
	if ( has_post_thumbnail() ) {

		the_post_thumbnail();
	}
	the_content( __( 'Read More...', 'fart') );
}

/**
 * Gets additional theme settings description
 */
function fart_get_customizer_sectoin_info() {

	$premiumThemeUrl = 'http://tishonator.com/product/tart';

	return sprintf( __( 'The fArt theme is a free version of the professional WordPress theme tArt. <a href="%s" class="button-primary" target="_blank">Get tArt Theme</a><br />', 'tishonator' ), $premiumThemeUrl );
}

/**
 * Register theme settings in the customizer
 */
function fart_customize_register( $wp_customize ) {

	$premiumThemeUrl = 'http://tishonator.com/product/tart';

	/**
	 * Add Header Logo Section
	 */
    $wp_customize->add_section(
		'fart_header_logo_section',
		array(
			'title'       => __( 'Header Logo', 'fart' ),
			'capability'  => 'edit_theme_options',
			'description' => fart_get_customizer_sectoin_info(),
		)
	);

    // Add logo image
    $wp_customize->add_setting( 'fart_header_logo',
		array(
			'default' => '',
    		'sanitize_callback' => 'esc_url_raw'
		)
	);

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'fart_header_logo',
			array(
				'label'   	 => __( 'Logo Image', 'fart' ),
				'section' 	 => 'fart_header_logo_section',
				'settings'   => 'fart_header_logo',
			) 
		)
	);

    /**
	 * Add Social Sites Section
	 */
	$wp_customize->add_section(
		'fart_social_section',
		array(
			'title'       => __( 'Social Sites', 'fart' ),
			'capability'  => 'edit_theme_options',
			'description' => fart_get_customizer_sectoin_info(),
		)
	);
	
	// Add facebook url
	$wp_customize->add_setting(
		'fart_social_facebook',
		array(
		    'default'           => '#',
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_facebook',
        array(
            'label'          => __( 'Facebook Page URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_facebook',
            'type'           => 'text',
            )
        )
	);

	// Add google+ url
	$wp_customize->add_setting(
		'fart_social_google',
		array(
		    'default'           => '#',
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_google',
        array(
            'label'          => __( 'Google+ Page URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_google',
            'type'           => 'text',
            )
        )
	);

	// Add twitter url
	$wp_customize->add_setting(
		'fart_social_twitter',
		array(
		    'default'           => '#',
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_twitter',
        array(
            'label'          => __( 'Twitter Page URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_twitter',
            'type'           => 'text',
            )
        )
	);

	// Add LinkedIn url
	$wp_customize->add_setting(
		'fart_social_linkedin',
		array(
		    'default'           => '#',
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_linkedin',
        array(
            'label'          => __( 'LinkedIn Page URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_linkedin',
            'type'           => 'text',
            )
        )
	);

	// Add Instagram url
	$wp_customize->add_setting(
		'fart_social_instagram',
		array(
		    'default'           => '#',
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_instagram',
        array(
            'label'          => __( 'instagram Page URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_instagram',
            'type'           => 'text',
            )
        )
	);

	// Add RSS Feeds url
	$wp_customize->add_setting(
		'fart_social_rss',
		array(
		    'default'           => get_bloginfo( 'rss2_url' ),
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_rss',
        array(
            'label'          => __( 'RSS Feeds URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_rss',
            'type'           => 'text',
            )
        )
	);

	// Add Tumblr url
	$wp_customize->add_setting(
		'fart_social_tumblr',
		array(
		    'default'           => '#',
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_tumblr',
        array(
            'label'          => __( 'Tumblr Page URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_tumblr',
            'type'           => 'text',
            )
        )
	);

	// Add YouTube channel url
	$wp_customize->add_setting(
		'fart_social_youtube',
		array(
		    'default'           => '#',
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_youtube',
        array(
            'label'          => __( 'YouTube channel URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_youtube',
            'type'           => 'text',
            )
        )
	);

	// Add Pinterest page url
	$wp_customize->add_setting(
		'fart_social_pinterest',
		array(
		    'default'           => '#',
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_pinterest',
        array(
            'label'          => __( 'Pinterest Page URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_pinterest',
            'type'           => 'text',
            )
        )
	);

	// Add VK page url
	$wp_customize->add_setting(
		'fart_social_vk',
		array(
		    'default'           => '#',
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_vk',
        array(
            'label'          => __( 'VK Page URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_vk',
            'type'           => 'text',
            )
        )
	);

	// Add Flickr page url
	$wp_customize->add_setting(
		'fart_social_flickr',
		array(
		    'default'           => '#',
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_flickr',
        array(
            'label'          => __( 'Flickr Page URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_flickr',
            'type'           => 'text',
            )
        )
	);

	// Add Vine page url
	$wp_customize->add_setting(
		'fart_social_vine',
		array(
		    'default'           => '#',
		    'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_social_vine',
        array(
            'label'          => __( 'Vine Page URL', 'fart' ),
            'section'        => 'fart_social_section',
            'settings'       => 'fart_social_vine',
            'type'           => 'text',
            )
        )
	);
	
	/**
	 * Add Slider Section
	 */
	$wp_customize->add_section(
		'fart_slider_section',
		array(
			'title'       => __( 'Slider', 'fart' ),
			'capability'  => 'edit_theme_options',
			'description' => fart_get_customizer_sectoin_info(),
		)
	);
	
	// Add slide 1 content
	$wp_customize->add_setting(
		'fart_slide1_content',
		array(
		    'default'           => __( '<h2>Lorem ipsum dolor</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><a class="btn" title="Read more" href="#">Read more</a>', 'fart' ),
		    'sanitize_callback' => 'force_balance_tags',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_slide1_content',
        array(
            'label'          => __( 'Slide #1 Content', 'fart' ),
            'section'        => 'fart_slider_section',
            'settings'       => 'fart_slide1_content',
            'type'           => 'textarea',
            )
        )
	);
	
	// Add slide 1 background image
	$wp_customize->add_setting( 'fart_slide1_image',
		array(
			'default' => get_template_directory_uri().'/images/slider/' . '1.jpg',
    		'sanitize_callback' => 'esc_url_raw'
		)
	);

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'fart_slide1_image',
			array(
				'label'   	 => __( 'Slide 1 Image', 'fart' ),
				'section' 	 => 'fart_slider_section',
				'settings'   => 'fart_slide1_image',
			) 
		)
	);
	
	// Add slide 2 content
	$wp_customize->add_setting(
		'fart_slide2_content',
		array(
		    'default'           => __( '<h2>Lorem ipsum dolor</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><a class="btn" title="Read more" href="#">Read more</a>', 'fart' ),
		    'sanitize_callback' => 'force_balance_tags',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_slide2_content',
        array(
            'label'          => __( 'Slide #2 Content', 'fart' ),
            'section'        => 'fart_slider_section',
            'settings'       => 'fart_slide2_content',
            'type'           => 'textarea',
            )
        )
	);
	
	// Add slide 2 background image
	$wp_customize->add_setting( 'fart_slide2_image',
		array(
			'default' => get_template_directory_uri().'/images/slider/' . '2.jpg',
    		'sanitize_callback' => 'esc_url_raw'
		)
	);

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'fart_slide2_image',
			array(
				'label'   	 => __( 'Slide 2 Image', 'fart' ),
				'section' 	 => 'fart_slider_section',
				'settings'   => 'fart_slide2_image',
			) 
		)
	);
	
	// Add slide 3 content
	$wp_customize->add_setting(
		'fart_slide3_content',
		array(
		    'default'           => __( '<h2>Lorem ipsum dolor</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><a class="btn" title="Read more" href="#">Read more</a>', 'fart' ),
		    'sanitize_callback' => 'force_balance_tags',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_slide3_content',
        array(
            'label'          => __( 'Slide #3 Content', 'fart' ),
            'section'        => 'fart_slider_section',
            'settings'       => 'fart_slide3_content',
            'type'           => 'textarea',
            )
        )
	);
	
	// Add slide 3 background image
	$wp_customize->add_setting( 'fart_slide3_image',
		array(
			'default' => get_template_directory_uri().'/images/slider/' . '3.jpg',
    		'sanitize_callback' => 'esc_url_raw'
		)
	);

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'fart_slide3_image',
			array(
				'label'   	 => __( 'Slide 3 Image', 'fart' ),
				'section' 	 => 'fart_slider_section',
				'settings'   => 'fart_slide3_image',
			) 
		)
	);

	/**
	 * Add Footer Section
	 */
	$wp_customize->add_section(
		'fart_footer_section',
		array(
			'title'       => __( 'Footer', 'fart' ),
			'capability'  => 'edit_theme_options',
			'description' => fart_get_customizer_sectoin_info(),
		)
	);
	
	// Add footer copyright text
	$wp_customize->add_setting(
		'fart_footer_copyright',
		array(
		    'default'           => '',
		    'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fart_footer_copyright',
        array(
            'label'          => __( 'Copyright Text', 'fart' ),
            'section'        => 'fart_footer_section',
            'settings'       => 'fart_footer_copyright',
            'type'           => 'text',
            )
        )
	);
}

add_action('customize_register', 'fart_customize_register');

?>
