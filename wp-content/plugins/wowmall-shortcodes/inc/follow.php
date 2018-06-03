<?php

if ( ! class_exists( 'wowmallFollow' ) ) {

	class wowmallFollow {

		protected static $_instance = null;

		public function __construct() {

			add_shortcode( 'wowmall_follow', array(
				$this,
				'shortcode',
			) );
		}

		public function shortcode() {
			ob_start();
			if( function_exists( 'wowmall_social_nav' ) ) {
				wowmall_social_nav();
			}
			return ob_get_clean();
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