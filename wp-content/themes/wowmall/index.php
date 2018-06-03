<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wowmall
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header();
$col      = 'col-xl-12';
$post_col = 'col-xl-3 col-lg-4 col-md-6 col-sm-12 col-xs-12';
$template = '';
global $wp_query, $wowmall_options;
if( ! empty( $wowmall_options['blog_layout_type'] ) && ! in_array( $wowmall_options['blog_layout_type'], array( 'grid', 'masonry' ) ) ) {
	$col      = 'col-xl-10 col-lg-9 col-md-8';
	$post_col = 'col-xs-12';
	$template = '-list';
}
?>
<div class="container">
	<?php if ( is_home() ) : ?>
		<header class=page-header>
			<h1 class=page-title><?php single_post_title(); ?></h1>
		</header>
	<?php endif; ?>
	<div class=row>

		<main id=primary class="site-main content-area <?php echo esc_attr( $col ); ?>">

			<?php if ( have_posts() ) : ?>
				<div class="page-content">
					<div class="row posts-<?php echo count( $wp_query->posts ); ?>">
						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
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
						<?php endwhile; ?>
					</div>
				</div>
				<?php the_posts_pagination( array(
					'prev_text' => '<i class=myfont-left-open-2></i>',
					'next_text' => '<i class=myfont-right-open-2></i>',
					'type'      => 'list',
				) ); ?>

			<?php else : ?>

				<?php get_template_part( 'template-parts/content', 'none' ); ?>

			<?php endif; ?>

		</main>

		<?php get_sidebar(); ?>
	</div>
</div>

<?php get_footer(); ?>
