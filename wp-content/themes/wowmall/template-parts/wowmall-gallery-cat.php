<?php

global $wowmall_category;

$cat = $wowmall_category;

$cat_posts = get_posts( array(
	'post_type'   => 'gallery',
	'numberposts' => - 1,
	'tax_query'   => array(
		array(
			'taxonomy'         => 'gallery-cat',
			'field'            => 'id',
			'terms'            => $cat->term_id,
			'include_children' => false,
		),
	),
	'orderby'        => ! empty( $wowmall_options['gallery_orderby'] ) ? $wowmall_options['gallery_orderby'] : 'menu_order',
	'order'          => ! empty( $wowmall_options['gallery_order'] ) ? $wowmall_options['gallery_order'] : 'ASC',
) );
if ( ! empty( $cat_posts ) ) {
	foreach ( $cat_posts as $i => $post ) {
		if ( has_post_thumbnail( $post->ID ) ) {
			$title                       = esc_html( get_the_title( $post->ID ) );
			$thumb_id                    = get_post_thumbnail_id( $post->ID );
			$ligthbox_items[ $i ]        = new stdClass;
			$ligthbox_items[ $i ]->src   = esc_url( wp_get_attachment_image_url( $thumb_id, 'full' ) );
			$ligthbox_items[ $i ]->title = $title;
			if( ! wp_is_mobile() ) {
				$ligthbox_thumbs[] = '<img src="' . esc_url( wp_get_attachment_image_url( $thumb_id, 'gallery_img_size_lightbox_thumb' ) ) . '" class=mfp-prevent-close>';
			}
			$tags                        = get_the_tags( $post->ID );
			$tags_arr                    = array();
			$ligthbox_tags[ $i ]         = '<div class=gallery-tags>';
			if ( ! empty( $tags ) ) {
				foreach ( $tags as $key => $tag ) {
					$tags_arr[ $key ]['link'] = esc_url( get_term_link( $tag, 'post_tag' ) );
					$tags_arr[ $key ]['name'] = $tag->name;
				}
				foreach ( $tags_arr as $k => $tag ) {
					if ( 0 < $k ) {
						$ligthbox_tags[ $i ] .= ', ';
					}
					$ligthbox_tags[ $i ] .= '<a href="' . $tag['link'] . '" rel="tag">' . $tag['name'] . '</a>';
				}
			}
			$ligthbox_tags[ $i ] .= '</div>';
		}
	}
}
?>
<figure>
	<?php do_action( 'wowmall_gallery_subcategory_thumbnail', $cat ); ?>
	<?php do_action( 'wowmall_gallery_subcategory_caption', $cat ); ?>
</figure>
<?php

$magnific_params = array();

if ( ! empty( $ligthbox_thumbs ) && 1 < count( $ligthbox_thumbs ) ) {
	$magnific_params['thumbs'] = '<div class="swiper-container mfp-prevent-close" id=mfp-swiper><div class="swiper-wrapper mfp-prevent-close"><div class="swiper-slide mfp-prevent-close">' . join( '</div><div class="swiper-slide mfp-prevent-close">', $ligthbox_thumbs ) . '</div></div><div class="swiper-scrollbar mfp-prevent-close"></div></div>';
}

if ( ! empty( $ligthbox_items ) ) {
	$magnific_params['items'] = $ligthbox_items;
}

if ( ! empty( $ligthbox_tags ) ) {
	$magnific_params['tags'] = $ligthbox_tags;
}
wp_localize_script( 'wowmall-gallery', 'wowmall_gallery_' . $cat->term_id , $magnific_params );