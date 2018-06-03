( function ($) {
	'use strict';
	var slide = $('#wowmall-lookbook-slide img'),
		popup = $( '.wowmall-lookbook-popup' ),
		prev_id = $('#wowmall-lookbook-popup-prev-id'),
		select = $('#wowmall-lookbook-popup-select'),
		id = $('#wowmall-lookbook-slide').closest('form#post').find('input#post_ID').val();

	function movecircle( event ) {
		$( '.wowmall-lookbook-point.mouse' ).css({top: ( event.pageY - $(this).offset().top ), left: ( event.pageX - $(this).offset().left )});
	}

	function add_popup_to_movecircle( event ) {
		$( '.wowmall-lookbook-point.mouse' ).removeClass( 'mouse' ).draggable({
			drag: function( event, ui ) {
				popup.css({top: ui.position.top, left: ui.position.left})
			}
		});
		popup.css({top: ( event.pageY - $(this).offset().top ), left: ( event.pageX - $(this).offset().left )}).show().find( '.btn-remove' ).hide();
		$( '.wowmall-lookbook-point[id]' ).off( 'click', open_popup );
		select.find('option').not('[disabled]').first().prop('selected', true);
		slide.off( 'mousemove', create_cicrle ).off( 'mouseleave', remove_cicrle ).off( "mousemove", movecircle ).off( 'click', add_popup_to_movecircle );
		prev_id.val('');
	}

	function create_cicrle() {
		if( ! $( '.wowmall-lookbook-point.mouse' ).length )
			slide.after('<span class="wowmall-lookbook-point mouse"/>')
	}

	function remove_cicrle() {
		$( '.wowmall-lookbook-point.mouse' ).remove();
	}

	slide.on( 'mousemove', create_cicrle ).on( 'mouseleave', remove_cicrle ).on( "mousemove", movecircle ).on( 'click', add_popup_to_movecircle );

	popup.find( '.btn-cancel' ).on( 'click', function (  ) {
		popup.hide();
		var prev_id_val = prev_id.val();
		$( '.wowmall-lookbook-point' ).not('.mouse').not('[id]').remove();
		if( '' !== prev_id_val ) {
			$( '.wowmall-lookbook-point#' + prev_id_val ).draggable( "destroy" );
			select.find('option[value=' + prev_id_val + ']').prop('disabled', true);
		}
		$( '.wowmall-lookbook-point[id]' ).on( 'click', open_popup );
		slide.on( 'mousemove', create_cicrle ).on( 'mouseleave', remove_cicrle ).on( "mousemove", movecircle ).on( 'click', add_popup_to_movecircle );
	} );

	popup.find( '.btn-ok' ).on( 'click', function (  ) {
		var product_id = select.val(),
			placeX = ( popup.offset().left - slide.offset().left ) * 100 / slide.width(),
			placeY = ( popup.offset().top - slide.offset().top ) * 100 / slide.height(),
			prev_id_val = prev_id.val();

		if( '' !== prev_id_val ) {
			$( '.wowmall-lookbook-point#' + prev_id_val ).append( '<span class="wowmall-lookbook-loader"/>' );
		} else {
			$( '.wowmall-lookbook-point' ).not( '.mouse' ).not( '[id]' ).append( '<span class="wowmall-lookbook-loader"/>' );
		}

		$.post( ajaxurl, { action: 'wowmall_lookbook_set_product', id: id, product_id: product_id, placeX: placeX, placeY: placeY, prev_id: prev_id.val() }, function ( responce ) {
			if( '' !== prev_id_val ) {
				$( '.wowmall-lookbook-point#' + prev_id_val ).draggable( "destroy" ).attr( { id: product_id } ).find( '.wowmall-lookbook-loader' ).remove();
				select.find('option[value=' + prev_id_val + ']').prop('disabled', false);
			} else {
				$( '.wowmall-lookbook-point' ).not( '.mouse' ).not( '[id]' ).attr( { id: product_id } ).find( '.wowmall-lookbook-loader' ).remove();
			}
			select.find('option[value=' + product_id + ']').prop('disabled', true);
			popup.hide();
			$( '.wowmall-lookbook-point[id]' ).on( 'click', open_popup );
			slide.on( 'mousemove', create_cicrle ).on( 'mouseleave', remove_cicrle ).on( "mousemove", movecircle ).on( 'click', add_popup_to_movecircle );
		} );
	} );

	$( '.wowmall-lookbook-point[id]' ).on( 'click', open_popup );

	function open_popup( event ) {
		var id = $(event.target).attr('id');
		$( '.wowmall-lookbook-point[id]' ).off( 'click', open_popup );
		popup.css({top: $(event.target).css('top'), left: $(event.target).css('left')}).show();
		select.find('option').prop('selected', false);
		select.find('option[value=' + id + ']').prop('disabled', false).prop('selected', true);
		prev_id.val(id);
		popup.find( '.btn-remove' ).show();
		slide.off( 'mousemove', create_cicrle ).off( 'mouseleave', remove_cicrle ).off( "mousemove", movecircle ).off( 'click', add_popup_to_movecircle );
		$( event.target ).draggable({
			drag: function( event, ui ) {
				popup.css({top: ui.position.top, left: ui.position.left})
			}
		});
	}

	popup.find( '.btn-remove' ).on( 'click', function (  ) {
		var product_id = prev_id.val();
		$( '.wowmall-lookbook-point#' + product_id ).append( '<span class="wowmall-lookbook-loader"/>' );
		$.post( ajaxurl, { action: 'wowmall_lookbook_unset_product', id: id, product_id: product_id }, function ( responce ) {
			$( '.wowmall-lookbook-point#' + product_id ).remove();
			select.find('option[value=' + product_id + ']').prop('disabled', false);
			popup.hide();
			$( '.wowmall-lookbook-point[id]' ).on( 'click', open_popup )
			slide.on( 'mousemove', create_cicrle ).on( 'mouseleave', remove_cicrle ).on( "mousemove", movecircle ).on( 'click', add_popup_to_movecircle );
			prev_id.val('');
		} );
	} );

}(jQuery) );