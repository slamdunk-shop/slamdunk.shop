<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wowmall
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query, $wowmall_options;
if( isset( $wp_query->query['post_type'] ) && 'gallery' === $wp_query->query['post_type'] ) {
	get_template_part( 'taxonomy-gallery-cat' );
	return;
}
get_header();
$col      = 'col-xl-12';
$post_col = 'col-xl-3 col-lg-4 col-md-6 col-sm-12 col-xs-12';
$template = '';
if( ! empty( $wowmall_options['blog_layout_type'] ) && ! in_array( $wowmall_options['blog_layout_type'], array( 'grid', 'masonry' ) ) ) {
	$col      = 'col-xl-10 col-lg-9 col-md-8';
	$post_col = 'col-xs-12';
	$template = '-list';
} ?>
<div class=container>
	<header class=page-header>
		<?php
		the_archive_title( '<h1 class=page-title>', '</h1>' );
		the_archive_description( '<div class="taxonomy-description archive-taxonomy-description">', '</div>' );
		?>
	</header>
<div class=row>

	<div id=primary class="content-area <?php echo esc_attr( $col ); ?>">
		<main id=main class=site-main>

		<?php if ( have_posts() ) { ?>
			<div class=page-content>
				<div class="row posts-<?php echo count( $wp_query->posts ); ?>">
			<?php while ( have_posts() ) { the_post(); ?>
				<div class="<?php echo esc_attr( $post_col ); ?>">
				<?php

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content' . $template, get_post_format() );
				?>
				</div>
			<?php } ?>
				</div>
			</div>
			<?php the_posts_pagination( array(
				'prev_text' => '<i class=myfont-left-open-2></i>',
				'next_text' => '<i class=myfont-right-open-2></i>',
				'type'      => 'list',
			) );
		} else {
			get_template_part( 'template-parts/content', 'none' );
		} ?>
		</main>
	</div>
<?php get_sidebar(); ?>
</div>
</div>
<?php get_footer(); ?>
