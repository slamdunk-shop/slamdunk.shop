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

	function preloadBrandsSwipers() {
		for ( var i in all_swipers ) {
			preloadBrandsSwiper( i );
		}
		if ( ! all_swipers_size ) {
			$( window ).off( 'scroll resize', $.debounce( 500, preloadBrandsSwipers ) );
		}
	}

	if ( $( document.body ).hasClass( 'lazy-enabled' ) ) {
		$( window ).on( 'scroll resize', $.debounce( 500, preloadBrandsSwipers ) );
	}
	function preloadBrandsSwiper( i ) {
		if ( $( document.body ).hasClass( 'lazy-enabled' ) ) {
			var item = all_swipers[ i ],
			    self = item.container;
			if ( $.abovethetop( self, settings ) ||
				$.leftofbegin( self, settings ) ) {
				/* Nothing. */
			} else if ( ! $.belowthefold( self, settings ) && ! $.rightoffold( self, settings ) ) {
				item.params.lazyLoading                  = true;
				item.params.lazyLoadingInPrevNext        = true;
				item.params.lazyLoadingOnTransitionStart = true;
				item.params.onLazyImageReady             = function ( swiper ) {
					$(window).trigger('wowmall_images_resize');
				};
				item.onResize();
				delete all_swipers[ i ];
				all_swipers_size --;
			}
		}
	}

	$( '.wowmall-brands-carousel .swiper-container' ).each( function ( n ) {
		var i = 'key_' + n;
		all_swipers_size ++;
		all_swipers[ i ] = swiper[ n ] = new Swiper( $( this ), {
			slidesPerView        : $( this ).data( 'visible' ),
			spaceBetween         : 116,
			nextButton           : '#swiper-button-next' + $( this ).attr( 'id' ),
			prevButton           : '#swiper-button-prev' + $( this ).attr( 'id' ),
			autoplay             : mobile ? 5000 : null,
			loop                 : true,
			preloadImages        : ! $( document.body ).hasClass( 'lazy-enabled' ),
			watchSlidesProgress  : true,
			watchSlidesVisibility: true,
			breakpoints          : {
				1899: {
					spaceBetween: 30
				},
				1199: {
					slidesPerView: 7 < $( this ).data( 'visible' ) ? 7 : $( this ).data( 'visible' ),
					spaceBetween : 30
				},
				1069: {
					slidesPerView: 6 < $( this ).data( 'visible' ) ? 6 : $( this ).data( 'visible' ),
					spaceBetween : 30
				},
				929 : {
					slidesPerView: 5 < $( this ).data( 'visible' ) ? 5 : $( this ).data( 'visible' ),
					spaceBetween : 30
				},
				799 : {
					slidesPerView: 4 < $( this ).data( 'visible' ) ? 4 : $( this ).data( 'visible' ),
					spaceBetween : 30
				},
				579 : {
					slidesPerView: 3 < $( this ).data( 'visible' ) ? 3 : $( this ).data( 'visible' ),
					spaceBetween : 30
				},
				479 : {
					slidesPerView: 2 < $( this ).data( 'visible' ) ? 2 : $( this ).data( 'visible' ),
					spaceBetween : 30
				}
			}
		} );
		preloadBrandsSwiper( i );
	} );
}( jQuery ) );