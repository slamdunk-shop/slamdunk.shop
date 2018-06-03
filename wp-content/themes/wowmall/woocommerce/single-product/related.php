<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) :

global $product, $woocommerce_loop, $wowmall_options, $wowmall_wc_quick_view;

if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
	return;
}

$layout = ! empty( $wowmall_options['product_page_layout'] ) ? $wowmall_options['product_page_layout'] : '2';

if( ( '1' === $layout && ! wp_is_mobile() ) ) {
	if ( ! empty( $wowmall_options['product_stiky'] ) ) {
		echo '</div>';
	}
	echo '</div><div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 wowmall-related-wrapper">';
}

$data_atts = ' data-visible=' . esc_attr( $columns ) . '';

$GLOBALS['wowmall_wc_loop_condition'] = 'grid';
if ( wp_is_mobile() ) {
	$GLOBALS['wowmall_wc_loop_condition'] = 'big';
}

?>

	<div class="related products">

		<h4><?php esc_html_e( 'Related Products', 'wowmall' ); ?></h4>
	<div id="related" class="swiper-container"<?php echo esc_html( $data_atts ); ?>>
		<ul class="swiper-wrapper products">
		<?php foreach ( $related_products as $related_product ) : ?>

			<?php
			$post_object = get_post( $related_product->get_id() );

			setup_postdata( $GLOBALS['post'] =& $post_object );

			wc_get_template_part( 'content', 'product' ); ?>

		<?php endforeach; ?>

		</ul>
	</div>
	<?php if( ! empty( $wowmall_options['related_arrows'] ) ) { ?>
		<div class=swiper-button-prev></div>
		<div class=swiper-button-next></div>
	<?php } ?>
	</div>

<?php

unset( $GLOBALS['wowmall_wc_loop_condition'] );

endif;

wp_reset_postdata();
