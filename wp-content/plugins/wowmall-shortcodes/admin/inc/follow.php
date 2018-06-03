<?php

if ( ! class_exists( 'wowmallFollowAdmin' ) ) {

	class wowmallFollowAdmin {

		protected static $_instance = null;

		public function __construct() {

			add_action( 'vc_before_init', array(
				$this,
				'vc_map',
			) );
		}

		public function vc_map() {

			$params = array(
				'name'                    => esc_html__( 'Wowmall Follow', 'wowmall-shortcodes' ),
				'base'                    => 'wowmall_follow',
				'description'             => esc_html__( 'Add Social Media Profiles links anywhere', 'wowmall-shortcodes' ),
				'category'                => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				'weight'                  => -999,
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

	wowmallFollowAdmin::instance();
}