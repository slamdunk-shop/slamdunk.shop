<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Output the start of the page wrapper.
 */

if ( ! function_exists( 'wowmall_wc_output_content_wrapper' ) ) {

	function wowmall_wc_output_content_wrapper() {
		global $wowmall_options;
		if ( is_product_category() ) {
			$term         = get_queried_object();
			$display_type = get_term_meta( $term->term_id, 'display_type', true );

			if ( '' === $display_type ) {
				$display_type = get_option( 'woocommerce_category_archive_display' );
			}
			if ( 'subcategories' === $display_type ) {
				echo '<div class=container>';

				return;
			}
		}
		if ( ! is_search() && ! empty( $wowmall_options['wc_shop_layout'] ) && '2' === $wowmall_options['wc_shop_layout'] && is_shop() ) {
			echo '<div class=container>';

			return;
		}
		if ( $cat_layout = ( ! is_search() && ! empty( $wowmall_options['wc_loop_cat_layout'] ) && '2' === $wowmall_options['wc_loop_cat_layout'] && is_product_category() && 0 === absint( get_query_var( 'paged' ) ) ) && have_posts() ) {
			$term         = get_queried_object();
			$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
			$image        = '';
			if ( $thumbnail_id ) {
				$image = wp_get_attachment_image_src( $thumbnail_id, 'original' );
				$image = ' style="background-image:url(' . $image[0] . ')"';
			}
			echo '<div class=term-description' . $image . '><div class=container><div class=row><div class="col-xl-6 col-xs-12 term-description-col"><div class=term-description-outer><div class=term-description-inner><h2 class=wc-product-category-title>';
			woocommerce_page_title();
			echo '</h2>';

			/**
			 * woocommerce_archive_description hook.
			 * @hooked woocommerce_taxonomy_archive_description - 10
			 * @hooked woocommerce_product_archive_description - 10
			 */
			do_action( 'woocommerce_archive_description' );
			woocommerce_product_subcategories( array(
				'before' => '<ul class=wc-loop-product-categories>',
				'after'  => '</ul>',
			) );
			echo '</div></div></div></div></div></div>';
		}
		echo '<div class=container>';
		if ( ! $cat_layout && have_posts() ) {
			if ( apply_filters( 'woocommerce_show_page_title', true ) ) { ?>
				<header class=page-header>
					<h1 class="page-title woocommerce-page-title"><?php
						woocommerce_page_title();
						?></h1></header>
				<?php
			}

			/**
			 * woocommerce_archive_description hook.
			 * @hooked woocommerce_taxonomy_archive_description - 10
			 * @hooked woocommerce_product_archive_description - 10
			 */
			do_action( 'woocommerce_archive_description' );
			woocommerce_product_subcategories( array(
				'before' => '<ul class=wc-loop-product-categories>',
				'after'  => '</ul>',
			) );
		}
		$col         = ' col-xxl-10 col-xl-9 col-md-8';
		$shop_layout = ! empty( $wowmall_options['wc_shop_layout'] ) ? $wowmall_options['wc_shop_layout'] : '1';
		$layout      = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
		if ( ! is_active_sidebar( 'sidebar-shop' ) ) {
			$col = ' col-xs-12';
		}
		if ( ( '2' === $shop_layout && ! is_search() && ! is_product_taxonomy() && ! is_product() ) || ( '3' === $layout && ! is_product() ) ) {
			$col = ' col-xs-12';
		}
		if ( is_product() && ( ! isset( $wowmall_options['product_sidebar'] ) || ! $wowmall_options['product_sidebar'] ) ) {
			$col = ' col-xs-12';
		}
		echo '<div class=row>
	<main id=primary class="site-main content-area' . $col . '">';
	}
}

if ( ! function_exists( 'wowmall_wc_output_content_wrapper_end' ) ) {
	function wowmall_wc_output_content_wrapper_end() {
		global $wowmall_options;
		if ( is_product_category() ) {
			$term         = get_queried_object();
			$display_type = get_term_meta( $term->term_id, 'display_type', true );

			if ( '' === $display_type ) {
				$display_type = get_option( 'woocommerce_category_archive_display' );
			}
			if ( 'subcategories' === $display_type ) {
				echo '</div>';

				return;
			}
		}
		if ( ! is_search() && ! empty( $wowmall_options['wc_shop_layout'] ) && '2' === $wowmall_options['wc_shop_layout'] && is_shop() ) {
			echo '</div>';

			return;
		}
		echo '</main>';
	}
}

if ( ! function_exists( 'wowmall_wc_output_wrapper_end' ) ) {
	function wowmall_wc_output_wrapper_end() {
		echo '</div>
</div>';
	}
}

if ( ! function_exists( 'wowmall_wc_show_page_title' ) ) {
	function wowmall_wc_show_page_title() {

		if ( is_product() ) {
			return false;
		}
		global $wowmall_options;
		if ( is_shop() && ( isset( $wowmall_options['shop_page_title'] ) && ! $wowmall_options['shop_page_title'] ) ) {
			return false;
		}

		return true;
	}
}

function wowmall_single_product_large_thumbnail_size() {
	global $wowmall_options;
	if ( ! isset( $wowmall_options['product_page_layout'] ) || '2' === $wowmall_options['product_page_layout'] ) {
		return 'woo_img_size_single_2';
	}

	return 'woo_img_size_single_1';
}

function wowmall_wc_single_grid_start() {
	global $wowmall_wc_quick_view;
	if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
		return;
	}
	echo '<div class=row><div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 images-wrapper">';
}

function wowmall_woocommerce_single_grid_middle() {
	global $wowmall_options, $wowmall_wc_quick_view;
	if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
		echo '<div class=wc-quick-view-content-wrapper>';

		return;
	}
	echo '</div><div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 wowmall-wc-single-product-content-wrapper">';
	if ( ! wp_is_mobile() && ! empty( $wowmall_options['product_stiky'] ) ) {
		echo '<div class=stick-in-parent>';
	}
}

function wowmall_woocommerce_single_grid_end() {
	global $wowmall_options, $wowmall_wc_quick_view;
	if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
		echo '</div>';

		return;
	}
	$layout = ! empty( $wowmall_options['product_page_layout'] ) ? $wowmall_options['product_page_layout'] : '2';
	if ( '2' === $layout && ! wp_is_mobile() && ! empty( $wowmall_options['product_stiky'] ) ) {
		echo '</div>';
	}
	echo '</div></div>';
}

function wowmall_woocommerce_template_single_title() {
	global $wowmall_wc_quick_view;
	if ( ! empty( $wowmall_wc_quick_view ) ) {
		the_title( '<h1 class="product_title entry-title"><a href="' . get_permalink() . '">', '</a></h1>' );

		return;
	}
	the_title( '<h1 class="product_title entry-title">', '</h1>' );
}

function wowmall_get_condition() {
	global $wowmall_wc_loop_condition;
	if ( isset( $wowmall_wc_loop_condition ) ) {
		return $wowmall_wc_loop_condition;
	}
	$condition = ! empty( $_COOKIE['wowmall-wc-grid-list'] ) ? $_COOKIE['wowmall-wc-grid-list'] : 'grid';
	if ( ! wp_is_mobile() && 'big' === $condition ) {
		$condition = 'grid';
	}

	return $condition;
}

function wowmall_woocommerce_show_product_flashes() {
	global $wowmall_options, $product, $woocommerce_loop, $wowmall_wc_quick_view;
	$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	if ( '2' === $layout && ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	ob_start();
	woocommerce_show_product_sale_flash();
	$new = get_post_meta( $product->get_id(), '_new', true );
	if ( 'yes' === $new ) {
		echo '<span class=new>' . esc_html__( 'New', 'wowmall' ) . '</span>';
	}
	$condition = wowmall_get_condition();
	if ( ! $product->is_in_stock() || ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) || ( 'list' === $condition && ( is_shop() || is_product_taxonomy() ) ) || ( is_product() && ! ( ! empty( $woocommerce_loop['name'] ) && ( 'related' === $woocommerce_loop['name'] || 'up-sells' === $woocommerce_loop['name'] || 'cross-sells' === $woocommerce_loop['name'] ) ) ) ) {
		$availability      = $product->get_availability();
		$availability_html = empty( $availability['availability'] ) ? '' : '<span class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</span>';
		if ( empty( $availability_html ) && ! empty( $wowmall_options['instock_label'] ) ) {
			$availability_html = 'in-stock' === $availability['class'] ? '<span class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html__( 'In stock', 'wowmall' ) . '</span>' : '';
		}
		echo '' . $availability_html;
	}
	if ( $product->is_featured() ) {
		echo '<span class=featured>' . esc_html__( 'Featured', 'wowmall' ) . '</span>';
	}
	$status = ob_get_clean();
	if ( ! empty( $status ) ) {
		echo '<div class=product-status>' . $status . '</div>';
	}
}

function wowmall_woocommerce_template_single_cats() {
	global $product, $wowmall_wc_quick_view, $wowmall_options, $woocommerce_loop;
	if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
		return;
	}
	ob_start();
	do_action( 'woocommerce_product_meta_start' );

	echo wc_get_product_category_list( $product->get_id(), ', ', '', '' );

	do_action( 'woocommerce_product_meta_end' );
	$condition = is_product() ? 'grid' : wowmall_get_condition();
	$content   = ob_get_clean();
	if ( is_product() && ! ( ! empty( $woocommerce_loop['name'] ) && ( 'related' === $woocommerce_loop['name'] || 'up-sells' === $woocommerce_loop['name'] || 'cross-sells' === $woocommerce_loop['name'] ) ) && wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) && ( ! isset( $wowmall_options['sku'] ) || $wowmall_options['sku'] ) ) { ?>

		<span class="sku_wrapper product_meta"><?php esc_html_e( 'SKU:', 'wowmall' ); ?> <span
					class=sku><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'wowmall' ); ?></span></span>

	<?php }
	if ( ( ( ( ! empty( $content ) && 'list' === $condition ) || 'grid' === $condition || wp_is_mobile() ) && ( is_shop() || is_product_taxonomy() ) ) || ( ! empty( $content ) && is_product() ) || ! ( is_product() || is_shop() || is_product_taxonomy() ) ) { ?>
		<div class=loop-product-categories>
			<?php
			if ( empty( $content ) ) {
				$content = '&nbsp;';
			}
			echo '' . $content; ?>
		</div>
		<?php
	}
}

function wowmall_woocommerce_template_single_tags() {
	global $post, $product, $wowmall_wc_quick_view;
	if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
		return;
	}
	$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
	ob_start();
	do_action( 'woocommerce_product_meta_start' );
	echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class=tagged_as>' . _n( 'Tag', 'Tags', $tag_count, 'wowmall' ) . ': ', '</span>' );
	do_action( 'woocommerce_product_meta_end' );
	$content = ob_get_clean();
	if ( ! empty( $content ) ) { ?>
		<div class=product_meta>
			<?php echo '' . $content; ?>
		</div>
		<?php
	}
}

function wowmall_woocommerce_template_single_rating() {

	if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
		return;
	}
	global $product, $wowmall_wc_quick_view;

	$rating_count = $product->get_rating_count();

	if ( 1 > $rating_count ) {
		return;
	}
	$review_count = $product->get_review_count();
	$average      = $product->get_average_rating();
	?>
	<div class=woocommerce-product-rating>
		<div class=star-rating title="<?php printf( esc_html__( 'Rated %s out of 5', 'wowmall' ), $average ); ?>">
			<span style="width:<?php echo( ( $average / 5 ) * 100 ); ?>%">
				<strong class=rating><?php echo esc_html( $average ); ?></strong> <?php printf( esc_html__( 'out of %s5%s', 'wowmall' ), '<span>', '</span>' ); ?>
				<?php printf( esc_html__( 'based on %s customer rating(s)', 'wowmall' ), '<span class=rating>' . $rating_count . '</span>' ); ?>
			</span>
		</div>
		<?php
		if ( comments_open() ) {
			$url = '#reviews';
			if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
				$url = $product->get_permalink() . $url;
			}
			$string = '<a href=' . $url . ' class=woocommerce-review-link rel=nofollow>%s</a>';
		}
		else {
			$string = '<span>%s</span>';
		}
		printf( $string, sprintf( esc_html__( '%s Review(s)', 'wowmall' ), '<span class=count>' . $review_count . '</span>' ) );
		?>
	</div>
	<?php
}

function wowmall_wc_template_single_excerpt() {
	global $wowmall_options;
	if ( wp_is_mobile() && ( ! isset( $wowmall_options['wc_excerpt_mobile'] ) || ! $wowmall_options['wc_excerpt_mobile'] ) ) {
		return;
	}
	woocommerce_template_single_excerpt();
}

function wowmall_wc_price_rating_wrapper() {
	$condition = is_product() ? 'grid' : wowmall_get_condition();
	$filter    = current_filter();
	global $wowmall_options, $wowmall_wc_quick_view;
	$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	if ( ( ( ( is_product() && ! wp_is_mobile() ) || ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) ) && 'woocommerce_single_product_summary' === $filter ) || ( 'list' === $condition && '2' !== $layout && ( is_shop() || is_product_taxonomy() ) && 'woocommerce_after_shop_loop_item_title' === $filter ) || ( 'big' !== $condition && wp_is_mobile() && ( is_shop() || is_product_taxonomy() ) && 'woocommerce_after_shop_loop_item_title' === $filter ) ) {
		echo '<div class=price-rating-wrapper>';
	}
}

function wowmall_wc_price_rating_wrapper_end() {
	$condition = is_product() ? 'grid' : wowmall_get_condition();
	$filter    = current_filter();
	global $wowmall_options, $wowmall_wc_quick_view;
	$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	if ( ( ( ( is_product() && ! wp_is_mobile() ) || ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) ) && 'woocommerce_single_product_summary' === $filter ) || ( 'list' === $condition && '2' !== $layout && ( is_shop() || is_product_taxonomy() ) && 'woocommerce_after_shop_loop_item_title' === $filter ) || ( 'big' !== $condition && wp_is_mobile() && ( is_shop() || is_product_taxonomy() ) && 'woocommerce_after_shop_loop_item_title' === $filter ) ) {
		echo '</div>';
	}

}

function wowmall_wc_single_add_to_cart_buttons_wrapper() {
	echo '<div class=wowmall-wc-single-add-to-cart-buttons-wrapper>';
}

function wowmall_woocommerce_product_collapse() {
	$tabs = apply_filters( 'woocommerce_product_tabs', array() );
	if ( empty( $tabs ) ) {
		return;
	}
	global $wowmall_options;
	?>
	<div class=wc-product-collapse id=wc-product-collapse role=tablist aria-multiselectable=true>
	<?php foreach ( $tabs as $key => $tab ) {
		$expanded = false;
		if ( 'description' === $key && ! empty( $wowmall_options['desc_tab_opened'] ) ) {
			$expanded = true;
		}
		?>
		<div class="panel collapse-panel">
			<a id=heading-<?php echo esc_attr( $key ); ?> role=tab data-toggle=collapse
			   data-parent=#wc-product-collapse href=#<?php echo esc_attr( $key ); ?>
			   aria-expanded=<?php echo empty( $expanded ) ? 'false' : 'true'; ?>
			   aria-controls=<?php echo esc_attr( $key ); ?> class=collapsed>
				<?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?>
			</a>
			<div id=<?php echo esc_attr( $key ); ?> class="panel-collapse collapse<?php if ( ! empty( $expanded ) ) {
				echo ' in';
			} ?>" role=tabpanel
			aria-labelledby=heading-<?php echo esc_attr( $key ); ?>
			aria-expanded=<?php echo empty( $expanded ) ? 'false' : 'true'; ?>>
			<div class=collapse-body>
				<?php call_user_func( $tab['callback'], $key, $tab ); ?>
			</div>
		</div>
		</div>
	<?php } ?>
	</div>
	<?php
}

function wowmall_woocommerce_review_gravatar_size() {
	return 72;
}

function wowmall_wc_single_product_image_html( $image ) {

	$detect = wowmall_func()->mobile_detect();
	if ( $detect->isMobile() && ! $detect->isTablet() ) {
		return '<div id=wowmall-wc-mobile-single-images class=swiper-container><div class=swiper-wrapper><div class=swiper-slide>' . $image . '</div>';
	}

	return $image;
}

function wowmall_woocommerce_show_product_images() {
	global $wowmall_options, $wowmall_wc_quick_view, $post, $product;

	$attachment_ids = $product->get_gallery_image_ids();
	$thumb_id       = get_post_thumbnail_id();
	if ( ! empty( $thumb_id ) ) {
		array_unshift( $attachment_ids, $thumb_id );
	}
	if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
		?>
		<div class="images quick-view-images">
			<a href="<?php the_permalink(); ?>">
				<?php
				if ( ! empty( $attachment_ids ) ) {

					$effect = isset( $wowmall_options['wc_quick_view_thumb_effect'] ) ? $wowmall_options['wc_quick_view_thumb_effect'] : 'slide'; ?>
					<div class=swiper-container data-effect=<?php echo esc_attr( $effect ); ?>>
						<div class=swiper-wrapper>
							<?php

							foreach ( $attachment_ids as $attachment_id ) {
								printf( '<div class=swiper-slide>%s</div>', wp_get_attachment_image( $attachment_id, 'woo_img_size_single_1', 0 ) );
							} ?>
						</div>
						<div class=swiper-button-prev id=prev-quick-view></div>
						<div class=swiper-button-next id=next-quick-view></div>
					</div>
					<?php
				}
				else {
					echo wc_placeholder_img( 'woo_img_size_single_1' );
				}
				?>
			</a>
		</div>
		<?php
		return;
	}
	$lightbox = ! isset( $wowmall_options['product_lightbox'] ) || $wowmall_options['product_lightbox'];
	if ( $lightbox ) {
		$thumbs = array();

		if ( ! wp_is_mobile() && ! empty( $attachment_ids ) && 0 < count( $attachment_ids ) ) {

			foreach ( $attachment_ids as $attachment_id ) {

				$thumbs[] = wp_get_attachment_image_src( $attachment_id, 'gallery_img_size_lightbox_thumb', 0 );
			}
		}
		wp_localize_script( 'single-product-lightbox', 'singleProductLightbox', array(
			'thumbs' => $thumbs,
		) );
	}

	if ( wp_is_mobile() || empty( $wowmall_options['product_page_layout'] ) || '2' === $wowmall_options['product_page_layout'] ) {
		woocommerce_show_product_images();

		return;
	}
	$video_url = get_post_meta( $product->get_id(), '_video_url', true );
	$video_url = esc_url( $video_url );
	if ( ! empty( $video_url ) && ( false !== strpos( $video_url, 'youtube.com/' ) || false !== strpos( $video_url, 'youtu.be/' ) || false !== strpos( $video_url, 'vimeo.com/' ) ) ) {
		array_push( $attachment_ids, 'wowmall_product_video' );
	}
	?>
	<div class="images product_page_layout_1">
		<?php $gallery = 1 < count( $attachment_ids ) ? '[product-gallery]' : ''; ?>
		<div class=thumbs-wrapper>
			<div id=gallery-thumbs class=swiper-container>
				<div class=swiper-wrapper>
					<?php
					if ( ! empty( $attachment_ids ) ) {
						foreach ( $attachment_ids as $attachment_id ) {

							if ( 'wowmall_product_video' === $attachment_id ) {
								$ratio = '116.9230769230769';
								if ( isset( $wowmall_options['woo_img_size_single_thumb']['width'] ) && isset( $wowmall_options['woo_img_size_single_thumb']['height'] ) ) {
									$ratio = $wowmall_options['woo_img_size_single_thumb']['height'] / $wowmall_options['woo_img_size_single_thumb']['width'];
								}
								if ( 2 < $ratio ) {
									$ratio = 2;
								}
								if ( .5 > $ratio ) {
									$ratio = .5;
								}
								$ratio = $ratio * 100;
								$video = '<a style="padding-top:' . $ratio . '%" href="' . $video_url . '" class="wowmall-product-video"><span>' . esc_html__( 'Video', 'wowmall' ) . '</span></a>';
								echo '<div class=swiper-slide><span>' . $video . '</span></div>';
								continue;
							}

							echo sprintf( '<div class=swiper-slide><span>%s</span></div>', wp_get_attachment_image( $attachment_id, 'woo_img_size_single_thumb', 0 ) );
						}
					}
					else {
						echo sprintf( '<div class=swiper-slide><span>%s</span></div>', wc_placeholder_img( 'woo_img_size_single_thumb' ) );
					} ?>
				</div>
				<?php if ( $gallery ) { ?>
					<div class=swiper-button-prev id=prev-thumbs></div>
					<div class=swiper-button-next id=next-thumbs></div>
				<?php } ?>
			</div>
		</div>
		<div id=gallery-images>
			<div class=swiper-container>
				<div class=swiper-wrapper>
					<?php

					if ( ! empty( $attachment_ids ) ) {

						$image_class = '';

						if ( ! isset( $wowmall_options['product_zoom'] ) || $wowmall_options['product_zoom'] ) {

							$image_class = 'zoom';
						}
						foreach ( $attachment_ids as $attachment_id ) {

							if ( 'wowmall_product_video' === $attachment_id ) {
								if ( 1 === count( $attachment_ids ) ) {
									$ratio = '116.9230769230769';
									if ( isset( $wowmall_options['woo_img_size_single_1']['width'] ) && isset( $wowmall_options['woo_img_size_single_1']['height'] ) ) {
										$ratio = $wowmall_options['woo_img_size_single_1']['height'] / $wowmall_options['woo_img_size_single_1']['width'];
									}
									if ( 2 < $ratio ) {
										$ratio = 2;
									}
									if ( .5 > $ratio ) {
										$ratio = .5;
									}
									$ratio = $ratio * 100;
									$video = '<a style="padding-top:' . $ratio . '%" href="' . $video_url . '" class="wowmall-product-video"><span>' . esc_html__( 'Video', 'wowmall' ) . '</span></a>';
									echo '<div class=swiper-slide>' . $video . '</div>';
								}
								continue;
							}
							$props = wc_get_product_attachment_props( $attachment_id, $post );

							if ( ! $props['url'] ) {
								continue;
							}

							$link = $lightbox ? '<a href="%1$s" class="%2$s" title="%3$s" data-rel=prettyPhoto%4$s>%5$s</a>' : '<span data-url="%1$s" class="%2$s">%5$s</span>';

							printf( '<div class=swiper-slide>' . $link . '</div>', esc_url( $props['url'] ), esc_attr( $image_class ), esc_attr( $props['caption'] ), $gallery, wp_get_attachment_image( $attachment_id, 'woo_img_size_single_1', 0 ) );
						}
					}
					else {
						printf( '<div class=swiper-slide>%s</div>', wc_placeholder_img( 'woo_img_size_single_1' ) );
					} ?>
				</div>
				<?php if ( $gallery ) { ?>
					<div class=swiper-button-prev id=prev-images></div>
					<div class=swiper-button-next id=next-images></div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php
}

function wowmall_woocommerce_show_product_thumbnails() {

	global $post, $product, $wowmall_options;

	$attachment_ids = $product->get_gallery_image_ids();
	$video          = '';
	$video_url      = get_post_meta( $product->get_id(), '_video_url', true );
	$video_url      = esc_url( $video_url );
	$detect         = wowmall_func()->mobile_detect();

	if ( ! empty( $video_url ) ) {
		if ( false !== strpos( $video_url, 'youtube.com/' ) ) {
			parse_str( parse_url( $video_url, PHP_URL_QUERY ), $query );
			if ( ! empty( $query['v'] ) ) {
				$frame_url = '//www.youtube.com/embed/' . $query['v'];
			}
		}
		elseif ( false !== strpos( $video_url, 'youtu.be/' ) ) {
			$id = parse_url( $video_url, PHP_URL_PATH );
			if ( ! empty( $id ) ) {
				$frame_url = '//www.youtube.com/embed' . $id;
			}
		}
		elseif ( false !== strpos( $video_url, 'vimeo.com/' ) ) {
			$id = ( int ) substr( parse_url( $video_url, PHP_URL_PATH ), 1 );
			if ( $id ) {
				$frame_url = '//player.vimeo.com/video' . $id;
			}
		}
		if ( ! empty( $frame_url ) ) {
			$video = '<div class=wowmall-product-video-frame-outer><div class=wowmall-product-video-frame data-url="' . $video_url . '"><iframe src="' . $frame_url . '" allowfullscreen></iframe></div></div>';
		}
	}

	if ( ! empty( $attachment_ids ) || ! empty( $video ) ) {

		if ( ! $detect->isMobile() || $detect->isTablet() ) { ?>
			<div class=thumbnails><?php
		}
		if ( ! empty( $attachment_ids ) ) {
			foreach ( $attachment_ids as $attachment_id ) {

				$image_class = '';

				if ( ( ! isset( $wowmall_options['product_zoom'] ) || $wowmall_options['product_zoom'] ) && ! wp_is_mobile() ) {

					$image_class = 'zoom';
				}

				$props = wc_get_product_attachment_props( $attachment_id, $post );

				if ( ! $props['url'] ) {
					continue;
				}
				if ( $detect->isMobile() && ! $detect->isTablet() ) { ?>
					<div class=swiper-slide>
				<?php }
				$link = ( ! isset( $wowmall_options['product_lightbox'] ) || $wowmall_options['product_lightbox'] ) ? '<a href="%1$s" class="%2$s" title="%3$s" data-rel=prettyPhoto[product-gallery]>%4$s</a>' : '<span data-url="%1$s" class="%2$s">%4$s</span>';
				printf( $link, esc_url( $props['url'] ), esc_attr( $image_class ), esc_attr( $props['caption'] ), wp_get_attachment_image( $attachment_id, 'woo_img_size_single_2', 0 ) );
				if ( $detect->isMobile() && ! $detect->isTablet() ) { ?>
					</div>
				<?php }
			}
		}
		if ( ! empty( $video ) ) {
			if ( $detect->isMobile() && ! $detect->isTablet() ) { ?>
				<div class=swiper-slide>
			<?php }
			echo $video;
			if ( $detect->isMobile() && ! $detect->isTablet() ) { ?>
				</div>
			<?php }
		}
		if ( ! $detect->isMobile() || $detect->isTablet() ) { ?>
			</div>
		<?php }
	}

	if ( $detect->isMobile() && ! $detect->isTablet() ) { ?>
		</div>
		<?php if ( ! empty( $wowmall_options['product_mobile_arrows'] ) && ( ! empty( $attachment_ids ) || ! empty( $video ) ) ) { ?>
			<div class=swiper-button-next></div>
			<div class=swiper-button-prev></div>
			<?php
		}
		elseif ( ! empty( $video ) ) { ?>
			<div class=swiper-button-prev id=prev-wowmall-wc-mobile-single-images></div>
		<?php } ?>
		</div>
	<?php }
}

function wowmall_woocommerce_template_single_share() {
	global $wowmall_options, $wowmall_wc_quick_view;

	if ( isset( $wowmall_options['is_product_share_enable'] ) && ! $wowmall_options['is_product_share_enable'] ) {
		return;
	}

	if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
		return;
	}
	wowmall_share_buttons();
}

function wowmall_single_product_archive_thumbnail_size() {
	if ( is_product() ) {
		return 'woo_img_size_small';
	}
	if ( ( is_shop() || is_product_taxonomy() ) ) {
		$condition = wowmall_get_condition();
		if ( 'list' === $condition ) {
			return 'woo_img_size_list';
		}
	}
	global $wowmall_options, $woocommerce_loop;
	if ( ! empty( $woocommerce_loop['name'] ) && in_array( $woocommerce_loop['name'], array(
			'recent_products',
			'products',
			'sale_products',
			'best_selling_products',
			'top_rated_products',
			'featured_products',
			'product_attribute',
			'product_cat',
		) ) ) {
		$cols = empty( $woocommerce_loop['columns'] ) ? 4 : absint( $woocommerce_loop['columns'] );
		switch ( $cols ) {
			case '6':
				return 'woo_img_size_small';
				break;
			case '5':
				return 'woo_img_size_average';
				break;
			case '3':
				return 'woo_img_size_list';
				break;
			case '2':
				return 'woo_img_size_single_2';
				break;
			default:
				return 'woo_img_size_big';
				break;
		}
	}
	if ( ! empty( $wowmall_options['wc_loop_layout'] ) ) {
		$col = $wowmall_options['wc_loop_layout'];
		switch ( $col ) {
			case 4:
				return 'woo_img_size_average';
			case 5:
				return 'woo_img_size_big';
			case 6:
				return 'woo_img_size_list';
			case 7:
				return 'woo_img_size_single_2';
			case 8:
				return 'full';
			default:
				return 'woo_img_size_small';
		}
	}

	return 'woo_img_size_small';
}

function wowmall_wc_output_related_products_args( $args ) {
	global $wowmall_options;
	if ( ! empty( $wowmall_options['related_hide_outofstock'] ) ) {
		add_filter( 'pre_option_woocommerce_hide_out_of_stock_items', 'wowmall_wc_hide_out_of_stock_on_related' );
	}
	$args['posts_per_page'] = 5;
	$args['columns']        = 3;

	return $args;
}

function wowmall_wc_product_post_class( $classes, $class = '', $post_id = '' ) {
	if ( ! $post_id || 'product' !== get_post_type( $post_id ) ) {
		return $classes;
	}
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $classes;
	}
	if ( wp_doing_ajax() ) {
		$classes[] = 'product';
	}
	global $woocommerce_loop, $wowmall_options, $wowmall_wc_quick_view, $wowmall_product_in_compare, $wowmall_product_in_wishlist;
	if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
		return $classes;
	}
	if ( ! empty( $wowmall_product_in_compare ) && $wowmall_product_in_compare ) {
		return $classes;
	}
	if ( ! empty( $wowmall_product_in_wishlist ) && $wowmall_product_in_wishlist ) {
		return $classes;
	}
	if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'wowmall_ajax_search' === $_REQUEST['action'] ) {
		return $classes;
	}
	if ( ! isset( $wowmall_options['wc_loop_thumb_zoom'] ) || $wowmall_options['wc_loop_thumb_zoom'] ) {
		$classes[] = 'hover-effect';

	}
	if ( ! is_shop() && is_search() ) {
		return $classes;
	}
	if ( ! empty( $woocommerce_loop['name'] ) && in_array( $woocommerce_loop['name'], array(
			'recent_products',
			'products',
			'sale_products',
			'best_selling_products',
			'top_rated_products',
			'featured_products',
			'product_attribute',
			'product_cat',
		) ) ) {
		$cols = empty( $woocommerce_loop['columns'] ) ? 4 : absint( $woocommerce_loop['columns'] );
		switch ( $cols ) {
			case '6':
				$classes[] = 'col-xxl-2';
				$classes[] = 'col-xl-3';
				$classes[] = 'col-lg-4';
				$classes[] = 'col-md-6';
				break;
			case '5':
				$classes[] = 'col-xxl';
				$classes[] = 'col-xl-4';
				$classes[] = 'col-lg-6';
				break;
			case '3':
				$classes[] = 'col-xl-4';
				$classes[] = 'col-lg-6';
				break;
			case '2':
				$classes[] = 'col-lg-6';
				break;
			default:
				$classes[] = 'col-xxl-3';
				$classes[] = 'col-xl-4';
				$classes[] = 'col-lg-6';
				break;
		}
		$classes[] = 'col-sm-6';

		return $classes;
	}
	$loop_layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	if ( '2' === $loop_layout && ( is_shop() || is_product_taxonomy() ) ) {
		$classes[] = 'wc-loop-catalog-product';
	}
	if ( is_product() ) {
		if ( ! empty( $woocommerce_loop['name'] ) && ( 'related' === $woocommerce_loop['name'] || 'up-sells' === $woocommerce_loop['name'] || 'cross-sells' === $woocommerce_loop['name'] ) ) {
			$classes[] = 'swiper-slide';

		}
		else {
			$layout    = ! empty( $wowmall_options['product_page_layout'] ) ? $wowmall_options['product_page_layout'] : '2';
			$classes[] = 'product_page_layout_' . $layout;
		}

		return $classes;
	}

	if ( is_cart() ) {
		if ( ! empty( $woocommerce_loop['name'] ) && 'cross-sells' === $woocommerce_loop['name'] ) {
			$classes[] = 'swiper-slide';

		}

		return $classes;
	}
	if ( is_shop() || is_product_taxonomy() || wowmall()->page_has_wc_shortcode() ) {
		$condition = wowmall_get_condition();
		if ( 'grid' === $condition ) {
			if ( wp_is_mobile() && ( is_product_taxonomy() || is_shop() ) ) {
				$classes[] = 'product-grid';
				$classes[] = 'col-xs-6';
			}
		}

		if ( 'big' === $condition ) {
			$classes[] = 'product-big';
		}
		if ( 'list' === $condition ) {
			$classes[] = 'product-list';
			$classes[] = 'col-xs-12';
		}
		elseif ( ! is_product() ) {
			if ( ! is_active_sidebar( 'sidebar-shop' ) ) {
				$classes[] = 'col-xxl-2';
				$classes[] = 'col-xl-3';
				$classes[] = 'col-lg-4';
				$classes[] = 'col-md-6';
			}
			else {
				switch ( $loop_layout ) {
					case '3':
						$classes[] = 'col-xxl-2';
						$classes[] = 'col-xl-3';
						$classes[] = 'col-lg-4';
						$classes[] = 'col-md-6';
						break;
					case '4':
						$classes[] = 'col-xxl-3';
						$classes[] = 'col-xl-4';
						$classes[] = 'col-lg-6';
						break;
					case '5':
						$classes[] = 'col-xl-4';
						$classes[] = 'col-lg-6';
						break;
					default:
						$classes[] = 'col-xxl';
						$classes[] = 'col-xl-4';
						$classes[] = 'col-lg-6';
						break;
				}
			}
			$classes[] = 'col-sm-6';
		}
	}

	return $classes;
}

function wowmall_woocommerce_enqueue_styles( $styles ) {
	unset( $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );

	return $styles;
}

function wowmall_woocommerce_template_loop_product_title() {
	echo "<h2 class=wc-loop-product-title><a href='" . get_the_permalink() . "'>" . get_the_title() . "</a></h2>";
}

function wowmall_wc_loop_cats_rating_wrapper() {
	$condition = is_product() && ! wp_is_mobile() ? 'grid' : wowmall_get_condition();
	if ( ( 'grid' === $condition && ! wp_is_mobile() ) || ( 'big' === $condition && wp_is_mobile() ) ) {
		echo '<div class=wc-loop-product-cats-rating-wrapper>';
	}
}

function wowmall_wc_template_loop_rating() {
	global $wowmall_options;
	$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	if ( '2' === $layout ) {
		return;
	}
	$condition = is_product() && ! wp_is_mobile() ? 'grid' : wowmall_get_condition();
	$filter    = current_filter();
	if ( ( ! wp_is_mobile() && 'grid' === $condition && 'woocommerce_before_shop_loop_item_title' === $filter && ( is_product_taxonomy() || is_shop() ) ) || ( wp_is_mobile() && 'big' === $condition && 'woocommerce_before_shop_loop_item_title' === $filter && ( is_product_taxonomy() || is_shop() ) ) || ( ( 'list' === $condition || wp_is_mobile() && 'grid' === $condition ) && 'woocommerce_after_shop_loop_item_title' === $filter && ( is_product_taxonomy() || is_shop() ) ) || ( ! ( is_product_taxonomy() || is_shop() ) && 'woocommerce_before_shop_loop_item_title' === $filter ) ) {
		ob_start();
		woocommerce_template_loop_rating();
		$rating = trim( ob_get_clean() );
		if ( empty( $rating ) ) {
			return;
		} ?>
		<div class=wc-loop-rating>
			<?php
			global $product;
			$rating_count = $product->get_rating_count();
			if ( 0 < $rating_count ) {
				$review_count = $product->get_review_count();
				if ( 'list' === $condition && ( is_product_taxonomy() || is_shop() ) && 'woocommerce_after_shop_loop_item_title' === $filter ) {
					$rating .= '<span>' . sprintf( esc_html__( '%s Review(s)', 'wowmall' ), '<span class=count>' . $review_count . '</span>' ) . '</span>';
				}
				elseif ( ! empty( $wowmall_options['review_count'] ) ) {
					$rating .= ' <span>(' . $review_count . ')</span>';
				}
			}
			echo '' . $rating; ?>
		</div>
	<?php }
}

function wowmall_wc_loop_cats_rating_wrapper_end() {
	$condition = is_product() && ! wp_is_mobile() ? 'grid' : wowmall_get_condition();
	if ( ( 'grid' === $condition && ! wp_is_mobile() ) || ( 'big' === $condition && wp_is_mobile() ) ) {
		echo '</div>';
	}
}

function wowmall_wc_loop_product_wrapper() {
	$condition = is_product() ? 'grid' : wowmall_get_condition();
	if ( 'grid' === $condition || 'big' === $condition ) {
		echo '<div class=wc-loop-product-wrapper>';
	}
	else {
		echo '<div class=wc-loop-product-list-image-wrapper>';
	}
}

function wowmall_wc_loop_product_wrapper_end() {
	echo '</div>';
}

function wowmall_wc_loop_product_add_to_cart_wrapper() {
	global $wowmall_options;
	$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	if ( '2' === $layout ) {
		return;
	}
	echo '<div class=wc-loop-product-add-to-cart-wrapper>';
}

function wowmall_wc_sale_flash() {
	return '<span class=onsale>' . esc_html__( 'Sale', 'wowmall' ) . '</span>';
}

function wowmall_wc_loop_product_quick_view_button() {
	if ( wp_is_mobile() ) {
		return;
	}
	global $wowmall_options, $product;
	$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	$enable = ! isset( $wowmall_options['quick_view_enable'] ) || $wowmall_options['quick_view_enable'];
	if ( '2' === $layout || empty( $product ) || ! $enable ) {
		return;
	}
	$condition = is_product() ? 'grid' : wowmall_get_condition();
	$classes   = array(
		'wowmall-wc-quick-view-button',
		'btn',
	);
	if ( 'grid' === $condition || ! ( is_product_taxonomy() || is_shop() ) ) {
		$classes[] = 'btn-icon';
	}
	else {
		$classes[] = 'btn-sm btn-default';
	}
	echo '<span class="wowmall-wc-quick-view-button-wrapper"><a href="#" class="' . esc_attr( join( ' ', $classes ) ) . '"><span class=btn-text>' . esc_html__( 'Quick view', 'wowmall' ) . '</span></a></span>';
}

function wowmall_get_original_product_id( $id ) {

	global $sitepress;

	if ( isset( $sitepress ) ) {

		$id = icl_object_id( $id, 'product', true, $sitepress->get_default_language() );
	}

	return $id;
}

function wowmall_wc_loop_product_add_to_cart_wrapper_end() {
	global $wowmall_options;
	$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	if ( '2' === $layout ) {
		return;
	}
	echo '</div>';
}

function wowmall_wc_loop_add_to_cart_link( $link, $product ) {
	$classes = array(
		'product_type_' . $product->get_type(),
		$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
		$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
	);
	global $wowmall_options, $wowmall_product_in_compare, $wowmall_product_in_wishlist;
	$layout    = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	$condition = is_product() ? 'grid' : wowmall_get_condition();
	if ( ! ( '2' === $layout && ( is_shop() || is_product_taxonomy() ) ) ) {
		$classes[] = 'btn';
		if ( ! ( $product->is_purchasable() && $product->is_in_stock() ) ) {
			$classes[] = 'read_more_product_button';
		}
		if ( ( 'list' === $condition && ! wp_is_mobile() && ( is_shop() || is_product_taxonomy() ) ) || ( isset( $wowmall_product_in_compare ) && $wowmall_product_in_compare ) || ( isset( $wowmall_product_in_wishlist ) && $wowmall_product_in_wishlist ) ) {
			$classes[] = 'btn-sm';
			if ( ! ( $product->is_purchasable() && $product->is_in_stock() ) ) {
				$classes[] = 'btn-default';
			}
			else {
				$classes[] = 'btn-primary';
			}
		}
		else {
			$classes[] = 'btn-inline';
		}
	}
	$classes = array_filter( $classes );
	$class   = join( ' ', $classes );
	$text    = '<span class=add_to_cart_button_text>' . esc_html( $product->add_to_cart_text() ) . '</span>';
	$link    = sprintf( '<a rel="nofollow" href="%s" data-quantity="1" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>', esc_url( $product->add_to_cart_url() ), esc_attr( $product->get_id() ), esc_attr( $product->get_sku() ), esc_attr( $class ), $text );

	return $link;
}

function wowmall_wc_loop_product_variables() {
	global $product, $wowmall_options;
	$enable = ! empty( $wowmall_options['colors'] );
	$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	if ( wp_is_mobile() || '2' === $layout || ! $enable || ! $product->is_type( 'variable' ) ) {
		return;
	}
	$attributes           = $product->get_variation_attributes();
	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if ( ! empty( $attribute_taxonomies ) ) {
		foreach ( $attribute_taxonomies as $attribute ) {
			if ( 'color' === $attribute->attribute_type && array_key_exists( 'pa_' . $attribute->attribute_name, $attributes ) ) {
				$terms = wc_get_product_terms( $product->get_id(), 'pa_' . $attribute->attribute_name, array( 'fields' => 'all' ) );
				$html  = array();
				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $attributes[ 'pa_' . $attribute->attribute_name ] ) ) {
						$color = get_term_meta( $term->term_id, 'color', true );
						$color = $color ? $color : '#000';

						$html[] = '<span style="background-color:' . esc_attr( $color ) . '" title="' . esc_attr( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '"></span>';
					}
				}
				if ( ! empty( $html ) ) {
					echo '<div class=wowmall-wc-loop-color-variations>' . join( '', $html ) . '</div>';
				}
			}
		}
	}
}

function wowmall_wc_loop_sorting_wrapper() {
	echo '<div class=wc-loop-sorting-wrapper>';
}

function wowmall_wc_loop_sorting_wrapper_end() {
	echo '</div>';
}

function wowmall_wc_loop_grid_list_button() {
	echo '<div class=wc-grid-list-wrapper>';
	$condition = wowmall_get_condition();
	$layouts   = wp_is_mobile() ? array(
		'grid',
		'list',
		'big',
	) : array(
		'grid',
		'list',
	);

	foreach ( $layouts as $layout ) {
		$active = $layout === $condition ? ' active' : '';
		echo '<button class="wc-grid-list-button wc-grid-list-button__' . esc_attr( $layout . $active ) . '" data-condition=' . esc_attr( $layout ) . '></button>';
	}
	echo '</div>';
}

function wowmall_wc_loop_product_content_wrapper() {
	$condition = is_product() ? 'grid' : wowmall_get_condition();
	if ( 'list' === $condition ) {
		echo '</div><div class=wc-loop-product-content-wrapper>';
	}
}

function wowmall_wc_loop_product_excerpt() {
	$condition = is_product() ? 'grid' : wowmall_get_condition();
	if ( 'list' === $condition && ( is_shop() || is_product_taxonomy() ) ) { ?>
		<div class="wc-loop-excerpt">
			<?php woocommerce_template_single_excerpt(); ?>
		</div>
	<?php }
}

function wowmall_wc_product_categories_widget_args( $list_args ) {

	require_once WOWMALL_THEME_DIR . '/inc/walkers/class-product-cat-list-walker.php';

	$list_args['walker'] = new Wowmall_WC_Product_Cat_List_Walker;

	return $list_args;
}

function wowmall_wc_cart_item_thumbnail( $image, $cart_item, $cart_item_key ) {
	global $product;
	$default_product = $product;

	$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
	$product           = $_product;
	if ( is_cart() ) {
		$image = $_product->get_image( 'woo_img_size_cart' );
	} else {
		$image = $_product->get_image( 'woo_img_size_minicart' );
	}
	$product = $default_product;

	return $image;
}

function wowmall_wc_cart_item_name( $title, $cart_item, $cart_item_key ) {
	if ( is_checkout() ) {
		$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
		if ( $product_permalink ) {
			$title = sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $title );
		}
		return '<h6 class=cart-product-title>' . $title . '</h6>';
	}
	return '<h6 class=cart-product-title>' . $title . '</h6>';
}

function wowmall_minicart_posttext() {
	global $wowmall_options;
	if ( ! empty( $wowmall_options['header_cart_text'] ) ) {
		echo '<div class=mini-cart-posttext>' . esc_html( $wowmall_options['header_cart_text'] ) . '</div>';
	}
}

function wowmall_wc_add_to_cart_message_html( $message, $products ) {
	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
	if ( preg_match( "/$regexp/siU", $message, $matches ) ) {
		end( $products );
		$product_id = key( $products );
		if ( has_post_thumbnail( $product_id ) ) {
			$image_id = get_post_thumbnail_id( $product_id );
		}
		elseif ( ( $parent_id = wp_get_post_parent_id( $product_id ) ) && has_post_thumbnail( $parent_id ) ) {
			$image_id = get_post_thumbnail_id( $parent_id );
		}
		else {
			$image_id = 0;
		}
		$image = '<span class="woocommerce-message-inner-added without_image"></span>';
		if ( $image_id ) {
			$image_src = wp_get_attachment_image_src( $image_id, 'woo_img_size_single_thumb' );
			if ( is_array( $image_src ) ) {
				$image = '<span class=woocommerce-message-inner-added><img src="' . esc_attr( $image_src[0] ) . '" width=' . esc_attr( $image_src[1] ) . ' height=' . esc_attr( $image_src[2] ) . '></span>';
			}
		}
		$message = $image . '<div class=woocommerce-message-inner>' . str_replace( $matches[0], '', $message ) . '</div><a class="btn btn-default btn-sm" href="' . esc_url( $matches[2] ) . '">' . $matches[3] . '</a>';
	}

	return $message;
}

function wowmall_wc_order_button_html() {
	return '<button type="submit" class="btn btn-block btn-primary btn-order" name=woocommerce_checkout_place_order id=place_order>' . esc_html__( 'Place order', 'wowmall' ) . '</button>';
}

function wowmall_wc_default_address_fields( $fields ) {
	$fields = array_map( 'wowmall_add_colon', $fields );

	return $fields;
}

function wowmall_add_colon( $field ) {
	if ( ! empty( $field['label'] ) ) {
		$field['label'] .= ':';
	}

	return $field;
}

function wowmall_wc_get_country_locale( $fields ) {
	array_walk_recursive( $fields, 'wowmall_add_colon_recursive' );

	return $fields;
}

function wowmall_add_colon_recursive( &$field, $key ) {
	if ( 'label' === $key ) {
		$field .= ':';
	}
}

function wowmall_wc_cart_shipping_method_full_label( $label, $method ) {
	$label = $method->get_label();

	if ( 0 < $method->cost ) {
		$label .= ': ';
	}
	$label = '<span class=label>' . $label . '</span>';
	if ( $method->cost > 0 ) {
		if ( WC()->cart->tax_display_cart == 'excl' ) {
			$label .= '<span class=shipping_method_price>' . wc_price( $method->cost ) . '</span>';
			if ( $method->get_shipping_tax() > 0 && WC()->cart->prices_include_tax ) {
				$label .= ' <small class=tax_label>' . WC()->countries->ex_tax_or_vat() . '</small>';
			}
		}
		else {
			$label .= '<span class=shipping_method_price>' . wc_price( $method->cost + $method->get_shipping_tax() ) . '</span>';
			if ( $method->get_shipping_tax() > 0 && ! WC()->cart->prices_include_tax ) {
				$label .= ' <small class=tax_label>' . WC()->countries->inc_tax_or_vat() . '</small>';
			}
		}
	}

	return $label;
}

function wowmall_wc_format_address( $address, $return = false ) {

	$address['country'] = ( isset( WC()->countries->countries[ $address['country'] ] ) ) ? WC()->countries->countries[ $address['country'] ] : $address['country'];

	$address['state'] = ( $address['country'] && $address['state'] && isset( WC()->countries->states[ $address['country'] ][ $address['state'] ] ) ) ? WC()->countries->states[ $address['country'] ][ $address['state'] ] : $address['state'];

	$address = array_filter( $address );

	if ( ! $address ) {
		return false;
	}

	$address_fields = WC()->countries->get_default_address_fields();
	ob_start();
	?>
	<table class="shop_table shop_table_responsive order_address">
		<tbody>
		<?php foreach ( $address_fields as $key => $field ) {
			if ( array_key_exists( $key, $address ) ) {
				echo '<tr><th>' . $field['label'] . '</th><td>' . $address[ $key ] . '</td></tr>';
			}
		}
		?>
		</tbody>
	</table>
	<?php
	$content = ob_get_clean();
	if ( $return ) {
		return $content;
	}
	echo '' . $content;
}

function wowmall_wc_format_address_array( $address ) {
	return '<li>' . $address . '</li>';
}

function wowmall_wc_account_menu_item_classes( $classes, $endpoint ) {
	global $wp;

	// Set current item class.
	if ( 'orders' === $endpoint && ! empty( $wp->query_vars['view-order'] ) ) {
		$classes[] = 'is-active';
	}

	return $classes;
}

function wowmall_wc_order_item_quantity_html( $qty, $item ) {
	return ' &nbsp; <span class=product-quantity>' . sprintf( 'x %s', $item['qty'] ) . '</span>';
}

function wowmall_wc_template_loop_price() {
	global $wowmall_options;
	$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	if ( '2' === $layout ) {
		return;
	}
	woocommerce_template_loop_price();
	unset( $layout );
}

function wowmal_wc_empty_price_html() {
	return '&nbsp;';
}

function wowmall_wc_template_loop_add_to_cart() {
	global $wowmall_options;
	$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	if ( '2' === $layout && ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	woocommerce_template_loop_add_to_cart();
}

function wowmall_template_loader( $template ) {
	if ( is_embed() || is_search() || ! wowmall()->is_woocommerce_activated() ) {
		return $template;
	}
	if ( is_product_category() ) {
		$term         = get_queried_object();
		$display_type = get_term_meta( $term->term_id, 'display_type', true );

		if ( '' === $display_type ) {
			$display_type = get_option( 'woocommerce_category_archive_display' );
		}
		if ( 'subcategories' === $display_type ) {
			$find   = array( 'woocommerce.php' );
			$file   = 'archive-product-collection.php';
			$find[] = $file;
			$find[] = WC()->template_path() . $file;
			if ( $file ) {
				$template = locate_template( array_unique( $find ) );
				if ( ! $template || WC_TEMPLATE_DEBUG_MODE ) {
					$template = WC()->plugin_path() . '/templates/' . $file;
				}
			}
		}
	}
	elseif ( is_shop() ) {
		global $wowmall_options;
		$layout = ! empty( $wowmall_options['wc_shop_layout'] ) ? $wowmall_options['wc_shop_layout'] : '1';
		if ( '2' !== $layout ) {
			return $template;
		}
		$find   = array( 'woocommerce.php' );
		$file   = 'archive-product-collection.php';
		$find[] = $file;
		$find[] = WC()->template_path() . $file;
		if ( $file ) {
			$template = locate_template( array_unique( $find ) );
			if ( ! $template || WC_TEMPLATE_DEBUG_MODE ) {
				$template = WC()->plugin_path() . '/templates/' . $file;
			}
		}
	}

	return $template;
}

function wowmall_wc_subcategory_thumbnail( $category ) {
	global $wowmall_options, $wowmall_mega_page, $woocommerce_loop;
	$layout               = ! empty( $wowmall_options['wc_shop_layout'] ) ? $wowmall_options['wc_shop_layout'] : 1;
	$thumbnail_id         = get_term_meta( $category->term_id, 'thumbnail_id', true );
	$small_thumbnail_size = 'woo_img_size_big';
	if ( ! $wowmall_mega_page ) {
		if ( '2' !== $layout && is_shop() ) {
			return;
		}
		if ( is_product_category() ) {
			$term         = get_queried_object();
			$display_type = get_term_meta( $term->term_id, 'display_type', true );

			if ( '' === $display_type ) {
				$display_type = get_option( 'woocommerce_category_archive_display' );
			}
			if ( 'subcategories' !== $display_type ) {
				return;
			}
		}
		if ( ! empty( $woocommerce_loop['columns'] ) && ! is_woocommerce() && ( empty( $woocommerce_loop['name'] ) || 'wowmall_collection' !== $woocommerce_loop['name'] ) ) {
			$cols = empty( $woocommerce_loop['columns'] ) ? 4 : absint( $woocommerce_loop['columns'] );
			switch ( $cols ) {
				case '6':
					$small_thumbnail_size = 'woo_img_size_small';
					break;
				case '5':
					$small_thumbnail_size = 'woo_img_size_average';
					break;
				case '3':
					$small_thumbnail_size = 'woo_img_size_list';
					break;
				case '2':
					$small_thumbnail_size = 'woo_img_size_single_2';
					break;
				default:
					$small_thumbnail_size = 'woo_img_size_big';
					break;
			}
		}
		else {
			$size                 = get_term_meta( $category->term_id, 'collection_size', true );
			$size                 = $size ? $size : 0;
			$small_thumbnail_size = 'woo_img_size_collection_' . $size;
		}
	}
	if ( $thumbnail_id ) {
		echo wp_get_attachment_image( $thumbnail_id, $small_thumbnail_size, false, array( 'alt' => esc_attr( $category->name ) ) );
	}
	else {
		global $product;
		$default_product = $product;
		$product         = null;
		echo wc_placeholder_img( $small_thumbnail_size );
		$product = $default_product;
	}
}

function wowmall_product_cat_class( $classes, $class, $category ) {
	global $wowmall_options, $wowmall_mega_page, $post, $woocommerce_loop;
	$layout  = ! empty( $wowmall_options['wc_shop_layout'] ) ? $wowmall_options['wc_shop_layout'] : 1;
	$display = false;
	if ( is_product_category() ) {
		$term         = get_queried_object();
		$display_type = get_term_meta( $term->term_id, 'display_type', true );

		if ( '' === $display_type ) {
			$display_type = get_option( 'woocommerce_category_archive_display' );
		}
		if ( 'subcategories' === $display_type ) {
			$display = true;
		}
	}
	if ( '2' === $layout && is_shop() ) {
		$display = true;
	}
	if ( $wowmall_mega_page ) {
		$display = false;
	}
	if ( ! empty( $post->post_content ) && has_shortcode( $post->post_content, 'wowmall_collection' ) ) {
		$display = true;
	}

	if ( $display ) {
		if ( false !== $key = array_search( 'product', $classes ) ) {
			unset( $classes[ $key ] );
		}
		$size = get_term_meta( $category->term_id, 'collection_size', true );
		$size = $size ? $size : 0;
		switch ( $size ) {
			case 1 :
				$classes[] = 'height-2';
				break;
			case 2 :
				$classes[] = 'width-2';
				break;
			case 3 :
				$classes[] = 'width-2';
				$classes[] = 'height-2';
				break;
		}
	}
	if ( ! empty( $post->post_content ) && has_shortcode( $post->post_content, 'product_categories' ) ) {
		if ( isset( $woocommerce_loop['columns'] ) ) {
			$columns = $woocommerce_loop['columns'];
			if ( 5 !== $columns ) {
				$col       = ceil( 12 / $columns );
				$classes[] = 'col-xxl-' . $col;
			}
			else {
				$classes[] = 'col-xxl';
			}
			$columns   = 1 < $columns ? $columns - 1 : $columns;
			$col       = ceil( 12 / $columns );
			$classes[] = 'col-xl-' . $col;
			$columns   = 1 < $columns ? $columns - 1 : $columns;
			$col       = ceil( 12 / $columns );
			$classes[] = 'col-lg-' . $col;
			$columns   = 1 < $columns ? $columns - 1 : $columns;
			$col       = ceil( 12 / $columns );
			$classes[] = 'col-md-' . $col;
			$columns   = 1 < $columns ? $columns - 1 : $columns;
			$col       = ceil( 12 / $columns );
			$classes[] = 'col-sm-' . $col;
		}
	}

	return $classes;
}

function wowmall_wc_template_loop_category_title( $category ) {
	global $wowmall_options, $wowmall_mega_page, $post;
	$layout  = ! empty( $wowmall_options['wc_shop_layout'] ) ? $wowmall_options['wc_shop_layout'] : 1;
	$display = false;
	if ( is_product_category() ) {
		$term         = get_queried_object();
		$display_type = get_term_meta( $term->term_id, 'display_type', true );

		if ( '' === $display_type ) {
			$display_type = get_option( 'woocommerce_category_archive_display' );
		}
		if ( 'subcategories' === $display_type ) {
			$display = true;
		}
	}
	if ( '2' === $layout && is_shop() ) {
		$display = true;
	}
	if ( $wowmall_mega_page ) {
		$display = false;
	}
	if ( ! empty( $post->post_content ) && has_shortcode( $post->post_content, 'wowmall_collection' ) ) {
		$display = true;
	}
	if ( $display ) { ?>
		<div class=wc-loop-cat-title-count>
			<h2 class=wc-loop-cat-title>
				<?php echo esc_html( $category->name ); ?>
			</h2><?php

			if ( $category->count > 0 ) { ?>
				<mark class=count>
					<?php printf( _nx( '%s Product', '%s Products', $category->count, 'woocommerce loop category title', 'wowmall' ), $category->count ); ?>
				</mark>
			<?php }
			?>
		</div>
		<?php
		return;
	}
	if ( ! empty( $post->post_content ) && has_shortcode( $post->post_content, 'product_categories' ) ) { ?>
		<h6 class=wc-loop-product-title>
			<?php echo esc_html( $category->name ); ?>
		</h6>
		<?php return;
	}
	echo esc_html( $category->name );
}

function wowmall_wc_single_btns_wrapper() {
	global $wowmall_wc_quick_view;
	if ( $wowmall_wc_quick_view ) {
		return;
	}
	echo '<div class=wowmall-wc-single-btns-wrapper>';
}

function wowmall_wc_single_btns_wrapper_end() {
	global $wowmall_wc_quick_view;
	if ( $wowmall_wc_quick_view ) {
		return;
	}
	echo '</div></div>';
}

function wowmall_wc_page_title( $page_title ) {
	if ( is_search() ) {
		if ( have_posts() ) {
			$page_title = sprintf( esc_html__( 'Search Results for %s', 'wowmall' ), '<span class=wowmall-search-query-title>&#x27;' . get_search_query() . '&#x27;</span>' );
		}
		else {
			$page_title = esc_html__( 'Your search returns no results.', 'wowmall' );
		}
		if ( get_query_var( 'paged' ) ) {
			$page_title .= sprintf( esc_html__( '&nbsp;&ndash; Page %s', 'wowmall' ), get_query_var( 'paged' ) );
		}
	}

	return $page_title;
}

function wowmall_wc_loop_list_compare_wishlist_btns_wrapper() {
	global $woocommerce_loop;
	$condition = wowmall_get_condition();
	if ( 'grid' === $condition || wp_is_mobile() ) {
		return;
	}
	if ( is_product() && ( ! empty( $woocommerce_loop['name'] ) && ( 'related' === $woocommerce_loop['name'] || 'up-sells' === $woocommerce_loop['name'] || 'cross-sells' === $woocommerce_loop['name'] ) ) ) {
		return;
	}
	echo '<div class=compare-wishlist-btns-wrapper>';
}

function wowmall_wc_loop_list_compare_wishlist_btns_wrapper_end() {
	global $woocommerce_loop;
	$condition = wowmall_get_condition();
	if ( 'grid' === $condition || wp_is_mobile() ) {
		return;
	}
	if ( is_product() && ( ! empty( $woocommerce_loop['name'] ) && ( 'related' === $woocommerce_loop['name'] || 'up-sells' === $woocommerce_loop['name'] || 'cross-sells' === $woocommerce_loop['name'] ) ) ) {
		return;
	}
	echo '</div>';
}

function wowmall_wc_wishlist_add_button_grid() {

	if ( ! function_exists( 'wowmall_wishlist_add_button' ) ) {
		return;
	}
	global $wowmall_options;

	$condition = wowmall_get_condition();

	if ( ! is_product() && ( '2' === $wowmall_options['wc_loop_layout'] || ( 'list' === $condition && ! wp_is_mobile() ) ) ) {
		return;
	}
	wowmall_wishlist_add_button();
}

function wowmall_wc_compare_add_button_grid() {

	if ( ! function_exists( 'wowmall_compare_add_button' ) ) {
		return;
	}
	global $wowmall_options;

	$condition = wowmall_get_condition();

	if ( ! is_product() && ( '2' === $wowmall_options['wc_loop_layout'] || ( 'list' === $condition && ! wp_is_mobile() ) ) ) {
		return;
	}
	wowmall_compare_add_button();
}

function wowmall_wc_wishlist_add_button_list() {
	if ( ! function_exists( 'wowmall_wishlist_add_button' ) ) {
		return;
	}
	global $wowmall_options;

	$condition = wowmall_get_condition();

	if ( ! is_product() && '2' !== $wowmall_options['wc_loop_layout'] && 'list' === $condition && ! wp_is_mobile() ) {
		wowmall_wishlist_add_button();
	}
}

function wowmall_wc_compare_add_button_list() {
	if ( ! function_exists( 'wowmall_compare_add_button' ) ) {
		return;
	}
	global $wowmall_options;

	$condition = wowmall_get_condition();

	if ( ! is_product() && '2' !== $wowmall_options['wc_loop_layout'] && 'list' === $condition && ! wp_is_mobile() ) {
		wowmall_compare_add_button();
	}
}

function wowmall_wc_available_variation( $data, $product, $variation ) {
	if ( $attachment = get_post( $variation->get_id() ) ) {
		$data['image']          = wowmall_wc_get_product_attachment_props( $variation->get_image_id() );
		$data['lightbox_thumb'] = wp_get_attachment_image_src( $variation->get_image_id(), 'gallery_img_size_lightbox_thumb', 0 );
		$data['gallery_thumb']  = wp_get_attachment_image_src( $variation->get_image_id(), 'woo_img_size_single_thumb', 0 );
	}

	return $data;
}

function wowmall_wc_get_product_attachment_props( $attachment_id = null, $product = false ) {

	$props = wc_get_product_attachment_props( $attachment_id, $product );

	if ( $attachment = get_post( $attachment_id ) ) {

		global $wowmall_options;
		$layout = ! empty( $wowmall_options['product_page_layout'] ) ? $wowmall_options['product_page_layout'] : 2;
		$size   = 'woo_img_size_single_' . $layout;

		// Large version.
		$src                 = wp_get_attachment_image_src( $attachment_id, 'full' );
		$props['full_src']   = $src[0];
		$props['full_src_w'] = $src[1];
		$props['full_src_h'] = $src[2];

		// Image source.
		$src             = wp_get_attachment_image_src( $attachment_id, $size );
		$props['src']    = $src[0];
		$props['src_w']  = $src[1];
		$props['src_h']  = $src[2];
		$props['srcset'] = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, $size ) : false;
		$props['sizes']  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $attachment_id, $size ) : false;
	}

	return $props;
}

function wowmall_wc_dropdown_variation_attribute_options_html( $html, $args ) {
	global $product, $wowmall_options;
	$attribute             = $args['attribute'];
	$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
	$class                 = $args['class'];
	$options               = $args['options'];
	$show_option_none      = true;
	$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$show_option_none_text = $args['show_option_none'];
	if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[ $attribute ];
	}
	$attribute_taxonomies = wc_get_attribute_taxonomies();
	$is_color             = false;
	$is_size              = false;
	if ( ! empty( $attribute_taxonomies ) ) {
		foreach ( $attribute_taxonomies as $global_attribute ) {
			if ( 'pa_' . $global_attribute->attribute_name === $attribute ) {
				if ( 'color' === $global_attribute->attribute_type && ( ! isset( $wowmall_options['custom_variations_color'] ) || $wowmall_options['custom_variations_color'] ) ) {
					$is_color = true;
					break;
				}
				elseif ( 'size' === $global_attribute->attribute_type && ( ! isset( $wowmall_options['custom_variations_size'] ) || $wowmall_options['custom_variations_size'] ) ) {
					$is_size = true;
					break;
				}
			}
		}
	}
	if ( $is_color ) {
		$class .= ' wowmall-color';
	}
	elseif ( $is_size ) {
		$class .= ' wowmall-size';
	}
	$html = '<select id=' . esc_attr( $id ) . ' class="' . esc_attr( $class ) . '" name=' . esc_attr( $name ) . ' data-attribute_name=attribute_' . esc_attr( sanitize_title( $attribute ) ) . ' data-show_option_none=' . ( $show_option_none ? 'yes' : 'no' ) . '>';
	$html .= '<option class=blank-option value="">' . esc_html( $show_option_none_text ) . '</option>';

	if ( ! empty( $options ) ) {
		if ( $product && taxonomy_exists( $attribute ) ) {
			// Get terms if this is a taxonomy - ordered. We need the names too.
			$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $options ) ) {
					$data = '';
					if ( $is_color ) {
						$color = get_term_meta( $term->term_id, 'color', true );
						$color = $color ? $color : '#000';
						$data  = ' data-color=' . esc_attr( $color );
					}
					$html .= '<option' . $data . ' value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
				}
			}
		}
		else {
			foreach ( $options as $option ) {
				// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
				$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
				$html     .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
			}
		}
	}
	$html .= '</select>';

	return $html;
}

function wowmall_loop_shop_per_page() {
	global $wowmall_options;
	$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
	switch ( $layout ) {
		case '3':
			return ! empty( $wowmall_options['posts_per_page_no_sidebar'] ) ? $wowmall_options['posts_per_page_no_sidebar'] : 18;
			break;
		case '4':
			return ! empty( $wowmall_options['posts_per_page_average'] ) ? $wowmall_options['posts_per_page_average'] : 8;
			break;
		case '5':
			return ! empty( $wowmall_options['posts_per_page_big'] ) ? $wowmall_options['posts_per_page_big'] : 6;
			break;
		default:
			return ! empty( $wowmall_options['posts_per_page_sidebar'] ) ? $wowmall_options['posts_per_page_sidebar'] : 15;
			break;
	}
}

function wowmall_wc_add_success( $message ) {

	if ( false === strpos( $message, 'woocommerce-message-inner' ) ) {
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		if ( preg_match( "/$regexp/siU", $message, $matches ) ) {
			$message = '<div class=woocommerce-message-inner>' . str_replace( $matches[0], '', $message ) . '</div><a class="btn btn-default btn-sm" href="' . esc_url( $matches[2] ) . '">' . $matches[3] . '</a>';
		}
	}

	return $message;
}

function wowmall_wc_price( $return, $price, $args ) {
	$negative        = 0 > $price;
	$args            = apply_filters( 'wc_price_args', wp_parse_args( $args, array(
		'ex_tax_label' => false,
		'currency'     => '',
		'price_format' => get_woocommerce_price_format(),
	) ) );
	$formatted_price = ( $negative ? '-' : '' ) . sprintf( $args['price_format'], get_woocommerce_currency_symbol( $args['currency'] ), $price );
	$return          = $formatted_price;

	if ( $args['ex_tax_label'] && wc_tax_enabled() ) {
		$return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
	}

	return $return;
}

function wowmall_wc_layered_nav_count( $html, $count ) {
	return '<span class=count>' . absint( $count ) . '</span>';
}

function woocommerce_product_subcategories( $args = array() ) {
	global $wp_query, $wowmall_options;

	$defaults = array(
		'before'        => '',
		'after'         => '',
		'force_display' => false,
	);

	$args = wp_parse_args( $args, $defaults );

	// Main query only
	if ( ! is_main_query() && ! $args['force_display'] ) {
		return false;
	}

	// Don't show when filtering, searching or when on page > 1 and ensure we're on a product archive
	if ( is_search() || is_filtered() || is_paged() || ( ! is_product_category() && ! is_shop() ) ) {
		return false;
	}

	// Check categories are enabled
	if ( is_shop() && 'both' !== get_option( 'woocommerce_shop_page_display' ) ) {
		return false;
	}

	// Find the category + category parent, if applicable
	$term      = get_queried_object();
	$parent_id = empty( $term->term_id ) ? 0 : $term->term_id;

	if ( is_product_category() ) {
		$display_type = get_woocommerce_term_meta( $term->term_id, 'display_type', true );

		switch ( $display_type ) {
			case 'products' :
				return false;
				break;
			case '' :
				if ( '' === get_option( 'woocommerce_category_archive_display' ) && '2' !== $wowmall_options['wc_shop_layout'] ) {
					return false;
				}
				break;
		}
	}

	// NOTE: using child_of instead of parent - this is not ideal but due to a WP bug ( https://core.trac.wordpress.org/ticket/15626 ) pad_counts won't work
	$product_categories = get_categories( apply_filters( 'woocommerce_product_subcategories_args', array(
		'parent'       => $parent_id,
		'menu_order'   => 'ASC',
		'hide_empty'   => 0,
		'hierarchical' => 1,
		'taxonomy'     => 'product_cat',
		'pad_counts'   => 1,
	) ) );

	if ( ! apply_filters( 'woocommerce_product_subcategories_hide_empty', false ) ) {
		$product_categories = wp_list_filter( $product_categories, array( 'count' => 0 ), 'NOT' );
	}

	if ( $product_categories ) {
		echo $args['before'];

		foreach ( $product_categories as $category ) {
			wc_get_template( 'content-product_cat.php', array(
				'category' => $category,
			) );
		}

		// If we are hiding products disable the loop and pagination
		if ( is_product_category() ) {
			$display_type = get_term_meta( $term->term_id, 'display_type', true );

			switch ( $display_type ) {
				case 'subcategories' :
					$wp_query->post_count    = 0;
					$wp_query->max_num_pages = 0;
					break;
				case '' :
					if ( 'subcategories' === get_option( 'woocommerce_category_archive_display' ) ) {
						$wp_query->post_count    = 0;
						$wp_query->max_num_pages = 0;
					}
					break;
			}
		}

		if ( is_shop() && 'subcategories' === get_option( 'woocommerce_shop_page_display' ) ) {
			$wp_query->post_count    = 0;
			$wp_query->max_num_pages = 0;
		}

		echo $args['after'];

		return true;
	}

	return false;
}

function woocommerce_maybe_show_product_subcategories( $loop_html ) {
	$display_type = woocommerce_get_loop_display_mode();

	// If displaying categories, append to the loop.
	if ( 'subcategories' === $display_type ) {
		ob_start();
		woocommerce_output_product_categories( array(
			'parent_id' => is_product_category() ? get_queried_object_id() : 0,
		) );
		$loop_html .= ob_get_clean();
		wc_set_loop_prop( 'total', 0 );

		global $wp_query;

		if ( $wp_query->is_main_query() ) {
			$wp_query->post_count    = 0;
			$wp_query->max_num_pages = 0;
		}
	}

	return $loop_html;
}

function wowmall_wc_product_thumbnails_columns() {
	return 1;
}

function wowmall_wc_hide_out_of_stock_on_related() {
	remove_filter( 'pre_option_woocommerce_hide_out_of_stock_items', 'wowmall_wc_hide_out_of_stock_on_related' );

	return 'yes';
}

function wowmall_wc_empty_cart_message() { ?>
	<div class=wc-cart-empty>
		<h2><?php esc_html_e( 'Shopping Cart is Empty', 'wowmall' ); ?></h2>
		<p class=cart-empty>
			<?php esc_html_e( 'You have no items in your shopping cart.', 'wowmall' ) ?>
		</p>
	</div>
<?php }

function wowmall_wc_additional_variation_images_main_images_class() {
	return '.woocommerce-product-gallery__image';
}

function wowmall_wc_additional_variation_images_gallery_images_class() {
	return '.woocommerce-product-gallery__image .thumbnails';
}

function wowmall_load_images_ajax() {
	if ( ! isset( $_POST['variation_id'] ) ) {
		die();
	}
	$class = new WC_Additional_Variation_Images_Frontend();
	$class->load_images_ajax_pre30();
}

function wowmall_wcavi_single_product_image_html( $html, $id, $post_id = null, $image_class = '' ) {
	if ( isset( $_REQUEST['action'] ) && 'wc_additional_variation_images_load_frontend_images_ajax' === $_REQUEST['action'] ) {
		global $wowmall_options;

		$image_link  = wp_get_attachment_url( $id );
		$image_title = esc_attr( get_the_title( $id ) );
		$link        = ( ! isset( $wowmall_options['product_lightbox'] ) || $wowmall_options['product_lightbox'] ) ? '<a href="%1$s" class="%2$s" title="%3$s" data-rel=prettyPhoto[product-gallery]>%4$s</a>' : '<span data-url="%1$s" class="%2$s">%4$s</span>';
		$html        = sprintf( $link, $image_link, esc_attr( $image_class ), $image_title, wp_get_attachment_image( $id, 'woo_img_size_single_2', 0 ) );
	}

	return $html;
}

function wowmall_wc_tags_sku_search( $q ) {
	if ( ( ( ! $q->is_main_query() || is_admin() ) && ( empty( $_REQUEST['action'] ) || 'wowmall_ajax_search' !== $_REQUEST['action'] ) ) || empty( $_REQUEST['post_type'] ) || 'product' !== $_REQUEST['post_type'] || empty( $_REQUEST['s'] ) ) {
		return;
	}
	add_filter( 'posts_clauses_request', function ( $clauses ) {
		global $wpdb, $wowmall_options;
		$replacement = "post_title LIKE $1";
		if ( ! isset( $wowmall_options['product_search_in_tags'] ) || $wowmall_options['product_search_in_tags'] ) {
			if ( false === strpos( $clauses['where'], "WHERE taxonomy = 'product_tag' AND term_id IN ( SELECT term_id FROM" ) ) {
				$replacement .= " ) OR ( {$wpdb->posts}.ID IN ( SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE taxonomy = 'product_tag' AND term_id IN ( SELECT term_id FROM {$wpdb->terms} WHERE name LIKE $1 ) ) )";
			}
		}
		if ( ! isset( $wowmall_options['product_search_in_sku'] ) || $wowmall_options['product_search_in_sku'] ) {
			if ( false === strpos( $clauses['where'], "WHERE meta_key = '_sku' AND meta_value LIKE" ) ) {
				$replacement .= " ) OR ( {$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value LIKE $1 ) ) OR ( {$wpdb->posts}.ID IN ( SELECT post_parent FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_type = 'product_variation' AND {$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value LIKE $1 ) )";
			}
		}
		if ( "post_title LIKE $1" !== $replacement ) {
			$clauses['where'] = preg_replace( "/post_title LIKE (\'\{[^\%]+\}\')/", $replacement, $clauses['where'] );
		}

		return $clauses;
	} );
}

function woocommerce_widget_shopping_cart_button_view_cart() {
	echo '<a href="' . esc_url( wc_get_cart_url() ) . '" class="btn btn-border btn-minicart">' . esc_html__( 'View cart', 'woocommerce' ) . '</a>';
}

function woocommerce_widget_shopping_cart_proceed_to_checkout() {
	echo '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="btn btn-primary btn-checkout">' . esc_html__( 'Proceed to checkout', 'wowmall' ) . '</a>';
}

function wowmall_wc_widget_cart_item_quantity( $html, $cart_item, $cart_item_key ) {
	$cart_item['quantity'] = 999 < $cart_item['quantity'] ? '999+' : $cart_item['quantity'];
	$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
	$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
	$html = '<span class=quantity><span class=quantity_number>' . $cart_item['quantity'] . '</span><span class=quantity_multilpier>&nbsp;&nbsp;x&nbsp;&nbsp;</span><span class="amount">' . $product_price . '</span></span>';
	return $html;
}

function wowmall_footer_scripts() {
	$theme_ver = ! empty( $_COOKIE['wowmall_version'] ) ? (int) join( '', explode( '.', $_COOKIE['wowmall_version'] ) ) : 0;
	$version  = (int) join( '', explode( '.', wowmall()->get_version() ) );
	if( $version > $theme_ver ) {
		$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie('wowmall_version',  wowmall()->get_version(), time()+YEAR_IN_SECONDS*10, COOKIEPATH, COOKIE_DOMAIN, $secure);
	}
	if( wowmall()->is_woocommerce_activated() ) {
		if ( 150 > $theme_ver ) {
			echo '<script>
				jQuery( function( $ ) {
					$( document.body ).trigger( \'wc_fragment_refresh\' );
				});
			</script>';
		}
	}
}