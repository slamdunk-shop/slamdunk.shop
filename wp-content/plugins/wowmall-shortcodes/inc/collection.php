<?php

if ( ! class_exists( 'wowmallCollection' ) ) {

	class wowmallCollection {

		protected static $_instance = null;

		public function __construct() {

			add_shortcode( 'wowmall_collection', array(
				$this,
				'shortcode',
			) );
		}

		public function shortcode( $atts = array() ) {

			$atts = shortcode_atts( array(
				'css'      => '',
				'el_class' => '',
				'categories' => 'all',

			), $atts );

			$atts['categories'] = explode( ',', $atts['categories'] );

			$args = array(
				'parent'       => 0,
				'menu_order'   => 'ASC',
				'hide_empty'   => 0,
				'hierarchical' => 1,
				'taxonomy'     => 'product_cat',
				'pad_counts'   => 1,
			);

			if( ! in_array( 'all', $atts['categories'] ) && ! empty( $atts['categories'] ) ) {
				$args['include'] = join( ',', $atts['categories'] );
			}

			$product_categories = get_categories( apply_filters( 'woocommerce_product_subcategories_args', $args ) );

			if ( ! apply_filters( 'woocommerce_product_subcategories_hide_empty', false ) ) {
				$product_categories = wp_list_filter( $product_categories, array( 'count' => 0 ), 'NOT' );
			}

			ob_start();

			if ( $product_categories ) {

				if ( '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) {
					$class = '';
					if ( '' !== $atts['css'] && function_exists( 'vc_shortcode_custom_css_class' ) ) {
						$class .= vc_shortcode_custom_css_class( $atts['css'], ' ' );
					}
					if ( ! empty( $atts['el_class'] ) ) {
						$class .= ' ' . $atts['el_class'];
					} ?>
					<div class="<?php echo $class; ?>">
				<?php }

				?>

				<div class="woocommerce wowmall-collection">

					<?php woocommerce_product_loop_start(); ?>

					<li class="grid-sizer"></li>

					<?php
						global $woocommerce_loop;
					foreach ( $product_categories as $category ) {

						$woocommerce_loop['name'] = 'wowmall_collection';
						wc_get_template( 'content-product_cat.php', array(
							'category' => $category,
						) );
					}
					woocommerce_product_loop_end();
					woocommerce_reset_loop(); ?>
				</div>
			<?php }

			if ( '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) { ?>
				</div>
			<?php }

			return ob_get_clean();
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