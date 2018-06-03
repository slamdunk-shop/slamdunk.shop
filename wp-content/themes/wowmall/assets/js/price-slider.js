(function ( $ ) {
	'use strict';
	var form = $( 'input#max_price' ).closest( 'form' );
	if( ! form.find( 'button' ).length ) {
		var min_val = parseInt( $( 'input#min_price' ).val(), 10 ),
			max_val = parseInt( $( 'input#max_price' ).val(), 10 );
		$( document.body ).on( 'price_slider_change', function ( event, min, max ) {
			if ( min !== min_val || max !== max_val ) {
				form.submit();
			}
		} );
	}
	$( 'input#min_price[type="number"], input#max_price[type="number"]' ).on( 'change', function () {
		if( $( this ).val() > $( this ).attr('max') ) {
			$( this ).val($( this ).attr('max'));
		}
		if( $( this ).val() < $( this ).attr('min') ) {
			$( this ).val($( this ).attr('min'));
		}
		$( '.price_slider' ).slider( "values", [ $( 'input#min_price[type="number"]' ).val(), $( 'input#max_price[type="number"]' ).val() ] );
	} );
	$( function () {
		$( 'input#min_price[type="number"], input#max_price[type="number"]' ).show();
	} );
})( jQuery );