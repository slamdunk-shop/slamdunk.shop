<?php
/**
 * The template for displaying product widget entries.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-product.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$class = '';
if( $product->is_on_sale() ) {
	$class = ' class=on-sale-product';
}
?>
<li<?php echo esc_html( $class ); ?>>
	<?php do_action( 'woocommerce_widget_product_item_start', $args ); ?>
	<div class=widget-product-thumb>
		<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
			<?php echo '' . $product->get_image( 'woo_img_size_minicart' ); ?>
		</a>
	</div>
	<div class=widget-product-content>
		<h6 class=widget-product-title><a href="<?php echo esc_url( $product->get_permalink() ); ?>>"><?php echo esc_html( $product->get_name ); ?></a></h6>
		<?php if ( ! empty( $show_rating ) ) : ?>
			<?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
		<?php endif; ?>
		<?php
		$price = $product->get_price_html();
		if( ! empty( $price ) ) { ?>
			<span class=amount><?php echo '' . $price; ?></span>
		<?php }
		?>
	</div>

	<?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>
</li>