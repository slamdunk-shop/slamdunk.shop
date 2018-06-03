(function ( $ ) {
	'use strict';
	var related = $( '#related' );
	new Swiper( related, {
		slidesPerView:   related.data( 'visible' ),
		spaceBetween:    40,
		nextButton:      related.parent().find( '.swiper-button-next' ),
		prevButton:      related.parent().find( '.swiper-button-prev' ),
		onAfterResize:   function () {
			if( $('.wrapper-sticky').length ) {
				$( '.stick-in-parent' ).hcSticky( 'reinit' );
			}
			$( window ).trigger( 'scroll' );
		},
		onInit:          function () {
			if( $('.wrapper-sticky').length ) {
				$( '.stick-in-parent' ).hcSticky( 'reinit' );
			}
			$( window ).trigger( 'scroll' );
		},
		onTransitionEnd: function () {
			$( window ).trigger( 'scroll' );
		},
		onTouchEnd:      function () {
			$( window ).trigger( 'wowmall_thumbs_swiper_reset_hover' );
		},
		preloadImages:   false,
		autoplay:        5000,
		breakpoints:     {
			1600: {
				slidesPerView: 2
			},
			1140: {
				slidesPerView: 1
			},
			767:  {
				slidesPerView: 2
			},
			559:  {
				slidesPerView: 1
			}
		}
	} );
})( jQuery );