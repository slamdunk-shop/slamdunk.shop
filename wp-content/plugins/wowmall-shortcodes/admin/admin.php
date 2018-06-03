<?php

if ( ! class_exists( 'wowmallShortcodesAdmin' ) ) {

	class wowmallShortcodesAdmin {

		protected static $_instance = null;

		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ), 9 );

			require_once 'inc/follow.php';
			require_once 'inc/gallery.php';
			require_once 'inc/mailchimp.php';
			include_once 'inc/posts-carousel.php';
			include_once 'inc/slider.php';
			include_once 'inc/instagram.php';
			if( wowmall_shortcodes()->is_woocommerce_activated() ) {
				include_once 'inc/brands.php';
				include_once 'inc/collection.php';
				require_once 'inc/lookbook.php';
				include_once 'inc/products-carousel.php';
			}

			add_action( 'init', array( $this, 'init' ), 0 );
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function register_admin_assets() {

			wp_register_style( 'wowmall-lookbook-post', wowmallShortcodes::$pluginurl . '/assets/css/lookbook.css', array(), wowmallShortcodes::$version );

			wp_register_script( 'wowmall-color-picker', wowmallShortcodes::$pluginurl . '/assets/js/color' . wowmallShortcodes::$suffix . '.js', array( 'wp-color-picker' ), wowmallShortcodes::$version, true );
			wp_register_script( 'wowmall-lookbook-post', wowmallShortcodes::$pluginurl . '/assets/js/lookbook.js', array( 'jquery-ui-draggable' ), wowmallShortcodes::$version, true );

			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';
			if ( in_array( $screen_id, array( 'edit-product' ) ) ) {
				wp_enqueue_script( 'wowmall_wc_quick-edit', wowmallShortcodes::$pluginurl . '/assets/js/quick-edit' . wowmallShortcodes::$suffix . '.js', array( 'woocommerce_quick-edit' ), wowmallShortcodes::$version, true );
			}
		}

		public function init() {
			if( wowmall_shortcodes()->is_woocommerce_activated() ) {
				global $wowmall_options;
				if( ! isset( $wowmall_options['wishlist_enable'] ) || $wowmall_options['wishlist_enable'] ) {
					include_once 'inc/wishlist.php';
				}
				if( ! isset( $wowmall_options['compare_enable'] ) || $wowmall_options['compare_enable'] ) {
					include_once 'inc/compare.php';
				}
				if( ! isset( $wowmall_options['custom_variations'] ) || $wowmall_options['custom_variations'] ) {
					include_once 'inc/variations.php';
				}
				include_once 'inc/new-products.php';

			}
		}
	}


	wowmallShortcodesAdmin::instance();
}