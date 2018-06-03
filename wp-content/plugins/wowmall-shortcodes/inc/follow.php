<?php

if ( ! class_exists( 'wowmallFollow' ) ) {

	class wowmallFollow {

		protected static $_instance = null;

		public function __construct() {

			add_shortcode( 'wowmall_follow', array(
				$this,
				'shortcode',
			) );
			if( is_admin() ) {
				add_action( 'vc_before_init', array(
					$this,
					'vc_map',
				) );
			}
		}

		public function shortcode() {
			ob_start();
			if( function_exists( 'wowmall_social_nav' ) ) {
				wowmall_social_nav();
			}
			return ob_get_clean();
		}

		public function vc_map() {

			$params = array(
				'name'        => esc_html__( 'Wowmall Follow', 'wowmall-shortcodes' ),
				'base'        => 'wowmall_follow',
				'description' => esc_html__( 'Add Social Media Profiles links anywhere', 'wowmall-shortcodes' ),
				'category'    => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				'weight' => -999,
				'show_settings_on_create' => false,
			);

			vc_map( $params );
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
	wowmallFollow::instance();
}