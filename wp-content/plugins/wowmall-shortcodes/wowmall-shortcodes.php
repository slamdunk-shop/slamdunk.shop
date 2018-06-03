<?php
/**
 * Plugin Name: Wowmall Shortcodes
 *
 * Description: Wowmall Wordpress Theme Shortcodes plugin
 * Version: 1.5.0
 * Requires at least: 4.6
 * Tested up to: 4.9
 *
 * Text Domain: wowmall-shortcodes
 * Domain Path: /languages/
 *
 */
if ( ! class_exists( 'wowmallShortcodes' ) ) {

	class wowmallShortcodes {

		protected static $_instance = null;

		public static $plugindir, $pluginurl, $suffix, $version;

		public function __construct() {

			$this::$plugindir = dirname(__FILE__);

			$this::$pluginurl = plugins_url('',__FILE__);
			
			$this::$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$this::$version = '1.5.0';

			if( is_admin() ) {
				include_once 'admin/admin.php';
			}

			add_action( 'init', array( $this, 'init' ), 0 );

			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'plugins_loaded', array( $this, 'remove_revslider_template' ), 11 );

			add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 10 );

			include_once 'inc/optimize.php';
			include_once 'inc/slider.php';
			require_once 'inc/hooks.php';
			require_once 'inc/functions.php';
			require_once 'inc/gallery.php';
			require_once 'inc/follow.php';
			require_once 'inc/mailchimp.php';
			include_once 'inc/posts-carousel.php';
			include_once 'inc/instagram.php';
			if( $this->is_woocommerce_activated() ) {
				include_once 'inc/collection.php';
				require_once 'inc/lookbook.php';
				include_once 'inc/products-carousel.php';
				include_once 'inc/brands.php';
			}
		}

		public function init() {
			if( $this->is_woocommerce_activated() ) {
				global $wowmall_options;
				if( ! isset( $wowmall_options['wishlist_enable'] ) || $wowmall_options['wishlist_enable'] ) {
					include_once 'inc/wishlist.php';
				}
				if( ! isset( $wowmall_options['compare_enable'] ) || $wowmall_options['compare_enable'] ) {
					include_once 'inc/compare.php';
				}
			}
		}

		public function load_textdomain() {

			load_plugin_textdomain( 'wowmall-shortcodes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		public function remove_revslider_template() {
			if( ! class_exists( 'RevSliderPageTemplate' ) ) {
				return;
			}
			global $post;
			if( empty( $post ) ) {
				$revslider_template = RevSliderPageTemplate::get_instance();
				remove_filter( 'template_include', array( $revslider_template, 'view_project_template' ) );
			}
		}

		public function is_woocommerce_activated() {

			return class_exists( 'woocommerce' ) ? true : false;
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}
			return self::$_instance;
		}
		
		public function register_assets() {

			wp_register_style( 'wowmall-compare', $this::$pluginurl . '/assets/css/compare.css', array(), $this::$version );
			wp_register_script( 'wowmall-compare', $this::$pluginurl . '/assets/js/compare' . $this::$suffix . '.js', array( 'jquery' ), $this::$version, true );

			wp_register_style( 'tablesaw', $this::$pluginurl . '/assets/css/tablesaw.css', array(), '2.0.3' );
			wp_register_script( 'tablesaw', $this::$pluginurl . '/assets/js/tablesaw' . $this::$suffix . '.js', array( 'jquery' ), '2.0.3', true );

			wp_register_script( 'tablesaw-init', $this::$pluginurl . '/assets/js/tablesaw-init' . $this::$suffix . '.js', array( 'tablesaw' ), $this::$version, true );

			wp_localize_script( 'wowmall-compare', 'wowmallCompare', array(
				'compareText' => esc_html__( 'Add to Compare', 'wowmall-shortcodes' ),
				'removeText'  => esc_html__( 'Remove from Compare List', 'wowmall-shortcodes' ),
			) );

			wp_register_style( 'wowmall-wishlist', $this::$pluginurl . '/assets/css/wishlist.css', array(), $this::$version );
			wp_register_script( 'wowmall-wishlist', $this::$pluginurl . '/assets/js/wishlist' . $this::$suffix . '.js', array( 'wowmall-theme-script' ), $this::$version, true );

			wp_localize_script( 'wowmall-wishlist', 'wowmallWishlist', array(
				'addText'   => esc_html__( 'Add to Wishlist', 'wowmall-shortcodes' ),
				'addedText' => esc_html__( 'Added to Wishlist', 'wowmall-shortcodes' )
			) );

			wp_register_style( 'wowmall-lookbook-post', $this::$pluginurl . '/assets/css/lookbook.css', array(), $this::$version );

			wp_register_script( 'wowmall-products-carousel', $this::$pluginurl . '/assets/js/products-carousel' . $this::$suffix . '.js', array( 'wowmall-theme-script' ), $this::$version, true );


			wp_register_script( 'wowmall-lookbook-post-front', $this::$pluginurl . '/assets/js/lookbook-front.js', array( 'wowmall-theme-script' ), $this::$version, true );
			wp_localize_script( 'wowmall-lookbook-post-front', 'wowmallLookbook', array( 'ajaxurl' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ) ) );
		}

		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		public function get_original_product_id( $id ) {

			global $sitepress;

			if( isset( $sitepress ) ) {

				$id = icl_object_id($id, 'product', true, $sitepress->get_default_language());
			}
			return $id;
		}
	}

	wowmallShortcodes::instance();
}

function wowmall_shortcodes() {
	if ( method_exists ( 'wowmallShortcodes', 'instance' ) ) {
		return wowmallShortcodes::instance();
	}
	return null;
}
