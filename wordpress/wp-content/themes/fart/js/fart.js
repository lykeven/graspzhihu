jQuery( document ).ready(function() {

	jQuery('#navmain > div').on('click', function(e) {
			
		e.stopPropagation();

		// toggle main menu
		if ( jQuery(window).width() < 800 ) {

			var parentOffset = jQuery(this).parent().offset(); 
			
			var relY = e.pageY - parentOffset.top;
		
			if (relY < 36) {
			
				jQuery('ul:first-child', this).toggle(400);
			}
		}
	});
	
	// add submenu icons class in main menu (only for large resolution)
	if ( jQuery(window).width() >= 800 ) {
	
		jQuery('#navmain > div > ul > li:has("ul")').addClass('level-one-sub-menu');
		jQuery('#navmain > div > ul li ul li:has("ul")').addClass('level-two-sub-menu');										
	}
	
	jQuery("#navmain > div > ul li").mouseenter( function() {
		if ( jQuery(window).width() >= 800 ) {

			var curMenuLi = jQuery(this);
			jQuery("#navmain > div > ul > ul:not(:contains('#" + curMenuLi.attr('id') + "')) ul").hide();
		
			jQuery(this).children("ul").stop(true, true).css('display','none').slideDown(400);
		}
	});

	jQuery("#navmain > div > ul li").mouseleave( function() {
		if ( jQuery(window).width() >= 800 ) {
			
			jQuery(this).children("ul")
				.stop(true, true)
				.css('display', 'block')
				.slideUp(300);
		}
	});
	
	function fart_IsSmallResolution() {

		return (jQuery(window).width() <= 360);
	}

	function fart_IsMediumResolution() {
		
		var browserWidth = jQuery(window).width();

		return (browserWidth > 360 && browserWidth < 800);
	}

	function fart_IsLargeResolution() {

		return (jQuery(window).width() >= 800);
	}
	
	jQuery('#header-spacer').height(jQuery('#header-main-fixed').height());
	
	if (jQuery('#wpadminbar').length > 0) {
	
		jQuery('#header-main-fixed').css('top', jQuery('#wpadminbar').height() + 'px');
		jQuery('#wpadminbar').css('position', 'fixed');
	}
	
	jQuery(window).scroll(function () {
		if ( jQuery(this).scrollTop() > 120 ) {
			
			jQuery('.scrollup').fadeIn();
			
		} else {
			
			jQuery('.scrollup').fadeOut();
		}
	});
	
	jQuery('#camera_wrap').camera({
		height: fart_IsLargeResolution() ? '450px' : '300px',
		loader: 'bar',
		pagination: true,
		thumbnails: false,
		time: 4500
	});

	jQuery('.scrollup').click(function () {
		jQuery("html, body").animate({
			scrollTop: 0
		}, 500);
		
		return false;
	});
});