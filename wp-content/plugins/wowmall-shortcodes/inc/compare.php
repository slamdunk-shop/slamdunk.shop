<?php

// register action hooks

add_action( 'init', 'wowmall_compare_start_session', 1 );
add_action( 'wp_logout', 'wowmall_compare_end_session' );
add_action( 'wp_login', 'wowmall_compare_end_session' );

add_action( 'wp_enqueue_scripts', 'wowmall_compare_setup_plugin' );

add_action( 'wp_ajax_wowmall_compare_button', 'wowmall_compare_process_button_action' );
add_action( 'wp_ajax_nopriv_wowmall_compare_button', 'wowmall_compare_process_button_action' );

add_action( 'wp_ajax_wowmall_compare_remove', 'wowmall_compare_process_remove_button_action' );
add_action( 'wp_ajax_nopriv_wowmall_compare_remove', 'wowmall_compare_process_remove_button_action' );

add_action( 'woocommerce_single_product_summary', 'wowmall_compare_add_button', 35 );

add_filter( 'wowmall_compare_item_remove_button', 'wowmall_compare_item_remove_button' );

add_filter( 'wowmall_compare_item_thumbnail', 'wowmall_compare_item_thumbnail' );

add_filter( 'wowmall_compare_item_title', 'wowmall_compare_item_title' );

add_filter( 'wowmall_compare_item_price', 'wowmall_compare_item_price' );

add_filter( 'wowmall_compare_item_add_to_cart', 'wowmall_compare_item_add_to_cart' );

add_filter( 'wowmall_compare_item_description', 'wowmall_compare_item_description' );

add_filter( 'wowmall_compare_item_availability', 'wowmall_compare_item_availability' );

add_filter( 'wowmall_compare_item_attributes', 'wowmall_compare_item_attributes' );

// add shortcode hooks
add_shortcode( 'wowmall_compare_table', 'wowmall_compare_shortcode' );

/**
 * Renders compare list shortcode.
 * @since     1.0.0
 * @shortcode tm_woo_compare_table
 */
function wowmall_compare_shortcode() {

	wp_enqueue_style( 'wowmall-compare' );
	wp_enqueue_style( 'tablesaw' );
	wp_enqueue_script( 'tablesaw-init' );

	return wowmall_compare_list_render();
}

function wowmall_compare_start_session() {
	if ( ! session_id() ) {
		session_start();
	}
}

function wowmall_compare_end_session() {
	session_destroy();
}

/**
 * Renders appropriate button for a product.
 * @since 1.0.0
 */
function wowmall_compare_add_button() {

	global $wowmall_wc_quick_view, $post;

	if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
		return;
	}

	$id      = get_the_ID();
	$id      = wowmall_shortcodes()->get_original_product_id( $id );
	$classes = array(
		'wowmall-compare-button',
		'btn',
		'btn-icon',
	);

	$text = esc_html__( 'Add to Compare', 'wowmall-shortcodes' );
	if ( in_array( $id, wowmall_compare_get_list() ) ) {

		$text      = esc_html__( 'Remove from Compare', 'wowmall-shortcodes' );
		$classes[] = ' in_compare';

	}
	$text = '<span class=wowmall_compare_product_actions_tip>' . $text . '</span>';
	if ( wp_is_mobile() && ! is_product() && ! ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) {
		$text = '';
	}
	$html = sprintf( '<a href="#" class="%s">%s</a>', join( ' ', $classes ), $text );

	echo apply_filters( 'wowmall_compare_button', $html, $classes, $id, $text );
}

/**
 * Registers scripts, styles and page endpoint.
 * @since  1.0.0
 * @action init
 */
function wowmall_compare_setup_plugin() {

	wp_enqueue_script( 'wowmall-compare' );
}

/**
 * Returns compare list.
 * @sicne 1.0.0
 * @return array The array of product ids to compare.
 */
function wowmall_compare_get_list() {

	$list = ! empty( $_SESSION['wowmall-compare'] ) ? $_SESSION['wowmall-compare'] : array();

	if ( ! empty( $list ) ) {
		$list = explode( ':', $list );
	}

	return $list;
}

/**
 * Sets new list of products to compare.
 * @since 1.0.0
 *
 * @param array $list The new array of products to compare.
 */
function wowmall_compare_set_list( $list ) {
	$value                       = join( ':', $list );
	$_SESSION['wowmall-compare'] = $value;

	return true;
}

/**
 * Processes buttons actions.
 * @since  1.0.0
 * @action wp_ajax_wowmall_compare_add_to_list
 */
function wowmall_compare_process_button_action() {

	$url = urldecode( filter_input( INPUT_POST, 'url' ) );
	$id  = url_to_postid( $url );
	$id  = wowmall_shortcodes()->get_original_product_id( $id );

	$list = wowmall_compare_get_list();

	if ( ! empty( $list ) && false !== array_search( $id, $list ) ) {

		if ( wowmall_compare_remove( $id ) ) {
			wp_send_json_success( array(
				'action' => 'remove',
			) );
		}

	}
	else {

		if ( wowmall_compare_add( $id ) ) {
			wp_send_json_success( array(
				'action' => 'add',
			) );
		}
	}
	wp_send_json_error();
}

/**
 * Returns message when is no products in compare.
 * @since 1.0.2
 * @return string The message
 */
function wowmall_compare_empty_message() {

	$empty_text = esc_html__( 'No products were added to the compare', 'wowmall-shortcodes' );
	$html       = sprintf( '<p class="wowmall-compare-empty">%s</p>', $empty_text );

	return apply_filters( 'wowmall_compare_empty_message', $html, $empty_text );
}

/**
 * Processes remove button action.
 * @since  1.0.0
 * @action wp_ajax_wowmall_compare_remove
 */
function wowmall_compare_process_remove_button_action() {
	$id = filter_input( INPUT_POST, 'pid', FILTER_SANITIZE_NUMBER_INT );
	if ( ! wowmall_compare_remove( $id ) ) {
		wp_send_json_error();
	}
	$json = wowmall_compare_list_render_table();
	wp_send_json_success( $json );
}

/**
 * Adds product to compare list.
 * @since 1.0.0
 *
 * @param int $id The product id to add to the compare list.
 */
function wowmall_compare_add( $id ) {

	$list   = wowmall_compare_get_list();
	$list[] = $id;

	return wowmall_compare_set_list( $list );
}

/**
 * Removes product from compare list.
 * @since 1.0.0
 *
 * @param int $id The product id to remove from compare list.
 */
function wowmall_compare_remove( $id ) {

	$list = wowmall_compare_get_list();

	if ( empty( $list ) ) {
		return false;
	}

	if ( false !== ( $key = array_search( $id, $list ) ) ) {
		unset( $list[ $key ] );

		return wowmall_compare_set_list( $list );
	}
	unset( $key, $list );

	return false;
}

/**
 * Get products added to compare.
 * @since 1.0.0
 *
 * @param array $list The array of products ids.
 *
 * @return object The list of products
 */
function wowmall_compare_get_products( $list ) {

	$args     = array(
		'post_type'      => 'product',
		'post__in'       => $list,
		'orderby'        => 'post__in',
		'posts_per_page' => -1,
	);
	$products = new WP_Query( $args );

	return $products;
}

/**
 * Renders compare list.
 * @since 1.0.0
 *
 * @param array $atts The array of attributes to show in the table.
 *
 * @return string Compare table HTML.
 */
function wowmall_compare_list_render( $atts = array() ) {

	$content   = array();
	$content[] = '<div class="woocommerce wowmall-compare-list">';
	$content[] = '<div class="woocommerce wowmall-compare-wrapper">';
	$content[] = wowmall_compare_list_render_table();
	$content[] = '</div>';
	$content[] = '<span class="wowmall-compare-loader"></span>';
	$content[] = '</div>';

	return join( "\n", $content );
}

/**
 * Renders compare table.
 * @since 1.0.0
 * @return string Wishlist table HTML.
 */
function wowmall_compare_list_render_table() {

	$list = wowmall_compare_get_list();

	if ( empty( $list ) ) {

		return wowmall_compare_empty_message();
	}

	$html = array();

	$products                              = wowmall_compare_get_products( $list );
	$GLOBALS['wowmall_product_in_compare'] = true;

	$structure = apply_filters( 'wowmall_compare_structure', array(
		array(
			'remove_button',
			'thumbnail',
			'title',
			'price',
			'add_to_cart',
		),
		esc_html__( 'Description', 'wowmall-shortcodes' )  => 'description',
		esc_html__( 'Availability', 'wowmall-shortcodes' ) => 'availability',
		'attributes',
		'price',
	) );

	if ( is_array( $structure ) && ! empty( $structure ) ) {
		$html[] = '<table class="wowmall-compare-table tablesaw" data-tablesaw-mode="swipe">';
		$first  = key( $structure );
		next( $structure );
		$second = key( $structure );
		end( $structure );
		$last = key( $structure );
		reset( $structure );
		foreach ( $structure as $th => $td ) {
			if ( is_string( $td ) && 'attributes' === $td ) {
				$html[] = apply_filters( 'wowmall_compare_item_attributes', $products );
			}
			else {
				if ( $th === $first ) {
					$html[] = '<thead>';
				}
				else if ( $th === $second ) {
					$html[] = '<tbody>';
				}
				$html[] = '<tr class="wowmall-compare-row">';
				if ( $th === $first ) {
					$html[] = '<th class="wowmall-compare-heading-cell title" data-tablesaw-priority="persist" scope="col" data-tablesaw-sortable-col>';
				}
				else {
					$html[] = '<td class="wowmall-compare-heading-cell">';
				}
				if ( is_string( $th ) ) {
					$html[] = $th;
				}
				else {
					$html[] = '&nbsp;';
				}
				if ( $th === $first ) {
					$html[] = '</th>';
				}
				else {
					$html[] = '</td>';
				}
				while ( $products->have_posts() ) {

					$products->the_post();

					global $product;

					if ( empty( $product ) ) {
						continue;
					}

					if ( $th === $first ) {
						$html[] = '<th class="wowmall-compare-cell ' . join( ' ', get_post_class() ) . '" scope="col" data-tablesaw-sortable-col>';
					}
					else {
						$class  = is_string( $td ) ? ' wowmall-compare-cell-' . $td . ' ' : ' ';
						$html[] = '<td class="wowmall-compare-cell' . $class . join( ' ', get_post_class() ) . '">';
					}

					if ( is_array( $td ) && ! empty( $td ) ) {
						foreach ( $td as $item ) {
							$html[] = apply_filters( 'wowmall_compare_item_' . $item, '' );
						}
					}
					else {
						$html[] = apply_filters( 'wowmall_compare_item_' . $td, '' );
					}

					if ( $th === $first ) {
						$html[] = '</th>';
					}
					else {
						$html[] = '</td>';
					}
				}
				wp_reset_query();

				$html[] = '</tr>';
				if ( $th === $first ) {
					$html[] = '</thead>';
				}
				else {
					if ( $th === $last ) {
						$html[] = '</tbody>';
					}
				}
			}
		}
		$html[] = '</table>';
	}

	unset( $GLOBALS['wowmall_product_in_compare'] );

	return join( "\n", $html );
}

if ( ! function_exists( 'wowmall_compare_item_remove_button' ) ) {
	function wowmall_compare_item_remove_button() {

		global $product;

		if ( empty( $product ) ) {
			return '';
		}

		$pid = $product->get_id();
		$pid = wowmall_shortcodes()->get_original_product_id( $pid );

		return '<div class="wowmall-compare-remove" data-id="' . $pid . '"><span class="myfont-trash-1" data-id="' . $pid . '"></span></div>';
	}
}

if ( ! function_exists( 'wowmall_compare_item_thumbnail' ) ) {
	function wowmall_compare_item_thumbnail() {

		global $product;

		if ( empty( $product ) ) {
			return '';
		}

		return '<a class="product-thumb" href="' . $product->get_permalink() . '">' . $product->get_image( 'woo_img_size_small' ) . '</a>';
	}
}

if ( ! function_exists( 'wowmall_compare_item_title' ) ) {
	function wowmall_compare_item_title() {

		global $product;

		if ( empty( $product ) ) {
			return '';
		}

		return '<h6 class="product-title"><a href="' . $product->get_permalink() . '">' . $product->get_title() . '</a></h6>';
	}
}

if ( ! function_exists( 'wowmall_compare_item_price' ) ) {
	function wowmall_compare_item_price() {

		global $product;

		if ( empty( $product ) ) {
			return '';
		}
		ob_start();
		woocommerce_template_loop_price();

		return '<div class="price-wrapper">' . ob_get_clean() . '</div>';
	}
}

if ( ! function_exists( 'wowmall_compare_item_add_to_cart' ) ) {
	function wowmall_compare_item_add_to_cart() {

		global $product;

		if ( empty( $product ) ) {
			return '';
		}
		ob_start();
		woocommerce_template_loop_add_to_cart();

		return ob_get_clean();
	}
}

if ( ! function_exists( 'wowmall_compare_item_description' ) ) {
	function wowmall_compare_item_description() {

		global $product;

		if ( empty( $product ) ) {
			return '';
		}

		return '<div class="wowmall-compare-product-desc">' . get_the_excerpt() . '</div>';
	}
}

if ( ! function_exists( 'wowmall_compare_item_availability' ) ) {
	function wowmall_compare_item_availability() {

		global $product;

		if ( empty( $product ) ) {
			return '';
		}
		$availability = $product->get_availability();
		if ( empty( $availability['availability'] ) && 'in-stock' === $availability['class'] ) {
			$availability['availability'] = esc_html__( 'In stock', 'wowmall-shortcodes' );
		}
		$availability_html = '<span class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</span>';

		return '<div class="product-status">' . $availability_html . '</div>';
	}
}

if ( ! function_exists( 'wowmall_compare_item_attributes' ) ) {
	function wowmall_compare_item_attributes( $products ) {

		$product_attributes = array();

		while ( $products->have_posts() ) {

			$products->the_post();

			global $product;

			if ( empty( $product ) ) {
				continue;
			}

			$attributes = $product->get_attributes();

			if ( ! empty( $attributes ) ) {

				foreach ( $attributes as $attribute_id => $attribute ) {

					$attr = $attribute['is_taxonomy'] ? wc_get_product_terms( $product->get_id(), $attribute['name'], array( 'fields' => 'names' ) ) : array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );

					$product_attributes[ $product->get_id() ][ wc_attribute_label( $attribute['name'] ) ] = apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $attr ) ) ), $attribute, $attr );
				}
			}
			else {
				$product_attributes[ $product->get_id() ] = array();
			}
		}
		$rebuilded_attributes = array();

		foreach ( $product_attributes as $id => $attribute ) {

			foreach ( $attribute as $attr_name => $attribute_value ) {

				$rebuilded_attributes[ $attr_name ][ $id ] = $attribute_value;
			}
		}
		foreach ( $rebuilded_attributes as $attr_name => $attr_products ) {

			foreach ( $product_attributes as $id => $attribute ) {

				if ( ! array_key_exists( $id, $attr_products ) ) {

					$rebuilded_attributes[ $attr_name ][ $id ] = '&#8212;';
				}
			}
		}
		wp_reset_query();
		$html = array();
		foreach ( $rebuilded_attributes as $attr_name => $attributes ) {

			$html[] = '<tr class="wowmall-compare-row">';
			$html[] = '<td class="wowmall-compare-heading-cell title">';
			$html[] = $attr_name;
			$html[] = '</td>';
			while ( $products->have_posts() ) {

				$products->the_post();

				global $product;

				if ( empty( $product ) ) {
					continue;
				}
				$html[] = '<td class="wowmall-compare-cell">';
				$html[] = $attributes[ $product->get_id() ];
				$html[] = '</td>';
			}
			wp_reset_query();

			$html[] = '</tr>';
		}

		return join( "\n", $html );
	}
}