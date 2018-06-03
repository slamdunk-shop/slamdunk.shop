(function ( $ ) {
	'use strict';
	if ( 'undefined' !== typeof wowmall_gallery && 'undefined' !== typeof wowmallParams ) {
		var gallery_magnific_swiper;
		$( '#wowmall-gallery' ).magnificPopup( {
			delegate       : 'a.zoom-gallery',
			type           : 'image',
			tLoading       : wowmallParams.preloader,
			fixedContentPos: true,
			mainClass      : 'wowmall-gallery-lightbox mfp-fade mfp-with-zoom',
			closeMarkup    : '<button title="%title%" type="button" class="mfp-close"></button>',
			gallery        : {
				enabled           : true,
				navigateByImgClick: true,
				preload           : [ 0, 1 ] // Will preload 0 - before current, and 1 after the current image
			},
			image          : {
				tError  : '<a href="%url%">The image #%curr%</a> could not be loaded.',
				titleSrc: function ( item ) {
					return item.el.attr( 'title' );
				},
				markup  : '<div class="mfp-figure">' +
				'<div class="mfp-close"></div>' +
				'<figure>' +
				'<div class="mfp-img"></div>' +
				'<figcaption>' +
				'<div class="mfp-top-bar">' +
				'<h3 class="mfp-title"></h3>' +
				'<div class="mfp-tags"></div>' +
				'</div>' +
				'</figcaption>' +
				'</figure>' +
				'</div>'
			},
			zoom           : {
				enabled : true,
				duration: 300,
				easing  : 'ease-in-out',
				opener  : function ( element ) {
					return element.find( 'img' );
				}
			},
			callbacks      : {
				open             : function () {
					var popup = this;
					if ( ! $( 'body' ).hasClass( 'mobile' ) ) {
						if ( wowmall_gallery.thumbs_swiper ) {
							var index = this.index;
							$( this.container ).append( '<div class="mfp-thumbs mfp-prevent-close"/>' );
							var thumbs = $( this.container ).find( '.mfp-thumbs' );
							thumbs.append( wowmall_gallery.thumbs_swiper );
							gallery_magnific_swiper = new Swiper( thumbs.find( '#mfp-swiper' ), {
								initialSlide          : index,
								grabCursor            : false,
								slidesPerView         : 'auto',
								scrollbar             : '.swiper-scrollbar',
								scrollbarHide         : false,
								scrollbarDraggable    : true,
								scrollbarSnapOnRelease: true,
								centeredSlides        : true,
								onImagesReady         : function ( swiper ) {
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
						swipeLeft : function () {
							popup.next();
						},
						swipeRight: function () {
							popup.prev();
						}
					} );
				},
				imageLoadComplete: function () {
					var index          = this.index,
						tags           = $( this.ev ).find( '.wowmall-gallery-item:not(.wowmall-gallery-cat-item)' ).eq( index ).find( '.gallery-tags' ).html(),
					    tags_container = $( this.container ).find( '.mfp-tags' );
					tags_container.html( tags );
				},
				change           : function () {
					if ( 'undefined' !== typeof gallery_magnific_swiper ) {
						var index = this.index;
						change_slide( index, gallery_magnific_swiper );
					}
				}
			}
		} );
		$( 'a.zoom-cat-gallery' ).each( function () {
			var target = $( this ),
			    cid    = target.data( 'cid' );
			if ( undefined !== window[ 'wowmall_gallery_' + cid ] ) {
				var items        = window[ 'wowmall_gallery_' + cid ][ 'items' ],
				    swiper_items = window[ 'wowmall_gallery_' + cid ][ 'thumbs' ] ? window[ 'wowmall_gallery_' + cid ][ 'thumbs' ] : false,
				    tags         = window[ 'wowmall_gallery_' + cid ][ 'tags' ] ? window[ 'wowmall_gallery_' + cid ][ 'tags' ] : false;
				if ( undefined !== items ) {
					target.magnificPopup( {
						type           : 'image',
						items          : items,
						tLoading       : wowmallParams.preloader,
						mainClass      : 'wowmall-gallery-lightbox mfp-fade mfp-with-zoom',
						closeMarkup    : '<button title="%title%" type="button" class="mfp-close"></button>',
						fixedContentPos: true,
						gallery        : {
							enabled           : true,
							navigateByImgClick: true,
							preload           : [ 0, 1 ]
						},
						image          : {
							tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
							markup: '<div class="mfp-figure">' +
							'<div class="mfp-close"></div>' +
							'<figure>' +
							'<div class="mfp-img"></div>' +
							'<figcaption>' +
							'<div class="mfp-top-bar">' +
							'<h3 class="mfp-title"></h3>' +
							'<div class="mfp-tags"></div>' +
							'</div>' +
							'</figcaption>' +
							'</figure>' +
							'</div>'
						},
						callbacks      : {
							open             : function () {
								var popup = this;
								if ( ! $( 'body' ).hasClass( 'mobile' ) ) {
									if ( swiper_items ) {
										var index = this.index;
										$( this.container ).append( '<div class="mfp-thumbs mfp-prevent-close"/>' );
										var thumbs = $( this.container ).find( '.mfp-thumbs' );
										thumbs.append( swiper_items );
										gallery_magnific_swiper = new Swiper( thumbs.find( '#mfp-swiper' ), {
											initialSlide          : index,
											grabCursor            : false,
											slidesPerView         : 'auto',
											scrollbar             : '.swiper-scrollbar',
											scrollbarHide         : false,
											scrollbarDraggable    : true,
											scrollbarSnapOnRelease: true,
											centeredSlides        : true,
											onImagesReady         : function ( swiper ) {
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
									swipeLeft : function () {
										popup.next();
									},
									swipeRight: function () {
										popup.prev();
									}
								} );
							},
							imageLoadComplete: function () {
								if ( ! tags ) {
									return;
								}
								var index          = this.index,
								    tags_container = $( this.container ).find( '.mfp-tags' );
								tags_container.html( tags[ index ] );
							},
							change           : function () {
								if ( 'undefined' !== typeof gallery_magnific_swiper ) {
									var index = this.index;
									change_slide( index, gallery_magnific_swiper );
								}
							}
						}
					} );
				}
			}
		} );
	}
	function change_slide( index, swiper ) {
		swiper.slideTo( index );
		var slide = $( swiper.slides[ index ] );
		$( swiper.container ).find( '.swiper-slide' ).removeClass( 'swiper-slide-force-active swiper-slide-force-prev swiper-slide-force-next' );
		slide.addClass( 'swiper-slide-force-active' );
		if ( 0 < index ) {
			$( swiper.slides[ index - 1 ] ).addClass( 'swiper-slide-force-prev' );
		}
		if ( 'undefined' !== typeof swiper.slides[ index + 1 ] ) {
			$( swiper.slides[ index + 1 ] ).addClass( 'swiper-slide-force-next' );
		}
	}

	function resize_gallery() {
		$( '#wowmall-gallery' ).each( function () {
			var wrapper   = $( this ).find( '.wowmall-gallery-wrapper' ),
			    container = wrapper.find( '.wowmall-gallery-container' ),
			    cols      = container.data( 'cols' ),
			    w         = parseInt( wrapper.width() / cols ) * cols;
			container.css( {
				maxWidth: w
			} );
		} );
	}

	resize_gallery();
	$( window ).on( 'resize', $.debounce( 500, resize_gallery ) );
})( jQuery );