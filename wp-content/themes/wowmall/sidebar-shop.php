<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Wowmall
 */
global $wowmall_options;
$layout = ! empty( $wowmall_options['wc_loop_layout'] ) ? $wowmall_options['wc_loop_layout'] : '1';
$shop_layout = ! empty( $wowmall_options['wc_shop_layout'] ) ? $wowmall_options['wc_shop_layout'] : '1';

if ( ! is_active_sidebar( 'sidebar-shop' ) ) {
	return;
}

if ( is_product() && ( ! isset( $wowmall_options['product_sidebar'] ) || ! $wowmall_options['product_sidebar'] ) ) {
	return;
}
if ( ! is_search() && '2' === $shop_layout && is_shop() ) {
	return;
}
if ( '3' === $layout && ( is_shop() || is_product_taxonomy() ) ) {
	return;
}
if ( is_product_category() ) {
	$term 		  = get_queried_object();
	$display_type = get_term_meta( $term->term_id, 'display_type', true );

	if( '' === $display_type ) {
		$display_type = get_option( 'woocommerce_category_archive_display' );
	}

	if( 'subcategories' === $display_type ) {
		return;
	}
}
?>

<div id=secondary class="sidebar-shop widget-area col-xxl-2 col-xl-3 col-md-4" role=complementary>
	<?php if( wp_is_mobile() ) { ?>
	<button class="wowmall-filters-btn btn"><span class=closed><?php esc_html_e( 'Filter', 'wowmall' ) ?></span><span class=opened><?php esc_html_e( 'Close', 'wowmall' ) ?></span></button>
	<div class=wowmall-sidebar-inner>
	<?php }
	dynamic_sidebar( 'sidebar-shop' );
	if( wp_is_mobile() ) { ?>
	</div>
	<?php } ?>
</div>
