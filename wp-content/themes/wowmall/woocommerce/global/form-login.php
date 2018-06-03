<?php
/**
 * Login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/form-login.php.
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
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( is_user_logged_in() ) {
	return;
}

?>
<div class=login <?php if ( $hidden ) echo 'style="display:none;"'; ?>>

	<?php do_action( 'woocommerce_login_form_start' ); ?>

	<?php echo ( $message ) ? wpautop( wptexturize( $message ) ) : ''; // @codingStandardsIgnoreLine ?>
	<div class=row>
		<div class=col-xl-6>

			<p class="form-row form-row-first">
				<label for=username><?php esc_html_e( 'Username or email', 'wowmall' ); ?>: <span class=required>*</span></label>
				<input form=checkout-login type=text class=input-text name=username id=username placeholder="<?php esc_html_e( 'Enter your name or email, please', 'wowmall' ); ?>">
			</p>
			<p class="form-row form-row-last">
				<label for=password><?php esc_html_e( 'Password', 'wowmall' ); ?>: <span class=required>*</span></label>
				<input form=checkout-login class=input-text type=password name=password id=password placeholder="<?php esc_html_e( 'Enter your password, please', 'wowmall' ); ?>">
			</p>
			<div class=clear></div>

			<?php do_action( 'woocommerce_login_form' ); ?>
			<p class=form-row>
				<button form=checkout-login type=submit class="btn btn-sm btn-dark"><?php esc_html_e( 'Login', 'wowmall' ); ?></button>
			</p>
			<div class=remember-lost-pass>
				<p class=form-row>
					<input form=checkout-login name=rememberme type=checkbox id=rememberme value=forever>
					<label for=rememberme class=inline><?php esc_html_e( 'Remember me', 'wowmall' ); ?></label>
				</p>
				<p class="lost_password form-row">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'wowmall' ); ?></a>
				</p>
			</div>
			<div class=clear></div>
		</div>
	</div>

	<?php do_action( 'woocommerce_login_form_end' ); ?>
</div>
