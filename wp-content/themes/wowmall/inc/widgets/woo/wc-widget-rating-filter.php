<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rating Filter Widget and related functions.
 *
 *
 * @author   WooThemes
 * @category Widgets
 * @package  WooCommerce/Widgets
 * @version  2.6.0
 * @extends  WC_Widget
 */
class Wowmall_WC_Widget_Rating_Filter extends WC_Widget_Rating_Filter {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget ( $args, $instance ) {
		global $wp_the_query;

		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}
		if ( ! $wp_the_query->post_count ) {
			return;
		}
		ob_start();

		$found         = false;
		$rating_filter = isset( $_GET['rating_filter'] ) ? array_filter( array_map( 'absint', explode( ',', $_GET['rating_filter'] ) ) ) : array();

		$this->widget_start( $args, $instance );

		echo '<ul>';

		for ( $rating = 5; $rating >= 1; $rating-- ) {
			$count = $this->get_filtered_product_count( $rating );

			if ( empty( $count ) ) {
				continue;
			}

			$found = true;

			if ( in_array( $rating, $rating_filter ) ) {
				$link_ratings = implode( ',', array_diff( $rating_filter, array( $rating ) ) );
			}
			else {
				$link_ratings = implode( ',', array_merge( $rating_filter, array( $rating ) ) );
			}

			$class       = in_array( $rating, $rating_filter ) ? 'wc-layered-nav-rating chosen' : 'wc-layered-nav-rating';
			$link        = apply_filters( 'woocommerce_rating_filter_link', $link_ratings ? add_query_arg( 'rating_filter', $link_ratings ) : remove_query_arg( 'rating_filter' ) );
			$rating_html = wc_get_star_rating_html( $rating );
			$count_html  = esc_html( apply_filters( 'woocommerce_rating_filter_count', "{$count}", $count, $rating ) );

			printf( '<li class="%s"><a href="%s"><span class=star-rating>%s</span></a><span class=count>%s</span></li>', esc_attr( $class ), esc_url( $link ), $rating_html, $count_html );
		}

		echo '</ul>';

		$this->widget_end( $args );

		if ( ! $found ) {
			ob_end_clean();
		}
		else {
			echo ob_get_clean();
		}
	}
}
