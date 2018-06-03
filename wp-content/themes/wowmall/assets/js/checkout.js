(function ( $ ) {
	'use strict';
	$( 'input.input-text[name=coupon_code]' ).on( 'change', function ( e ) {
		$( 'input[type=hidden][name=coupon_code]' ).val( $( this ).val() );
	} );
	$( document.body ).on( 'click', 'a.showlogin', function ( e ) {
		e.preventDefault();
		$( 'div.login' ).slideToggle();
	} ).on( 'update_checkout', function () {
		$( 'form.checkout' ).removeClass( 'processing' ).unblock();
	} );
	$( 'form.checkout_coupon' ).on( 'submit', function ( e ) {
		var $form = $( 'form.checkout' );
		if ( $( this ).is( '.processing' ) ) {
			return false;
		}
		$form.addClass( 'processing' ).block( {
			message   : null,
			overlayCSS: {
				background: '#fff',
				opacity   : 0.6
			}
		} );
	} );
})( jQuery );