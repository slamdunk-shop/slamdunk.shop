<?php

add_action( 'vc_before_init', 'vc_map_wowmall_compare' );

function vc_map_wowmall_compare() {

	$params = array(
		'name'                    => esc_html__( 'Wowmall Compare', 'wowmall-shortcodes' ),
		'base'                    => 'wowmall_compare_table',
		'description'             => esc_html__( 'Add Compare page shortcode', 'wowmall-shortcodes' ),
		'category'                => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
		'weight'                  => -999,
		'show_settings_on_create' => false,
	);

	vc_map( $params );
}