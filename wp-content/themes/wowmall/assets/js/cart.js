(function ( $ ) {
	'use strict';

	crossSells();
	$( document.body ).on( 'updated_wc_div cart_page_refreshed', crossSells );
	function shippingCalculatorSelect() {
		$( '.shipping-calculator-form select' ).each( function () {
			var self = $( this );
			if ( self.hasClass( 'select2-hidden-accessible' ) ) {
				self.select2( 'destroy' );
			}
			self.select2();
		} );
	}

	$( document.body ).on( 'country_to_state_changing updated_wc_div updated_cart_totals cart_page_refreshed', shippingCalculatorSelect );
	function crossSells() {
		var crosssells = $( '#cross-sells' );
		new Swiper( crosssells, {
			slidesPerView:   crosssells.data( 'visible' ),
			spaceBetween:    40,
			nextButton:      crosssells.parent().find( '.swiper-button-next' ),
			prevButton:      crosssells.parent().find( '.swiper-button-prev' ),
			onAfterResize:   function () {
				$( window ).trigger( 'scroll' );
			},
			onInit:          function () {
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
					slidesPerView: 3
				},
				1199: {
					slidesPerView: 2
				},
				991:  {
					slidesPerView: 3
				},
				869:  {
					slidesPerView: 2
				},
				559:  {
					slidesPerView: 1
				}
			}
		} );
	}

	$( document.body ).on( 'updated_wc_div', function () {
		$( document ).trigger( 'wowmall_wc_products_changed' );
	} );

})( jQuery );