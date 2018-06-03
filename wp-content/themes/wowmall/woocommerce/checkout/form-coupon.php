<?php
/**
 * Checkout coupon form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-coupon.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() || ! empty( WC()->cart->applied_coupons ) ) { // @codingStandardsIgnoreLine.
	return;
}

?>
<div class=checkout-coupon-wrapper>
		<?php echo '<div class=checkout-coupon>' . apply_filters( 'woocommerce_checkout_coupon_message', '<h3>' . __( 'Have a coupon?', 'woocommerce' ) . '</h3> <a href=# class=showcoupon>' . __( 'Click here to enter your code', 'woocommerce' ) . '</a>' ) . '</div>';
	?>
	<div class=checkout_coupon style="display:none">
		<div class=row>
			<div class=col-xl-6>
				<p class="form-row form-row-first">
					<label for=coupon_code><?php esc_html_e( 'Coupon code', 'woocommerce' ); ?></label>
				</p>
				<div class=flex-input>
					<input form=checkout-coupon type=text name=coupon_code class=input-text placeholder="<?php esc_attr_e( 'Code', 'wowmall' ); ?>" id=coupon_code value="">
					<button form=checkout-coupon type=submit class="btn btn-sm btn-dark" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply', 'wowmall' ); ?></button>
				</div>
				<div class=clear></div>
			</div>
		</div>
	</div>
</div>