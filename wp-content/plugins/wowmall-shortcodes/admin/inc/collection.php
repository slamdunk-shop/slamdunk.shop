<?php

if ( ! class_exists( 'wowmallCollectionAdmin' ) ) {

	class wowmallCollectionAdmin {

		protected static $_instance = null;

		public function __construct() {

			add_action( 'vc_before_init', array(
				$this,
				'vc_map',
			) );
		}

		public function vc_map() {

			$args       = array(
				'taxonomy' => 'product_cat',
			);
			$terms      = get_categories( $args );
			$categories = array();

			if ( ! empty( $terms ) ) {

				foreach ( $terms as $term ) {
					$categories[$term->name] = $term->term_id;
				}
			}
			$categories = array_merge( array( esc_html__( 'All', 'wowmall-shortcodes' ) => 'all' ), $categories );

			$params = array(
				'name'        => esc_html__( 'Wowmall Products Collection', 'wowmall-shortcodes' ),
				'base'        => 'wowmall_collection',
				'description' => esc_html__( 'Add Products Collection shortcode', 'wowmall-shortcodes' ),
				'category'    => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				'weight'      => -999,
				'params'      => array(
					array(
						'type'       => 'multiselect',
						'heading'    => esc_html__( 'Filter by Category', 'wowmall-shortcodes' ),
						'param_name' => 'categories',
						'value'      => $categories,
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

	wowmallCollectionAdmin::instance();
}