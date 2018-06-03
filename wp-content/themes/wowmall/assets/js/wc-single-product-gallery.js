(function ( $ ) {
	'use strict';
	var thumbsSwiper = new Swiper( '#gallery-thumbs', {
			direction:                    'vertical',
			slidesPerView:                'auto',
			nextButton:                   '#next-thumbs',
			prevButton:                   '#prev-thumbs',
			preloadImages:                true,
			lazyLoading:                  true,
			lazyLoadingInPrevNext:        true,
			lazyLoadingOnTransitionStart: true,
			lazyLoadingInPrevNextAmount:  9999,
			spaceBetween:                 11
		} ),
		effect       = 'undefined' !== typeof singleSwipeEffect ? singleSwipeEffect : 'slide',
		imagesSwiper = new Swiper( '#gallery-images > .swiper-container', {
			nextButton:                   '#next-images',
			prevButton:                   '#prev-images',
			effect:                       effect,
			preloadImages:                false,
			lazyLoading:                  true,
			lazyLoadingInPrevNext:        true,
			lazyLoadingOnTransitionStart: true,
			cube:                         {
				shadow:       false,
				slideShadows: true,
				shadowOffset: 20,
				shadowScale:  0.94
			},
			onInit:                       function ( swiper ) {
				change_slide( swiper );
			},
			onSlideChangeStart:           function ( swiper ) {
				change_slide( swiper );
			}
		} );
	$( thumbsSwiper.slides ).on( 'click', function () {
		if ( $( this ).find( '.wowmall-product-video' ).length ) {
			return
		}
		imagesSwiper.slideTo( $( this ).index() );
	} );
	$( '.variations_form' ).on( 'reset_image', function ( event ) {
			imagesSwiper.slideTo( 0 );
			var $form          = $( event.target ),
				$product       = $form.closest( '.product' ),
				$product_thumb = $product.find( '.images.product_page_layout_1 #gallery-thumbs .swiper-slide:eq(0) img' ),
				$product_img   = $product.find( '.images.product_page_layout_1 #gallery-images img:eq(0)' ),
				$product_link  = $product.find( '.images.product_page_layout_1 #gallery-images .swiper-slide:eq(0) a.zoom' );
			$product_thumb.wc_reset_variation_attr( 'src' );
			$product_thumb.wc_reset_variation_attr( 'srcset' );
			$product_thumb.wc_reset_variation_attr( 'sizes' );
			$product_img.wc_reset_variation_attr( 'src' );
			$product_img.wc_reset_variation_attr( 'title' );
			$product_img.wc_reset_variation_attr( 'alt' );
			$product_img.wc_reset_variation_attr( 'srcset' );
			$product_img.wc_reset_variation_attr( 'sizes' );
			$product_link.wc_reset_variation_attr( 'href' );
			if ( $product.find( '.images.product_page_layout_1 #gallery-thumbs .swiper-slide.wowmall-variation-slide:eq(0)' ).length ) {
				thumbsSwiper.removeSlide( 0 );
				imagesSwiper.removeSlide( 0 );
			}
			$( '.variations_form' ).off( 'found_variation', wowmall_found_variation ).on( 'found_variation', wowmall_found_variation );
		} )
		.on( 'wc_variation_form', function ( event ) {
			var $form = $( event.target )
			$form.on( 'update_variation_values', function () {
					$form.off( 'found_variation', wowmall_found_variation ).on( 'found_variation', wowmall_found_variation );
				} )
				.trigger( 'check_variations' );
		} );
	function wowmall_found_variation( event, variation ) {
		imagesSwiper.slideTo( 0 );
		thumbsSwiper.slideTo( 0 );
		var $form          = $( event.target ),
			$product       = $form.closest( '.product' ),
			$product_thumb = $product.find( '.images.product_page_layout_1 #gallery-thumbs .swiper-slide:eq(0) img' ),
			$product_img   = $product.find( '.images.product_page_layout_1 #gallery-images img:eq(0)' ),
			$product_link  = $product.find( '.images.product_page_layout_1 #gallery-images .swiper-slide:eq(0) a.zoom' );
		if ( variation && variation.image.src && variation.image.src.length > 1 ) {
			$product_thumb.wc_set_variation_attr( 'src', variation.gallery_thumb.src );
			$product_thumb.wc_set_variation_attr( 'srcset', variation.image.srcset );
			$product_thumb.wc_set_variation_attr( 'sizes', variation.image.sizes );
			$product_img.wc_set_variation_attr( 'src', variation.image.src );
			$product_img.wc_set_variation_attr( 'title', variation.image.title );
			$product_img.wc_set_variation_attr( 'alt', variation.image.alt );
			$product_img.wc_set_variation_attr( 'srcset', variation.image.srcset );
			$product_img.wc_set_variation_attr( 'sizes', variation.image.sizes );
			$product_link.wc_set_variation_attr( 'href', variation.image.full_src );
			if ( $product.find( '.images.product_page_layout_1 #gallery-thumbs .swiper-slide:eq(0) .wowmall-product-video' ).length && 'undefined' !== typeof variation.gallery_thumb ) {
				var slide = '<div class="swiper-slide wowmall-variation-slide"><span><img src="' + variation.gallery_thumb[0] + '" width="' + variation.gallery_thumb[1] + '" height="' + variation.gallery_thumb[2] + '" alt="' + variation.image.alt + '" srcset="' + variation.image.srcset + '" title="' + variation.image.title + '" sizes="' + variation.image.sizes + '"></span></div>';
				thumbsSwiper.prependSlide( slide );
				$( thumbsSwiper.slides ).off( 'click' ).on( 'click', function () {
					if ( $( this ).find( '.wowmall-product-video' ).length ) {
						return
					}
					imagesSwiper.slideTo( $( this ).index() );
				} );
				slide = '<div class="swiper-slide wowmall-variation-slide"><a href="' + variation.image.url + '" class=zoom title="' + variation.image.caption + '" data-rel="prettyPhoto[product-gallery]"><img src="' + variation.image.src + '" class="attachment-woo_img_size_single_1 size-woo_img_size_single_1 swiper-lazy swiper-lazy-loaded" alt="' + variation.image.alt + '" srcset="' + variation.image.srcset + '" sizes="' + variation.image.sizes + '"></a></div>';
				imagesSwiper.prependSlide( slide );
				thumbsSwiper.slideTo( 0 );
				imagesSwiper.slideTo( 0 );
			}
		}
		else {
			$product_thumb.wc_reset_variation_attr( 'src' );
			$product_thumb.wc_reset_variation_attr( 'srcset' );
			$product_thumb.wc_reset_variation_attr( 'sizes' );
			$product_img.wc_reset_variation_attr( 'src' );
			$product_img.wc_reset_variation_attr( 'title' );
			$product_img.wc_reset_variation_attr( 'alt' );
			$product_img.wc_reset_variation_attr( 'srcset' );
			$product_img.wc_reset_variation_attr( 'sizes' );
			$product_link.wc_reset_variation_attr( 'href' );
			if ( $product.find( '.images.product_page_layout_1 #gallery-thumbs .swiper-slide.wowmall-variation-slide:eq(0)' ).length ) {
				thumbsSwiper.removeSlide( 0 );
				imagesSwiper.removeSlide( 0 );
			}
		}
		var el = $( '.images.product_page_layout_1 #gallery-images .swiper-slide:eq(0) a.zoom' )[0];
		if ( 'undefined' === typeof el ) {
			return;
		}
		$( el ).trigger( 'zoom.destroy' );
		$.wowmall_wc_zoom( el );
		$.wowmall_lazy_images();
	}

	function change_slide( swiper ) {
		thumbsSwiper.slideTo( swiper.activeIndex );
		var slide = $( thumbsSwiper.slides[swiper.activeIndex] );
		$( thumbsSwiper.container ).find( '.swiper-slide' ).removeClass( 'swiper-slide-force-active swiper-slide-force-prev swiper-slide-force-next' );
		slide.addClass( 'swiper-slide-force-active' );
		if ( 0 < swiper.activeIndex ) {
			$( thumbsSwiper.slides[swiper.activeIndex - 1] ).addClass( 'swiper-slide-force-prev' );
		}
		if ( 'undefined' !== typeof thumbsSwiper.slides[swiper.activeIndex + 1] ) {
			$( thumbsSwiper.slides[swiper.activeIndex + 1] ).addClass( 'swiper-slide-force-next' );
		}
	}

	$( document ).trigger( 'wowmall_wc_products_changed' );
})( jQuery );