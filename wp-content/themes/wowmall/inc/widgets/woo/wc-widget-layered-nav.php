<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Layered Navigation Widget.
 *
 * @author   WooThemes
 * @category Widgets
 * @package  WooCommerce/Widgets
 * @version  2.6.0
 * @extends  WC_Widget
 */
class Wowmall_WC_Widget_Layered_Nav extends WC_Widget_Layered_Nav {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}

		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		$taxonomy           = isset( $instance['attribute'] ) ? wc_attribute_taxonomy_name( $instance['attribute'] ) : $this->settings['attribute']['std'];
		$query_type         = isset( $instance['query_type'] ) ? $instance['query_type'] : $this->settings['query_type']['std'];
		$display_type       = isset( $instance['display_type'] ) ? $instance['display_type'] : $this->settings['display_type']['std'];


		if ( ! taxonomy_exists( $taxonomy ) ) {
			return;
		}

		$get_terms_args = array( 'hide_empty' => '1' );

		$orderby = wc_attribute_orderby( $taxonomy );

		switch ( $orderby ) {
			case 'name' :
				$get_terms_args['orderby']    = 'name';
				$get_terms_args['menu_order'] = false;
			break;
			case 'id' :
				$get_terms_args['orderby']    = 'id';
				$get_terms_args['order']      = 'ASC';
				$get_terms_args['menu_order'] = false;
			break;
			case 'menu_order' :
				$get_terms_args['menu_order'] = 'ASC';
			break;
		}

		$terms = get_terms( $taxonomy, $get_terms_args );

		if ( 0 === sizeof( $terms ) ) {
			return;
		}

		switch ( $orderby ) {
			case 'name_num' :
				usort( $terms, '_wc_get_product_terms_name_num_usort_callback' );
			break;
			case 'parent' :
				usort( $terms, '_wc_get_product_terms_parent_usort_callback' );
			break;
		}

		$attribute_taxonomies = wc_get_attribute_taxonomies();

		$is_color = false;
		$is_size = false;

		if ( ! empty( $attribute_taxonomies ) ) {
			global $wowmall_options;
			foreach ( $attribute_taxonomies as $attribute ) {
				if( 'pa_'  . $attribute->attribute_name === $taxonomy ) {
					if( 'color' === $attribute->attribute_type && ( ! isset( $wowmall_options['custom_variations_color'] ) || $wowmall_options['custom_variations_color'] ) ) {
						$is_color = true;
						break;
					}
					elseif( 'size' === $attribute->attribute_type && ( ! isset( $wowmall_options['custom_variations_size'] ) || $wowmall_options['custom_variations_size'] ) ) {
						$is_size = true;
						break;
					}
				}
			}
		}

		ob_start();

		$this->widget_start( $args, $instance );

		if( $is_color ) {
			$found = $this->layered_nav_color_size( $terms, $taxonomy, $query_type, 'color' );
		} elseif ( $is_size ) {
			$found = $this->layered_nav_color_size( $terms, $taxonomy, $query_type, 'size' );
		} elseif ( 'dropdown' === $display_type ) {
			$found = $this->layered_nav_dropdown( $terms, $taxonomy, $query_type );
		} else {
			$found = $this->layered_nav_list( $terms, $taxonomy, $query_type );
		}

		$this->widget_end( $args );

		// Force found when option is selected - do not force found on taxonomy attributes
		if ( ! is_tax() && is_array( $_chosen_attributes ) && array_key_exists( $taxonomy, $_chosen_attributes ) ) {
			$found = true;
		}

		if ( ! $found ) {
			ob_end_clean();
		} else {
			echo ob_get_clean();
		}
	}

	protected function layered_nav_color_size( $terms, $taxonomy, $query_type, $class ) {

		// List display
		echo '<div class="wowmall-' . $class . '-select">';

		$term_counts        = $this->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );
		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		$found              = false;

		foreach ( $terms as $term ) {
			$current_values = isset( $_chosen_attributes[ $taxonomy ]['terms'] ) ? $_chosen_attributes[ $taxonomy ]['terms'] : array();
			$option_is_set  = in_array( $term->slug, $current_values );
			$count          = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

			// Skip the term for the current archive
			if ( $this->get_current_term_id() === $term->term_id ) {
				continue;
			}

			// Only show options with count > 0
			if ( 0 < $count ) {
				$found = true;
			}

			$filter_name    = 'filter_' . sanitize_title( str_replace( 'pa_', '', $taxonomy ) );
			$current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( $_GET[ $filter_name ] ) ) : array();
			$current_filter = array_map( 'sanitize_title', $current_filter );

			if ( ! in_array( $term->slug, $current_filter ) ) {
				$current_filter[] = $term->slug;
			}

			$link = remove_query_arg( $filter_name, $this->get_current_page_url() );

			// Add current filters to URL.
			foreach ( $current_filter as $key => $value ) {
				// Exclude query arg for current term archive term
				if ( $value === $this->get_current_term_slug() ) {
					unset( $current_filter[ $key ] );
				}

				// Exclude self so filter can be unset on click.
				if ( $option_is_set && $value === $term->slug ) {
					unset( $current_filter[ $key ] );
				}
			}

			if ( ! empty( $current_filter ) ) {
				$link = add_query_arg( $filter_name, implode( ',', $current_filter ), $link );

				// Add Query type Arg to URL
				if ( 'or' === $query_type && ! ( 1 === sizeof( $current_filter ) && $option_is_set ) ) {
					$link = add_query_arg( 'query_type_' . sanitize_title( str_replace( 'pa_', '', $taxonomy ) ), 'or', $link );
				}
			}
			$link      = esc_url( apply_filters( 'woocommerce_layered_nav_link', $link, $term, $taxonomy ) );
			$style = '';
			if( 'color' === $class ) {

				$color = get_term_meta( $term->term_id, 'color', true );
				$color = $color ? $color : '#000';

				$style = ' style="background-color: ' . $color . '"';
			}
			if ( $count > 0 ) {
				$link      = esc_url( apply_filters( 'woocommerce_layered_nav_link', $link, $term, $taxonomy ) );
				$term_html = '<a' . ( $option_is_set ? ' class="selected"' : '' ) . ' href="' . $link . '" title="' . esc_attr( $term->name ) . '"' . $style . '>' . esc_html( $term->name ) . '</a>';
			} else {
				$term_html = '<span' . ( $option_is_set ? ' class="selected"' : '' ) . $style . ' title="' . esc_attr( $term->name ) . '">' . esc_html( $term->name ) . '</span>';
			}
			echo wp_kses_post( $term_html );
		}

		echo '</div>';

		return $found;
	}
}
