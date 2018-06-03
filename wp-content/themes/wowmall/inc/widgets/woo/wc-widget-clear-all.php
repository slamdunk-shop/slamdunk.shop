<?php

class Wowmall_WC_Widget_Clear_All extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce wowmall_widget_clear_all';
		$this->widget_description = __( 'Shows a "Clear All" button', 'wowmall' );
		$this->widget_id          = 'wowmall_wc_widget_clear_all';
		$this->widget_name        = __( 'WooCommerce Clear All Button', 'wowmall' );
		$this->settings           = array();

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}

		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		$min_price          = isset( $_GET['min_price'] ) ? wc_clean( $_GET['min_price'] ) : 0;
		$max_price          = isset( $_GET['max_price'] ) ? wc_clean( $_GET['max_price'] ) : 0;
		$min_rating         = isset( $_GET['min_rating'] ) ? absint( $_GET['min_rating'] ) : 0;
		$base_link          = $this->get_page_base_url();

		if ( 0 < count( $_chosen_attributes ) || 0 < $min_price || 0 < $max_price || 0 < $min_rating ) {

			$this->widget_start( $args, $instance );

			echo '<a class="btn btn-sm btn-primary" href="' . esc_url( $base_link ) . '">' . esc_html__( 'Clear all', 'wowmall' ) . '</a>';

			$this->widget_end( $args );
		}
	}



	/**
	 * Get current page URL for layered nav items.
	 *
	 * @return string
	 */
	protected function get_page_base_url() {
		if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
		} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) {
			$link = get_post_type_archive_link( 'product' );
		} elseif ( is_product_category() ) {
			$link = get_term_link( get_query_var( 'product_cat' ), 'product_cat' );
		} elseif ( is_product_tag() ) {
			$link = get_term_link( get_query_var( 'product_tag' ), 'product_tag' );
		} else {
			$queried_object = get_queried_object();
			$link = get_term_link( $queried_object->slug, $queried_object->taxonomy );
		}

		// Post Type Arg
		if ( isset( $_GET['post_type'] ) ) {
			$link = add_query_arg( 'post_type', wc_clean( $_GET['post_type'] ), $link );
		}

		return $link;
	}

}