<?php
/**
 * Shop breadcrumb
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/breadcrumb.php.
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
 * @version     2.3.0
 * @see         woocommerce_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$args =  array(
	'delimiter'   => '&nbsp;&nbsp;<span class=separator>|</span>&nbsp;&nbsp;',
	'wrap_before' => '<nav class=site-breadcrumb><div class=container><div class=breadcrumbs-inner>',
	'wrap_after'  => '</div></div></nav>',
	'before'      => '',
	'after'       => '',
	'home'        => '<span class=myfont-home></span>',
);

require_once ( WOWMALL_THEME_DIR . 'inc/breadcrumb.php' );

$breadcrumbs = new Wowmall_Breadcrumb();
$breadcrumb  = $breadcrumbs->generate();

if ( ! empty( $breadcrumb ) ) {

	echo '' . $args['wrap_before'];

	if ( ! empty( $args['home'] ) ) {
		echo '<a href="' . esc_url( home_url() ) . '">' . $args['home'] . '</a>' . $args['delimiter'];
	}
	foreach ( $breadcrumb as $key => $crumb ) {

		if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
			echo '<a href="' . esc_url( $crumb[1] ) . '">' . $crumb[0] . '</a>';
		} else {
			echo '<span class=breadcrumb_text>' . esc_html( $crumb[0] ) . '</span>';
		}
		if ( sizeof( $breadcrumb ) !== $key + 1 ) {
			echo '' . $args['delimiter'];
		}
	}
	echo '' . $args['wrap_after'];
}
