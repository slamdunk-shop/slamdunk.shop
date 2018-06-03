( function ($) {
	'use strict';
	var mobile = $(document.body).hasClass( 'mobile' ),
	    swiper = [];

	$( '.wowmall-slider' ).each( function ( n ) {
		swiper[n] = new Swiper($(this), {
			spaceBetween: 0,
			nextButton: '#swiper-button-next' + $(this).attr( 'id' ),
			prevButton: '#swiper-button-prev' + $(this).attr( 'id' ),
			//autoplay: 9000,
			loop: true,
			effect: 'fade',
			watchSlidesProgress: true,
			watchSlidesVisibility: true,
			onSlideChangeStart: pleload_slide
		});
	} );
	function pleload_slide() {
		if ( $.isFunction( $.fn.lazyload ) ) {
			$( ".wowmall-slider .swiper-slide-active .wowmall-slide-img[data-src], .wowmall-slider .swiper-slide-duplicate-active .wowmall-slide-img[data-src]" ).lazyload( {
				data_attribute: 'src'
			} );
		}
	}
	$(document).on("vc-full-width-row-single", function ( e ) {
		swiper.forEach(function(item) {
			item.onResize();
		});
	});
}(jQuery) );