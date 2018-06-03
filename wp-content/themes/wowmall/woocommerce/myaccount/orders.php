<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
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
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>
<h3><?php esc_html_e( 'Orders', 'wowmall' ); ?></h3>
<?php if ( $has_orders ) : ?>
	<table class="woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
			<tr>
				<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
					<th class="<?php echo esc_attr( $column_id ); ?>"><span class=nobr><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $customer_orders->orders as $customer_order ) :
				$order      = wc_get_order( $customer_order );
				$item_count = $order->get_item_count();
				?>
				<tr class=order>
					<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
						<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
							<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) :
								do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order );

							elseif ( 'order-number' === $column_id ) : ?>
								<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
									<?php echo esc_html_x( '#', 'hash before order number', 'wowmall' ) . $order->get_order_number(); ?>
								</a>
							<?php elseif ( 'order-date' === $column_id ) : ?>
								<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
							<?php elseif ( 'order-status' === $column_id ) :
								echo wc_get_order_status_name( $order->get_status() );

							elseif ( 'order-total' === $column_id ) :
								echo sprintf( _n( '%s for %s item', '%s for %s items', $item_count, 'wowmall' ), $order->get_formatted_order_total(), $item_count );

							elseif ( 'order-actions' === $column_id ) :
									$actions = array(
										'pay'    => array(
											'url'  => $order->get_checkout_payment_url(),
											'name' => esc_html__( 'Pay', 'wowmall' )
										),
										'view'   => array(
											'url'  => $order->get_view_order_url(),
											'name' => esc_html__( 'View', 'wowmall' )
										),
										'cancel' => array(
											'url'  => $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ),
											'name' => esc_html__( 'Cancel', 'wowmall' )
										)
									);

									if ( ! $order->needs_payment() ) {
										unset( $actions['pay'] );
									}

									if ( ! in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) ) ) {
										unset( $actions['cancel'] );
									}

									if ( $actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order ) ) {
										foreach ( $actions as $key => $action ) {
											echo '<a href="' . esc_url( $action['url'] ) . '" class="btn btn-inline ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
										}
									}
								endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_before_account_orders_pagination' );

	if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class=woocommerce-Pagination>
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-Button woocommerce-Button--previous btn btn-sm" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'wowmall' ); ?></a>
			<?php endif;

			if ( $current_page !== intval( $customer_orders->max_num_pages ) ) : ?>
				<a class="woocommerce-Button woocommerce-Button--next btn btn-sm" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'wowmall' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif;

else : ?>
	<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
		<div class="woocommerce-message-inner">
		<?php esc_html_e( 'No order has been made yet.', 'wowmall' ); ?>
	</div>
		<a class="woocommerce-Button btn btn-primary btn-sm" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Go Shop', 'wowmall' ) ?>
		</a>
	</div>
<?php endif;

do_action( 'woocommerce_after_account_orders', $has_orders );