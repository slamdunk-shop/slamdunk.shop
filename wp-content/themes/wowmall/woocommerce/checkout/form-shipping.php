<?php
/**
 * Checkout shipping information form
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class=woocommerce-shipping-fields>
	<div class=row>
		<div class=col-xl-6>
			<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>

				<p class=form-row id=ship-to-different-address>
					<input id=ship-to-different-address-checkbox
						   class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?>
						   type=checkbox name=ship_to_different_address value=1>
					<label for=ship-to-different-address-checkbox
						   class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox"><?php esc_html_e( 'Ship to a different address?', 'wowmall' ); ?></label>
				</p>

				<div class=shipping_address>

					<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

					<?php
					$fields = $checkout->get_checkout_fields( 'shipping' );
					foreach ( $fields as $key => $field ) {
						if ( empty( $field['placeholder'] ) && ! empty( $field['label'] ) ) {
							$field['placeholder'] = str_replace( ':', '', $field['label'] );
						}
						if ( ! empty( $field['label'] ) ) {
							$field['label'] = str_replace( ':', '', $field['label'] ) . ':';
						}
						if ( isset( $field['country_field'], $fields[ $field['country_field'] ] ) ) {
							$field['country'] = $checkout->get_value( $field['country_field'] );
						}
						woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );

					} ?>

					<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

				</div>

			<?php endif; ?>
		</div>
	</div>

	<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

	<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes' ) ) : ?>

		<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>

			<h3><?php esc_html_e( 'Additional Information', 'wowmall' ); ?></h3>

		<?php endif; ?>

		<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>

			<?php
			if ( ! empty( $field['label'] ) ) {
				$field['label'] = str_replace( ':', '', $field['label'] ) . ':';
			}
			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

		<?php endforeach; ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
</div>
