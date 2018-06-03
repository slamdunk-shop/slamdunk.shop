<?php
/**
 * The template for displaying gallery pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wowmall
 */

add_action( 'wp_enqueue_scripts', 'wowmall_enqueue_gallery_styles' );
get_header();
global $wp_query, $wowmall_options;
$layout = ! empty( $wowmall_options['gallery_layout_type'] ) ? $wowmall_options['gallery_layout_type'] : 'masonry';
if ( ! in_array( $layout, array(
	'grid',
	'masonry',
) )
) {
	$layout = 'masonry';
}
$cols = ! empty( $wowmall_options['gallery_columns'] ) ? (int) $wowmall_options['gallery_columns'] : 4;

if ( 4 < $cols ) {
	$cols = 4;
} elseif ( 3 > $cols ) {
	$cols = 3;
}
$element_class   = '';
$container_class = 'wowmall-gallery-container ';
if( 'grid' === $layout ) {
	$element_class .= ' col-sm-' . 12 / $cols;
	$container_class .= 'row wowmall-gallery-container-grid';
} elseif ( 'masonry' === $layout ) {
	$container_class .= 'wowmall-gallery-container-masonry cols-' . $cols;
}
do_action( 'wowmall_before_gallery' );
?>
<div class=container>
	<header class=page-header>

		<h1 class=page-title><?php single_cat_title(); ?></h1>
		<?php the_archive_description( '<div class=taxonomy-description>', '</div>' );
		?>
	</header><!-- .page-header -->

	<main id=primary class="content-area site-main">

		<?php if ( have_posts() ) :
			$gallery = wowmallGallery::instance();
			$gallery->setup_front_placeholder();
			$size = 'gallery_img_size_' . $layout . '_';
			$size = 4 === $cols ? $size . 'small' : $size . 'medium';
			?>
			<div class=page-content>
				<div id=wowmall-gallery>
					<div class=wowmall-gallery-wrapper>
						<div class="posts-<?php echo count($wp_query->posts) . ' ' . $container_class; ?>" data-cols="<?php echo esc_attr( $cols ); ?>">

						<?php do_action( 'wowmall_gallery_subcategories', array( 'size' => $size, 'element_class' => $element_class ) ); ?>
						<?php while ( have_posts() ) : the_post();
							global $post;
							$post->wowmall_thumb_size = $size;
							?>
							<div class="wowmall-gallery-item<?php echo esc_attr( $element_class ); ?>">
							<?php

								/*
								 * Include the Post-Format-specific template for the content.
								 * If you want to override this in a child theme, then include a file
								 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
								 */
								get_template_part( 'template-parts/wowmall-gallery-item' );
							?>
							</div>
						<?php endwhile;
						?>
						</div>
					</div>
					<?php
					the_posts_pagination( array(
						'prev_text' => '<i class=myfont-left-open-2></i>',
						'next_text' => '<i class=myfont-right-open-2></i>',
						'type'      => 'list',
					) ); ?>
				</div>
			</div>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

	</main><!-- #primary -->
</div>
<?php get_footer(); ?>
