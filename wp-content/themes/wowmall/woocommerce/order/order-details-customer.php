<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details">
	<header><h3><?php esc_html_e( 'Customer Details', 'wowmall' ); ?></h3></header>
	<table class="woocommerce-table woocommerce-table--customer-details shop_table customer_details">
		<?php if ( $order->get_customer_note() ) : ?>
			<tr>
				<th><?php esc_html_e( 'Note:', 'wowmall' ); ?></th>
				<td><?php echo wptexturize( $order->get_customer_note() ); ?></td>
			</tr>
		<?php endif;

		if ( $order->get_billing_email() ) : ?>
			<tr>
				<th><?php esc_html_e( 'E-mail:', 'wowmall' ); ?></th>
				<td><?php echo esc_html( $order->get_billing_email() ); ?></td>
			</tr>
		<?php endif;

		if ( $order->get_billing_phone() ) : ?>
			<tr>
				<th><?php esc_html_e( 'Telephone:', 'wowmall' ); ?></th>
				<td><?php echo esc_html( $order->get_billing_phone() ); ?></td>
			</tr>
		<?php endif;

		do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
	</table>
	<h3><?php esc_html_e( 'Billing Address', 'wowmall' ); ?></h3>
	<?php echo ( $address = $order->get_address( 'billing' ) ) ? wowmall_wc_format_address( $address ) : esc_html__( 'N/A', 'wowmall' );

	if ( $show_shipping ) : ?>

	<h3><?php esc_html_e( 'Shipping Address', 'wowmall' ); ?></h3>
	<?php echo ( $address = $order->get_address( 'shipping' ) ) ? wowmall_wc_format_address( $address ) : esc_html__( 'N/A', 'wowmall' );

	endif; ?>
</section>
