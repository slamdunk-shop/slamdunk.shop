(function ( $ ) {
	'use strict';
	$( 'div#clock' ).countdown( $( 'div#clock' ).data( 'countdown' ), function ( event ) {
		$( this ).html( event.strftime( $( 'div#clock' ).data( 'format' ) ) );
	} );
})( jQuery );