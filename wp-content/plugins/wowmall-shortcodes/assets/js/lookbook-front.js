( function ($) {
	'use strict';
	var products = [],
		rtl = $( document.body ).hasClass( 'rtl' );
	$('.wowmall-lookbook-slide-wrapper .wowmall-lookbook-point').on( 'click', open_popup );

	function add_popup( point, id ) {
		var lookbook = point.closest('.wowmall-lookbook-slide-wrapper');

		lookbook.append( products[ id ] );
		if ( $.isFunction( $.fn.lazyload ) ) {
			$('.wowmall-lookbook-popup-content img.swiper-lazy:not(.swiper-lazy-loaded)' ).each(function (){
				var img = $(this);
				img.lazyload( {
					data_attribute: "src",
					load          : function () {
						img.addClass( 'swiper-lazy-loaded' );
						var sizes = img.data('sizes'),
						    srcset = img.data('srcset');
						img.attr({
							sizes: sizes,
							srcset: srcset
						}).removeAttr('data-srcset data-sizes data-src');
						if( img.hasClass('lazy-svg') ) {
							img.next('svg').remove();
						}
					}
				} );
			});
		}
		place_point();

		$(window).on( 'resize', $.debounce(500, place_point ));
	}

	function place_point() {
		$('.wowmall-lookbook-popup-content').each(function (  ) {
			var popup = $(this),
			    id = popup.data('id'),
			    point = $('.wowmall-lookbook-point#'+id),
			    lookbook = point.closest('.wowmall-lookbook-slide-wrapper'),
			    popup_w = popup.outerWidth(),
			    popup_h = popup.outerHeight(),
			    lokbook_w = lookbook.outerWidth(),
			    lokbook_h = lookbook.outerHeight(),
			    point_l = parseFloat(point.css('left')),
			    point_t = parseFloat(point.css('top')),
			    left,
			    right,
			    top,
			    bottom,
			    popup_class = '';

			if ( lokbook_w < popup_w + point_l ) {
				if ( popup_w + 10 <= point_l ) {
					left  = 'auto';
					right = (lokbook_w - point_l) * 100 / lokbook_w + '%';
				} else {
					popup_class = 'centered'
				}
			} else {
				left  = point_l * 100 / lokbook_w + '%';
				right = 'auto';
			}
			if ( lokbook_h < popup_h + point_t ) {
				if ( popup_h + 10 <= point_t ) {
					top    = 'auto';
					bottom = (lokbook_h - point_t) * 100 / lokbook_h + '%';
				} else {
					popup_class = 'centered'
				}
			} else {
				top    = point_t * 100 / lokbook_h + '%';
				bottom = 'auto';
			}
			if( 'centered' == popup_class ) {
				left = '50%';
				right = 'auto';
				top = '50%';
				bottom = 'auto';
			}
			popup.css({
				left: left,
				top: top,
				bottom: bottom,
				right: right
			}).removeClass('centered').addClass(popup_class).find( '.close' ).on( 'click', close_popup );
		});
	}

	function open_popup( event ) {
		if ( 'undefined' === typeof wowmallLookbook || event.target !== event.currentTarget ) {
			return;
		}
		var point = $( event.target ),
		    id    = point.attr( 'id' );
		if ( point.hasClass( 'active' ) || point.hasClass( 'disabled' ) ) {
			return;
		}
		close_popup();
		point.off( 'click', open_popup ).addClass( 'active' );
		if ( 'undefined' === typeof products[ id ] ) {
			$('.wowmall-lookbook-slide-wrapper .wowmall-lookbook-point').not('.active').addClass('disabled');
			point.append( '<span class="wowmall-lookbook-loader"/>' );
			$.post( wowmallLookbook.ajaxurl, { action: 'wowmall_lookbook_get_product', id: id }, function ( responce ) {
				point.find( '.wowmall-lookbook-loader' ).remove();
				products[ id ] = responce.data;
				$('.wowmall-lookbook-slide-wrapper .wowmall-lookbook-point').removeClass('disabled');
				add_popup( point, id );
			} );
		} else {
			add_popup( point, id );
		}
		point.off( 'click', open_popup );
	}
	function close_popup() {
		$(window).off( 'resize', $.debounce(500, place_point ));
		$('.wowmall-lookbook-popup-content').remove();
		$('.wowmall-lookbook-slide-wrapper .wowmall-lookbook-point').removeClass('active').on( 'click', open_popup );
	}
}(jQuery) );