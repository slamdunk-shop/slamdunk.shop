(function ( $ ) {
	'use strict';
	/**
	 * Append a field to a form
	 *
	 */
	$.fn.appendField = function ( data ) {
		// for form only
		if ( ! this.is( 'form' ) ) return;
		// wrap data
		if ( ! $.isArray( data ) && data.name && data.value ) {
			data = [ data ];
		}
		var $form = this;
		// attach new params
		$.each( data, function ( i, item ) {
			$( '<input/>' )
				.attr( 'type', 'hidden' )
				.attr( 'name', item.name )
				.val( item.value ).appendTo( $form );
		} );
		return $form;
	};
	function html5Form_submit() {
		$( 'form[id]' ).submit( function ( e ) {
			// serialize data
			var data = $( '[form=' + this.id + ']' ).serializeArray();
			// append data to form
			$( this ).appendField( data );
		} ).each( function () {
			var form    = this,
			    $fields = $( '[form=' + this.id + ']' );
			$fields.filter( 'button, input' ).filter( '[type=reset],[type=submit]' ).click( function ( event ) {
				event.preventDefault();
				var type = this.type.toLowerCase();
				if ( type === 'reset' ) {
					// reset form
					form.reset();
					// for elements outside form
					$fields.each( function () {
						this.value   = this.defaultValue;
						this.checked = this.defaultChecked;
					} ).filter( 'select' ).each( function () {
						$( this ).find( 'option' ).each( function () {
							this.selected = this.defaultSelected;
						} );
					} );
				} else {
					$( form ).appendField( { name: this.name, value: this.value } ).submit();
				}
			} );
		} ).keydown( function ( event ) {
			if ( 13 === event.which ) {
				event.preventDefault();
				var $el  = $( event.target ),
				    form = $el.attr( 'form' );
				if ( 'undefined' !== typeof form && $( 'form#' + form ).length ) {
					$( 'form#' + form ).submit();
				} else {
				}
			}
		} );
	}

	html5Form_submit();
	$( document.body ).on( 'updated_wc_div cart_page_refreshed', html5Form_submit );
	$( document ).on( 'animate_svg', function () {
		var load_event = document.createEvent( "HTMLEvents" );
		load_event.initEvent( "fakesmile_init", true, true );
		window.document.dispatchEvent( load_event );
	} );
})( jQuery );