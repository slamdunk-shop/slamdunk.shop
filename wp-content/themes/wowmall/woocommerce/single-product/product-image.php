<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.2
 */

defined( 'ABSPATH' ) || exit;

global $post, $product, $wowmall_options;

$post_thumbnail_id = $product->get_image_id();

?>
<div class=images>
	<div class=woocommerce-product-gallery__image>
		<?php
		if ( has_post_thumbnail() ) {
			$attachment_count = count( $product->get_gallery_image_ids() );
			$gallery          = $attachment_count > 0 ? '[product-gallery]' : '';
			$props            = wc_get_product_attachment_props( get_post_thumbnail_id(), $post );
			$image            = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
				'title' => $props['title'],
				'alt'   => $props['alt'],
			) );
			$classes          = array(
				'woocommerce-main-image',
			);
			$link             = ( ! isset( $wowmall_options['product_lightbox'] ) || $wowmall_options['product_lightbox'] ) ? '<a href="%1$s" class="%2$s" title="%3$s" data-rel="prettyPhoto%4$s">%5$s</a>' : '<span data-url="%1$s" class="%2$s">%5$s</span>';

			if ( ( ! isset( $wowmall_options['product_zoom'] ) || $wowmall_options['product_zoom'] ) && ! wp_is_mobile() ) {

				$classes[] = 'zoom';
			}

			$image_class = join( ' ', $classes );
			$html        = sprintf( $link, esc_url( $props['url'] ), $image_class, esc_attr( $props['caption'] ), $gallery, $image );
		}
		else {
			$html = sprintf( '<img src="%s" alt="%s" class=wp-post-image>', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
		}
		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id );

		do_action( 'woocommerce_product_thumbnails' );
		?>
	</div>
</div>
