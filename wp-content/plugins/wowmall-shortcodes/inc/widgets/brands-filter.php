<?php

class Wowmall_WC_Widget_Brands_Filter extends WC_Widget_Layered_Nav {

	public function __construct() {

		add_filter( 'woocommerce_product_query_tax_query', array(
			$this,
			'woocommerce_product_query_tax_query',
		) );

		add_filter( 'woocommerce_attribute_taxonomies', array(
			$this,
			'woocommerce_attribute_taxonomies',
		) );

		$this->widget_cssclass    = 'woocommerce widget_layered_nav';
		$this->widget_description = __( 'Filter products by brands when viewing product archives and categories.', 'wowmall-shortcodes' );
		$this->widget_id          = 'woocommerce_brands_filter';
		$this->widget_name        = __( 'WooCommerce brands filter', 'wowmall-shortcodes' );
		WC_Widget::__construct();
	}

	public function init_settings() {

		$this->settings = array(
			'title'      => array(
				'type'  => 'text',
				'std'   => __( 'Filter by brand', 'wowmall-shortcodes' ),
				'label' => __( 'Title', 'woocommerce' ),
			),
			'query_type' => array(
				'type'    => 'select',
				'std'     => 'or',
				'label'   => __( 'Query type', 'woocommerce' ),
				'options' => array(
					'and' => __( 'AND', 'woocommerce' ),
					'or'  => __( 'OR', 'woocommerce' ),
				),
			),
		);
	}

	public function woocommerce_product_query_tax_query( $q ) {

		if ( isset( $_GET['filter_brand'] ) && ! empty( $q ) ) {

			foreach( $q as $key => $query ) {
				if( isset( $query['taxonomy'] ) && 'pa_brand' === $query['taxonomy'] ) {
					$q[$key]['taxonomy'] = 'brand';
					break;
				}
			}
		}

		return $q;
	}

	public function get_brands() {
		$list_args = array(
			'hide_empty'   => 0,
			'hierarchical' => 0,
			'taxonomy'     => 'brand',
			'pad_counts'   => 1,
			'menu_order'   => 'ASC',
		);

		return get_categories( $list_args );
	}

	public function widget( $args, $instance ) {

		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}
		if( 'brand' === $this->get_current_taxonomy() ) {
			return;
		}

		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		$query_type = isset( $instance['query_type'] ) ? $instance['query_type'] : $this->settings['query_type']['std'];
		$brands     = $this->get_brands();

		if ( empty( $brands ) ) {
			return;
		}

		ob_start();

		$this->widget_start( $args, $instance );

		$found = $this->layered_nav_list( $brands, 'pa_brand', $query_type );

		$this->widget_end( $args );

		// Force found when option is selected - do not force found on taxonomy attributes
		if ( ! is_tax() && is_array( $_chosen_attributes ) && array_key_exists( 'pa_brand', $_chosen_attributes ) ) {
			$found = true;
		}

		if ( ! $found ) {
			ob_end_clean();
		} else {
			echo ob_get_clean();
		}
	}

	protected function layered_nav_list( $terms, $taxonomy, $query_type ) {
		// List display
		echo '<ul>';

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

			if ( 0 < $count ) {
				$found = true;
			}

			$filter_name    = 'filter_brand';
			$current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( $_GET[ $filter_name ] ) ) : array();
			$current_filter = array_map( 'sanitize_title', $current_filter );

			if ( ! in_array( $term->slug, $current_filter ) ) {
				$current_filter[] = $term->slug;
			}

			$link = $this->get_page_base_url( $taxonomy );

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
					$link = add_query_arg( 'query_type_brand', 'or', $link );
				}
			}
			if ( (int) $count > 0 ) {
				$link      = esc_url( apply_filters( 'woocommerce_layered_nav_link', $link, $term, $taxonomy ) );
				$term_html = '<a href="' . $link . '">' . esc_html( $term->name ) . '</a>';
			} else {
				$link      = false;
				$term_html = '<span>' . esc_html( $term->name ) . '</span>';
			}

			$term_html .= ' ' . apply_filters( 'woocommerce_layered_nav_count', '<span class="count">(' . absint( $count ) . ')</span>', $count, $term );

			echo '<li class="wc-layered-nav-term ' . ( $option_is_set ? 'chosen' : '' ) . '">';
			echo wp_kses_post( apply_filters( 'woocommerce_layered_nav_term_html', $term_html, $term, $link, $count ) );
			echo '</li>';
		}

		echo '</ul>';

		return $found;
	}

	public function woocommerce_attribute_taxonomies( $attribute_taxonomies ) {

		if( ( is_admin() && ! wp_doing_ajax() ) || ! isset( $_GET['filter_brand'] ) ) {
			return $attribute_taxonomies;
		}

		$brands = $this->get_brands();

		if ( empty( $brands ) ) {
			return $attribute_taxonomies;
		}
		global $wp_taxonomies;

		$brand = $brands[0];

		$attribute_taxonomies[] = (object) array(
			'attribute_label'   => 'Brand',
			'attribute_name'    => 'brand',
			'attribute_type'    => 'text',
			'attribute_orderby' => 'menu_order',
			'attribute_id'      => $brand->term_id,
			'attribute_public'  => 0,
		);
		if ( isset( $wp_taxonomies['brand'] ) ) {
			$wp_taxonomies['pa_brand'] = $wp_taxonomies['brand'];
		}

		return $attribute_taxonomies;
	}

	/**
	 * Count products within certain terms, taking the main WP query into consideration.
	 *
	 * @param  array  $term_ids
	 * @param  string $taxonomy
	 * @param  string $query_type
	 * @return array
	 */
	protected function get_filtered_term_product_counts( $term_ids, $taxonomy, $query_type ) {
		global $wpdb;

		$tax_query  = WC_Query::get_main_tax_query();
		$meta_query = WC_Query::get_main_meta_query();

		if ( 'or' === $query_type ) {
			foreach ( $tax_query as $key => $query ) {
				if ( is_array( $query ) && ( $taxonomy === $query['taxonomy'] || $taxonomy === 'pa_' . $query['taxonomy'] ) ) {
					unset( $tax_query[ $key ] );
				}
			}
		}

		$meta_query      = new WP_Meta_Query( $meta_query );
		$tax_query       = new WP_Tax_Query( $tax_query );
		$meta_query_sql  = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql   = $tax_query->get_sql( $wpdb->posts, 'ID' );

		// Generate query
		$query           = array();
		$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, terms.term_id as term_count_id";
		$query['from']   = "FROM {$wpdb->posts}";
		$query['join']   = "
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			" . $tax_query_sql['join'] . $meta_query_sql['join'];

		$query['where']   = "
			WHERE {$wpdb->posts}.post_type IN ( 'product' )
			AND {$wpdb->posts}.post_status = 'publish'
			" . $tax_query_sql['where'] . $meta_query_sql['where'] . "
			AND terms.term_id IN (" . implode( ',', array_map( 'absint', $term_ids ) ) . ")
		";

		if ( $search = WC_Query::get_main_search_query_sql() ) {
			$query['where'] .= ' AND ' . $search;
		}

		$query['group_by'] = "GROUP BY terms.term_id";
		$query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
		$query             = implode( ' ', $query );

		$results           = $wpdb->get_results( $query );

		return wp_list_pluck( $results, 'term_count', 'term_count_id' );
	}
}