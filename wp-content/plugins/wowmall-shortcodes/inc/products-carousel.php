<?php

if ( ! class_exists( 'wowmallProductsCarousel' ) ) {

	class wowmallProductsCarousel {

		protected static $_instance = null;

		public function __construct() {

			add_shortcode( 'wowmall_products_carousel', array(
				$this,
				'shortcode',
			) );
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
				'hide_outofstock' => '',
				'category'        => 'all',
				'el_class'        => '',
				'products_count'  => 12,
				'arrows'          => 'yes',
				'arrows_mobile'   => '',
				'autoplay'        => 0,
				'autoplay_mobile' => 5000,
			), $atts );

			$atts['category'] = explode( ',', $atts['category'] );

			if ( 1 > $atts['products_count'] ) {
				$atts['products_count'] = -1;
			}
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();

			$query_args = array(
				'posts_per_page' => $atts['products_count'],
				'post_status'    => 'publish',
				'post_type'      => 'product',
				'no_found_rows'  => 1,
				'order'          => $atts['order'],
				'meta_query'     => array(),
				'post_parent'    => 0,
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

			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) || 'yes' === $atts['hide_outofstock'] ) {
				$query_args['tax_query'][] = array(
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
                         data-direction=<?php echo $atts['direction']; ?> data-autoplay=<?php echo (int) $atts['autoplay']; ?>
                         data-autoplay-mobile=<?php echo (int) $atts['autoplay_mobile']; ?>>
                        <ul class="swiper-wrapper products">
							<?php
							add_filter( 'post_class', array(
								$this,
								'post_class',
							), 20, 3 );
							if ( 'vertical' === $atts['direction'] ) {
								$GLOBALS['wowmall_wc_loop_condition'] = 'list';
							}
							else {
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
					<?php if ( ( ! wp_is_mobile() && 'yes' === $atts['arrows'] ) || ( wp_is_mobile() && 'yes' === $atts['arrows_mobile'] ) ) { ?>
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
					unset( $classes[$key] );
				}
			}
			$classes[] = 'swiper-slide';

			return $classes;
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