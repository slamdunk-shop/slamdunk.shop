<?php

if ( ! class_exists( 'wowmallProductsCarousel' ) ) {

	class wowmallProductsCarousel {

		protected static $_instance = null;

		public function __construct() {

			add_shortcode( 'wowmall_products_carousel', array(
				$this,
				'shortcode',
			) );
			if ( is_admin() ) {
				add_action( 'vc_before_init', array(
					$this,
					'vc_map',
				) );
			}
		}

		public function shortcode( $atts = array() ) {

			$atts = shortcode_atts( array(
				'title'           => '',
				'title_align'     => 'left',
				'visible'         => 6,
				'show'            => 'all',
				'orderby'         => 'Random',
				'order'           => 'ASC',
				'css'             => '',
				'direction'       => 'horizontal',
				'category'        => 'all',
				'el_class'        => '',
				'products_count'  => 12,
				'arrows'          => 'yes',
				'autoplay'        => 0,
				'autoplay_mobile' => 5000,
			), $atts );

			$atts['category'] = explode( ',', $atts['category'] );

			if ( 1 > $atts['products_count'] ) {
				$atts['products_count'] = - 1;
			}
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();

			$query_args = array(
				'posts_per_page' => $atts['products_count'],
				'post_status'    => 'publish',
				'post_type'      => 'product',
				'no_found_rows'  => 1,
				'order'          => $atts['order'],
				'meta_query'     => array(),
                'post_parent' => 0,
				'tax_query'      => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => is_search() ? $product_visibility_term_ids['exclude-from-search'] : $product_visibility_term_ids['exclude-from-catalog'],
						'operator' => 'NOT IN',
					),
				),
			);

			if ( ! in_array( 'all', $atts['category'] ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => $atts['category'],
				);
			}

			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['outofstock'],
						'operator' => 'NOT IN',
					),
				);
			}

			switch ( $atts['show'] ) {
				case 'featured' :
					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['featured'],
					);
					break;
				case 'new' :
					$query_args['meta_query'][] = array(
						'key'   => '_new',
						'value' => 'yes',
					);
					break;
				case 'onsale' :
					$product_ids_on_sale    = wc_get_product_ids_on_sale();
					$product_ids_on_sale[]  = 0;
					$query_args['post__in'] = $product_ids_on_sale;
					break;
			}

			switch ( $atts['orderby'] ) {
				case 'price' :
					$query_args['meta_key'] = '_price';
					$query_args['orderby']  = 'meta_value_num';
					break;
				case 'rand' :
					$query_args['orderby'] = 'rand';
					break;
				case 'sales' :
					$query_args['meta_key'] = 'total_sales';
					$query_args['orderby']  = 'meta_value_num';
					break;
				default :
					$query_args['orderby'] = 'date';
			}

            $query_args = apply_filters( 'wowmall_shortcodes_products_carousel_query_args', $query_args, $atts );

			$products = new WP_Query( $query_args );

			ob_start();

			if ( $products->have_posts() ) {

				wp_enqueue_script( 'wowmall-products-carousel' );

				if ( '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) {
					$class = '';
					if ( '' !== $atts['css'] && function_exists( 'vc_shortcode_custom_css_class' ) ) {
						$class .= vc_shortcode_custom_css_class( $atts['css'], ' ' );
					}
					if ( ! empty( $atts['el_class'] ) ) {
						$class .= ' ' . $atts['el_class'];
					} ?>
					<div class="<?php echo $class; ?>">
				<?php } ?>
				<div class="wowmall-products-carousel woocommerce">
					<?php if ( ! empty( $atts['title'] ) ) {
						$style = '';
						if ( 'left' !== $atts['title_align'] ) {
							$style = ' style="text-align:' . $atts['title_align'] . '"';
						}
						echo '<h4' . $style . '>' . $atts['title'] . '</h4>';
					}
					$id = uniqid();
					?>
					<div class=swiper-container id=<?php echo $id; ?> data-visible=<?php echo $atts['visible']; ?>
					     data-direction=<?php echo $atts['direction']; ?> data-autoplay=<?php echo (int) $atts['autoplay']; ?> data-autoplay-mobile=<?php echo (int) $atts['autoplay_mobile']; ?>>
						<ul class="swiper-wrapper products">
							<?php
							add_filter( 'post_class', array(
								$this,
								'post_class',
							), 20, 3 );
							if ( 'vertical' === $atts['direction'] ) {
								$GLOBALS['wowmall_wc_loop_condition'] = 'list';
							} else {
								$GLOBALS['wowmall_wc_loop_condition'] = 'grid';
								if ( wp_is_mobile() ) {
									$GLOBALS['wowmall_wc_loop_condition'] = 'big';
								}
							}
							global $wowmall_options;
							$layout                            = isset( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : 1;
							$wowmall_options['wc_loop_layout'] = 1;
							if ( 6 > $atts['visible'] ) {
								$wowmall_options['wc_loop_layout'] = 4;
							}
							if ( 5 > $atts['visible'] ) {
								$wowmall_options['wc_loop_layout'] = 5;
							}
							if ( 4 > $atts['visible'] ) {
								$wowmall_options['wc_loop_layout'] = 6;
							}
							if ( 3 > $atts['visible'] ) {
								$wowmall_options['wc_loop_layout'] = 7;
							}
							if ( 2 > $atts['visible'] ) {
								$wowmall_options['wc_loop_layout'] = 8;
							}
							if ( 'vertical' === $atts['direction'] ) {
								$wowmall_options['wc_loop_layout'] = 9;
							}
							while ( $products->have_posts() ) {
								$products->the_post();
								wc_get_template( 'content-product.php', array( 'show_rating' => false ) );
							}
							unset( $GLOBALS['wowmall_wc_loop_condition'] );
							$wowmall_options['wc_loop_layout'] = $layout;
							remove_filter( 'post_class', array(
								$this,
								'post_class',
							), 20 ); ?>
						</ul>
					</div>
					<?php if ( ! wp_is_mobile() && 'yes' === $atts['arrows'] ) { ?>
						<div class=swiper-button-prev
						     data-direction=<?php echo $atts['direction']; ?> id=swiper-button-prev<?php echo $id; ?>></div>
						<div class=swiper-button-next
						     data-direction=<?php echo $atts['direction']; ?> id=swiper-button-next<?php echo $id; ?>></div>
					<?php } ?>
				</div>
				<?php if ( '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) { ?>
					</div>
				<?php }
			}

			wp_reset_query();

			return ob_get_clean();
		}

		public static function post_class( $classes ) {
			foreach ( $classes as $key => $class ) {
				if ( 0 === strpos( $class, 'col-' ) ) {
					unset( $classes[ $key ] );
				}
			}
			$classes[] = 'swiper-slide';

			return $classes;
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
					$categories[ $term->name ] = $term->slug;
				}
			}

			$categories = array_merge( array( esc_html__( 'All', 'wowmall-shortcodes' ) => 'all' ), $categories );
			$params     = array(
				'name'        => esc_html__( 'Wowmall Products Carousel', 'wowmall-shortcodes' ),
				'base'        => 'wowmall_products_carousel',
				'description' => esc_html__( 'Add Products Carousel shortcode', 'wowmall-shortcodes' ),
				'category'    => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				'weight'      => - 999,
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
			$html .= '<div class="textfield_number_block">' . '<input name="' . esc_attr( $settings['param_name'] ) . '" type="number" class="wpb_vc_param_value wpb-textfield_number ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" pattern="[0-9]">' . '</div>';

			return $html;
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	wowmallProductsCarousel::instance();
}