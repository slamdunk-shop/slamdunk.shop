<?php

if ( ! class_exists( 'wowmallCollection' ) ) {

	class wowmallCollection {

		protected static $_instance = null;

		public function __construct() {

			add_shortcode( 'wowmall_collection', array(
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

			global $woocommerce_loop;

			$atts = shortcode_atts( array(
				'css'      => '',
				'el_class' => '',

			), $atts );

			$product_categories = get_categories( apply_filters( 'woocommerce_product_subcategories_args', array(
				'parent'       => 0,
				'menu_order'   => 'ASC',
				'hide_empty'   => 0,
				'hierarchical' => 1,
				'taxonomy'     => 'product_cat',
				'pad_counts'   => 1,
			) ) );

			if ( ! apply_filters( 'woocommerce_product_subcategories_hide_empty', false ) ) {
				$product_categories = wp_list_filter( $product_categories, array( 'count' => 0 ), 'NOT' );
			}

			ob_start();

			if ( $product_categories ) {

				if(  '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) {
					$class = '';
					if( '' !== $atts['css'] && function_exists( 'vc_shortcode_custom_css_class' ) ) {
						$class .= vc_shortcode_custom_css_class( $atts['css'], ' ' );
					}
					if( ! empty( $atts['el_class'] ) ) {
						$class .= ' ' . $atts['el_class'];
					} ?>
					<div class="<?php echo $class; ?>">
				<?php }

				?>

				<div class="woocommerce wowmall-collection">

					<?php woocommerce_product_loop_start(); ?>

					<li class="grid-sizer"></li>

					<?php foreach ( $product_categories as $category ) {
						wc_get_template( 'content-product_cat.php', array(
							'category' => $category,
						) );
					}

					woocommerce_product_loop_end(); ?>
				</div>
			<?php }

			if(  '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) { ?>
				</div>
			<?php }

			return ob_get_clean();
		}

		public function vc_map() {

			$params = array(
				'name'                    => esc_html__( 'Wowmall Products Collection', 'wowmall-shortcodes' ),
				'base'                    => 'wowmall_collection',
				'description'             => esc_html__( 'Add Products Collection shortcode', 'wowmall-shortcodes' ),
				'category'                => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				'weight'                  => - 999,
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

	wowmallCollection::instance();
}