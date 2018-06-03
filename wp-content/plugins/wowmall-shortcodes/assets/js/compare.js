( function ( $ ) {
	'use strict';
	$( document ).ready( function () {
		var wowmallCompareLoadingClass = 'loading',
		    wowmallCompareAddedClass   = 'added in_compare',
		    btnSelector                = '.wowmall-compare-button';

		function productButtonsInit() {
			$( btnSelector ).each( function () {
				var button = $( this );
				button.off( 'click' ).on( 'click', function ( event ) {
					event.preventDefault();
					button
						.removeClass( wowmallCompareAddedClass )
						.addClass( wowmallCompareLoadingClass );
					$.post(
						wowmallParams.ajax_url,
						{
							action: 'wowmall_compare_button',
							url   : encodeURIComponent(button.closest( '.product' ).find( '.woocommerce-LoopProduct-link' ).attr('href'))
						},
						function ( response ) {
							button.removeClass( wowmallCompareLoadingClass );
							if ( response.success ) {
								switch ( response.data.action ) {
									case 'add':
										button
											.addClass( wowmallCompareAddedClass )
											.find( '.wowmall_compare_product_actions_tip' )
											.text( wowmallCompare.removeText );
										break;
									case 'remove':
										button
											.removeClass( wowmallCompareAddedClass )
											.find( '.wowmall_compare_product_actions_tip' )
											.text( wowmallCompare.compareText );
										break;
									default:
										break;
								}
							}
						}
					);
				} );
			} );
		}

		function tableButtonsInit() {
			$( '.wowmall-compare-remove span' )
				.off( 'click' )
				.on( 'click', function ( event ) {
					event.preventDefault();
					var button      = $( event.currentTarget ),
					    compareList = $( 'div.wowmall-compare-list' );
					compareList.addClass( wowmallCompareLoadingClass );
					$.post(
						wowmallParams.ajax_url,
						{
							action: 'wowmall_compare_remove',
							pid   : button.data( 'id' )
						},
						function ( response ) {
							compareList.removeClass( wowmallCompareLoadingClass );
							if ( response.success ) {
								$( 'div.wowmall-compare-wrapper' ).html( response.data );
								$( document ).trigger( 'wowmall_wc_products_changed' ).trigger( 'enhance.tablesaw' );
							}
						}
					);
				} );
		}

		tableButtonsInit();
		productButtonsInit();
		$( document ).on( 'wowmall_wc_products_changed', function () {
			tableButtonsInit();
			productButtonsInit();
		} );
	} );
}( jQuery ) );