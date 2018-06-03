<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$reg = get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes';

if ( $reg ) {
	$min     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$min_path = empty( $min ) ? '' : '/min';

	wp_enqueue_script( 'bootstrap-tabs', WOWMALL_THEME_URI . '/assets/js/bootstrap' . $min_path . '/tab' . $min . '.js', array( 'jquery' ), null, true );
	global $wp_scripts;
	$wp_scripts->registered['wowmall-theme-script']->deps[] = 'bootstrap-tabs';
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class=row id="customer_login">
	<div class="col-xl-4 offset-xl-4 col-lg-8 offset-lg-2">
		<div class=entry-header>
			<?php if ( $reg ) { ?><ul role=tablist><li class=active><?php } ?>
			<h2 class=entry-title><?php if ( $reg ) { ?><a href=#login aria-controls=login role=tab data-toggle=tab><?php } ?><?php esc_html_e( 'Login', 'woocommerce' ); ?><?php if ( $reg ) { ?></a></h2></li><li><h2 class=entry-title>&nbsp;/ <a href=#reg aria-controls=reg role=tab data-toggle=tab><?php esc_html_e( 'Register', 'woocommerce' ); ?></a><?php } ?></h2>
			<?php if ( $reg ) { ?></li></ul><?php } ?>
		</div>
		<div class=tab-content>
			<?php wc_print_notices();
		if ( $reg ) { ?>
			<div role=tabpanel class="tab-pane fade in active" id=login>
		<?php } ?>

		<form class="woocommerce-form woocommerce-form-login login" method="post">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="username"><?php esc_html_e( 'Username or email', 'wowmall' ); ?>:&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" placeholder="<?php esc_html_e( 'Enter your username or email, please', 'wowmall' ); ?>" required /><?php // @codingStandardsIgnoreLine ?>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>:&nbsp;<span class="required">*</span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" placeholder="<?php esc_html_e( 'Enter your password, please', 'wowmall' ); ?>" required />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row button-row">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="woocommerce-Button btn btn-primary btn-sm btn-block" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Login', 'woocommerce' ); ?></button>
			</p>
			<p class="woocommerce-LostPassword lost_password">
				<span class=wc-remember-wrapper>
					<input class="woocommerce-Input woocommerce-Input--checkbox" name=rememberme type=checkbox id=rememberme value=forever>
					<label for=rememberme class=inline><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></label>
				</span>
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

	</div>

	<div role=tabpanel class="tab-pane fade" id=reg>

		<form method="post" class="woocommerce-form woocommerce-form-register register">

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>:&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" placeholder="<?php esc_html_e( 'Enter your username, please', 'wowmall' ); ?>" required /><?php // @codingStandardsIgnoreLine ?>
				</p>

			<?php endif; ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>:&nbsp;<span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" placeholder="<?php esc_html_e( 'Enter your email, please', 'wowmall' ); ?>" required /><?php // @codingStandardsIgnoreLine ?>
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>:&nbsp;<span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" placeholder="<?php esc_html_e( 'Enter your password, please', 'wowmall' ); ?>" required />
				</p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="form-row button-row">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="woocommerce-Button btn btn-primary btn-sm btn-block" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_attr_e( 'Register', 'woocommerce' ); ?></button>
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>

<?php endif; ?>
</div>

</div>

</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
