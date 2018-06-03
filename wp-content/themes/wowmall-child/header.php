<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Wowmall
 */

global $wowmall_options;

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset=<?php bloginfo( 'charset' ); ?>>
		<meta name=viewport content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
<script type="text/javascript" src="//vk.com/js/api/openapi.js?152"></script>
<script type="text/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = 'https://vk.com/rtrg?p=VK-RTRG-234162-9Nzmp';</script>
	</head>

	<body <?php body_class(); ?>>
		<div id=page class="hfeed site">
			<a class="skip-link screen-reader-text" href=#content><?php esc_html_e( 'Skip to content', 'wowmall' ); ?></a>
			<?php
			$header_layout = ! empty( $wowmall_options['header_layout'] ) ? $wowmall_options['header_layout'] : '1';
			if( wp_is_mobile() ) {
				$header_layout = 'mobile';
			} else {
				if( ! isset( $wowmall_options['header_sticky_enable'] ) || $wowmall_options['header_sticky_enable'] ) {
					get_template_part( 'template-parts/header/header', 'sticky' );
				}
			}
			get_template_part( 'template-parts/header/header', $header_layout );
			wowmall_breadcrumb();
			?>
			<div id=content class=site-content>