(function ( $ ) {
	'use strict';
	if ( 'undefined' === typeof wc_single_product_params ) {
		return false;
	}
	var indent = parseInt( $( document.body ).css( 'paddingTop' ), 10 );
	indent     = $( '.header-sticky-wrapper' ).length ? indent + $( '.header-sticky-wrapper' ).height() : indent;
	$( document.body )
		.on( 'init', '#wc-product-collapse', function () {
			var hash = window.location.hash,
				url  = window.location.href;
			if ( 0 <= hash.toLowerCase().indexOf( 'comment-' ) || '#reviews' === hash || 0 < url.indexOf( 'comment-page-' ) || 0 < url.indexOf( 'cpage=' ) ) {
				$( '#heading-reviews' ).trigger( 'click' );
			}
		} )
		.on( 'click', 'a.woocommerce-review-link', function () {
			$( '#heading-reviews' ).trigger( 'click' );
			/*$( 'html, body' ).animate( {
				scrollTop: $( '#heading-reviews' ).offset().top - indent
			} );*/
		} )
		.on( 'click', '#respond span.stars i', function () {
			var $star = $( this );
			if ( $star.hasClass( 'active' ) ) {
				return false;
			}
			$star.closest( '#respond' ).find( '#rating' ).val( $star.text() );
			$star.addClass( 'active' ).siblings().removeClass( 'active' ).closest( '.stars' ).addClass( 'selected' );
			return false;
		} )
		.on( 'submit', '#respond #commentform', function () {
			if ( 'undefined' !== typeof wc_single_product_params && !$( this ).find( '#rating ' ).val() && wc_single_product_params.review_rating_required === 'yes' ) {
				window.alert( wc_single_product_params.i18n_required_rating_text );
				return false;
			}
		} );
	$( '#wc-product-collapse' ).trigger( 'init' );

	function stick( event ) {
		var $stick = $( '.stick-in-parent' );
		var top    = null;
		if ( $stick.length ) {
			var $wrapper = $stick.closest( '.wrapper-sticky' ),
				$parent  = $wrapper.parent();
			if ( $stick.height() !== $parent.height() ) {
				$wrapper.css( {
					height: $stick.height()
				} );
			}
			if ( $stick.height() > $parent.height() ) {
				top       = 0;
				var start = $parent.offset().top,
					end   = $parent.offset().top + $parent.height();

				if ( $stick.offset().top < start ) {
					top = start - $wrapper.offset().top;
				}
				else if ( ( $stick.offset().top + $stick.height() ) > end ) {
					top = end - $stick.height() - $wrapper.offset().top;
				}
				if ( top < 0 ) {
					top = 0
				}
			}
			console.log('123');
			if ( null !== top ) {
				if ( $stick.hasClass( 'sticky' ) ) {
					top = top + $wrapper.offset().top - $( window ).scrollTop();
				}
				$stick.animate( {
					top: top
				}, {
					complete: function () {
						$( '.stick-in-parent' ).hcSticky( 'reinit' );
						$( window ).trigger( 'scroll' );
						if ( 'shown' === event.type && 'undefined' !== wcSingleParams && wcSingleParams.scroll_to_tab ) {
							$( 'html, body' ).animate( {
								scrollTop: $( event.target ).parent().offset().top - indent
							} );
						}
					}
				} );
			}
			else {
				if ( 'shown' === event.type && 'undefined' !== wcSingleParams && !!wcSingleParams.scroll_to_tab ) {
					$( 'html, body' ).animate( {
						scrollTop: $( event.target ).parent().offset().top - indent
					} );
				}
			}
		}
	}

	$( '.wc-product-collapse .panel-collapse' ).on( 'hidden.bs.collapse', stick ).on( 'shown.bs.collapse', function ( event ) {
		stick( event );
		if( ! $( '.stick-in-parent' ).length && 'undefined' !== wcSingleParams && !!wcSingleParams.scroll_to_tab ) {
			indent = parseInt( $( document.body ).css( 'paddingTop' ), 10 );
			indent     = $( '.header-sticky-wrapper' ).length ? indent + $( '.header-sticky-wrapper' ).height() : indent;
			$( 'html, body' ).animate( {
				scrollTop: $( event.target ).parent().offset().top - indent
			} );
		}
		$( '.wc-product-collapse .panel-collapse' ).on( 'hidden.bs.collapse', stick );
	} ).on( 'show.bs.collapse', function () {
		$( '.wc-product-collapse .panel-collapse' ).off( 'hidden.bs.collapse', stick );
	} );
	$( '.wc-product-collapse .panel-collapse:last' ).on( 'shown.bs.collapse', function () {
		$( this ).closest( '.collapse-panel' ).addClass( 'shown' );
	} ).on( 'hidden.bs.collapse', function () {
		$( this ).closest( '.collapse-panel' ).removeClass( 'shown' );
	} );
	$( '.stick-in-parent' ).hcSticky( {
		top: indent + 20
	} );
	$( window ).on( 'resize', $.debounce( 100, function () {
		if( $('.wrapper-sticky').length ) {
			$( '.stick-in-parent' ).hcSticky( 'reinit' );
		}
		$( window ).trigger( 'scroll' );
	} ) );
	$( '.variations_form' )
		.on( 'reset_image', function ( event ) {
			var $form              = $( event.target ),
				$product           = $form.closest( '.product' ),
				$product_link      = $product.find( 'div.images a:eq(0)' ),
				$product_zoom_link = $product.find( 'div.images a.zoom:eq(0)' ),
				$product_zoom      = $product.find( 'div.images span.zoom' );
			if ( $product_zoom.length ) {
				$product_zoom.wc_reset_variation_attr( 'data-url' );
			}
			if ( undefined !== $product_link.data( 'o_href' ) && !$product_link.data( 'o_href' ).length ) {
				setTimeout( function () {
					$product_link.removeAttr( 'href' );
				}, 100 );
			}
			else {
				$product_zoom_link.trigger( 'zoom.destroy' );
				$.wowmall_wc_zoom( $product_zoom_link[0] );
				$.wowmall_lazy_images();
			}
			$form.off( 'found_variation', wowmall_found_variation ).on( 'found_variation', wowmall_found_variation );
		} )
		.on( 'wc_variation_form', function ( event ) {
			var $form = $( event.target );
			$form.on( 'update_variation_values', function () {
				$form.off( 'found_variation', wowmall_found_variation ).on( 'found_variation', wowmall_found_variation );
			} ).trigger( 'check_variations' );
		} );

	function wowmall_found_variation( event, variation ) {
		var $form         = $( event.target ),
			$product      = $form.closest( '.product' ),
			$product_link = $product.find( 'div.images a:eq(0)' ),
			$product_zoom = $product.find( 'div.images span.zoom' );
		if ( variation && variation.image.src && variation.image.src.length > 1 ) {
			if ( $product_zoom.length ) {
				$product_zoom.wc_set_variation_attr( 'data-url', variation.image.full_src );
			}
		}
		else {
			if ( undefined !== $product_link.data( 'o_href' ) && !$product_link.data( 'o_href' ).length ) {
				setTimeout( function () {
					$product_link.removeAttr( 'href' );
				}, 100 );
			}
			if ( $product_zoom.length ) {
				$product_zoom.wc_reset_variation_attr( 'data-url' );
			}
		}
		var el = $( 'div.product div.images .zoom:eq(0)' )[0];
		if ( 'undefined' === typeof el ) {
			return;
		}
		$( el ).trigger( 'zoom.destroy' );
		$.wowmall_wc_zoom( el );
		$.wowmall_lazy_images();
	}

	$.wowmall_wc_zoom = function( el ) {
		if ( $().zoom && 'undefined' !== typeof el && ( 'undefined' !== typeof $( el ).attr( 'data-url' ) || 'undefined' !== typeof el.href ) ) {
			var url = 'undefined' !== typeof $( el ).data( 'url' ) ? $( el ).attr( 'data-url' ) : el.href,
				img = $( el ).find( 'img' ),
				src = img.hasClass( 'swiper-lazy' ) && !img.hasClass( 'swiper-lazy-loaded' ) ? img.attr( 'data-src' ) : img.attr( 'src' );
			if ( url !== src ) {
				$( el ).zoom( {
					url: url
				} );
			}
		}
	};

	if ( $().zoom ) {
		$( '.product .images .zoom' ).each( function () {
			$.wowmall_wc_zoom( this );
		} );
		$( '.variations_form' ).on( 'reset_image', function () {
			var el = $( '.product .images .zoom:first' )[0];
			if ( 'undefined' === typeof el ) {
				return;
			}
			$( el ).trigger( 'zoom.destroy' );
			$.wowmall_wc_zoom( el );
		} );
	}

	var upsells = $( '#up-sells' );
	new Swiper( upsells, {
		slidesPerView:   upsells.data( 'visible' ),
		spaceBetween:    40,
		nextButton:      upsells.parent().find( '.swiper-button-next' ),
		prevButton:      upsells.parent().find( '.swiper-button-prev' ),
		onAfterResize:   function () {
			if( $('.wrapper-sticky').length ) {
				$( '.stick-in-parent' ).hcSticky( 'reinit' );
			}
			$( window ).trigger( 'scroll' );
		},
		onInit:          function () {
			if( $('.wrapper-sticky').length ) {
				$( '.stick-in-parent' ).hcSticky( 'reinit' );
			}
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
				slidesPerView: 2
			},
			1140: {
				slidesPerView: 1
			},
			767:  {
				slidesPerView: 2
			},
			559:  {
				slidesPerView: 1
			}
		}
	} );
	$( '.wowmall-size-guides' ).magnificPopup( {
		type:            'image',
		delegate:        'a',
		tLoading:        wowmallParams.preloader,
		fixedContentPos: true,
		mainClass:       'wowmall-single-product-lightbox mfp-fade mfp-with-zoom',
		closeMarkup:     '<button title="%title%" type="button" class="mfp-close"></button>',
		image:           {
			tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
			markup: '<div class="mfp-figure">' +
					'<div class="mfp-close"></div>' +
					'<figure>' +
					'<div class="mfp-img"></div>' +
					'<figcaption>' +
					'</figcaption>' +
					'</figure>' +
					'</div>'
		}
	} );

	$( 'form.variations_form' ).on( 'wc_additional_variation_images_frontend_image_swap_callback', function ( event, response, gallrery_class ) {
		if ( response.gallery_images.length ) {
			$( gallrery_class ).html( response.gallery_images );
		}
		else {
			$.wc_additional_variation_images_frontend.imageSwapOriginal();
		}
	} );

	function initGroupedCart() {
		$( '.group_table input.qty' ).each( function () {
			$( 'form.cart' ).append( '<input type=hidden name=' + $( this ).attr( 'name' ) + ' value=' + $( this ).val() + '>' );
			$( this ).on( 'change', function () {
				$( 'form.cart input[name="' + $( this ).attr( 'name' ) + '"]' ).val( $( this ).val() );

			} )
		} );
	}

	initGroupedCart();
	$( document.body ).on( 'wowmall_wc_quick_view_opened', initGroupedCart );

})( jQuery );