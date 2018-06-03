<?php

$wowmall_func = wowmall_func();

add_action( 'init', array(
	$wowmall_func,
	'after_setup_theme',
), 9 );

add_action( 'wp_enqueue_scripts', array(
	$wowmall_func,
	'aio_front_scripts',
), 99 );

add_filter( 'wp_setup_nav_menu_item', 'wowmall_wp_setup_nav_menu_item' );

add_action( 'wp_enqueue_scripts', 'wowmall_add_preloader_styles', 999 );

add_action( 'wp_head', 'wowmall_share_meta_tags' );

add_action( 'wp_head', 'wowmall_enqueue_styles', 160 );

add_action( 'wp_footer', 'wowmall_enqueue_extra_scripts', 999 );

add_filter( 'get_site_icon_url', 'wowmall_get_site_icon_url', 10, 3 );

add_filter( 'site_icon_meta_tags', 'wowmall_site_icon_meta_tags' );

add_action( 'default_option_site_icon', 'wowmall_default_option_site_icon' );

add_filter( 'body_class', 'wowmall_body_class' );

add_filter( 'comment_form_fields', 'wowmall_comment_form_fields' );

add_filter( 'excerpt_length', 'wowmall_excerpt_length' );

add_filter( 'excerpt_more', 'wowmall_excerpt_more' );

add_action( 'wowmall_post_format_link', 'wowmall_post_format_link' );

add_action( 'wowmall_post_format_audio', 'wowmall_post_format_audio' );

add_action( 'wowmall_post_format_quote', 'wowmall_post_format_quote' );

add_filter( 'previous_post_link', 'wowmall_post_link', 10, 5 );

add_filter( 'next_post_link', 'wowmall_post_link', 10, 5 );

add_filter( 'show_recent_comments_widget_style', '__return_false' );

add_filter( 'navigation_markup_template', 'wowmall_navigation_template', 10, 2 );

add_action( 'wp_enqueue_scripts', 'wowmall_add_newsletter_popup' );

add_action( 'wowmall_before_gallery', 'wowmall_enqueue_gallery_assets' );

add_filter( 'wp_generate_tag_cloud', 'wowmall_generate_tag_cloud', 10, 3 );

add_filter( 'shortcode_atts_vc_progress_bar', 'wowmall_shortcode_atts_vc_progress_bar' );

add_filter( 'get_search_form', 'wowmall_get_search_form' );

add_filter( 'the_password_form', 'wowmall_the_password_form' );

add_filter( 'pre_get_posts', 'wowmall_exclude_pages_from_search' );

add_filter( 'monster-widget-config', 'wowmall_monster_widget_config' );

add_filter( 'wc-monster-widget-config', 'wowmall_wc_monster_widget_config' );