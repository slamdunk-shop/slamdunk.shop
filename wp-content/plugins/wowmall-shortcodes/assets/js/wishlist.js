( function ( $ ) {
	'use strict';
	$( document ).ready( function () {
		var wowmallWishlistLoadingClass = 'loading',
		    wowmallWishlistAddedClass   = 'added in_wishlist',
		    buttonSelector              = '.wowmall-wishlist-button';

		function productButtonsInit() {
			$( buttonSelector ).each( function () {
				var button = $( this );
				button.off( 'click' ).on( 'click', function ( event ) {
					event.preventDefault();
					if ( button.hasClass( 'in_wishlist' ) ) {
						return;
					}
					button
						.removeClass( wowmallWishlistAddedClass )
						.addClass( wowmallWishlistLoadingClass );
					$.post(
						wowmallParams.ajax_url,
						{
							action: 'wowmall_wishlist_add',
							url   : $(document.body).hasClass( 'single-product' ) ? encodeURIComponent(location.href) : encodeURIComponent(button.closest( '.product' ).find( '.woocommerce-LoopProduct-link' ).attr('href'))
						},
						function ( response ) {
							button.removeClass( wowmallWishlistLoadingClass );
							if ( response.success ) {
								button
									.addClass( wowmallWishlistAddedClass )
									.find( '.wowmall_wishlist_product_actions_tip' )
									.text( wowmallWishlist.addedText );
							}
						}
					);
				} );
			} );
		}

		function wishlistButtonsInit() {
			$( '.wowmall-wishlist-remove' )
				.off( 'click' )
				.on( 'click', function ( event ) {
					event.preventDefault();
					var button   = $( event.currentTarget ),
					    wishList = $( 'div.wowmall-wishlist' );
					wishList.addClass( wowmallWishlistLoadingClass );
					$.post(
						wowmallParams.ajax_url,
						{
							action: 'wowmall_wishlist_remove',
							pid   : button.data( 'id' )
						},
						function ( response ) {
							wishList.removeClass( wowmallWishlistLoadingClass );
							if ( response.success ) {
								$( 'div.wowmall-wishlist-wrapper' ).html( response.data );
								$( document ).trigger( 'wowmall_wc_products_changed' );
							}
						}
					);
				} );
		}

		wishlistButtonsInit();
		productButtonsInit();
		$( document ).on( 'wowmall_wc_products_changed', function () {
			wishlistButtonsInit();
			productButtonsInit();
		} );
	} );
}( jQuery ) );