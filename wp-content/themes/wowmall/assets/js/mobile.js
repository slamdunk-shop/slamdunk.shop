(function ( $ ) {
	'use strict';

	var wowmall_vc_carousel_swipers = [],
		wowmall_vc_carousel_enable  = false,
		scrollTop;

	function wowmall_vc_accordion() {
		$( '.mobile-accordion' ).each( function () {
			var accordion = $( this );
			if ( 768 > window.innerWidth ) {
				if ( !accordion.hasClass( 'open' ) ) {
					if ( !accordion.find( '.mobile-accordion-more' ).length ) {
						accordion
							.find( 'p:first' )
							.after( '<a href="#" class="mobile-accordion-more btn btn-inline">' + wowmallMobileParams.readmore_text + '</a>' );
						accordion
							.find( 'p:last' )
							.after( '<a href="#" class="mobile-accordion-close btn btn-inline">' + wowmallMobileParams.readless_text + '</a>' );
					}
					var button    = accordion.find( '.mobile-accordion-more' ),
						close_btn = accordion.find( '.mobile-accordion-close' );
					accordion.css( { maxHeight: button.position().top + 134 } );
					button.on( 'click', function ( e ) {
						e.preventDefault();
						accordion.addClass( 'open' );
					} );
					close_btn.on( 'click', function ( e ) {
						e.preventDefault();
						accordion.removeClass( 'open' );
					} );
				}
			}
			else {
				accordion.css( { maxHeight: 'inherit' } );
				if ( accordion.find( '.mobile-accordion-more' ).length ) {
					accordion.find( '.mobile-accordion-more' ).remove();
				}
				if ( accordion.find( '.mobile-accordion-close' ).length ) {
					accordion.find( '.mobile-accordion-close' ).remove();
				}
			}
		} );
	}

	function wowmall_vc_carousel() {
		if ( 768 > window.innerWidth ) {
			if ( !wowmall_vc_carousel_enable ) {
				$( '.wowmall-vc-carousel' ).each( function ( n ) {
					var carousel = $( this );
					carousel.wrapInner( '<div class="swiper-container"/>' ).find( '> .swiper-container' ).wrapInner( '<div class="swiper-wrapper"/>' ).find( '> .swiper-wrapper' ).find( '> .vc_column_container' ).addClass( 'swiper-slide' );
					wowmall_vc_carousel_swipers[n] = new Swiper( carousel.find( '> .swiper-container' ), {
						grabCursor:      false,
						autoplay:        5000,
						onTransitionEnd: function () {
							$.wowmall_lazy_images();
						}
					} );
					wowmall_vc_carousel_enable     = true;
				} );
			}
		}
		else {
			if ( wowmall_vc_carousel_enable ) {
				wowmall_vc_carousel_swipers.forEach( function ( item, i, arr ) {
					item.destroy( true, true );
					wowmall_vc_carousel_enable = false;
				} );
				$( '.wowmall-vc-carousel' ).each( function ( n ) {
					var carousel = $( this );
					var inner    = carousel.find( '> .swiper-container' ).find( '> .swiper-wrapper' ).html();
					carousel.html( inner );
					carousel.find( '> .swiper-container' ).remove();
					carousel.find( '> .vc_column_container' ).removeClass( 'swiper-slide' );
				} );
			}
		}
	}

	wowmall_vc_accordion();

	wowmall_vc_carousel();

	$( window ).resize( $.debounce( 500, function () {
		wowmall_vc_carousel();
		wowmall_vc_accordion();
	} ) );

	function mobile_filters_close_listener( event ) {
		if ( !( $( event.target ).is( '.wowmall-sidebar-inner' ) || $( event.target ).closest( '.wowmall-sidebar-inner' ).length ) ) {
			$( '.wowmall-filters-btn' ).on( 'click', mobile_filters_open_listener );
			$( document.body ).off( 'touchmove click', mobile_filters_close_listener )
				.removeClass( 'wowmall-filters-shown' );
			$( 'html' ).removeClass( 'wowmall-prevent-scrolling' ).removeAttr( 'style' );
			$( 'html, body' ).animate( {
				scrollTop: scrollTop
			}, 0 );
		}
	}

	function mobile_filters_open_listener( event ) {
		$( '.wowmall-filters-btn' ).off( 'click', mobile_filters_open_listener );
		event.stopPropagation();
		var body = $( 'body' );
		body.addClass( 'wowmall-filters-shown' );
		scrollTop = body.scrollTop();
		$( 'html' ).css( {
			top: -scrollTop
		} ).addClass( 'wowmall-prevent-scrolling' );
		$( document.body ).on( 'touchmove click', mobile_filters_close_listener );
	}

	$( '.wowmall-filters-btn' ).on( 'click', mobile_filters_open_listener );

	$( '.site-footer .footer-inner > .container .vc_column_container > .vc_column-inner' ).each( function () {
		if ( $( this ).find( '> .wpb_wrapper > .wpb_content_element > .wpb_wrapper' ).children( ':first' ).is( ':header' ) || $( this ).find( '> .wpb_wrapper .widgettitle' ).length || $( this ).find( '> .wpb_wrapper > div:first > :header' ).length || $( this ).find( '> .wpb_wrapper > .aio-icon-component .aio-icon-title' ).length ) {
			$( this ).addClass( 'closed wowmall-toggler' ).find( ' > .wpb_wrapper' ).prepend( '<button class=wowmall-toggle-btn/>' ).find( '.wowmall-toggle-btn' ).on( 'click', function () {
				$( this ).closest( '.vc_column-inner' ).toggleClass( 'closed' );
			} );
		}
	} );
	if ( $( '.wowmall-sidebar-inner' ).length ) {
		var container = $( '.wowmall-sidebar-inner' );
		$( '#secondary img.swiper-lazy:not(.swiper-lazy-loaded), #secondary img[data-wowmall-lazy="swiper-lazy"]:not(.swiper-lazy-loaded)' ).each(
			function () {
				var img = $( this );
				if ( !img.closest( '.swiper-container' ).length ) {
					$.wowmall_lazy_init( img, container );
				}
			}
		);
	}
	if ( $( '#wowmall-wc-mobile-single-images' ).length ) {
		var mobileSingleSwiper = new Swiper( '#wowmall-wc-mobile-single-images', {
			preloadImages:                false,
			lazyLoading:                  true,
			lazyLoadingInPrevNext:        true,
			autoHeight:                   true,
			lazyLoadingOnTransitionStart: true,
			prevButton:                   $( '#wowmall-wc-mobile-single-images .swiper-button-prev' ),
			nextButton:                   $( '#wowmall-wc-mobile-single-images .swiper-button-next' ),
			onReachEnd:                   function () {
				$( '#prev-wowmall-wc-mobile-single-images' ).addClass( 'visible' )
			},
			onSlidePrevStart:             function () {
				$( '#prev-wowmall-wc-mobile-single-images' ).removeClass( 'visible' )
			}
		} );
		$( '.variations_form' ).on( 'reset_image', function () {
			mobileSingleSwiper.slideTo( 0 );
			$( '.variations_form' ).off( 'found_variation', wowmall_mobile_found_variation ).on( 'found_variation', wowmall_mobile_found_variation );
		} );
	}
	function wowmall_mobile_found_variation() {
		mobileSingleSwiper.slideTo( 0 );
	}
})( jQuery );