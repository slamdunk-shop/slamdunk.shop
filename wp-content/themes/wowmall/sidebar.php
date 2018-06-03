<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Wowmall
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}

global $wowmall_options;

if( ( is_home() || is_archive() || is_search() ) && isset( $wowmall_options['blog_layout_type'] ) && 'list' !== $wowmall_options['blog_layout_type'] ) {
	return;
}
$col = ' col-xl-2 col-lg-3 col-md-4';
if( is_single() ) {
	if( isset( $wowmall_options['blog_sidebar_single'] ) && ! $wowmall_options['blog_sidebar_single'] ) {
		return;
	}
	$col = ' col-xl-2 col-lg-3 col-md-4 offset-xl-1';
}

?>

<div id=secondary class="widget-area<?php echo esc_attr( $col ); ?>" role=complementary>
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</div>
