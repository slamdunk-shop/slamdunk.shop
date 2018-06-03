<?php

if ( ! class_exists( 'wowmallPostsCarouselAdmin' ) ) {

	class wowmallPostsCarouselAdmin {

		protected static $_instance = null;

		public function __construct() {

			add_action( 'vc_before_init', array(
				$this,
				'vc_map',
			) );
		}

		public function vc_map() {

			vc_add_shortcode_param( 'multiselect', array(
				$this,
				'multiselect_settings_field',
			) );

			$formats = array();

			if ( current_theme_supports( 'post-formats' ) ) {
				$post_formats = get_theme_support( 'post-formats' );

				if ( is_array( $post_formats[0] ) ) {
					foreach ( $post_formats[0] as $format ) {
						$formats[esc_html__( ucfirst( $format ), 'wowmall-shortcodes' )] = $format;
					}
				}
			}

			$params = array(
				'name'        => esc_html__( 'Wowmall Posts Carousel', 'wowmall-shortcodes' ),
				'base'        => 'wowmall_posts_carousel',
				'description' => esc_html__( 'Add Posts Carousel shortcode', 'wowmall-shortcodes' ),
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
						'heading'    => esc_html__( 'Number of visible posts', 'wowmall-shortcodes' ),
						'param_name' => 'visible',
						'value'      => array(
							'1' => '1',
							'2' => '2',
							'3' => '3',
							'4' => '4',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Show', 'wowmall-shortcodes' ),
						'param_name' => 'show',
						'value'      => array(
							esc_html__( 'All Posts', 'wowmall-shortcodes' )      => 'all',
							esc_html__( 'Featured Posts', 'wowmall-shortcodes' ) => 'featured',
						),
					),
					array(
						'type'       => 'multiselect',
						'heading'    => esc_html__( 'Exclude Post formats', 'wowmall-shortcodes' ),
						'param_name' => 'format',
						'value'      => $formats,
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Order by', 'wowmall-shortcodes' ),
						'param_name' => 'orderby',
						'value'      => array(
							esc_html__( 'Date', 'wowmall-shortcodes' )   => 'date',
							esc_html__( 'Random', 'wowmall-shortcodes' ) => 'rand',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Order', 'wowmall-shortcodes' ),
						'param_name' => 'order',
						'value'      => array(
							esc_html__( 'ASC', 'wowmall-shortcodes' )  => 'asc',
							esc_html__( 'DESC', 'wowmall-shortcodes' ) => 'desc',
						),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra class name', 'wowmall-shortcodes' ),
						'param_name'  => 'el_class',
						'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wowmall-shortcodes' ),
					),
					array(
						'type'       => 'css_editor',
						'heading'    => __( 'Css Box', 'wowmall-shortcodes' ),
						'param_name' => 'css',
						'group'      => __( 'Design options', 'wowmall-shortcodes' ),
					),
				),
			);

			vc_map( $params );
		}

		public function multiselect_settings_field( $settings = array(), $value = '' ) {
			$html = '';
			if ( ! empty( $value ) ) {
				if ( ! is_array( $value ) ) {
					$value = explode( ',', $value );
				}
			}
			$defaults = array(
				'param_name' => '',
				'type'       => '',
			);
			$settings = wp_parse_args( $settings, $defaults );
			$html     .= '<div class="multiselect_block">' . '<select multiple name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-multiselectfield ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field">';
			foreach ( $settings['value'] as $key => $item ) {
				$html .= '<option value="' . $item . '"' . selected( in_array( $item, $value ), true ) . '>' . $key . '</option>';
			}
			$html .= '</select></div>';

			return $html;
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	wowmallPostsCarouselAdmin::instance();
}