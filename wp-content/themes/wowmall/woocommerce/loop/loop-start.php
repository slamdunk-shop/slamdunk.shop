<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wowmall_options;

$layout      = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
$shop_layout = ! empty( $wowmall_options['wc_shop_layout'] ) ? $wowmall_options['wc_shop_layout'] : '1';
$cols        = '';
$condition   = wowmall_get_condition();

if ( 'grid' === $condition && is_active_sidebar( 'sidebar-shop' ) && ( '1' === $layout || '2' === $layout ) && is_shop() || is_product_taxonomy() || is_search() ) {
	$cols = ' cols-5';
}
if ( '2' === $shop_layout && is_shop() && ! is_search() ) {
	$cols = ' masonry';
}

if ( is_product_category() ) {
	$term 		  = get_queried_object();
	$display_type = get_term_meta( $term->term_id, 'display_type', true );

	if( '' === $display_type ) {
		$display_type = get_option( 'woocommerce_category_archive_display' );
	}
	if( 'subcategories' === $display_type ) {
		$cols = ' masonry';
	}
}

if( is_page() ) {
	global $post;
	if ( has_shortcode( $post->post_content, 'product_categories' ) ) {
		$columns = wc_get_loop_prop( 'columns' );
		if( ! empty( $columns ) && 5 === wc_get_loop_prop( 'columns' ) ) {
			$cols = ' cols-5';
		}
	}
	if ( has_shortcode( $post->post_content, 'wowmall_collection' ) ) {
		$cols = ' masonry';
	}
}

?>
<div class="row<?php echo esc_attr( $cols ); ?>">
<ul class=products>
