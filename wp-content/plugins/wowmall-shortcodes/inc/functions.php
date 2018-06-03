<?php

function wowmall_posts_carousel_item_thumb() {
	if ( has_post_thumbnail() ) { ?>
		<div class=entry-thumb>
		<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>">
			<?php
				the_post_thumbnail();
			 ?>
		</a>
		</div>
	<?php
	}
}

function wowmall_posts_carousel_item_date() {
	$format = 'd F';
	if( get_the_date('Y') != date('Y') ) {
		$format = 'd F Y';
	}
	$time_string = sprintf( '<time class="entry-date published" datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date( $format ) )
	);
	echo $time_string;
}

function wowmall_posts_carousel_item_title() {
	the_title( sprintf( '<h3 class=entry-title><a href="%s" rel=bookmark>', esc_url( get_permalink() ) ), '</a></h3>' );
}