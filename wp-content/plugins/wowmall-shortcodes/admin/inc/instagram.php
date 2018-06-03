<?php

if ( ! class_exists( 'wowmallInstagramAdmin' ) ) {

	class wowmallInstagramAdmin {

		protected static $_instance = null;

		public static $placeholder;

		public function __construct() {

			add_action( 'vc_before_init', array(
				$this,
				'vc_map',
			) );
		}

		public function vc_map() {

			$params = array(
				'name'        => esc_html__( 'Wowmall Instagram', 'wowmall-shortcodes' ),
				'base'        => 'wowmall_instagram',
				'description' => esc_html__( 'Display Instagram Feed', 'wowmall-shortcodes' ),
				'category'    => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				'weight'      => -999,
				'params'      => array(
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Title', 'wowmall-shortcodes' ),
						'param_name' => 'title',
						'value'      => '',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Title Align', 'wowmall-shortcodes' ),
						'param_name' => 'title_align',
						'value'      => array(
							esc_html__( 'Left', 'wowmall-shortcodes' )   => 'left',
							esc_html__( 'Center', 'wowmall-shortcodes' ) => 'center',
							esc_html__( 'Right', 'wowmall-shortcodes' )  => 'right',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Number of rows', 'wowmall-shortcodes' ),
						'param_name' => 'rows',
						'value'      => array(
							'1' => '1',
							'2' => '2',
							'3' => '3',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Number of columns', 'wowmall-shortcodes' ),
						'param_name' => 'columns',
						'value'      => array(
							'4' => '4',
							'6' => '6',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Search by', 'wowmall-shortcodes' ),
						'param_name' => 'search_by',
						'value'      => array(
							'Hashtag' => 'hashtag',
							'User'    => 'user',
						),
					),
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Hashtag or username', 'wowmall-shortcodes' ),
						'param_name' => 'search',
						'value'      => '',
					),
					array(
						'type'       => 'checkbox',
						'heading'    => esc_html__( 'Likes', 'wowmall-shortcodes' ),
						'param_name' => 'likes',
					),
					array(
						'type'       => 'checkbox',
						'heading'    => esc_html__( 'Comments', 'wowmall-shortcodes' ),
						'param_name' => 'comments',
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

	wowmallInstagramAdmin::instance();
}