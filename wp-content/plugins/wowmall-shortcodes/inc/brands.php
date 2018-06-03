<?php

if ( ! class_exists( 'wowmallBrands' ) ) {

	class wowmallBrands {

		protected static $_instance = null;

		public static $front_placeholder;

		public function __construct() {

			add_action( 'init', array(
				$this,
				'register_taxonomy',
			) );

			add_filter( 'woocommerce_sortable_taxonomies', array(
				$this,
				'sortable_taxonomies',
			) );

			add_shortcode( 'wowmall_brands', array(
				$this,
				'shortcode',
			) );

			add_action( 'after_setup_theme', array(
				$this,
				'add_image_sizes',
			), 11 );

			add_action( 'widgets_init', array(
				$this,
				'widgets_init',
			) );

			add_action( 'wc_single_variation_after_attribute_label_size', array(
				$this,
				'wc_single_variation_after_attribute_label_size',
			) );
		}

		public function add_image_sizes() {

			add_image_size( 'wowmall-brand', 100, 9999 );
			add_image_size( 'wowmall-brand-2x', 200, 9999 );
			add_image_size( 'wowmall-brand-3x', 300, 9999 );
		}

		public function setup_front_placeholder() {

			if ( is_null( self::$front_placeholder ) ) {

				global $wowmall_options;

				$color1 = ! empty( $wowmall_options['accent_color_1'] ) ? str_replace( '#', '', $wowmall_options['accent_color_1'] ) : 'fc6f38';

				$color2 = ! empty( $wowmall_options['accent_color_1'] ) ? str_replace( '#', '', $wowmall_options['accent_color_2'] ) : '222';

				self::$front_placeholder = apply_filters( 'wowmall_gallery_placeholder', '<img width=%1$s height=%2$s src="https://placeholdit.imgix.net/~text?txtsize=30&bg=' . $color2 . '&txtclr=' . $color1 . '&w=%1$s&h=%2$s&txt=%3$s" alt="%3$s">' );
			}
		}

		public function register_taxonomy() {
			$labels = array(
				'name'          => _x( 'Brands', 'taxonomy general name', 'wowmall-shortcodes' ),
				'singular_name' => _x( 'Brand', 'taxonomy singular name', 'wowmall-shortcodes' ),
				'search_items'  => __( 'Search Brands', 'wowmall-shortcodes' ),
				'all_items'     => __( 'All Brands', 'wowmall-shortcodes' ),
				'edit_item'     => __( 'Edit Brand', 'wowmall-shortcodes' ),
				'update_item'   => __( 'Update Brand', 'wowmall-shortcodes' ),
				'add_new_item'  => __( 'Add New Brand', 'wowmall-shortcodes' ),
				'new_item_name' => __( 'New Brand Name', 'wowmall-shortcodes' ),
				'menu_name'     => __( 'Brands', 'wowmall-shortcodes' ),
			);

			$args = array(
				'hierarchical' => false,
				'labels'       => $labels,
				'show_ui'      => true,
				'rewrite'      => array(
					'with_front' => false,
				),
				'query_var'    => true,
			);

			if ( post_type_exists( 'product' ) ) {

				register_taxonomy( 'brand', array( 'product' ), $args );
			}
		}

		public function sortable_taxonomies( $taxonomies ) {
			$taxonomies[] = 'brand';

			return $taxonomies;
		}

		public function shortcode( $atts = array() ) {

			$atts = shortcode_atts( array(
				'title'       => '',
				'title_align' => 'left',
				'visible'     => 8,
				'css'         => '',
				'el_class'    => '',
			), $atts );

			$list_args = array(
				'hide_empty'   => 0,
				'hierarchical' => 0,
				'taxonomy'     => 'brand',
				'pad_counts'   => 1,
				'menu_order'   => 'ASC',
			);

			$cats = get_categories( $list_args );

			ob_start();

			if ( ! empty( $cats ) ) {

				$this->setup_front_placeholder();

				$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script( 'wowmall-brands-carousel', wowmallShortcodes::$pluginurl . '/assets/js/brands-carousel' . $min . '.js', array( 'wowmall-theme-script' ), null, true );

				$id = uniqid();
				if ( '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) {
					$class = '';
					if ( '' !== $atts['css'] && function_exists( 'vc_shortcode_custom_css_class' ) ) {
						$class .= vc_shortcode_custom_css_class( $atts['css'], ' ' );
					}
					if ( ! empty( $atts['el_class'] ) ) {
						$class .= ' ' . $atts['el_class'];
					}; ?>
					<div class="<?php echo $class; ?>">
				<?php }
				?>
				<div class=wowmall-brands-carousel>
					<?php if ( ! empty( $atts['title'] ) ) {
						$style = '';
						if ( 'left' !== $atts['title_align'] ) {
							$style = ' style="text-align:' . $atts['title_align'] . '"';
						}
						echo '<h4' . $style . '>' . $atts['title'] . '</h4>';
					} ?>
					<div class=swiper-container id=<?php echo $id; ?> data-visible=<?php echo $atts['visible']; ?>>
						<div class=swiper-wrapper>
							<?php foreach ( $cats as $cat ) { ?>
								<a class="wowmall-brand-item swiper-slide"
								   href="<?php echo esc_url( get_term_link( $cat, 'brand' ) ); ?>">
									<?php
									$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
									if ( $thumbnail_id ) {
										$image = wp_get_attachment_image( $thumbnail_id, 'wowmall-brand' );
									}
									else {
										$image = sprintf( self::$front_placeholder, 100, 100, $cat->name );
									} ?>
									<?php echo $image; ?>
								</a>
							<?php } ?>
						</div>
					</div>
					<?php if ( ! wp_is_mobile() && ! empty( $atts['title'] ) ) { ?>
						<div class=swiper-button-prev id=swiper-button-prev<?php echo $id; ?>></div>
						<div class=swiper-button-next id=swiper-button-next<?php echo $id; ?>></div>
					<?php } ?>
				</div>
				<?php if ( '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) { ?>
					</div>
				<?php }
			}

			return ob_get_clean();
		}

		public function widgets_init() {
			if ( class_exists( 'WC_Widget_Layered_Nav' ) && ! class_exists( 'Wowmall_WC_Widget_Brands_Filter' ) ) {
				require_once 'widgets/brands-filter.php';
				register_widget( 'Wowmall_WC_Widget_Brands_Filter' );
				if ( class_exists( 'WC_Widget_Layered_Nav_Filters' ) && ! class_exists( 'Wowmall_Shortcodes_WC_Widget_Layered_Nav_Filters' ) ) {
					require_once 'widgets/class-wc-widget-layered-nav-filters.php';
					unregister_widget( 'WC_Widget_Layered_Nav_Filters' );
					register_widget( 'Wowmall_Shortcodes_WC_Widget_Layered_Nav_Filters' );
				}
			}
		}

		public function wc_single_variation_after_attribute_label_size() {

			global $product;
			if ( empty( $product ) ) {
				return;
			}

			$brands = get_the_terms( $product->get_id(), 'brand' );
			if ( empty( $brands ) ) {
				return;
			}

			$guides = array();

			foreach ( $brands as $brand ) {
				$guide = (int) get_term_meta( $brand->term_id, 'sizes_id', true );
				if ( ! $guide ) {
					continue;
				}
				$guide = wp_get_attachment_url( $guide );
				if ( ! $guide ) {
					continue;
				}
				$guides[ $brand->name ] = $guide;
			}
			if ( empty( $guides ) ) {
				return;
			}
			$count = count( $guides );
			echo '<span class="wowmall-size-guides">';
			if ( 1 < $count ) {
				echo _n( 'Size Guide', 'Size Guides: ', $count, 'wowmall-shortcodes' );
				foreach ( $guides as $name => $guide ) {
					echo '<a href="' . $guide . '">' . $name . '</a> ';
				}
			}
			else {
				echo '<a href="' . array_shift( $guides ) . '">' . _n( 'Size Guide', 'Size Guides:', $count, 'wowmall-shortcodes' ) . '</a> ';
			}
			echo '</span>';

		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	wowmallBrands::instance();
}