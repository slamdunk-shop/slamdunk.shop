(function ( $ ) {
	'use strict';

	var swipers = [],
		items   = [];

	function loop_hover_preload( event ) {
		var item = event.currentTarget;
		$( item ).off( 'mouseenter', loop_hover_preload );
		var size = $( item ).find( 'img[class*="size-"]' ).attr( 'class' );
		if ( size ) {
			var over = true;
			$( item ).on( 'mouseleave', function () {
				over = false;
			} ).on( 'mouseenter', function () {
				over = true;
			} );
			$( item ).addClass( 'effect-' + wowmallWcLoop.effect );
			size = size.split( ' ' );
			size.some( function ( i ) {
				if ( 0 === i.indexOf( 'size-' ) ) {
					size = i.split( '-' )[1];
					return;
				}
			} );
			var id = $( item ).closest( '.product' ).attr( 'class' ).split( ' ' );
			id.some( function ( i ) {
				if ( 0 === i.indexOf( 'post-' ) ) {
					id = i.split( '-' )[1];
					return;
				}
			} );
			$.get(
				wowmallWcLoop.shortinit_ajax_url,
				{
					id:     id,
					action: 'wowmall_get_loop_thumb',
					'size': size
				},
				function ( response ) {
					if ( response.data ) {
						$( '<img />' )
							.on( "load", function () {
								$( item )
									.append( $( this ) )
									.off( 'mouseenter mouseleave' );
								if ( over ) {
									$( item ).addClass( 'hover' );
								}
								if ( 'cube' === wowmallWcLoop.effect || 'flip' === wowmallWcLoop.effect ) {
									$( item ).wrapInner( '<div class=swiper-wrapper/>' ).wrapInner( '<div class="swiper-container wc-loop-thumbs-swiper"/>' ).find( 'img' ).addClass( 'swiper-slide' );
									swipers.push( new Swiper( $( item ).find( '.wc-loop-thumbs-swiper' ), {
											effect:       wowmallWcLoop.effect,
											grabCursor:   false,
											speed:        600,
											followFinger: false,
											cube:         {
												shadow:       false,
												slideShadows: true,
												shadowOffset: 20,
												shadowScale:  0.94
											},
											onInit:       function ( swiper ) {
												$( item )
													.addClass( 'preloaded' )
													.on( 'mouseenter focus', function () {
														swiper.slideNext();
													} ).on( 'mouseleave focusout', function () {
													swiper.slidePrev();
												} );
												setTimeout( function () {
													if ( over ) {
														swiper.slideNext();
													}
												}, 100 );
											}
										} )
									);
								}
								else {
									$( item )
										.addClass( 'preloaded' )
										.on( 'mouseenter', function () {
											$( item ).removeClass( 'unhover' ).addClass( 'hover' );
										} )
										.on( 'mouseleave', function () {
											$( item ).removeClass( 'hover' ).addClass( 'unhover' );
										} );
									if ( over ) {
										$( item ).addClass( 'hover' );
									}
								}
							} )
							.attr( {
								src:     response.data.src,
								sizes:   response.data.sizes,
								srcset:  response.data.srcset,
								'class': 'wowmall-hover-img'
							} );
					}
				}
			).fail( function ( response ) {

				if ( 500 === response.status ) {
					wowmallWcLoop.shortinit_ajax_url = wowmallParams.ajax_url;
					loop_hover_preload( event );
				}
			} );
		}
		else {
			$( item ).addClass( 'preloaded' );
		}
	}

	function loop_hover() {

		if ( !!wowmallWcLoop.thumbsHover && $( document.body ).hasClass( 'desktop' ) ) {
			$( '.woocommerce-LoopProduct-link:not(.preloaded)' ).each( function ( i, item ) {
				$( item ).off( 'mouseenter', loop_hover_preload ).on( 'mouseenter', loop_hover_preload )
			} );
		}
	}

	loop_hover();

	$.productThumbsSwipers = function () {
		$( '.wc-loop-product-wrapper *' ).on( 'mouseenter', function () {
			$( this ).closest( '.wc-loop-product-wrapper' ).addClass( 'active' );
		} ).on( 'mouseleave', function () {
			$( this ).closest( '.wc-loop-product-wrapper' ).removeClass( 'active' );
		} );
	};

	function wowmall_color_size_click( el, select ) {
		if ( el.hasClass( 'selected' ) ) {
			return false;
		}
		select.val( el.data( 'value' ) ).trigger( 'change' );
		el.addClass( 'selected' ).siblings().removeClass( 'selected' );
	}

	function wcProductVariations() {
		$( '.variations select:not(.wowmall-color):not(.wowmall-size)' ).select2();
		$( '.variations select.wowmall-color' ).each( function () {
			var select              = $( this ),
				id                  = select.attr( 'id' ) + '-color',
				label               = $( 'label[for="' + select.attr( 'id' ) + '"]' ),
				default_color_label = label.html();
			if ( select.next().hasClass( 'wowmall-color-select' ) ) {
				return;
			}
			label.data( 'default-label', default_color_label );
			$( this ).after( '<div class="wowmall-color-select" id="' + id + '"/>' );
			var new_select = $( '#' + id );
			select.find( 'option[data-color]' ).each( function () {
				var option   = $( this ),
					selected = option.prop( 'selected' ) ? ' selected' : '';
				new_select.append( '<button type="button" title="' + option.text() + '" data-value="' + option.val() + '" class="' + selected + '" style="background-color: ' + option.data( 'color' ) + '">' + option.text() + '</button>' );
				if ( selected ) {
					label.html( default_color_label + ' <span class=wowmall-current-color-label>' + option.text() + '</span>' );
				}
			} );
			select.hide();
			new_select.find( 'button' ).on( 'click', function () {
				wowmall_color_size_click( $( this ), select );
				label.html( default_color_label + ' <span class=wowmall-current-color-label>' + $( this ).attr( 'title' ) + '</span>' );
			} );
		} );
		$( '.variations select.wowmall-size' ).each( function () {
			var select = $( this ),
				id     = select.attr( 'id' ) + '-size';
			if ( select.next().hasClass( 'wowmall-size-select' ) ) {
				return;
			}
			$( this ).after( '<div class="wowmall-size-select" id="' + id + '"/>' );
			var new_select = $( '#' + id );
			select.find( 'option:not(.blank-option)' ).each( function () {
				var option   = $( this ),
					selected = option.prop( 'selected' ) ? ' selected' : '';
				new_select.append( '<button type="button" title="' + option.text() + '" data-value="' + option.val() + '" class="' + selected + '">' + option.text() + '</button>' );
			} );
			select.hide();
			new_select.find( 'button' ).on( 'click', function () {
				wowmall_color_size_click( $( this ), select );
			} );
		} );
		$( '.variations_form' ).off( 'reset_data' ).on( 'reset_data', function () {
				$( '.reset_variations' ).off( 'click' ).on( 'click', clear_colors_and_sizes );
			} )
			.on( 'reset_data', clear_colors_and_sizes )
			.on( 'woocommerce_update_variation_values', function () {
				$( '.variations .wowmall-color-select, .variations .wowmall-size-select' ).each( function () {
					var select = $( this ).parent().find( 'select' );
					$( this ).find( 'button' ).each( function () {
						if ( select.find( 'option[value="' + $( this ).data( 'value' ) + '"]' ).length ) {
							$( this ).show();
						}
						else {
							$( this ).hide();
						}
					} );
				} );
			} );
	}

	wcProductVariations();

	function clear_colors_and_sizes() {
		$( '.variations_form' ).off( 'reset_data', clear_colors_and_sizes );
		$( '.variations select.wowmall-color' ).each( function () {
			var select = $( this );
			$( '#' + select.attr( 'id' ) + '-color button' ).removeClass( 'selected' );
			$( 'label[for="' + select.attr( 'id' ) + '"]' ).html( $( 'label[for="' + select.attr( 'id' ) + '"]' ).data( 'default-label' ) );
		} );
		$( '.variations select.wowmall-size' ).each( function () {
			var select = $( this );
			$( '#' + select.attr( 'id' ) + '-size button' ).removeClass( 'selected' );
		} );
		if ( $().select2 ) {
			$( '.variations select:not(.wowmall-color):not(.wowmall-size)' ).select2();
		}
	}

	function getQuickViewItems() {
		if ( !items.length ) {
			$( '.wowmall-wc-quick-view-button' ).each( function ( i, item ) {
				items.push( {
					src: wowmallParams.ajax_url + '?url=' + encodeURIComponent( $( item ).closest( '.product' ).find( '.woocommerce-LoopProduct-link' ).attr( 'href' ) ) + '&action=wowmall_wc_quick_view'
				} );
			} );
		}
		return items;
	}

	$.btnQuickViewInit = function () {
		if ( 'undefined' === typeof wowmallParams ) {
			return
		}
		$( '.wowmall-wc-quick-view-button' ).each( function ( i, item ) {
			$( item ).off( 'click' ).on( 'click', function () {
				$.magnificPopup.open( {
					items:        getQuickViewItems(),
					type:         'ajax',
					tLoading:     wowmallParams.preloader,
					mainClass:    'mfp-zoom-out wowmall-wc-lightbox',
					removalDelay: 500,
					callbacks:    {
						open:             function () {
							$( document.body ).addClass( 'overflow-hidden' );
							$( document ).trigger( 'animate_svg' );
						},
						change:           function () {
							$( document ).trigger( 'animate_svg' );
						},
						parseAjax:        function ( mfpResponse ) {
							mfpResponse.data = mfpResponse.data.data
						},
						ajaxContentAdded: function () {
							if ( typeof wc_add_to_cart_variation_params !== 'undefined' ) {
								init_quick_view_variations( $( this.content ) );
								$( this.content ).find( '.variations_form' ).wc_variation_form().find( '.variations select:eq(0)' ).change();
							}
							var el = $( '.quick-view-images > a > .swiper-container' );
							new Swiper( el, {
								grabCursor:                   false,
								nextButton:                   '#next-quick-view',
								prevButton:                   '#prev-quick-view',
								lazyLoadingInPrevNext:        true,
								lazyLoadingOnTransitionStart: true,
								preloadImages:                false,
								lazyLoading:                  true,
								effect:                       el.data( 'effect' ),
								cube:                         {
									shadow:       false,
									slideShadows: true,
									shadowOffset: 20,
									shadowScale:  0.94
								}
							} );
							wcProductVariations();
							$( document.body ).trigger( 'wowmall_wc_quick_view_opened' );
						},
						close:            function () {
							$( document.body ).removeClass( 'overflow-hidden' );
						}
					},
					gallery:      {
						enabled: true
					}
				}, i );
			} );
		} );
	};
	$.btnQuickViewInit();

	function init_quick_view_variations( content ) {
		content.find( '.variations_form' ).on( 'reset_image', function ( event ) {
			var $form        = $( event.target ),
				$product     = $form.closest( '.product' ),
				$product_img = $product.find( 'div.images .swiper-slide:eq(0) img' );
			$product_img.wc_reset_variation_attr( 'src' );
			$product_img.wc_reset_variation_attr( 'srcset' );
			$product_img.wc_reset_variation_attr( 'sizes' );
			$product_img.wc_reset_variation_attr( 'title' );
			$product_img.wc_reset_variation_attr( 'alt' );
			content.find( '.variations_form' ).off( 'found_variation', wowmall_found_variation ).on( 'found_variation', wowmall_found_variation );
		} );
		content.find( '.variations_form' ).on( 'wc_variation_form', function () {
			content.find( '.variations_form' ).on( 'update_variation_values', function () {
				$( '.variations_form' ).off( 'found_variation', wowmall_found_variation ).on( 'found_variation', wowmall_found_variation );
			} );
			content.find( '.variations_form' ).trigger( 'check_variations' );
		} );
	}

	function wowmall_found_variation( event, variation ) {
		var $form        = $( event.target ),
			$product     = $form.closest( '.product' ),
			$product_img = $product.find( 'div.images .swiper-slide:eq(0) img' );
		if ( variation && variation.image.src && variation.image.src.length > 1 ) {
			$product_img.wc_set_variation_attr( 'src', variation.image.src );
			$product_img.wc_set_variation_attr( 'srcset', variation.image.srcset );
			$product_img.wc_set_variation_attr( 'sizes', variation.image.sizes );
			$product_img.wc_set_variation_attr( 'title', variation.image.title );
			$product_img.wc_set_variation_attr( 'alt', variation.image.alt );
		}
		else {
			$product_img.wc_reset_variation_attr( 'src' );
			$product_img.wc_reset_variation_attr( 'srcset' );
			$product_img.wc_reset_variation_attr( 'sizes' );
			$product_img.wc_reset_variation_attr( 'title' );
			$product_img.wc_reset_variation_attr( 'alt' );
		}
		var el = $( 'div.product div.images a.zoom:eq(0)' )[0];
		if ( 'undefined' === typeof el ) {
			return;
		}
		$.wowmall_lazy_images();
	}

	$( document.body ).on( 'added_to_cart wowmall_added_to_cart', function ( event, fragments, cart_hash, $button ) {
		$button = typeof $button === 'undefined' ? false : $button;
		if ( !wc_add_to_cart_params.is_cart && $button ) {
			var $product   = $button.closest( '.product' ),
				$added_btn = $product.find( '.added_to_cart' );
			if ( $added_btn.length !== 0 ) {
				if ( 'undefined' !== typeof wowmallWcLoop ) {
					$button.find( '.add_to_cart_button_text' ).text( wowmallWcLoop.added_to_cart );
				}
				if ( $product.hasClass( 'product-list' ) || $product.hasClass( 'wowmall-compare-cell' ) ) {
					if ( $( 'body' ).hasClass( 'mobile' ) ) {
						$added_btn.addClass( 'btn btn-inline' );
					}
					else {
						$added_btn.addClass( 'btn btn-default btn-sm' );
					}
				}
				$( document.body ).trigger( 'wowmall_resresh_header_cart' );
			}
			else {
				setTimeout( function () {
					$( document.body ).trigger( 'wowmall_added_to_cart', [null, null, $button] );
				}, 100 )
			}
		}
	} );

	if ( $().masonry ) {
		$( '.masonry > ul.products' ).masonry( {
			itemSelector:    '.product-category',
			columnWidth:     '.grid-sizer',
			percentPosition: true
		} );
	}
	$( '.widget_product_categories select' ).select2( {
		minimumResultsForSearch: 10
	} );

	$( window ).on( 'wowmall_thumbs_swiper_reset_hover', function () {
		for ( var i in swipers ) {
			swipers[i].slidePrev();
		}
	} );
	$( document ).on( 'wowmall_wc_products_changed', function () {
		swipers = [];
		loop_hover();
		$.btnQuickViewInit();
		$.wowmall_lazy_images();
	} );
	if ( $().tab ) {
		$( '.page-my-account a[data-toggle="tab"]' ).on( 'shown.bs.tab', function ( e ) {
			$( 'form.register #reg_password, form.checkout #account_password, form.edit-account #password_1, form.lost_reset_password #password_1' )
				.trigger( 'change' );
			$( window ).on( 'load', function () {
				$( 'form.register #reg_password, form.checkout #account_password, form.edit-account #password_1, form.lost_reset_password #password_1' )
					.trigger( 'change' );
			} );
			$.wowmall_lazy_images();
			if ( history.pushState ) {
				history.pushState( null, null, e.target.hash );
			}
		} ).tab();
		if ( window.location.hash ) {
			$( '.page-my-account a[data-toggle="tab"][href="' + window.location.hash + '"]' ).tab( 'show' );
		}
	}
	$( document ).on( 'enhance.tablesaw', function () {
		$( '.wowmall-compare-table' ).on( 'tablesawcolumns', function () {
			$( '.tablesaw-cell-visible' ).removeAttr( 'style' );
			$( '.wowmall-compare-row' ).each( function () {
				$( this ).find( '.tablesaw-cell-visible' ).last().css( {
					'width':     'auto',
					'min-width': '334px'
				} );
			} );
			$.wowmall_lazy_images();
		} );
	} );
})( jQuery );