(function ( $ ) {
	'use strict';
	var first_thumb;
	if ( 'undefined' !== typeof singleProductLightbox && 'undefined' !== typeof singleProductLightbox.thumbs && 0 !== singleProductLightbox.thumbs.length ) {
		first_thumb = singleProductLightbox.thumbs[0];
	}

	function init_lightbox() {
		var gallery_magnific_swiper,
			items = [];
		$( '.wowmall-product-video' ).off( 'click' );
		$( 'a[data-rel=prettyPhoto][href],a[data-rel="prettyPhoto[product-gallery]"][href]' ).each( function ( i, item ) {
			var el = $( this );
			items.push( {
				src:  el.attr( 'href' ),
				type: 'image'
			} );
			el.off( 'click' ).on( 'click', function ( event ) {
				event.preventDefault();
				$.magnificPopup.open( {
					items:           items,
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
					},
					iframe:          {
						markup:   '<button type="button" class="mfp-close"></button>' +
								  '<div class="mfp-iframe-scaler">' +
								  '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
								  '</div>',
						patterns: {
							youtu: {
								index: 'youtu.be',
								id:    function ( url ) {

									// Capture everything after the hostname, excluding possible querystrings.
									var m = url.match( /^.+youtu.be\/([^?]+)/ );

									if ( null !== m ) {

										return m[1];

									}

									return null;

								},
								src:   '//www.youtube.com/embed/%id%?autoplay=1&rel=0'
							}
						}
					},
					callbacks:       {
						open:   function () {
							var popup = this;
							if ( !$( 'body' ).hasClass( 'mobile' ) ) {
								if ( 'undefined' !== typeof singleProductLightbox && 'undefined' !== typeof singleProductLightbox.thumbs && 0 !== singleProductLightbox.thumbs.length ) {
									if ( !( $( '#gallery-thumbs .wowmall-product-video' ).length || $( '.wowmall-product-video-frame' ).length ) && 2 > singleProductLightbox.thumbs.length ) {
										return;
									}
									var index = this.index;
									$( this.container ).append( '<div class="mfp-thumbs mfp-prevent-close"><div class="swiper-container mfp-prevent-close" id=mfp-swiper><div class="swiper-wrapper mfp-prevent-close"></div><div class="swiper-scrollbar mfp-prevent-close"></div></div>' );
									var thumbs  = $( this.container ).find( '.mfp-thumbs' ),
										wrapper = thumbs.find( '.swiper-wrapper' );
									singleProductLightbox.thumbs.forEach( function ( item, i, arr ) {
										wrapper.append( '<div class="swiper-slide mfp-prevent-close"><img src="' + item[0] + '" alt="" width=' + item[1] + ' height=' + item[2] + ' class=mfp-prevent-close></div>' )
									} );
									if ( $( '#gallery-thumbs .wowmall-product-video' ).length || $( '.wowmall-product-video-frame' ).length ) {
										thumbs.find( '.swiper-slide:last' ).after( '<div class="swiper-slide mfp-prevent-close"><div class="mfp-slide-video mfp-prevent-close"></div>' );
									}
									gallery_magnific_swiper = new Swiper( thumbs.find( '#mfp-swiper' ), {
										initialSlide:           index,
										grabCursor:             false,
										slidesPerView:          'auto',
										scrollbar:              '.swiper-scrollbar',
										scrollbarHide:          false,
										scrollbarDraggable:     true,
										scrollbarSnapOnRelease: true,
										centeredSlides:         true,
										onImagesReady:          function ( swiper ) {
											$( swiper.scrollbar.drag ).addClass( 'mfp-prevent-close' );
											change_slide( index, swiper );
										}
									} );
									$( gallery_magnific_swiper.slides ).click( function () {
										popup.goTo( $( this ).index() );
										change_slide( $( this ).index(), gallery_magnific_swiper );
									} );
								}
								return;
							}
							$( '.mfp-container' ).swipe( {
								swipeLeft:  function () {
									popup.next();
								},
								swipeRight: function () {
									popup.prev();
								}
							} );
						},
						change: function () {
							if ( 'undefined' !== typeof gallery_magnific_swiper ) {
								var index = this.index;
								change_slide( index, gallery_magnific_swiper );
							}
						}
					},
					gallery:         {
						enabled:            true,
						navigateByImgClick: true,
						preload:            [0, 1] // Will preload 0 - before current, and 1 after the current image
					}
				}, i );
			} );
		} );
		if ( $( '.wowmall-product-video-frame' ).length ) {
			items.push( {
				src:  $( '.wowmall-product-video-frame' ).data( 'url' ),
				type: 'iframe'
			} );
		}
		if ( $( '#gallery-thumbs .wowmall-product-video' ).length ) {
			items.push( {
				src:  $( '#gallery-thumbs .wowmall-product-video' ).attr( 'href' ),
				type: 'iframe'
			} );
			if ( 1 < $( '#gallery-thumbs .swiper-slide' ).length ) {
				$( '.wowmall-product-video' ).on( 'click', function ( event ) {
					var index = items.length - 1;
					event.preventDefault();
					$.magnificPopup.open( {
						items:           items,
						tLoading:        wowmallParams.preloader,
						mainClass:       'wowmall-single-product-lightbox mfp-fade mfp-with-zoom',
						closeMarkup:     '<button title="%title%" type="button" class="mfp-close"></button>',
						fixedContentPos: true,
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
						},
						iframe:          {
							markup:   '<button type="button" class="mfp-close"></button>' +
									  '<div class="mfp-iframe-scaler">' +
									  '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
									  '</div>',
							patterns: {
								youtu: {
									index: 'youtu.be',
									id:    function ( url ) {

										// Capture everything after the hostname, excluding possible querystrings.
										var m = url.match( /^.+youtu.be\/([^?]+)/ );

										if ( null !== m ) {

											return m[1];

										}

										return null;

									},
									src:   '//www.youtube.com/embed/%id%?autoplay=1&rel=0'
								}
							}
						},
						callbacks:       {
							open:   function () {
								var popup = this;
								if ( !$( 'body' ).hasClass( 'mobile' ) ) {
									if ( 'undefined' !== typeof singleProductLightbox && 'undefined' !== typeof singleProductLightbox.thumbs && 0 !== singleProductLightbox.thumbs.length ) {
										if ( !$( '#gallery-thumbs .wowmall-product-video' ).length && 2 > singleProductLightbox.thumbs.length ) {
											return;
										}
										var index = this.index;
										$( this.container ).append( '<div class="mfp-thumbs mfp-prevent-close"><div class="swiper-container mfp-prevent-close" id=mfp-swiper><div class="swiper-wrapper mfp-prevent-close"></div><div class="swiper-scrollbar mfp-prevent-close"></div></div>' );
										var thumbs  = $( this.container ).find( '.mfp-thumbs' ),
											wrapper = thumbs.find( '.swiper-wrapper' );
										singleProductLightbox.thumbs.forEach( function ( item, i, arr ) {
											wrapper.append( '<div class="swiper-slide mfp-prevent-close"><img src="' + item[0] + '" alt="" width=' + item[1] + ' height=' + item[2] + ' class=mfp-prevent-close></div>' )
										} );
										if ( $( '#gallery-thumbs .wowmall-product-video' ).length ) {
											thumbs.find( '.swiper-slide:last' ).after( '<div class="swiper-slide mfp-prevent-close"><div class="mfp-slide-video mfp-prevent-close"></div>' );
										}
										gallery_magnific_swiper = new Swiper( thumbs.find( '#mfp-swiper' ), {
											initialSlide:           index,
											grabCursor:             false,
											slidesPerView:          'auto',
											scrollbar:              '.swiper-scrollbar',
											scrollbarHide:          false,
											scrollbarDraggable:     true,
											scrollbarSnapOnRelease: true,
											centeredSlides:         true,
											onImagesReady:          function ( swiper ) {
												$( swiper.scrollbar.drag ).addClass( 'mfp-prevent-close' );
												change_slide( index, swiper );
											}
										} );
										$( gallery_magnific_swiper.slides ).click( function () {
											popup.goTo( $( this ).index() );
											change_slide( $( this ).index(), gallery_magnific_swiper );
										} );
									}
									return;
								}
								$( '.mfp-container' ).swipe( {
									swipeLeft:  function () {
										popup.next();
									},
									swipeRight: function () {
										popup.prev();
									}
								} );
							},
							change: function () {
								if ( 'undefined' !== typeof gallery_magnific_swiper ) {
									var index = this.index;
									change_slide( index, gallery_magnific_swiper );
								}
							}
						},
						gallery:         {
							enabled:            true,
							navigateByImgClick: true,
							preload:            [0, 1] // Will preload 0 - before current, and 1 after the current image
						}
					}, index );
				} );
			}
		}
		if ( $( '#gallery-thumbs .wowmall-product-video' ).length && 1 === $( '#gallery-thumbs .swiper-slide' ).length ) {
			$( '.wowmall-product-video' ).magnificPopup( {
				type:            'iframe',
				tLoading:        wowmallParams.preloader,
				fixedContentPos: true,
				mainClass:       'wowmall-single-product-lightbox mfp-fade mfp-with-zoom',
				iframe:          {
					markup:   '<button type="button" class="mfp-close"></button>' +
							  '<div class="mfp-iframe-scaler">' +
							  '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
							  '</div>',
					patterns: {
						youtu: {
							index: 'youtu.be',
							id:    function ( url ) {
								var m = url.match( /^.+youtu.be\/([^?]+)/ );

								if ( null !== m ) {

									return m[1];

								}

								return null;

							},
							src:   '//www.youtube.com/embed/%id%?autoplay=1&rel=0'
						}
					}
				}
			} );
		}
	}

	init_lightbox();

	function change_slide( index, swiper ) {
		if ( swiper instanceof Swiper ) {
			swiper.slideTo( index );
			var slide = $( swiper.slides[index] );
			$( swiper.container ).find( '.swiper-slide' ).removeClass( 'swiper-slide-force-active swiper-slide-force-prev swiper-slide-force-next' );
			slide.addClass( 'swiper-slide-force-active' );
			if ( 0 < index ) {
				$( swiper.slides[index - 1] ).addClass( 'swiper-slide-force-prev' );
			}
			if ( 'undefined' !== typeof swiper.slides[index + 1] ) {
				$( swiper.slides[index + 1] ).addClass( 'swiper-slide-force-next' );
			}
		}
	}

	$( '.variations_form' ).on( 'wc_variation_form', function () {
			$( '.variations_form' ).on( 'update_variation_values', function () {
				$( '.variations_form' ).off( 'found_variation', init_lightbox_variation ).on( 'found_variation', init_lightbox_variation );
			} ).trigger( 'check_variations' );
		} )
		.on( 'reset_image', function () {
			if ( 'undefined' !== typeof singleProductLightbox && 'undefined' !== typeof singleProductLightbox.thumbs && first_thumb && first_thumb !== singleProductLightbox.thumbs[0] ) {
				singleProductLightbox.thumbs[0] = first_thumb;
			}
			if ( !first_thumb ) {
				if ( 'undefined' === typeof singleProductLightbox ) {
					var singleProductLightbox = {};
				}
				singleProductLightbox.thumbs = [];
			}
			setTimeout(
				init_lightbox, 100
			);
			$( '.variations_form' ).off( 'found_variation', init_lightbox_variation ).on( 'found_variation', init_lightbox_variation );
		} );

	function init_lightbox_variation( event, variation ) {
		if ( 'undefined' !== typeof variation.lightbox_thumb && 'undefined' !== typeof singleProductLightbox && 'undefined' !== typeof singleProductLightbox.thumbs ) {
			singleProductLightbox.thumbs[0] = variation.lightbox_thumb;
		}
		if ( 'undefined' !== typeof singleProductLightbox && 'undefined' !== typeof singleProductLightbox.thumbs && 0 === singleProductLightbox.thumbs.length && 'undefined' !== typeof variation.lightbox_thumb && $( '.wowmall-product-video-frame' ).length ) {
			singleProductLightbox.thumbs[0] = variation.lightbox_thumb;
		}
		init_lightbox();
	}
})( jQuery );