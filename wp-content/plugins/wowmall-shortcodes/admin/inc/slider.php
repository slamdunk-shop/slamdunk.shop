<?php

if ( ! class_exists( 'wowmallSliderAdmin' ) ) {

	class wowmallSliderAdmin {

		protected static $_instance = null;

		public $slide_number;

		public function __construct() {

			add_action( 'vc_before_init', array(
				$this,
				'vc_map',
			) );

			add_action( 'save_post', array(
				$this,
				'reset_transients',
			) );

			add_action( 'trashed_post', array(
				$this,
				'reset_transients',
			) );
		}

		public function reset_transients() {
			delete_transient( 'wowmall_slides' );
		}

		public function vc_map() {

			$params = array(
				'name'                    => esc_html__( 'Wowmall Slider', 'wowmall-shortcodes' ),
				'base'                    => 'wowmall_slider',
				"as_parent"               => array( 'only' => 'wowmall_slider_item' ),
				"show_settings_on_create" => false,
				'category'                => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				"is_container"            => true,
				'params'                  => array(
					array(
						'type'       => 'checkbox',
						'heading'    => esc_html__( 'Full window mode', 'wowmall-shortcodes' ),
						'param_name' => 'full_window',
					),
					array(
						'type'        => 'textfield',
						'heading'     => esc_html__( 'Extra class name', 'wowmall-shortcodes' ),
						'param_name'  => 'el_class',
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wowmall-shortcodes' ),
					),
					array(
						'type'       => 'css_editor',
						'heading'    => esc_html__( 'Css Box', 'wowmall-shortcodes' ),
						'param_name' => 'css',
						'group'      => esc_html__( 'Design options', 'wowmall-shortcodes' ),
					),
				),
				"js_view"                 => 'VcColumnView',
			);

			vc_map( $params );

			$params = array(
				'name'     => esc_html__( 'Wowmall Slider Item', 'wowmall-shortcodes' ),
				'base'     => 'wowmall_slider_item',
				'category' => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				"as_child" => array( 'only' => 'wowmall_slider' ),
				'params'   => array(
					array(
						'type'       => 'attach_image',
						'heading'    => esc_html__( 'Slide Image', 'wowmall-shortcodes' ),
						'param_name' => 'slide',
					),
					array(
						'type'       => 'textarea_html',
						'heading'    => esc_html__( 'Slide Text', 'wowmall-shortcodes' ),
						'param_name' => 'content',
					),
				),
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

	wowmallSliderAdmin::instance();
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Wowmall_Slider extends WPBakeryShortCodesContainer {

	}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Wowmall_Slider_Item extends WPBakeryShortCode {

	}
}