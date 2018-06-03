( function ( $ ) {
	'use strict';
	var mobile           = $( document.body ).hasClass( 'mobile' ),
	    swiper           = [],
	    all_swipers      = [],
	    all_swipers_size = 0,
	    settings         = {
		    threshold: 0,
		    container: window
	    };
	if ( $( document.body ).hasClass( 'lazy-enabled' ) ) {
		$( window ).on( 'scroll resize', $.debounce( 500, preloadPostsSwipers ) );
	}
	function preloadPostsSwipers() {
		for ( var i in all_swipers ) {
			preloadPostsSwiper( i );
		}
		if ( ! all_swipers_size ) {
			$( window ).off( 'scroll resize', $.debounce( 500, preloadPostsSwipers ) );
		}
	}

	function preloadPostsSwiper( i ) {
		if ( $( document.body ).hasClass( 'lazy-enabled' ) ) {
			var item = all_swipers[ i ],
			    self = item.container;
			if ( $.abovethetop( self, settings ) ||
				$.leftofbegin( self, settings ) ) {
				/* Nothing. */
			} else if ( ! $.belowthefold( self, settings ) && ! $.rightoffold( self, settings ) ) {
				item.params.lazyLoading      = true;
				item.params.onLazyImageReady = function ( swiper ) {
					$( window ).trigger( 'wowmall_images_resize' );
				};
				item.onResize();
				delete all_swipers[ i ];
				all_swipers_size --;
			}
		}
	}

	$( '.wowmall-posts-carousel > .swiper-container' ).each( function ( n ) {
		var i = 'key_' + n;
		all_swipers_size ++;
		all_swipers[ i ] = swiper[ n ] = new Swiper( $( this ), {
			slidesPerView               : $( this ).data( 'visible' ),
			spaceBetween                : 40,
			nextButton                  : '#swiper-button-next' + $( this ).attr( 'id' ),
			prevButton                  : '#swiper-button-prev' + $( this ).attr( 'id' ),
			autoplay                    : mobile ? 5000 : null,
			preloadImages               : ! $( document.body ).hasClass( 'lazy-enabled' ),
			lazyLoadingInPrevNext       : true,
			lazyLoadingOnTransitionStart: true,
			watchSlidesProgress         : true,
			watchSlidesVisibility       : true,
			breakpoints                 : {
				1460: {
					slidesPerView: 3 < $( this ).data( 'visible' ) ? 3 : $( this ).data( 'visible' )
				},
				1019: {
					slidesPerView: 2 < $( this ).data( 'visible' ) ? 2 : $( this ).data( 'visible' )
				},
				559 : {
					slidesPerView: 1
				}
			}
		} );
		preloadPostsSwiper( i );
	} );
}( jQuery ) );