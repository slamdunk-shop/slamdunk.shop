/* global confirm, redux, redux_change */
(function ( $ ) {
	'use strict';
	$( '.wowmall-activate-license' ).off( 'click' ).on( 'click', function () {
		var data         = {},
			input = $( this ).closest('fieldset').find( 'input[type="text"]' );
		data.code        = input.val();
		if( ! data.code.length ) {
			alert( 'Empty Code!' );
			return;
		}
		data._ajax_nonce = $( this ).closest('fieldset').find( 'input[name="_ajax_nonce"]' ).val();
		data.field_id    = input.attr('id');
		$( '.wowmall-activate-license' ).parent().find( '.spinner' ).addClass('is-active');
		wp.ajax.post(
			'wowmall_activate_license',
			data
		).done( function ( response ) {
			input.attr({
				disabled: 'disabled',
				readonly: 'readonly'
			});
			$( '.wowmall-activate-license' ).parent().remove();
			alert( response );
			location.reload();
		} ).fail( function ( response ) {
			$( '.wowmall-activate-license' ).parent().find( '.spinner' ).removeClass('is-active');
			if ( undefined !== typeof response.status && 403 == response.status ) {
				response = response.statusText;
			}
			alert( response );
		} );
	} );

	$('.wrap-importer.theme.not-activated').off('click').on('click', function(e) {
		e.preventDefault();
		var id = $('.redux-container-license').eq(0).closest( '.redux-group-tab' ).data('rel');
		$( 'a#' + id + '_section_group_li_a' ).click();
	});

})( jQuery );