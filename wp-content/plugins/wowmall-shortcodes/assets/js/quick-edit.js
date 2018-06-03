( function( $ ) {
	'use strict';
	$( '#the-list' ).on( 'click', '.editinline', function() {

		var post_id = $( this ).closest( 'tr' ).attr( 'id' );

		post_id = post_id.replace( 'post-', '' );

		var $new = $( '#woocommerce_inline_' + post_id + '_new' ).text();

		if ( 'yes' === $new ) {
			$( 'input[name="_new"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
		} else {
			$( 'input[name="_new"]', '.inline-edit-row' ).removeAttr( 'checked' );
		}
	});
} )( jQuery );
