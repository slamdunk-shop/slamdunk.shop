<?php

if ( ! class_exists( 'wowmallProductsCarouselAdmin' ) ) {

	class wowmallProductsCarouselAdmin {

		protected static $_instance = null;

		public function __construct() {

			add_action( 'vc_before_init', array(
				$this,
				'vc_map',
			) );
		}

		public function vc_map() {

			vc_add_shortcode_param( 'textfield_number', array(
				$this,
				'textfield_number_settings_field',
			) );

			$args       = array(
				'taxonomy' => 'product_cat',
			);
			$terms      = get_categories( $args );
			$categories = array();

			if ( ! empty( $terms ) ) {

				foreach ( $terms as $term ) {
					$categories[$term->name] = $term->slug;
				}
			}

			$categories = array_merge( array( esc_html__( 'All', 'wowmall-shortcodes' ) => 'all' ), $categories );
			$params     = array(
				'name'        => esc_html__( 'Wowmall Products Carousel', 'wowmall-shortcodes' ),
				'base'        => 'wowmall_products_carousel',
				'description' => esc_html__( 'Add Products Carousel shortcode', 'wowmall-shortcodes' ),
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
						'heading'    => esc_html__( 'Carousel Direction', 'wowmall-shortcodes' ),
						'param_name' => 'direction',
						'value'      => array(
							esc_html__( 'Horizontal', 'wowmall-shortcodes' ) => 'horizontal',
							esc_html__( 'Vertical', 'wowmall-shortcodes' )   => 'vertical',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Number of visible products', 'wowmall-shortcodes' ),
						'param_name' => 'visible',
						'value'      => array(
							'1' => '1',
							'2' => '2',
							'3' => '3',
							'4' => '4',
							'5' => '5',
							'6' => '6',
						),
					),
					array(
						'type'       => 'textfield_number',
						'heading'    => esc_html__( 'Number of products', 'wowmall-shortcodes' ),
						'param_name' => 'products_count',
						'value'      => '12',
					),
					array(
						'type'       => 'checkbox',
						'heading'    => esc_html__( 'Arrows', 'wowmall-shortcodes' ),
						'param_name' => 'arrows',
						'value'      => array( esc_html__( 'Yes', 'wowmall-shortcodes' ) => 'yes' ),
						'std'        => 'yes',
					),
					array(
						'type'       => 'checkbox',
						'heading'    => esc_html__( 'Arrows on mobile', 'wowmall-shortcodes' ),
						'param_name' => 'arrows_mobile',
						'value'      => array( esc_html__( 'Yes', 'wowmall-shortcodes' ) => 'yes' ),
					),
					array(
						'type'       => 'textfield_number',
						'heading'    => esc_html__( 'Autoplay, in ms', 'wowmall-shortcodes' ),
						'param_name' => 'autoplay',
						'value'      => '0',
					),
					array(
						'type'       => 'textfield_number',
						'heading'    => esc_html__( 'Autoplay on mobile devices, in ms', 'wowmall-shortcodes' ),
						'param_name' => 'autoplay_mobile',
						'value'      => '5000',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Show', 'wowmall-shortcodes' ),
						'param_name' => 'show',
						'value'      => array(
							esc_html__( 'All Products', 'wowmall-shortcodes' )      => 'all',
							esc_html__( 'Featured Products', 'wowmall-shortcodes' ) => 'featured',
							esc_html__( 'On-sale Products', 'wowmall-shortcodes' )  => 'onsale',
							esc_html__( 'New Products', 'wowmall-shortcodes' )      => 'new',
						),
					),
					array(
						'type'       => 'multiselect',
						'heading'    => esc_html__( 'Filter by Category', 'wowmall-shortcodes' ),
						'param_name' => 'category',
						'value'      => $categories,
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Order by', 'wowmall-shortcodes' ),
						'param_name' => 'orderby',
						'value'      => array(
							esc_html__( 'Date', 'wowmall-shortcodes' )   => 'date',
							esc_html__( 'Price', 'wowmall-shortcodes' )  => 'price',
							esc_html__( 'Random', 'wowmall-shortcodes' ) => 'rand',
							esc_html__( 'Sales', 'wowmall-shortcodes' )  => 'sales',
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
						'type'       => 'checkbox',
						'heading'    => esc_html__( 'Hide out of stock items', 'wowmall-shortcodes' ),
						'param_name' => 'hide_outofstock',
						'value'      => array( esc_html__( 'Yes', 'wowmall-shortcodes' ) => 'yes' ),
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

		public function textfield_number_settings_field( $settings = array(), $value = '' ) {
			$html     = '';
			$defaults = array(
				'param_name' => '',
				'type'       => '',
			);
			$settings = wp_parse_args( $settings, $defaults );
			$html     .= '<div class="textfield_number_block">' . '<input name="' . esc_attr( $settings['param_name'] ) . '" type="number" class="wpb_vc_param_value wpb-textfield_number ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" pattern="[0-9]">' . '</div>';

			return $html;
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	wowmallProductsCarouselAdmin::instance();
}