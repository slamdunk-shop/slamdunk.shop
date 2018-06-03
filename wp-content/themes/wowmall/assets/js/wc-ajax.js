(function ( $ ) {
	'use strict';

	var orderingClass   = '.woocommerce-ordering',
		togglerClass    = '.wc-grid-list-button',
		wrapperClass    = '.wowmall-wc-ajax-products-wrapper',
		toggler         = $( togglerClass ),
		disabledClass   = 'active',
		cookie          = 'wowmall-wc-grid-list',
		wrapper         = $( wrapperClass ),
		paginationClass = '.woocommerce-pagination a.page-numbers',
		loadMoreClass   = 'a.wowmall-wc-ajax-load-more-button',
		infiniteClass   = '.wowmall-infinite-preloader',
		products_load_xhr;

	$.wowmallWcProductsAjax = function ( data ) {
		$( document ).trigger( 'animate_svg' );
		if ( 'undefined' === typeof wowmallParams ) {
			return;
		}
		var wrapper = $( wrapperClass );
		wrapper.addClass( 'loading' );
		data.action = 'wowmall_wc_rebuild_products';
		$.post(
			wowmallParams.ajax_url,
			data,
			function ( response ) {
				wrapper.removeClass( 'loading' );
				if ( response.success ) {
					wrapper.replaceWith( response.data.products );
					$( document ).trigger( 'wowmall_wc_products_changed' );
					wowmallWcAjaxInit();
				}
			}
		);
	};

	function wowmallWcGridListTogglerHandler( event ) {
		var self = $( event.currentTarget );
		if ( self.hasClass( disabledClass ) ) {
			return;
		}
		toggler.removeClass( disabledClass );
		self.addClass( disabledClass );
		var condition = event.currentTarget.dataset.condition,
			data      = {
				pageUrl: location.href
			};
		$.cookie( cookie, condition, { expires: 365, path: '/' } );
		$.wowmallWcProductsAjax( data );
	}

	$.wowmallWcOrderingHandler = function ( event ) {
		var self       = $( event.currentTarget ),
			currentUrl = location.href,
			form       = self.closest( 'form' ),
			formData   = form.serialize(),
			urlQuery   = location.search,
			pageUrl;
		if ( urlQuery.length ) {
			pageUrl = currentUrl.replace( urlQuery, '?' + formData );
		}
		else {
			pageUrl = currentUrl + '?' + formData;
		}
		if ( history.pushState ) {
			history.pushState( null, null, pageUrl );
		}
		var data = {
			pageUrl: pageUrl,
			task:    'ordering'
		};
		$.wowmallWcProductsAjax( data );
	};

	function tmWcPaginationHandler( event ) {
		if ( wowmallWcAjax.ajaxPagination ) {
			event.preventDefault();
			var data = {
				pageUrl: event.currentTarget.href
			};
			$.wowmallWcProductsAjax( data );
		}
	}

	function wowmallWcLoadMoreHandler( event ) {
		event.preventDefault();
		var wrapper = $( wrapperClass ),
			button  = $( event.currentTarget ),
			data    = {
				pageUrl:       button.attr( 'href' ),
				action:        'wowmall_wc_load_more',
				productsCount: wrapper.find( '.product' ).length
			};
		button.addClass( 'loading' );
		if ( products_load_xhr && products_load_xhr.readyState !== 4 ) {
			products_load_xhr.abort();
		}
		products_load_xhr = $.post(
			wowmallParams.ajax_url,
			data,
			function ( response ) {
				button.removeClass( 'loading' );
				if ( response.success ) {
					var wrapper = $( wrapperClass );
					wrapper.find( '.product' ).last().after( response.data.products );
					button.replaceWith( response.data.button );
					$( document ).trigger( 'wowmall_wc_products_changed' );
					wowmallWcAjaxInit();
				}
			}
		);
	}

	function wowmallWcAjaxInit() {
		toggler = $( togglerClass );
		toggler
			.off( 'click' )
			.on( 'click', wowmallWcGridListTogglerHandler );

		$( orderingClass )
			.off( 'change', 'select.orderby' )
			.on( 'change', 'select.orderby', $.wowmallWcOrderingHandler );

		$( paginationClass )
			.off( 'click' )
			.on( 'click', tmWcPaginationHandler );

		$( loadMoreClass )
			.off( 'click' )
			.on( 'click', wowmallWcLoadMoreHandler );

		orderingSelectmenu();
		infiniteScroll();
	}

	function infiniteScroll() {
		$( window ).off( 'scroll', infiniteScrollHandler );
		if ( $( infiniteClass ).length ) {
			$( window ).on( 'scroll', $.debounce( 10, infiniteScrollHandler ) );
		}
	}

	function infiniteScrollHandler() {
		if ( $( infiniteClass ).length && ( $( window ).scrollTop() + $( window ).height() ) > $( infiniteClass ).offset().top ) {
			var wrapper       = $( wrapperClass ),
				productsCount = wrapper.find( '.product' ).length,
				button        = $( infiniteClass );
			var data          = {
				pageUrl:       button.data( 'href' ),
				action:        'wowmall_wc_load_more',
				productsCount: productsCount
			};
			if ( products_load_xhr && products_load_xhr.readyState !== 4 ) {
				return;
			}
			products_load_xhr = $.post(
				wowmallParams.ajax_url,
				data,
				function ( response ) {
					if ( response.success ) {
						var wrapper = $( wrapperClass );
						wrapper.find( '.product' ).last().after( response.data.products );
						button.replaceWith( response.data.button );
						$( document ).trigger( 'wowmall_wc_products_changed' );
						wowmallWcAjaxInit();
					}
				}
			);
		}
	}

	function orderingSelectmenu() {
		/*if ( $().select2 ) {
			$( '.woocommerce-ordering select' ).select2( {
				minimumResultsForSearch: 10
			} );
		}
		else if ( $().selectmenu ) {*/
			$( '.woocommerce-ordering select' ).selectmenu( {
				appendTo: '.woocommerce-ordering',
				width:    254,
				change:   function ( event ) {
					$( event.target ).change();
				}
			} );
		//}
	}

	if ( 'undefined' !== typeof wowmallParams ) {
		$( function () {
			wowmallWcAjaxInit();
		} );
	}
	if ( $().select2 ) {
		$( 'select[class*=dropdown_layered_nav]:not(.wowmall-color):not(.wowmall-size)' ).select2( {
			minimumResultsForSearch: 10
		} );
	}
	else if ( $().selectmenu ) {
		$( 'select[class*=dropdown_layered_nav]:not(.wowmall-color):not(.wowmall-size)' ).selectmenu( {
			width:  '100%',
			create: function ( event ) {
				$( event.target ).selectmenu( 'option', 'appendTo', '#' + $( event.target ).closest( '.widget' ).attr( 'id' ) );
			},
			change: function ( event ) {
				$( event.target ).change();
			}
		} );
	}
})( jQuery );