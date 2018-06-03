<?php
/**
 * Displayed when no products are found matching the current query
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/no-products-found.php.
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
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( is_search() ) { ?>
	<h1 class="page-title woocommerce-page-title wc-no-products-title"><?php woocommerce_page_title(); ?></h1>
	<p class=wc-no-products><?php printf( esc_html__( 'Search Results for %s', 'wowmall' ), '<span class=wowmall-search-query-title>&#x27;' . get_search_query() . '&#x27;</span>' ); ?></p>
<?php
return;
}
?>
<div class="wc-no-products-wrapper">
	<h1 class="page-title woocommerce-page-title wc-no-products-title"><?php woocommerce_page_title(); ?></h1>
	<?php
	/**
	 * woocommerce_archive_description hook.
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	woocommerce_product_subcategories( array(
		'before' => '<ul class=wc-loop-product-categories>',
		'after'  => '</ul>',
	) );
	?>
	<p class=wc-no-products><?php esc_html_e( 'There are no products matching the selection.', 'wowmall' ); ?></p>
</div>