(function ( d ) {
	'use strict';
	var style_element      = d.createElement( 'STYLE' ),
		dom_events         = 'addEventListener' in d,
		add_event_listener = function ( type, callback ) {
			// Basic cross-browser event handling
			if ( dom_events ) {
				d.addEventListener( type, callback );
			}
			else {
				d.attachEvent( 'on' + type, callback );
			}
		},
		set_css            = function ( css_text ) {
			!!style_element.styleSheet ? style_element.styleSheet.cssText = css_text : style_element.innerHTML = css_text;
		}
	;
	d.getElementsByTagName( 'HEAD' )[0].appendChild( style_element );
	add_event_listener( 'mousedown', function () {
		document.body.classList.remove( 'focus-styles' );
		set_css( ':focus{outline:0}::-moz-focus-inner{border:0;}' );
	} );
	add_event_listener( 'keydown', function () {
		document.body.classList.add( 'focus-styles' );
		set_css( '' );
	} );
})( document );
(function ( $ ) {
	'use strict';
	$( window ).on( 'load', function () {
		$( 'body.wowmall-page-preloader' ).addClass( 'preloaded' );
		if ( $( document.body ).hasClass( 'lazy-enabled' ) ) {
			$( window ).trigger( 'resize' );
		}
	} );

	var search_xhr,
		search_input_val,
		header_height          = 0,
		mobile_layout_xhr,
		scrollTop,
		_oldslideUp            = $.fn.slideUp,
		_oldslideDown          = $.fn.slideDown,
		originalAddClassMethod = $.fn.addClass,
		originalafterMethod    = $.fn.after;

	$.fn.slideUp = function ( speed, callback ) {
		$( this ).trigger( 'slideUp' );
		return _oldslideUp.apply( this, arguments );
	};

	$.fn.slideDown = function ( speed, callback ) {
		var el = $( this );
		el.trigger( 'slideDownStart' );
		setTimeout( function () {
			el.trigger( 'slideDownEnd' );
		}, speed );
		return _oldslideDown.apply( this, arguments );
	};

	$.fn.addClass = function () {
		var result = originalAddClassMethod.apply( this, arguments );
		$( this ).trigger( 'cssClassAdded' );
		return result;
	};

	$.fn.after = function () {
		var result = originalafterMethod.apply( this, arguments );
		$( this ).trigger( 'afterMethodCalled' );
		return result;
	};

	function scrollBarWidth() {
		return window.innerWidth - document.body.clientWidth;
	}

	if ( 0 < scrollBarWidth() ) {
		$( 'body' ).addClass( 'scrollbar' ).addClass( 'scroll_width_' + scrollBarWidth() );
	}

	function add_megamenu() {
		$( '.menu-item-wowmall-megamenu' ).each( function () {
			var self = $( this );
			self.off( 'mouseenter focus click' ).on( 'mouseenter focus click', add_megamenu_listener );
		} );
	}

	function add_megamenu_listener( event ) {
		var self = $( event.currentTarget ),
			id   = self.data( 'id' );
		if ( 'undefined' !== typeof window['megamenu_' + id] ) {
			if ( !self.find( '>.wowmall-mega-sub' ).length ) {
				self.append( window['megamenu_' + id] );
				if ( self.find( '>.wowmall-mega-sub .upb_bg_img' ) && $().ultimate_bg_shift ) {
					jQuery( ".upb_bg_img" ).ultimate_bg_shift();
				}
				self.addClass( 'megamenu-appended' );
				$.wowmall_lazy_images();
				self.off( 'mouseenter focus click', add_megamenu_listener );

			}
		}
		else if ( !self.hasClass( 'megamenu-appended' ) ) {
			self.addClass( 'megamenu-appended' );
			$.wowmall_lazy_images();
			self.off( 'mouseenter focus click', add_megamenu_listener );
		}
	}

	add_megamenu();
	$( document.body ).on( 'wc_fragments_refreshed wc_fragments_loaded wowmall_resresh_header_cart', header_cart_focus );

	function header_cart_focus() {
		if ( $( document.body ).hasClass( 'woocommerce-checkout' ) || $( document.body ).hasClass( 'woocommerce-cart' ) ) {
			return;
		}
		var wrapper = $( '.header-cart-wrapper' );
		wrapper.find( ' a.cart-contents' ).off( 'click' ).on( 'click', function ( event ) {
			event.preventDefault();
			wrapper.toggleClass( 'active' );
			if ( wrapper.hasClass( 'active' ) ) {
				$( document.body ).on( 'click focus', close_header_cart_listener );
				lazy_cart();
			}
			else {
				$( document.body ).off( 'click focus', close_header_cart_listener );
			}
		} );
		lazy_cart();
		lazy_cart_widget();
	}

	function close_header_cart_listener( event ) {
		if ( !($( event.target ).hasClass( 'header-cart-wrapper' ) || $( event.target ).closest( '.header-cart-wrapper' ).length) ) {
			$( document.body ).off( 'click focus', close_header_cart_listener );
			$( '.header-cart-wrapper' ).removeClass( 'active' );
		}
	}

	function lazy_cart() {
		$( '.header-cart-wrapper img.swiper-lazy:not(.swiper-lazy-loaded)' ).each(
			function () {
				var container;
				if ( $( this ).closest( '#top-panel' ).length ) {
					container = '#top-panel .header-cart-wrapper .cart_list';
				}
				if ( $( this ).closest( '.header-sticky-wrapper' ).length ) {
					container = '.header-sticky-wrapper .header-cart-wrapper .cart_list';
				}
				$.wowmall_lazy_init( $( this ), container );
			}
		);
	}

	function lazy_cart_widget() {
		var container = $( '.wowmall-sidebar-inner' ).length ? $( '.wowmall-sidebar-inner' ) : undefined;
		$( '#secondary .widget_shopping_cart img.swiper-lazy:not(.swiper-lazy-loaded)' ).each(
			function () {
				$.wowmall_lazy_init( $( this ), container );
			}
		);
	}

	$.wowmall_lazy_init   = function ( img, container ) {
		if ( $().lazyload ) {
			if ( 'undefined' === typeof container ) {
				container = window;
			}
			img.lazyload( {
				container:      container,
				data_attribute: 'undefined' !== typeof img.data( 'wowmall-src' ) ? 'wowmall-src' : 'src',
				load:           function () {
					img.addClass( 'swiper-lazy-loaded' );
					var sizes  = img.data( 'sizes' ),
						srcset = img.data( 'srcset' );
					img.attr( {
						sizes:  sizes,
						srcset: srcset
					} ).removeAttr( 'data-srcset data-sizes data-src data-wowmall-src' );
				}
			} );
		}
	};
	$.wowmall_lazy_images = function () {
		$( 'img.swiper-lazy:not(.swiper-lazy-loaded), img[data-wowmall-lazy="swiper-lazy"]:not(.swiper-lazy-loaded)' ).each(
			function () {
				$.wowmall_lazy_init( $( this ) );
			}
		);
	};
	$.wowmall_lazy_images();
	$( document.body ).on( 'updated_wc_div cart_page_refreshed', $.wowmall_lazy_images )
		.on( 'adding_to_cart', addStuckHeader );
	$( '.entry-share-btns *' ).on( 'mouseenter focus', function () {
		$( this ).closest( '.entry-share-btns' ).addClass( 'active' );
	} ).on( 'mouseleave focusout', function () {
		$( this ).closest( '.entry-share-btns' ).removeClass( 'active' );
	} );
	$( '.wowmall-gallery-item *' ).on( 'mouseenter focus', function () {
		$( this ).closest( '.wowmall-gallery-item' ).addClass( 'active' );
	} ).on( 'mouseleave focusout', function () {
		$( this ).closest( '.wowmall-gallery-item' ).removeClass( 'active' );
	} );
	$( '.wpcf7' ).each( function () {
		var self  = $( this ),
			$form = self.find( '>form' );

		$( '.wpcf7-submit', $form ).on( 'afterMethodCalled', function () {
			self.find( '.ajax-loader' ).on( 'cssClassAdded', function () {
				self.addClass( 'wpcf7-form-processing' )
					.find( '.wpcf7-form-control' ).closest( 'p' ).removeClass( 'wpcf7-not-valid' );
			} );
			self.on( 'form-submit-validate', function () { //deprecated trigger, fallback for Contact Form 7 ver 4.7 and lower
					self.addClass( 'wpcf7-form-processing' )
						.find( '.wpcf7-form-control' ).closest( 'p' ).removeClass( 'wpcf7-not-valid' );
				} )
				.on( 'wpcf7:submit', function () {
					self.removeClass( 'wpcf7-form-processing' )
						.find( '.wpcf7-form-control' ).closest( 'p' ).removeClass( 'wpcf7-not-valid' );
				} )
				.on( 'wpcf7:invalid', function () {
					self.find( '.wpcf7-form-control' ).closest( 'p' ).removeClass( 'wpcf7-not-valid' );
					self.find( '.wpcf7-not-valid' ).closest( 'p' ).addClass( 'wpcf7-not-valid' );
				} );
		} );
	} );
	if ( $().select2 ) {
		$( '.widget_categories select[name=cat], .widget_archive select[name=archive-dropdown]' ).select2( {
			minimumResultsForSearch: 10
		} );
	}
	else if ( $().selectmenu ) {
		$( '.widget_categories select[name=cat], .widget_archive select[name=archive-dropdown]' ).selectmenu( {
			width:  '100%',
			create: function ( event ) {
				var id = $( event.target ).closest( '.widget' ).attr( 'id' );
				$( event.target ).selectmenu( "option", "appendTo", "#" + id );
			},
			change: function ( event ) {
				$( event.target ).change();
			}
		} );
	}
	$( '.wowmall-to-top' ).on( 'click', function ( event ) {
		event.preventDefault();
		$( 'html, body' ).animate( {
			scrollTop: 0
		}, 500 );
	} );
	$.wowmall_mc4wp_ajax_submit = function () {
		$( 'form.mc4wp-form' ).off( 'submit' ).on( 'submit', function ( event ) {
			event.preventDefault();
			var data = $( event.target ).serialize();
			data += '&action=wowmall_get_mc4wp_form';
			$( event.target ).addClass( 'processing' ).removeClass( 'mc4wp-form-error' ).removeClass( 'mc4wp-form-success' ).find( '.mc4wp-response' ).html( '' );
			$.post(
				wowmallParams.ajax_url,
				data,
				function ( response ) {
					if ( response.success ) {
						var parser = new DOMParser(),
							doc    = parser.parseFromString( response.data, "text/html" ),
							form   = doc.getElementById( event.target.id );
						if ( form ) {
							$( event.target ).replaceWith( form );
							$.wowmall_mc4wp_ajax_submit();
						}
						else {
							$( event.target ).off( 'submit' ).submit();
						}
					}
					else {
						$( event.target ).off( 'submit' ).submit();
					}
				}
			);
		} );
	};

	$.wowmall_mc4wp_ajax_submit();
	$( '.wowmall-top-search-wrapper .search-submit' ).on( 'click', function ( event ) {
		event.preventDefault();
		if ( $( '.wowmall-top-search' ).hasClass( 'expanded' ) ) {
			$( this ).closest( 'form' ).submit();
		}
		else {
			$( '.wowmall-top-search' ).addClass( 'expanded' );
			$( this ).closest( 'form' ).find( 'input[type=search]' ).focus();
			$( document.body ).on( 'click', search_close_listener );
		}
	} );
	if ( 'undefined' !== typeof wowmallParams && ('undefined' === typeof wowmallParams.ajax_search || !!wowmallParams.ajax_search) ) {
		var search_input   = $( '.wowmall-top-search-wrapper input[type=search]' ),
			search_results = $( '.wowmall-search-results' );
		search_input_val   = search_input.val();
		search_input.on( 'keyup',
			$.debounce( 500, function () {
				var new_search_input_val = search_input.val();
				if ( new_search_input_val !== search_input_val ) {
					search_input_val = new_search_input_val;
					if ( wowmallParams.ajax_search_min_length > $( this ).val().length ) {
						if ( search_xhr && 4 !== search_xhr.readyState ) {
							search_xhr.abort();
						}
						search_results.removeClass( 'shown' ).html( '' );
						return;
					}
					search_results.html( '<span class="wowmall-search-loading"></span>' ).addClass( 'shown' );
					if ( search_xhr && 4 !== search_xhr.readyState ) {
						search_xhr.abort();
					}
					var data   = $( this ).closest( 'form' ).serialize();
					search_xhr = $.get(
						wowmallParams.ajax_url,
						data,
						function ( response ) {
							search_results.html( response.data );
							ajax_search_lazy();
						}
					);
				}
			} )
		);
	}

	function ajax_search_lazy() {
		$( '.wowmall-search-results-inner img.swiper-lazy:not(.swiper-lazy-loaded)' ).each( function () {
			$.wowmall_lazy_init( $( this ), $( '.wowmall-search-results-inner' ) );
		} );
	}

	function search_close_listener( event ) {
		if ( $( event.target ).hasClass( 'search-close' ) || $( event.target ).closest( '.search-close' ).length || !($( event.target ).hasClass( 'wowmall-top-search' ) || $( event.target ).closest( '.wowmall-top-search' ).length) ) {
			$( document.body ).off( 'click', search_close_listener );
			$( '.wowmall-top-search' ).removeClass( 'expanded' );
			$( '.wowmall-search-results' ).removeClass( 'shown' ).html( '' );
			$( '.wowmall-top-search input[type=search]' ).val( '' );
			search_input_val = '';
		}
	}

	$( '.swiper-slide .woocommerce-LoopProduct-link, .swiper-container-horizontal .swiper-slide a' ).on( 'click', function () {
		window.location.href = $( this ).attr( "href" );
	} );

	function addStuckHeader() {
		if ( !header_height ) {
			header_height = $( '#header' ).offset().top + $( '#header' ).height() - $( '.header-sticky-wrapper' ).height();
		}
		if ( header_height >= $( window ).scrollTop() ) {
			return;
		}
		$( window ).off( 'scroll', addStuckHeader );
		if ( !$( '.header-sticky-wrapper .wowmall-main-menu-wrapper .main-menu' ).length ) {
			$( '#header:not(.header-layout-mobile) .main-menu' ).clone().appendTo( '.header-sticky-wrapper .wowmall-main-menu-wrapper' );
			add_megamenu();

		}
		if ( $( '.header-sticky-wrapper .header-cart-wrapper' ).length && !$( '.header-sticky-wrapper .header-cart-wrapper' ).html().length ) {
			if ( $( '#top-panel .header-cart-wrapper' ).length ) {
				$( '.header-sticky-wrapper .header-cart-wrapper' ).replaceWith( $( '#top-panel .header-cart-wrapper' ).clone() );
			}
			else if ( $( '#header:not(.header-layout-mobile) .header-cart-wrapper' ).length ) {
				$( '.header-sticky-wrapper .header-cart-wrapper' ).replaceWith( $( '#header:not(.header-layout-mobile) .header-cart-wrapper' ).clone() );
			}
			$( document.body ).trigger( 'wowmall_resresh_header_cart' );
		}
		stuckHeader();
		$( window ).on( 'scroll', $.throttle( 100, stuckHeader ) );
	}

	function stuckHeader() {
		if ( header_height < $( window ).scrollTop() ) {
			$( document.body ).addClass( 'stuck-header' );
			lazy_cart();
		}
		else {
			$( document.body ).removeClass( 'stuck-header' );
		}
	}

	if ( $( document.body ).is( '.desktop.header-sticky-enable' ) ) {
		$( window ).on( 'scroll', addStuckHeader );
	}
	$( '.vc_toggle_content' ).on( 'slideUp', function () {
		$( window ).trigger( 'scroll' );
	} );
	$( '[data-vc-tabs]' ).on( 'click.vc.tabs.data-api', function () {
		$( window ).trigger( 'scroll' );
	} );
	$( document ).on( 'show.vc.accordion hide.vc.accordion', function () {
		$( window ).trigger( 'scroll' );
		$( window ).trigger( 'resize' );
	} );
	if ( 'undefined' !== typeof wowmallParams && $( document.body ).hasClass( 'desktop' ) ) {
		$( window ).on( 'resize', $.debounce( 500, get_mobile_header ) );
		get_mobile_header();
	}

	function get_mobile_header() {
		if ( mobile_layout_xhr && mobile_layout_xhr.readyState !== 4 ) {
			mobile_layout_xhr.abort();
		}
		if ( 992 > window.innerWidth ) {
			$( window ).off( 'resize', get_mobile_header );
			mobile_layout_xhr = $.get(
				wowmallParams.ajax_url,
				{
					action: 'wowmall_get_mobile_layout'
				},
				function ( response ) {
					if ( response.success ) {
						$( '#header' ).after( response.data.header );
						$( document ).trigger( 'reinit_header_scripts' );
					}
				}
			);
		}
	}

	function mobile_menu_close_listener( event ) {
		if ( !($( event.target ).is( '#mobile-menu-wrapper' ) || $( event.target ).closest( '#mobile-menu-wrapper' ).length) ) {
			$( document.body ).off( 'touchmove click', mobile_menu_close_listener );
			$( '#mobile-menu-close' ).removeClass( 'active' );
			$( '.header-layout-mobile nav.navbar' ).removeClass( 'shown' );
			$( 'html' ).removeClass( 'wowmall-prevent-scrolling' ).removeAttr( 'style' );
			$( 'html, body' ).animate( {
				scrollTop: scrollTop
			}, 0 );
		}
	}

	function init_mobile_menu() {
		$( '#mobile-menu-open' ).off( 'click' ).on( 'click', function ( event ) {
			event.stopPropagation();
			$( '#mobile-menu-close' ).addClass( 'active' );
			$( '.header-layout-mobile nav.navbar' ).addClass( 'shown' );
			scrollTop = $( document.body ).scrollTop();
			$( 'html' ).css( {
				top: -scrollTop
			} ).addClass( 'wowmall-prevent-scrolling' );
			$( document.body ).on( 'touchmove click', mobile_menu_close_listener );
		} );
		$( '.menu-item-toggle' ).off( 'click' ).on( 'click', function ( event ) {
			event.preventDefault();
			$( this ).closest( 'li' ).toggleClass( 'active' ).find( '>.sub-menu, >.wowmall-mega-sub' ).slideToggle( 300 );
		} );
		$( '.wowmall-mega-sub .wpb_wrapper .widgettitle' ).append( '<span class=menu-item-toggle/>' ).find( '.menu-item-toggle' ).off( 'click' ).on( 'click', function () {
			$( this ).closest( '.wpb_wrapper' ).toggleClass( 'active' ).find( 'ul.menu' ).slideToggle( 300 );
		} );
		$( '#mobile-menu-wrapper .header-currency-wrapper .dropdown-toggle' ).off( 'click' ).on( 'click', function ( event ) {
			event.preventDefault();
			$( this ).closest( '.header-currency-wrapper' ).toggleClass( 'active' )
				.find( '>.dropdown-menu' ).slideToggle( 300 );
			if ( $( '.header-currency-wrapper' ).hasClass( 'active' ) ) {
				$( '#mobile-menu-wrapper' ).animate( {
					scrollTop: $( '#mobile-menu-wrapper' )[0].scrollHeight
				}, 300 );
			}
		} );
	}

	$( '.header-currency-wrapper .dropdown-toggle' ).on( 'click', currency_open_listener );

	function currency_open_listener() {
		$( '.header-currency-wrapper .dropdown-toggle' ).off( 'click', currency_open_listener );
		$( '.header-currency-wrapper' ).addClass( 'active' );
		setTimeout( function () {
			$( document.body ).on( 'click', currency_close_listener );
		}, 100 );
	}

	function currency_close_listener( event ) {
		if ( $( event.target ).is( '.header-currency-wrapper .dropdown-toggle' ) || $( event.target ).closest( '.header-currency-wrapper .dropdown-toggle' ).length || !($( event.target ).is( '.header-currency-wrapper' ) || $( event.target ).closest( '.header-currency-wrapper' ).length) ) {
			$( document.body ).off( 'click', currency_close_listener );
			$( '.header-currency-wrapper' ).removeClass( 'active' );
			setTimeout( function () {
				$( '.header-currency-wrapper .dropdown-toggle' ).on( 'click', currency_open_listener );
			}, 100 );
		}
	}

	if ( $( document.body ).hasClass( 'mobile' ) ) {
		init_mobile_menu();
	}
	$( document ).on( 'reinit_header_scripts', function () {
		init_mobile_menu();
		$( document.body ).trigger( 'wc_fragment_refresh' );
	} );

	$( '.vc_tta-container[data-vc-action="collapseAll"] .vc_tta-accordion .vc_tta-panel:first-child' ).addClass( '.vc_active' );

	$( window ).on( 'load resize wowmall_images_resize', $.debounce( 500, function () {
		$( 'img[srcset][width]' ).each( function () {
			var img        = $( this ),
				srcset_arr = [],
				srcset     = img.attr( 'srcset' ).split( ',' );
			srcset.forEach( function ( item ) {
				item         = item.trim().split( ' ' );
				var index    = parseFloat( item[1] ),
					lastChar = item[1].substr( item[1].length - 1 );
				if ( 'w' === lastChar ) {
					srcset_arr[index] = item[0];
				}
			} );
			var some = srcset_arr.some( function ( item, i ) {
				if ( img.outerWidth() <= i ) {
					img.attr( {
						sizes: '(max-width: ' + i + 'px) 100vw, ' + i + 'px'
					} );
					return i;
				}
				return false;
			} );
			if ( !some && srcset_arr.length ) {
				var i = srcset_arr.length - 1;
				img.attr( {
					sizes: '(max-width: ' + i + 'px) 100vw, ' + i + 'px'
				} );
			}
		} )
	} ) );

})( jQuery );