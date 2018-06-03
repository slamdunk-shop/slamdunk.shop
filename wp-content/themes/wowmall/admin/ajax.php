<?php

if( empty( $_GET['action'] ) ) {
	exit;
}


define('SHORTINIT', true);
//IMPORTANT: Change with the correct path to wp-load.php in your installation
require( '../../../../wp-load.php' );

$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );

function wowmall_get_loop_thumb() {
	require( ABSPATH . WPINC . '/link-template.php' );
	require( ABSPATH . WPINC . '/class-wp-post.php' );
	require( ABSPATH . WPINC . '/shortcodes.php' );
	require( ABSPATH . WPINC . '/formatting.php' );
	require( ABSPATH . WPINC . '/meta.php' );
	require( ABSPATH . WPINC . '/post.php' );
	require( ABSPATH . WPINC . '/media.php' );
	if ( !defined('WP_CONTENT_URL') )
		define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	$size = filter_input( INPUT_GET, 'size', FILTER_SANITIZE_STRING );
	$id  = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
	$attachment_ids = array_filter( explode( ',', get_post_meta( $id, '_product_image_gallery', true ) ) );
	if ( ! empty( $attachment_ids ) ) {
		$id  = array_shift( $attachment_ids );
		$image = wp_get_attachment_image_src($id, $size, 0);
		if ( $image ) {
			$srcset = '';
			$sizes = '';
			$image_meta = wp_get_attachment_metadata( $id );

			if ( is_array( $image_meta ) ) {
				$size_array = array( absint( $image[1] ), absint( $image[2] ) );
				$srcset = wp_calculate_image_srcset( $size_array, $image[0], $image_meta, $id );
				$sizes = wp_calculate_image_sizes( $size_array, $image[0], $image_meta, $id );
			}
			$img = array(
				'src' => $image[0],
				'width' => $image[1],
				'height' => $image[2],
				'srcset' => $srcset,
				'sizes' => $sizes,
			);
			wp_send_json_success( $img );
		}
	}
	die();
}

if ( is_callable( $action ) ) {
	call_user_func( $action );
	exit();
}

