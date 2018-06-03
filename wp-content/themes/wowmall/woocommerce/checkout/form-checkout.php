<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
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
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<?php wc_print_notices();
if ( ! is_user_logged_in() ) { ?>
	<form method=post class=login id=checkout-login style="display:none;">
		<?php wp_nonce_field( 'woocommerce-login' ); ?>
		<input type=hidden name=login value="<?php esc_attr_e( 'Login', 'wowmall' ); ?>">
		<input type=hidden name=redirect value="<?php echo esc_url( wc_get_page_permalink( 'checkout' ) ) ?>">
	</form>
<?php }
?>
<form class=checkout_coupon id=checkout-coupon method=post style="display:none">
	<input type=hidden name=coupon_code>
</form>
<form name=checkout method=post class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype=multipart/form-data>
	<div class=row>
		<div class="col-xl-8 col-lg-7">
			<?php do_action( 'woocommerce_before_checkout_form', $checkout );

			// If checkout registration is disabled and not logged in, the user cannot checkout
			if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
			echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', esc_html__( 'You must be logged in to checkout.', 'wowmall' ) );
			return;
			}
			if ( sizeof( $checkout->checkout_fields ) > 0 ) { ?>
				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
				<div id=customer_details>
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>
				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
			<?php } ?>
		</div>
		<div class="col-xl-4 col-lg-5">
			<div class="your-order bordered-box">
				<h3 id=order_review_heading><?php esc_html_e( 'Your order', 'wowmall' ); ?></h3>
				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
				<div id=order_review class=woocommerce-checkout-review-order>
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>
				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>
		</div>
	</div>
</form>
<?php do_action( 'woocommerce_after_checkout_form', $checkout );
