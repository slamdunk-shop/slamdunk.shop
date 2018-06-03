<?php

add_action( 'vc_before_init', 'vc_map_wowmall_wishlist' );

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