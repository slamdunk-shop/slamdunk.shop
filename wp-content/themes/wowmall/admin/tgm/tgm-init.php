<?php

/**
 * TGM Init Class
 */
require_once( WOWMALL_THEME_ADMIN_DIR . 'tgm/class-tgm-plugin-activation.php' );

function wowmall_options_register_required_plugins() {

	$plugins = array(
		array(
			'name'     => 'Contact Form 7',
			'slug'     => 'contact-form-7',
			'required' => true,
			'version'  => '5.0.2',
		),
		array(
			'name'     => 'Envato Market',
			'slug'     => 'envato-market',
			'source'   => 'http://envato.github.io/wp-envato-market/dist/envato-market.zip',
			'required' => false,
		),
		array(
			'name'     => 'Instagram Shop by Snapppt',
			'slug'     => 'shop-feed-for-instagram-by-snapppt',
			'required' => true,
			'version'  => '1.1.3',
		),
		array(
			'name'     => 'MailChimp for WordPress',
			'slug'     => 'mailchimp-for-wp',
			'required' => true,
			'version'  => '4.2.2',
		),
		array(
			'name'     => 'Redux Framework',
			'slug'     => 'redux-framework',
			'required' => true,
			'version'  => '3.6.9',
		),
		array(
			'name'     => 'Slider Revolution',
			'slug'     => 'revslider',
			'source'   => WOWMALL_THEME_DIR . '/inc/plugins/revslider.zip',
			'required' => true,
			'version'  => '5.4.7.4',
		),
		array(
			'name'     => 'WPBakery Visual Composer',
			'slug'     => 'js_composer',
			'source'   => WOWMALL_THEME_DIR . '/inc/plugins/js_composer.zip',
			'required' => true,
			'version'  => '5.4.7',
		),
		array(
			'name'     => 'Ultimate Addons for Visual Composer',
			'slug'     => 'Ultimate_VC_Addons',
			'source'   => WOWMALL_THEME_DIR . '/inc/plugins/Ultimate_VC_Addons.zip',
			'required' => true,
			'version'  => '3.16.23',
		),
		array(
			'name'     => 'WooCommerce',
			'slug'     => 'woocommerce',
			'required' => true,
			'version'  => '3.4.0',
		),
		array(
			'name'     => 'Wowmall Fontello Icons Importer',
			'slug'     => 'wowmall-icon-importer',
			'source'   => WOWMALL_THEME_DIR . '/inc/plugins/wowmall-icon-importer.zip',
			'required' => true,
			'version'  => '1.0.1',
		),
		array(
			'name'               => 'Wowmall Shortcodes',
			'slug'               => 'wowmall-shortcodes',
			'source'             => WOWMALL_THEME_DIR . '/inc/plugins/wowmall-shortcodes.zip',
			'required'           => true,
			'force_activation'   => true,
			'force_deactivation' => true,
			'version'            => '1.5.0',
		),
	);

	$config = array(
		'domain'       => 'wowmall',
		'menu'         => 'install-required-plugins',
		'is_automatic' => true,
	);
	tgmpa( $plugins, $config );

}

add_action( 'tgmpa_register', 'wowmall_options_register_required_plugins' );

