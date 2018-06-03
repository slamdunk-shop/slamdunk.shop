(function ( $ ) {
	'use strict';
	if ( $.cookie( 'wowmall-dont-show-subscribe-popup' ) ) {
		return;
	}
	var delay = 'undefined' !== typeof wowmallSubscribePopup ? wowmallSubscribePopup.delay : 2000;
	$( window ).on( 'load', function () {
		setTimeout( function () {
			$.post(
				wowmallParams.ajax_url,
				{
					action: 'wowmall_subscribe_popup'
				},
				function ( response ) {
					if ( response.success ) {
						$( document.body ).append( response.data );
						var dont_show_again    = $( '#wowmall-subscribe-close-popup' ),
						    is_dont_show_again = dont_show_again.length,
						    close_class        = is_dont_show_again ? ' mfp-prevent-close' : '';
						$.magnificPopup.open( {
							mainClass     : 'mfp-zoom-out mfp-subscribe',
							removalDelay  : 500,
							closeOnBgClick: false,
							closeMarkup   : '<button title="%title%" type="button" class="mfp-close' + close_class + '"></button>',
							items         : {
								src : '#wowmall-subscribe-popup',
								type: 'inline'
							},
							midClick      : true,
							callbacks     : {
								open : function () {
									$.wowmall_mc4wp_ajax_submit();
									if ( is_dont_show_again ) {
										$( this.container ).find( '.mfp-close' ).on( 'click', { popup: $.magnificPopup }, wowmall_ask_before_close );
									}
									$( document.body ).addClass( 'overflow-hidden' );
								},
								close: function () {
									$( document.body ).removeClass( 'overflow-hidden' );
								}
							}
						} );
					}
				}
			);
		}, delay );
		function wowmall_ask_before_close( event ) {
			$( event.data.popup.instance.content ).html( $( '#wowmall-subscribe-close-popup' ).html() );
			$( '.wowmall-subscribe-close-popup-btns .btn' ).on( 'click', function () {
				$( event.data.popup.close() );
				$.cookie( 'wowmall-dont-show-subscribe-popup-in-current-session', true, { path: '/' } );
			} );
			$( '.wowmall-dont-show-again' ).on( 'click', function () {
				$.cookie( 'wowmall-dont-show-subscribe-popup', true, { expires: 365, path: '/' } );
			} );
		}
	} );
})( jQuery );