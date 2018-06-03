<?php

// register action hooks

add_action( 'wp_enqueue_scripts', 'wowmall_wishlist_setup_plugin' );

add_action( 'wp_ajax_wowmall_wishlist_add', 'wowmall_wishlist_process_button_action' );
add_action( 'wp_ajax_nopriv_wowmall_wishlist_add', 'wowmall_wishlist_process_button_action' );

add_action( 'wp_ajax_wowmall_wishlist_remove', 'wowmall_wishlist_process_remove_button_action' );
add_action( 'wp_ajax_nopriv_wowmall_wishlist_remove', 'wowmall_wishlist_process_remove_button_action' );

add_action( 'init', 'tm_woowislist_session_to_db' );

add_action( 'woocommerce_single_product_summary', 'wowmall_wishlist_add_button', 35 );

// add shortcode hooks
add_shortcode( 'wowmall_wishlist_table', 'wowmall_wishlist_shortcode' );

if ( is_admin() ) {
	add_action( 'vc_before_init', 'vc_map_wowmall_wishlist' );
}

/**
 * Renders wishlist shortcode.
 * @since 1.0.0
 *
 * @param array $atts The array of shortcode attributes.
 */
function wowmall_wishlist_shortcode() {

	wp_enqueue_style( 'wowmall-wishlist' );

	return wowmall_wishlist_render();
}

/**
 * Renders appropriate button for a product.
 * @since 1.0.0
 */
function wowmall_wishlist_add_button() {

	global $wowmall_wc_quick_view, $post;

	if ( ! empty( $wowmall_wc_quick_view ) && $wowmall_wc_quick_view ) {
		return;
	}

	$id      = get_the_ID();
	$classes = array(
		'wowmall-wishlist-button',
		'btn',
		'btn-icon',
	);

	$text = esc_html__( 'Add to Wishlist', 'wowmall-shortcodes' );

	if ( in_array( $id, wowmall_wishlist_get_list() ) ) {

		$text      = esc_html__( 'Added to Wishlist', 'wowmall-shortcodes' );
		$classes[] = ' in_wishlist';

	}
	$text = '<span class=wowmall_wishlist_product_actions_tip>' . esc_html( $text ) . '</span>';

	if ( wp_is_mobile() && ! is_product() && ! ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) {
		$text = '';
	}

	$html = sprintf( '<a href="#" class="%s">%s</a>', implode( ' ', $classes ), $text );

	echo apply_filters( 'wowmall_wishlist_button', $html, $classes, $id, $text );
}

/**
 * Enqueue scripts and styles.
 * @since  1.0.0
 * @action wp_enqueue_scripts
 */
function wowmall_wishlist_setup_plugin() {

	wp_enqueue_script( 'wowmall-wishlist' );
}

/**
 * Returns wishlist list.
 * @sicne 1.0.0
 * @return array The array of product ids to wishlist.
 */
function wowmall_wishlist_get_list() {

	if ( is_user_logged_in() ) {

		$id   = get_current_user_id();
		$list = get_user_meta( $id, 'wowmall_wishlist_items', true );

		if ( ! empty( $list ) ) {

			$list = unserialize( $list );

		}
		else {

			$list = array();
		}
	}
	else {

		$list = ! empty( $_COOKIE['wowmall-wishlist'] ) ? $_COOKIE['wowmall-wishlist'] : array();

		if ( ! empty( $list ) ) {

			$list  = explode( ':', $list );
			$nonce = array_pop( $list );

			if ( ! wp_verify_nonce( $nonce, implode( $list ) ) ) {

				$list = array();
			}
		}
	}

	return $list;
}

/**
 * Sets new list of products to wishlist.
 * @since 1.0.0
 *
 * @param array $list The new array of products to wishlist.
 */
function wowmall_wishlist_set_list( $list ) {

	$nonce = wp_create_nonce( implode( $list ) );
	$value = implode( ':', array_merge( $list, array( $nonce ) ) );
	setcookie( 'wowmall-wishlist', $value, time() + 60 * 60 * 24 * 30 * 12, '/' );
	$_COOKIE['wowmall-wishlist'] = $value;

	return true;
}

/**
 * Returns wishlist page link.
 * @since 1.0.0
 * @return string The wishlist page link on success, otherwise FALSE.
 */
function wowmall_wishlist_get_page_link() {

	$page_id = intval( get_option( 'wowmall_wishlist_page', '' ) );

	if ( ! $page_id ) {

		return false;
	}
	$page_link = get_permalink( $page_id );

	if ( ! $page_link ) {

		return false;
	}

	return trailingslashit( $page_link );
}

/**
 * Processes buttons actions.
 * @since  1.0.0
 * @action wp_ajax_wowmall_wishlist_add_to_list
 */
function wowmall_wishlist_process_button_action() {

	$url = urldecode( filter_input( INPUT_POST, 'url' ) );
	$id  = url_to_postid( $url );
	$id  = wowmall_shortcodes()->get_original_product_id( $id );

	if ( wowmall_wishlist_add( $id ) ) {
		wp_send_json_success();
	}

	wp_send_json_error();
}

/**
 * Returns message when is no products in wishlist.
 * @since 1.0.0
 * @return string The message
 */
function wowmall_wishlist_empty_message() {

	$empty_text = esc_html__( 'No products were added to the wishlist', 'wowmall-shortcodes' );

	return apply_filters( 'wowmall_wishlist_empty_message', sprintf( '<p class="wowmall-wishlist-empty">%s</p>', $empty_text ), $empty_text );
}

/**
 * Processes remove button action.
 * @since  1.0.0
 * @action wp_ajax_wowmall_wishlist_remove
 */
function wowmall_wishlist_process_remove_button_action() {

	$id = absint( filter_input( INPUT_POST, 'pid' ) );
	if ( wowmall_wishlist_remove( $id ) ) {
		$json = wowmall_wishlist_render_table();
		wp_send_json_success( $json );
	}
	wp_send_json_error();
}

/**
 * Adds product to wishlist.
 * @since 1.0.0
 *
 * @param int $id The product id to add to the wishlist.
 */
function wowmall_wishlist_add( $id ) {

	$id = intval( $id );

	if ( is_user_logged_in() ) {

		$user_id = get_current_user_id();
		$list    = get_user_meta( $user_id, 'wowmall_wishlist_items', true );

		if ( ! empty( $list ) ) {

			$list = unserialize( $list );

		}
		else {

			$list = array();
		}
		$list[] = $id;
		$list   = array_unique( $list );
		$list   = serialize( $list );

		return update_user_meta( $user_id, 'wowmall_wishlist_items', $list );

	}

	$list   = wowmall_wishlist_get_list();
	$list[] = $id;
	$list   = array_unique( $list );

	return wowmall_wishlist_set_list( $list );
}

/**
 * Removes product from wishlist list.
 * @since 1.0.0
 *
 * @param int $id The product id to remove from wishlist.
 */
function wowmall_wishlist_remove( $id ) {

	$id = intval( $id );

	if ( is_user_logged_in() ) {

		$user_id = get_current_user_id();
		$list    = get_user_meta( $user_id, 'wowmall_wishlist_items', true );

		if ( ! empty( $list ) ) {

			$list = unserialize( $list );
			$key  = array_search( $id, $list );

			if ( false !== $key ) {

				unset( $list[$key] );
			}
			$list = serialize( $list );

			return update_user_meta( $user_id, 'wowmall_wishlist_items', $list );

		}

		return false;

	}

	$list = wowmall_wishlist_get_list();
	$key  = array_search( $id, $list );

	if ( false !== $key ) {

		unset( $list[$key] );

		return wowmall_wishlist_set_list( $list );
	}

	return false;
}

/**
 * Get products added to wishlist.
 * @since 1.0.0
 *
 * @param array $list The array of products ids.
 *
 * @return object The list of products
 */
function wowmall_wishlist_get_products( $list ) {

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
 * Renders wishlist.
 * @since 1.0.0
 * @return string Wishlist HTML.
 */
function wowmall_wishlist_render() {

	$content   = array();
	$content[] = '<div class="woocommerce wowmall-wishlist row">';
	$content[] = '<div class="wowmall-wishlist-wrapper col-xl-8 offset-xl-2 col-xs-12">';
	$content[] = wowmall_wishlist_render_table();
	$content[] = '</div>';
	$content[] = '<span class="wowmall-wishlist-loader"></span>';
	$content[] = '</div>';

	return implode( "\n", $content );
}

/**
 * Renders wishlist table.
 * @since 1.0.0
 *
 * @param array $atts The wishlist table attributes.
 *
 * @return string Wishlist table HTML.
 */
function wowmall_wishlist_render_table() {

	$list = wowmall_wishlist_get_list();

	if ( empty( $list ) ) {

		return wowmall_wishlist_empty_message();
	}
	$html     = array();
	$products = wowmall_wishlist_get_products( $list );

	if ( empty( $products->posts ) ) {

		return wowmall_wishlist_empty_message();
	}

	$GLOBALS['wowmall_product_in_wishlist'] = true;
	$html[]                                 = '<table>';

	while ( $products->have_posts() ) {

		$products->the_post();

		global $product;

		if ( empty( $product ) ) {

			continue;
		}
		$pid          = $product->get_id();
		$pid          = wowmall_shortcodes()->get_original_product_id( $pid );
		$availability = $product->get_availability();
		if ( empty( $availability['availability'] ) && 'in-stock' === $availability['class'] ) {
			$availability['availability'] = esc_html__( 'In stock', 'wowmall-shortcodes' );
		}
		$html[] = '<tr class="wowmall-wishlist-item">';
		$html[] = '<td class="wowmall-wishlist-remove-cell">';
		$html[] = '<button type="button" class="wowmall-wishlist-remove" data-id="' . $pid . '"><span class="dismiss myfont-cancel"></span></button>';
		$html[] = '</td>';
		$html[] = '<td class="wowmall-wishlist-thumb-cell">';
		$html[] = '<a class="product-thumb" href="' . $product->get_permalink() . '">' . $product->get_image( 'woo_img_size_cart' ) . '</a>';
		$html[] = '</td>';
		$html[] = '<td class="wowmall-wishlist-title-cell">';
		$html[] = '<h6 class="product-title"><a href="' . $product->get_permalink() . '">' . $product->get_title() . '</a></h6>';
		$html[] = '</td>';
		$html[] = '<td class="wowmall-wishlist-price-cell ' . implode( ' ', get_post_class() ) . '">';
		$html[] = '<div class="price-wrapper">';
		ob_start();
		woocommerce_template_loop_price();
		$html[] = ob_get_clean();
		$html[] = '</div>';
		$html[] = '</td>';
		$html[] = '<td class="wowmall-wishlist-status-cell">';
		$html[] = '<div class="product-status">';
		$html[] = '<span class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</span>';
		$html[] = '</div>';
		$html[] = '</td>';
		$html[] = '<td class="wowmall-wishlist-add-to-cart-cell">';
		ob_start();
		woocommerce_template_loop_add_to_cart();
		$html[] = ob_get_clean();
		$html[] = '</td>';
		$html[] = '</tr>';
	}
	wp_reset_query();
	unset( $GLOBALS['wowmall_product_in_compare'] );
	$html[] = '</table>';

	return implode( "\n", $html );
}

function tm_woowislist_session_to_db() {

	if ( is_user_logged_in() ) {

		$list         = wowmall_wishlist_get_list();
		$session_list = ! empty( $_COOKIE['wowmall-wishlist'] ) ? explode( ':', $_COOKIE['wowmall-wishlist'] ) : array();

		if ( ! empty( $session_list ) ) {

			foreach ( $session_list as $product_id ) {

				if ( ! in_array( $product_id, $list ) ) {

					wowmall_wishlist_add( $product_id );
				}
			}
			wowmall_wishlist_set_list( array() );
		}
	}
}

function vc_map_wowmall_wishlist() {

	$params = array(
		'name'                    => esc_html__( 'Wowmall Wishlist', 'wowmall-shortcodes' ),
		'base'                    => 'wowmall_wishlist_table',
		'description'             => esc_html__( 'Add Wishlist page shortcode', 'wowmall-shortcodes' ),
		'category'                => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
		'show_settings_on_create' => false,
	);

	vc_map( $params );
}