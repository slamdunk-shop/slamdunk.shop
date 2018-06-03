<?php

if ( ! class_exists( 'wowmallMailChimp' ) ) {

	class wowmallMailChimp {

		protected static $_instance = null;

		public function __construct() {

			add_shortcode( 'wowmall_mailchimp', array(
				$this,
				'shortcode',
			) );
		}

		public function shortcode( $atts = array(), $content = null ) {

			if ( ! function_exists( 'wowmall_footer_subscribe' ) ) {
				return null;
			}
			if ( isset( $atts['form'] ) ) {
				$atts['pretext'] = $content;
			}
			ob_start();

			wowmall_footer_subscribe( $atts );

			return ob_get_clean();
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	wowmallMailChimp::instance();
}