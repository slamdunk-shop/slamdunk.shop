( function ( $ ) {
	'use strict';
	var mobile = $( 'body' ).hasClass( 'mobile' ),
	    swiper = [];
	$( '.wowmall-products-carousel > .swiper-container' ).each( function ( n ) {
		var el        = $( this ),
		    direction = el.data( 'direction' );
		swiper[ n ]   = new Swiper( el, {
			slidesPerView               : el.data( 'visible' ),
			spaceBetween                : 40,
			direction                   : direction,
			nextButton                  : '#swiper-button-next' + el.attr( 'id' ),
			prevButton                  : '#swiper-button-prev' + el.attr( 'id' ),
			autoplay                    : mobile ? el.data( 'autoplay-mobile' ) : el.data( 'autoplay' ),
			preloadImages               : false,
			watchSlidesProgress         : true,
			watchSlidesVisibility       : true,
			onTransitionEnd             : function () {
				$( window ).trigger( 'scroll' );
			},
			onAfterResize               : function () {
				$( window ).trigger( 'scroll' );
			},
			onInit                      : function () {
				$( window ).trigger( 'scroll' );
			},
			onTouchEnd: function () {
				$( window ).trigger( 'wowmall_thumbs_swiper_reset_hover' );
			},
			breakpoints                 : {
				1599: {
					slidesPerView: 5 < $( this ).data( 'visible' ) && 'horizontal' === direction ? 5 : $( this ).data( 'visible' )
				},
				1299: {
					slidesPerView: 4 < $( this ).data( 'visible' ) && 'horizontal' === direction ? 4 : $( this ).data( 'visible' )
				},
				1199: {
					slidesPerView: 3 < $( this ).data( 'visible' ) && 'horizontal' === direction ? 3 : $( this ).data( 'visible' )
				},
				799 : {
					slidesPerView: 2 < $( this ).data( 'visible' ) ? 2 : $( this ).data( 'visible' )
				},
				559 : {
					slidesPerView: 1
				}
			}
		} );
		if ( 'vertical' === el.data( 'direction' ) ) {
			$( window ).on( 'resize', $.debounce( 200, function () {
				var params = swiper[ n ].params;
				if ( 799 < window.innerWidth ) {
					if ( 'vertical' !== params.direction ) {
						params.direction = 'vertical';
						swiper[ n ].destroy( true, true );
						swiper[ n ] = new Swiper( el, params );
					}
				} else if ( 'vertical' === params.direction ) {
					params.direction = 'horizontal';
					swiper[ n ].destroy( true, true );
					swiper[ n ] = new Swiper( el, params );
				}
			} ) ).resize();
		}
	} );
}( jQuery ) );